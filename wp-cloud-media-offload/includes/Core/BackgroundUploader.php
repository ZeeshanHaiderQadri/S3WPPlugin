<?php
namespace WPCMO\Core;

/**
 * Background Uploader using WP-Cron
 * Processes bulk uploads in background without overloading server
 */
class BackgroundUploader {
    
    private $batch_size = 10; // Process 10 images per cron run (gentle on CPU)
    private $cron_hook = 'wpcmo_background_upload_cron';
    
    public function __construct() {
        add_action($this->cron_hook, [$this, 'process_background_batch']);
        add_action('admin_init', [$this, 'maybe_schedule_cron']);
    }
    
    /**
     * Start background upload process
     */
    public function start_background_upload() {
        // Mark that background upload is active
        update_option('wpcmo_background_upload_active', true);
        update_option('wpcmo_background_upload_offset', 0);
        update_option('wpcmo_background_upload_stats', [
            'uploaded' => 0,
            'failed' => 0,
            'skipped' => 0,
            'started' => current_time('mysql'),
        ]);
        
        // Schedule cron if not already scheduled
        if (!wp_next_scheduled($this->cron_hook)) {
            wp_schedule_event(time(), 'wpcmo_every_minute', $this->cron_hook);
        }
        
        return [
            'success' => true,
            'message' => 'Background upload started! It will run automatically in the background.',
        ];
    }
    
    /**
     * Stop background upload process
     */
    public function stop_background_upload() {
        update_option('wpcmo_background_upload_active', false);
        wp_clear_scheduled_hook($this->cron_hook);
        
        return [
            'success' => true,
            'message' => 'Background upload stopped.',
        ];
    }
    
    /**
     * Get current status of background upload
     */
    public function get_status() {
        $active = get_option('wpcmo_background_upload_active', false);
        $offset = get_option('wpcmo_background_upload_offset', 0);
        $stats = get_option('wpcmo_background_upload_stats', []);
        
        $total_attachments = wp_count_posts('attachment')->inherit;
        $remaining = max(0, $total_attachments - $offset);
        $progress = $total_attachments > 0 ? round(($offset / $total_attachments) * 100) : 0;
        
        return [
            'active' => $active,
            'offset' => $offset,
            'total' => $total_attachments,
            'remaining' => $remaining,
            'progress' => $progress,
            'stats' => $stats,
            'next_run' => wp_next_scheduled($this->cron_hook),
        ];
    }
    
    /**
     * Process a batch in background via WP-Cron
     */
    public function process_background_batch() {
        // Check if background upload is active
        if (!get_option('wpcmo_background_upload_active', false)) {
            return;
        }
        
        $offset = get_option('wpcmo_background_upload_offset', 0);
        $stats = get_option('wpcmo_background_upload_stats', [
            'uploaded' => 0,
            'failed' => 0,
            'skipped' => 0,
        ]);
        
        // Get batch of attachments
        $attachments = get_posts([
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'posts_per_page' => $this->batch_size,
            'offset' => $offset,
            'post_status' => 'inherit',
        ]);
        
        // If no more attachments, stop the process
        if (empty($attachments)) {
            $this->complete_background_upload();
            return;
        }
        
        // Process each attachment
        $bulk_handler = new BulkUploadHandler();
        foreach ($attachments as $attachment) {
            $result = $this->upload_single_attachment($attachment->ID);
            
            if ($result === 'uploaded') {
                $stats['uploaded']++;
            } elseif ($result === 'failed') {
                $stats['failed']++;
            } else {
                $stats['skipped']++;
            }
        }
        
        // Update progress
        $new_offset = $offset + count($attachments);
        update_option('wpcmo_background_upload_offset', $new_offset);
        update_option('wpcmo_background_upload_stats', $stats);
        
        // Log progress
        error_log(sprintf(
            'WPCMO Background Upload: Processed %d images (Offset: %d, Uploaded: %d, Failed: %d, Skipped: %d)',
            count($attachments),
            $new_offset,
            $stats['uploaded'],
            $stats['failed'],
            $stats['skipped']
        ));
    }
    
    /**
     * Upload single attachment (reusing BulkUploadHandler logic)
     */
    private function upload_single_attachment($attachment_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wpcmo_uploads';
        
        // Check if already uploaded
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_name WHERE attachment_id = %d",
            $attachment_id
        ));
        
        if ($existing) {
            return 'skipped';
        }
        
        $file_path = get_attached_file($attachment_id);
        
        if (!file_exists($file_path)) {
            return 'failed';
        }
        
        $upload_dir = wp_upload_dir();
        $settings = get_option('wpcmo_settings', []);
        
        // Get the appropriate handler
        $provider = $settings['provider'] ?? 'aws_s3';
        if ($provider === 'wasabi') {
            $handler = new \WPCMO\Wasabi\WasabiHandler();
        } else {
            $handler = new \WPCMO\AWS\S3Handler();
        }
        
        // Upload main image
        $relative_path = str_replace($upload_dir['basedir'] . '/', '', $file_path);
        $s3_key = ($settings['file_path_prefix'] ?? 'wp-content/uploads/') . $relative_path;
        
        $result = $handler->upload_file($file_path, $s3_key);
        
        if (!$result['success']) {
            return 'failed';
        }
        
        // Store main image record
        $wpdb->insert($table_name, [
            'attachment_id' => $attachment_id,
            's3_key' => $s3_key,
            's3_url' => $result['s3_url'],
            'cloudfront_url' => $handler->get_file_url($s3_key),
            'file_size' => filesize($file_path),
        ]);
        
        // Upload thumbnails
        $metadata = wp_get_attachment_metadata($attachment_id);
        if (!empty($metadata['sizes']) && is_array($metadata['sizes'])) {
            $file_dir = dirname($file_path);
            
            foreach ($metadata['sizes'] as $size_name => $size_data) {
                $thumbnail_path = $file_dir . '/' . $size_data['file'];
                
                if (!file_exists($thumbnail_path)) {
                    continue;
                }
                
                $thumb_relative_path = str_replace($upload_dir['basedir'] . '/', '', $thumbnail_path);
                $thumb_s3_key = ($settings['file_path_prefix'] ?? 'wp-content/uploads/') . $thumb_relative_path;
                
                $thumb_result = $handler->upload_file($thumbnail_path, $thumb_s3_key);
                
                if ($thumb_result['success']) {
                    $wpdb->insert($table_name, [
                        'attachment_id' => $attachment_id,
                        's3_key' => $thumb_s3_key,
                        's3_url' => $thumb_result['s3_url'],
                        'cloudfront_url' => $handler->get_file_url($thumb_s3_key),
                        'file_size' => filesize($thumbnail_path),
                    ]);
                    
                    // Remove local thumbnail if enabled
                    if (!empty($settings['remove_local_files'])) {
                        @unlink($thumbnail_path);
                    }
                }
            }
        }
        
        // Remove local main file if enabled
        if (!empty($settings['remove_local_files'])) {
            @unlink($file_path);
        }
        
        return 'uploaded';
    }
    
    /**
     * Complete background upload
     */
    private function complete_background_upload() {
        $stats = get_option('wpcmo_background_upload_stats', []);
        $stats['completed'] = current_time('mysql');
        update_option('wpcmo_background_upload_stats', $stats);
        update_option('wpcmo_background_upload_active', false);
        
        // Clear scheduled cron
        wp_clear_scheduled_hook($this->cron_hook);
        
        // Log completion
        error_log(sprintf(
            'WPCMO Background Upload: COMPLETED! Uploaded: %d, Failed: %d, Skipped: %d',
            $stats['uploaded'] ?? 0,
            $stats['failed'] ?? 0,
            $stats['skipped'] ?? 0
        ));
    }
    
    /**
     * Maybe schedule cron if background upload is active
     */
    public function maybe_schedule_cron() {
        if (get_option('wpcmo_background_upload_active', false)) {
            if (!wp_next_scheduled($this->cron_hook)) {
                wp_schedule_event(time(), 'wpcmo_every_minute', $this->cron_hook);
            }
        }
    }
}

// Add custom cron schedule
add_filter('cron_schedules', function($schedules) {
    $schedules['wpcmo_every_minute'] = [
        'interval' => 60, // Every 60 seconds
        'display' => __('Every Minute (WPCMO)', 'wp-cloud-media-offload')
    ];
    return $schedules;
});

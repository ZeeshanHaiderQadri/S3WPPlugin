<?php
namespace WPCMO\Core;

class BulkUploadHandler {
    private $batch_size = 50;
    
    public function process_batch() {
        $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
        
        $attachments = get_posts([
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'posts_per_page' => $this->batch_size,
            'offset' => $offset,
            'post_status' => 'inherit',
        ]);
        
        $uploaded = 0;
        $failed = 0;
        $skipped = 0;
        
        foreach ($attachments as $attachment) {
            $result = $this->upload_attachment($attachment->ID);
            
            if ($result === 'uploaded') {
                $uploaded++;
            } elseif ($result === 'failed') {
                $failed++;
            } else {
                $skipped++;
            }
        }
        
        $total_attachments = wp_count_posts('attachment')->inherit;
        $processed = $offset + count($attachments);
        $remaining = max(0, $total_attachments - $processed);
        
        return [
            'uploaded' => $uploaded,
            'failed' => $failed,
            'skipped' => $skipped,
            'processed' => $processed,
            'total' => $total_attachments,
            'remaining' => $remaining,
            'complete' => $remaining === 0,
        ];
    }
    
    private function upload_attachment($attachment_id) {
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
        
        // Get the appropriate handler based on provider
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
        
        // Upload all thumbnails
        $metadata = wp_get_attachment_metadata($attachment_id);
        if (!empty($metadata['sizes']) && is_array($metadata['sizes'])) {
            $file_dir = dirname($file_path);
            
            foreach ($metadata['sizes'] as $size_name => $size_data) {
                $thumbnail_path = $file_dir . '/' . $size_data['file'];
                
                if (!file_exists($thumbnail_path)) {
                    continue;
                }
                
                // Build S3 key for thumbnail
                $thumb_relative_path = str_replace($upload_dir['basedir'] . '/', '', $thumbnail_path);
                $thumb_s3_key = ($settings['file_path_prefix'] ?? 'wp-content/uploads/') . $thumb_relative_path;
                
                // Upload thumbnail to S3
                $thumb_result = $handler->upload_file($thumbnail_path, $thumb_s3_key);
                
                if ($thumb_result['success']) {
                    // Store thumbnail record
                    $wpdb->insert($table_name, [
                        'attachment_id' => $attachment_id,
                        's3_key' => $thumb_s3_key,
                        's3_url' => $thumb_result['s3_url'],
                        'cloudfront_url' => $handler->get_file_url($thumb_s3_key),
                        'file_size' => filesize($thumbnail_path),
                    ]);
                    
                    // Remove local thumbnail if setting is enabled
                    if (!empty($settings['remove_local_files'])) {
                        @unlink($thumbnail_path);
                    }
                }
            }
        }
        
        // Remove local main file if setting is enabled
        if (!empty($settings['remove_local_files'])) {
            @unlink($file_path);
        }
        
        return 'uploaded';
    }
}

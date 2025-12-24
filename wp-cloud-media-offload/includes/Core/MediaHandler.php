<?php
namespace WPCMO\Core;

class MediaHandler {
    public function __construct() {
        error_log('WPCMO: MediaHandler constructor called');
        
        // Hook into standard WordPress uploads
        add_filter('wp_handle_upload', [$this, 'handle_upload'], 10, 2);
        
        // Hook into attachment creation (catches imports and other methods)
        add_action('add_attachment', [$this, 'handle_new_attachment'], 10, 1);
        
        // Hook AFTER WordPress generates thumbnails
        add_filter('wp_generate_attachment_metadata', [$this, 'handle_thumbnails'], 10, 2);
        
        // Filter attachment URLs to point to S3
        add_filter('wp_get_attachment_url', [$this, 'filter_attachment_url'], 10, 2);
        
        // Filter thumbnail URLs to point to S3
        add_filter('wp_get_attachment_image_src', [$this, 'filter_image_src'], 10, 4);
        
        // Filter for WooCommerce and other plugins that use wp_get_attachment_metadata
        add_filter('wp_get_attachment_metadata', [$this, 'filter_attachment_metadata'], 10, 2);
        
        // Handle attachment deletion
        add_action('delete_attachment', [$this, 'handle_delete']);
        
        error_log('WPCMO: MediaHandler hooks registered');
    }
    
    public function handle_new_attachment($attachment_id) {
        error_log('WPCMO: handle_new_attachment called for ID: ' . $attachment_id);
        
        $settings = get_option('wpcmo_settings', []);
        error_log('WPCMO: Settings: ' . print_r($settings, true));
        
        // Check if auto upload is enabled
        if (empty($settings['auto_upload'])) {
            error_log('WPCMO: Auto upload is disabled');
            return;
        }
        
        error_log('WPCMO: Auto upload is enabled');
        
        // Get the file path
        $file_path = get_attached_file($attachment_id);
        if (!$file_path || !file_exists($file_path)) {
            error_log('WPCMO: File not found: ' . ($file_path ?? 'no file'));
            return;
        }
        
        error_log('WPCMO: File exists: ' . $file_path);
        
        $upload_dir = wp_upload_dir();
        $relative_path = str_replace($upload_dir['basedir'] . '/', '', $file_path);
        
        $s3_key = ($settings['file_path_prefix'] ?? 'wp-content/uploads/') . $relative_path;
        
        error_log('WPCMO: S3 key: ' . $s3_key);
        error_log('WPCMO: Provider: ' . ($settings['provider'] ?? 'aws_s3'));
        
        // Get the appropriate handler based on provider
        $handler = $this->get_storage_handler($settings['provider'] ?? 'aws_s3');
        if (!$handler) {
            error_log('WPCMO: Failed to get storage handler');
            return;
        }
        
        error_log('WPCMO: Starting S3 upload...');
        $result = $handler->upload_file($file_path, $s3_key);
        error_log('WPCMO: Upload result: ' . print_r($result, true));
        
        if ($result['success']) {
            // Store cloud storage info in database
            global $wpdb;
            $table_name = $wpdb->prefix . 'wpcmo_uploads';
            
            $wpdb->insert($table_name, [
                'attachment_id' => $attachment_id,
                's3_key' => $s3_key,
                's3_url' => $result['s3_url'],
                'cloudfront_url' => $handler->get_file_url($s3_key),
                'file_size' => filesize($file_path),
            ]);
            
            error_log('WPCMO: Stored in database');
            
            // Remove local file if setting is enabled
            if (!empty($settings['remove_local_files'])) {
                @unlink($file_path);
                error_log('WPCMO: Local file removed');
            }
        } else {
            error_log('WPCMO: Upload failed: ' . ($result['error'] ?? 'unknown error'));
        }
    }
    
    public function handle_upload($upload, $context = 'upload') {
        error_log('WPCMO: handle_upload called');
        error_log('WPCMO: Upload data: ' . print_r($upload, true));
        
        $settings = get_option('wpcmo_settings', []);
        error_log('WPCMO: Settings: ' . print_r($settings, true));
        
        // Check if auto upload is enabled
        if (empty($settings['auto_upload'])) {
            error_log('WPCMO: Auto upload is disabled');
            return $upload;
        }
        
        error_log('WPCMO: Auto upload is enabled');
        
        // For now, allow uploads without license for testing
        // TODO: Re-enable license check in production
        // if (!$this->is_license_active()) {
        //     return $upload;
        // }
        
        if (!isset($upload['file']) || !file_exists($upload['file'])) {
            error_log('WPCMO: File not found: ' . ($upload['file'] ?? 'no file'));
            return $upload;
        }
        
        error_log('WPCMO: File exists: ' . $upload['file']);
        
        $file_path = $upload['file'];
        $upload_dir = wp_upload_dir();
        $relative_path = str_replace($upload_dir['basedir'] . '/', '', $file_path);
        
        $s3_key = ($settings['file_path_prefix'] ?? 'wp-content/uploads/') . $relative_path;
        
        error_log('WPCMO: S3 key: ' . $s3_key);
        error_log('WPCMO: Provider: ' . ($settings['provider'] ?? 'aws_s3'));
        
        // Get the appropriate handler based on provider
        $handler = $this->get_storage_handler($settings['provider'] ?? 'aws_s3');
        if (!$handler) {
            error_log('WPCMO: Failed to get storage handler');
            return $upload;
        }
        
        error_log('WPCMO: Starting S3 upload...');
        $result = $handler->upload_file($file_path, $s3_key);
        error_log('WPCMO: Upload result: ' . print_r($result, true));
        
        if ($result['success']) {
            // Store cloud storage info in database
            global $wpdb;
            $table_name = $wpdb->prefix . 'wpcmo_uploads';
            
            $wpdb->insert($table_name, [
                'attachment_id' => 0, // Will be updated later
                's3_key' => $s3_key,
                's3_url' => $result['s3_url'],
                'cloudfront_url' => $handler->get_file_url($s3_key),
                'file_size' => filesize($file_path),
            ]);
            
            // Track upload with license server
            $this->track_upload_with_server([
                'file_name' => basename($file_path),
                'file_type' => mime_content_type($file_path),
                'file_size' => filesize($file_path),
                's3_key' => $s3_key,
                's3_url' => $result['s3_url'],
            ]);
            
            // Remove local file if setting is enabled
            if (!empty($settings['remove_local_files'])) {
                @unlink($file_path);
            }
        }
        
        return $upload;
    }
    
    private function track_upload_with_server($upload_data) {
        $license_key = get_option('wpcmo_license_key', '');
        
        if (empty($license_key)) {
            return;
        }
        
        $response = wp_remote_post('https://your-license-server.com/api/v1/track-upload', [
            'body' => json_encode([
                'license_key' => $license_key,
                'domain' => home_url(),
                'attachment_id' => $upload_data['attachment_id'] ?? null,
                'file_name' => $upload_data['file_name'] ?? null,
                'file_type' => $upload_data['file_type'] ?? null,
                'file_size' => $upload_data['file_size'] ?? 0,
                's3_key' => $upload_data['s3_key'] ?? null,
                's3_url' => $upload_data['s3_url'] ?? null,
            ]),
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'timeout' => 15,
        ]);
        
        if (is_wp_error($response)) {
            error_log('WPCMO: Failed to track upload - ' . $response->get_error_message());
            return;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['limit_reached']) && $body['limit_reached']) {
            // Show admin notice about limit reached
            add_option('wpcmo_limit_reached', true);
        }
    }
    
    public function handle_thumbnails($metadata, $attachment_id) {
        error_log('WPCMO: handle_thumbnails called for ID: ' . $attachment_id);
        
        $settings = get_option('wpcmo_settings', []);
        
        // Check if auto upload is enabled
        if (empty($settings['auto_upload'])) {
            error_log('WPCMO: Auto upload is disabled, skipping thumbnails');
            return $metadata;
        }
        
        // Get the storage handler
        $handler = $this->get_storage_handler($settings['provider'] ?? 'aws_s3');
        if (!$handler) {
            error_log('WPCMO: Failed to get storage handler for thumbnails');
            return $metadata;
        }
        
        $upload_dir = wp_upload_dir();
        $file_path = get_attached_file($attachment_id);
        
        if (!$file_path) {
            error_log('WPCMO: Could not get file path for attachment ' . $attachment_id);
            return $metadata;
        }
        
        $file_dir = dirname($file_path);
        
        // Upload all thumbnail sizes
        if (!empty($metadata['sizes']) && is_array($metadata['sizes'])) {
            error_log('WPCMO: Found ' . count($metadata['sizes']) . ' thumbnails to upload');
            
            foreach ($metadata['sizes'] as $size_name => $size_data) {
                $thumbnail_path = $file_dir . '/' . $size_data['file'];
                
                if (!file_exists($thumbnail_path)) {
                    error_log('WPCMO: Thumbnail not found: ' . $thumbnail_path);
                    continue;
                }
                
                // Build S3 key for thumbnail
                $relative_path = str_replace($upload_dir['basedir'] . '/', '', $thumbnail_path);
                $s3_key = ($settings['file_path_prefix'] ?? 'wp-content/uploads/') . $relative_path;
                
                error_log('WPCMO: Uploading thumbnail ' . $size_name . ' to S3: ' . $s3_key);
                
                // Upload thumbnail to S3
                $result = $handler->upload_file($thumbnail_path, $s3_key);
                
                if ($result['success']) {
                    error_log('WPCMO: Thumbnail ' . $size_name . ' uploaded successfully');
                    
                    // Store thumbnail info in database
                    global $wpdb;
                    $table_name = $wpdb->prefix . 'wpcmo_uploads';
                    
                    $wpdb->insert($table_name, [
                        'attachment_id' => $attachment_id,
                        's3_key' => $s3_key,
                        's3_url' => $result['s3_url'],
                        'cloudfront_url' => $handler->get_file_url($s3_key),
                        'file_size' => filesize($thumbnail_path),
                    ]);
                    
                    // Remove local thumbnail if setting is enabled
                    if (!empty($settings['remove_local_files'])) {
                        @unlink($thumbnail_path);
                        error_log('WPCMO: Local thumbnail removed: ' . $thumbnail_path);
                    }
                } else {
                    error_log('WPCMO: Failed to upload thumbnail ' . $size_name . ': ' . ($result['error'] ?? 'unknown error'));
                }
            }
        } else {
            error_log('WPCMO: No thumbnails found in metadata');
        }
        
        return $metadata;
    }
    
    public function filter_attachment_url($url, $attachment_id) {
        // For now, allow URL filtering without license for testing
        // TODO: Re-enable license check in production
        // if (!$this->is_license_active()) {
        //     return $url;
        // }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'wpcmo_uploads';
        
        // Get the filename from the URL
        $filename = basename($url);
        
        // First try to find by attachment_id and filename match
        $record = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE attachment_id = %d AND s3_key LIKE %s ORDER BY id DESC LIMIT 1",
            $attachment_id,
            '%' . $wpdb->esc_like($filename)
        ));
        
        // If not found, try just by attachment_id (get the main image)
        if (!$record) {
            $record = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_name WHERE attachment_id = %d ORDER BY id ASC LIMIT 1",
                $attachment_id
            ));
        }
        
        if ($record) {
            $s3_url = $record->cloudfront_url ?: $record->s3_url;
            error_log('WPCMO: Filtered URL for attachment ' . $attachment_id . ': ' . $s3_url);
            return $s3_url;
        }
        
        // If no record found but file doesn't exist locally, try to construct S3 URL
        $file_path = get_attached_file($attachment_id);
        if ($file_path && !file_exists($file_path)) {
            error_log('WPCMO: File missing locally for attachment ' . $attachment_id . ', attempting to construct S3 URL');
            $settings = get_option('wpcmo_settings', []);
            $handler = $this->get_storage_handler($settings['provider'] ?? 'aws_s3');
            
            if ($handler) {
                $upload_dir = wp_upload_dir();
                $relative_path = str_replace($upload_dir['basedir'] . '/', '', $file_path);
                $s3_key = ($settings['file_path_prefix'] ?? 'wp-content/uploads/') . $relative_path;
                $constructed_url = $handler->get_file_url($s3_key);
                error_log('WPCMO: Constructed S3 URL: ' . $constructed_url);
                return $constructed_url;
            }
        }
        
        return $url;
    }
    
    public function filter_image_src($image, $attachment_id, $size, $icon) {
        if (!$image) {
            return $image;
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'wpcmo_uploads';
        
        // Get the file name from the original URL
        $filename = basename($image[0]);
        
        // Look up the S3 URL for this specific thumbnail
        $record = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE attachment_id = %d AND s3_key LIKE %s ORDER BY id DESC LIMIT 1",
            $attachment_id,
            '%' . $wpdb->esc_like($filename)
        ));
        
        if ($record) {
            $image[0] = $record->cloudfront_url ?: $record->s3_url;
            error_log('WPCMO: Filtered image src for attachment ' . $attachment_id . ' (' . $size . '): ' . $image[0]);
        } else {
            // If no record found but file doesn't exist locally, construct S3 URL
            $local_path = str_replace(wp_upload_dir()['baseurl'], wp_upload_dir()['basedir'], $image[0]);
            if (!file_exists($local_path)) {
                error_log('WPCMO: Thumbnail missing locally, constructing S3 URL for: ' . $filename);
                $settings = get_option('wpcmo_settings', []);
                $handler = $this->get_storage_handler($settings['provider'] ?? 'aws_s3');
                
                if ($handler) {
                    $upload_dir = wp_upload_dir();
                    $relative_path = str_replace($upload_dir['baseurl'] . '/', '', $image[0]);
                    $s3_key = ($settings['file_path_prefix'] ?? 'wp-content/uploads/') . $relative_path;
                    $image[0] = $handler->get_file_url($s3_key);
                    error_log('WPCMO: Constructed thumbnail URL: ' . $image[0]);
                }
            }
        }
        
        return $image;
    }
    
    public function filter_attachment_metadata($metadata, $attachment_id) {
        if (!$metadata || !is_array($metadata)) {
            return $metadata;
        }
        
        // Don't modify if file exists locally
        $file_path = get_attached_file($attachment_id);
        if ($file_path && file_exists($file_path)) {
            return $metadata;
        }
        
        // File is missing locally, so it's probably on S3
        // We need to ensure WordPress knows the file "exists" even though it's remote
        error_log('WPCMO: Filtering metadata for attachment ' . $attachment_id . ' (file missing locally)');
        
        return $metadata;
    }
    
    public function handle_delete($attachment_id) {
        if (!$this->is_license_active()) {
            return;
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'wpcmo_uploads';
        
        // Get ALL records for this attachment (main image + all thumbnails)
        $records = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE attachment_id = %d",
            $attachment_id
        ));
        
        if ($records) {
            $settings = get_option('wpcmo_settings', []);
            $handler = $this->get_storage_handler($settings['provider'] ?? 'aws_s3');
            
            if ($handler) {
                // Delete all files from S3 (main image + thumbnails)
                foreach ($records as $record) {
                    $handler->delete_file($record->s3_key);
                    error_log('WPCMO: Deleted from S3: ' . $record->s3_key);
                }
            }
            
            // Delete all database records for this attachment
            $wpdb->delete($table_name, ['attachment_id' => $attachment_id]);
            error_log('WPCMO: Deleted ' . count($records) . ' records from database for attachment ' . $attachment_id);
        }
    }
    
    private function is_license_active() {
        $license_status = get_option('wpcmo_license_status', 'inactive');
        return $license_status === 'active';
    }
    
    private function get_storage_handler($provider) {
        switch ($provider) {
            case 'aws_s3':
                return new \WPCMO\AWS\S3Handler();
            case 'wasabi':
                return new \WPCMO\Wasabi\WasabiHandler();
            default:
                return new \WPCMO\AWS\S3Handler(); // Default fallback
        }
    }
}

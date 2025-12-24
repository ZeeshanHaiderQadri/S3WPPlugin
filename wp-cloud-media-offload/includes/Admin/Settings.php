<?php
namespace WPCMO\Admin;

class Settings {
    public function __construct() {
        add_action('admin_init', [$this, 'register_settings']);
    }
    
    public function register_settings() {
        register_setting('wpcmo_settings_group', 'wpcmo_settings', [
            'sanitize_callback' => [$this, 'sanitize_settings'],
        ]);
    }
    
    public function sanitize_settings($input) {
        $sanitized = [];
        
        // Ensure provider is set and valid
        $valid_providers = ['aws_s3', 'wasabi', 'digitalocean', 'google_cloud'];
        $provider = sanitize_text_field($input['provider'] ?? 'aws_s3');
        $sanitized['provider'] = in_array($provider, $valid_providers) ? $provider : 'aws_s3';
        
        // AWS S3 settings
        $sanitized['aws_access_key'] = sanitize_text_field($input['aws_access_key'] ?? '');
        $sanitized['aws_secret_key'] = sanitize_text_field($input['aws_secret_key'] ?? '');
        $sanitized['aws_region'] = sanitize_text_field($input['aws_region'] ?? 'us-east-1');
        $sanitized['aws_bucket'] = sanitize_text_field($input['aws_bucket'] ?? '');
        
        // Wasabi settings
        $sanitized['wasabi_access_key'] = sanitize_text_field($input['wasabi_access_key'] ?? '');
        $sanitized['wasabi_secret_key'] = sanitize_text_field($input['wasabi_secret_key'] ?? '');
        $sanitized['wasabi_region'] = sanitize_text_field($input['wasabi_region'] ?? 'us-east-1');
        $sanitized['wasabi_bucket'] = sanitize_text_field($input['wasabi_bucket'] ?? '');
        
        // CDN settings
        $sanitized['cloudfront_enabled'] = !empty($input['cloudfront_enabled']);
        $sanitized['cloudfront_domain'] = sanitize_text_field($input['cloudfront_domain'] ?? '');
        
        // Upload settings
        $sanitized['auto_upload'] = !empty($input['auto_upload']);
        $sanitized['remove_local_files'] = !empty($input['remove_local_files']);
        $sanitized['file_path_prefix'] = sanitize_text_field($input['file_path_prefix'] ?? 'wp-content/uploads/');
        
        return $sanitized;
    }
}

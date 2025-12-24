<?php
namespace WPCMO\Core;

class Plugin {
    private static $instance = null;
    
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
        $this->load_dependencies();
    }
    
    private function init_hooks() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('wp_ajax_wpcmo_save_settings', [$this, 'ajax_save_settings']);
        add_action('wp_ajax_wpcmo_test_connection', [$this, 'ajax_test_connection']);
        add_action('wp_ajax_wpcmo_wasabi_detailed_test', [$this, 'ajax_wasabi_detailed_test']);
        add_action('wp_ajax_wpcmo_activate_license', [$this, 'ajax_activate_license']);
        add_action('wp_ajax_wpcmo_bulk_upload', [$this, 'ajax_bulk_upload']);
        add_action('wp_ajax_wpcmo_start_background_upload', [$this, 'ajax_start_background_upload']);
        add_action('wp_ajax_wpcmo_stop_background_upload', [$this, 'ajax_stop_background_upload']);
        add_action('wp_ajax_wpcmo_background_upload_status', [$this, 'ajax_background_upload_status']);
        add_filter('plugin_action_links_' . WPCMO_PLUGIN_BASENAME, [$this, 'add_plugin_action_links']);
    }
    
    private function load_dependencies() {
        error_log('WPCMO: Loading dependencies');
        new \WPCMO\Admin\Settings();
        error_log('WPCMO: Settings loaded');
        new \WPCMO\Core\MediaHandler();
        error_log('WPCMO: MediaHandler loaded');
        new \WPCMO\License\Manager();
        error_log('WPCMO: License Manager loaded');
        new \WPCMO\Core\BackgroundUploader();
        error_log('WPCMO: BackgroundUploader loaded');
    }
    
    public function add_admin_menu() {
        add_menu_page(
            __('Cloud Media Offload', 'wp-cloud-media-offload'),
            __('Cloud Media', 'wp-cloud-media-offload'),
            'manage_options',
            'wp-cloud-media-offload',
            [$this, 'render_admin_page'],
            'dashicons-cloud-upload',
            30
        );
        
        add_submenu_page(
            'wp-cloud-media-offload',
            __('Dashboard', 'wp-cloud-media-offload'),
            __('Dashboard', 'wp-cloud-media-offload'),
            'manage_options',
            'wp-cloud-media-offload',
            [$this, 'render_admin_page']
        );
        
        add_submenu_page(
            'wp-cloud-media-offload',
            __('Settings', 'wp-cloud-media-offload'),
            __('Settings', 'wp-cloud-media-offload'),
            'manage_options',
            'wpcmo-settings',
            [$this, 'render_settings_page']
        );
        
        add_submenu_page(
            'wp-cloud-media-offload',
            __('Bulk Upload', 'wp-cloud-media-offload'),
            __('Bulk Upload', 'wp-cloud-media-offload'),
            'manage_options',
            'wpcmo-bulk-upload',
            [$this, 'render_bulk_upload_page']
        );
        
        add_submenu_page(
            'wp-cloud-media-offload',
            __('License', 'wp-cloud-media-offload'),
            __('License', 'wp-cloud-media-offload'),
            'manage_options',
            'wpcmo-license',
            [$this, 'render_license_page']
        );
    }
    
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'wp-cloud-media-offload') === false && strpos($hook, 'wpcmo') === false) {
            return;
        }
        
        wp_enqueue_style('wpcmo-admin', WPCMO_PLUGIN_URL . 'assets/css/admin.css', [], WPCMO_VERSION);
        wp_enqueue_script('wpcmo-admin', WPCMO_PLUGIN_URL . 'assets/js/admin.js', ['jquery'], WPCMO_VERSION, true);
        
        wp_localize_script('wpcmo-admin', 'wpcmoData', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wpcmo_nonce'),
            'strings' => [
                'saving' => __('Saving...', 'wp-cloud-media-offload'),
                'saved' => __('Settings saved successfully!', 'wp-cloud-media-offload'),
                'error' => __('An error occurred. Please try again.', 'wp-cloud-media-offload'),
                'testing' => __('Testing connection...', 'wp-cloud-media-offload'),
                'connected' => __('Connection successful!', 'wp-cloud-media-offload'),
                'uploading' => __('Uploading...', 'wp-cloud-media-offload'),
            ]
        ]);
    }
    
    public function render_admin_page() {
        include WPCMO_PLUGIN_DIR . 'templates/admin/dashboard.php';
    }
    
    public function render_settings_page() {
        include WPCMO_PLUGIN_DIR . 'templates/admin/settings.php';
    }
    
    public function render_bulk_upload_page() {
        include WPCMO_PLUGIN_DIR . 'templates/admin/bulk-upload.php';
    }
    
    public function render_license_page() {
        include WPCMO_PLUGIN_DIR . 'templates/admin/license.php';
    }
    
    public function render_wasabi_test_page() {
        include WPCMO_PLUGIN_DIR . 'templates/admin/wasabi-test.php';
    }
    
    public function add_plugin_action_links($links) {
        $settings_link = '<a href="' . admin_url('admin.php?page=wpcmo-settings') . '">' . __('Settings', 'wp-cloud-media-offload') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }
    
    public function ajax_save_settings() {
        check_ajax_referer('wpcmo_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }
        
        $input = isset($_POST['settings']) ? $_POST['settings'] : [];
        
        // Log for debugging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('WPCMO Save Settings - Input: ' . print_r($input, true));
        }
        
        // Sanitize settings
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
        
        // Update settings
        update_option('wpcmo_settings', $sanitized);
        
        // Log for debugging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('WPCMO Save Settings - Saved: ' . print_r($sanitized, true));
        }
        
        wp_send_json_success([
            'message' => '✅ Settings saved successfully!',
            'settings' => $sanitized
        ]);
    }
    
    public function ajax_test_connection() {
        check_ajax_referer('wpcmo_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }
        
        // Get current form data for testing (if provided) or use saved settings
        $test_settings = isset($_POST['settings']) ? $_POST['settings'] : get_option('wpcmo_settings', []);
        
        // Ensure provider is properly set
        if (!isset($test_settings['provider']) || empty($test_settings['provider'])) {
            $test_settings['provider'] = 'aws_s3';
        }
        
        $provider = $test_settings['provider'];
        
        // Log for debugging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('WPCMO Test Connection - Provider: ' . $provider);
            error_log('WPCMO Test Connection - Settings: ' . print_r($test_settings, true));
        }
        
        // Check if AWS SDK is available
        if (!class_exists('Aws\S3\S3Client')) {
            wp_send_json_error([
                'message' => '❌ AWS SDK not found. Please run <code>composer install</code> in the plugin directory.'
            ]);
            return;
        }
        
        // Temporarily update settings for testing
        $original_settings = get_option('wpcmo_settings', []);
        update_option('wpcmo_settings', $test_settings);
        
        try {
            $handler = $this->get_storage_handler($provider);
            if (!$handler) {
                update_option('wpcmo_settings', $original_settings);
                wp_send_json_error(['message' => '❌ Invalid storage provider: ' . $provider]);
                return;
            }
            
            // For Wasabi, provide detailed testing
            if ($provider === 'wasabi' && method_exists($handler, 'test_connection_detailed')) {
                $detailed_results = $handler->test_connection_detailed();
                
                // Check if all tests passed
                $all_passed = true;
                $error_messages = [];
                $first_error = '';
                
                foreach ($detailed_results as $test => $result) {
                    if (strpos($result, 'FAIL') === 0) {
                        $all_passed = false;
                        $error_msg = str_replace('FAIL: ', '', $result);
                        $error_messages[] = $error_msg;
                        if (empty($first_error)) {
                            $first_error = $error_msg;
                        }
                    }
                }
                
                // Restore original settings
                update_option('wpcmo_settings', $original_settings);
                
                if ($all_passed) {
                    wp_send_json_success([
                        'message' => '✅ Wasabi connection successful! All tests passed.',
                        'details' => $detailed_results
                    ]);
                } else {
                    wp_send_json_error([
                        'message' => '❌ Wasabi connection failed: ' . $first_error,
                        'details' => $detailed_results
                    ]);
                }
            } else {
                // Standard test for other providers
                $result = $handler->test_connection();
                
                // Restore original settings
                update_option('wpcmo_settings', $original_settings);
                
                if ($result) {
                    wp_send_json_success([
                        'message' => '✅ Connection successful! ' . ucfirst(str_replace('_', ' ', $provider)) . ' is properly configured.'
                    ]);
                } else {
                    wp_send_json_error([
                        'message' => '❌ Connection failed. Please check your credentials and configuration.'
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Restore original settings
            update_option('wpcmo_settings', $original_settings);
            
            // Log the full error for debugging
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('WPCMO Test Connection Error: ' . $e->getMessage());
                error_log('WPCMO Test Connection Trace: ' . $e->getTraceAsString());
            }
            
            wp_send_json_error([
                'message' => '❌ Connection error: ' . $e->getMessage()
            ]);
        }
    }
    
    public function ajax_activate_license() {
        check_ajax_referer('wpcmo_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }
        
        $license_key = isset($_POST['license_key']) ? sanitize_text_field($_POST['license_key']) : '';
        $license_manager = new \WPCMO\License\Manager();
        $result = $license_manager->activate($license_key);
        
        if ($result['success']) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error($result);
        }
    }
    
    public function ajax_bulk_upload() {
        check_ajax_referer('wpcmo_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }
        
        $bulk_handler = new \WPCMO\Core\BulkUploadHandler();
        $result = $bulk_handler->process_batch();
        
        wp_send_json_success($result);
    }
    
    public function ajax_start_background_upload() {
        check_ajax_referer('wpcmo_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }
        
        $background_uploader = new \WPCMO\Core\BackgroundUploader();
        $result = $background_uploader->start_background_upload();
        
        wp_send_json_success($result);
    }
    
    public function ajax_stop_background_upload() {
        check_ajax_referer('wpcmo_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }
        
        $background_uploader = new \WPCMO\Core\BackgroundUploader();
        $result = $background_uploader->stop_background_upload();
        
        wp_send_json_success($result);
    }
    
    public function ajax_background_upload_status() {
        check_ajax_referer('wpcmo_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }
        
        $background_uploader = new \WPCMO\Core\BackgroundUploader();
        $status = $background_uploader->get_status();
        
        wp_send_json_success($status);
    }
    
    public function ajax_wasabi_detailed_test() {
        check_ajax_referer('wpcmo_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }
        
        $settings = get_option('wpcmo_settings', []);
        
        if (($settings['provider'] ?? 'aws_s3') !== 'wasabi') {
            wp_send_json_error(['message' => 'Wasabi is not the selected provider']);
        }
        
        try {
            $handler = new \WPCMO\Wasabi\WasabiHandler();
            
            if (method_exists($handler, 'test_connection_detailed')) {
                $results = $handler->test_connection_detailed();
                wp_send_json_success([
                    'message' => 'Detailed test completed',
                    'details' => $results
                ]);
            } else {
                wp_send_json_error(['message' => 'Detailed testing not available']);
            }
        } catch (Exception $e) {
            wp_send_json_error(['message' => 'Test error: ' . $e->getMessage()]);
        }
    }
    
    private function get_storage_handler($provider) {
        try {
            switch ($provider) {
                case 'aws_s3':
                    if (!class_exists('\WPCMO\AWS\S3Handler')) {
                        throw new \Exception('S3Handler class not found');
                    }
                    return new \WPCMO\AWS\S3Handler();
                case 'wasabi':
                    if (!class_exists('\WPCMO\Wasabi\WasabiHandler')) {
                        throw new \Exception('WasabiHandler class not found');
                    }
                    return new \WPCMO\Wasabi\WasabiHandler();
                default:
                    return new \WPCMO\AWS\S3Handler(); // Default fallback
            }
        } catch (\Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('WPCMO get_storage_handler error: ' . $e->getMessage());
            }
            throw $e;
        }
    }
}

<?php
namespace WPCMO\License;

class Manager {
    private $api_url = 'https://your-license-server.com/api/v1/';
    
    public function __construct() {
        add_action('admin_init', [$this, 'check_license']);
    }
    
    public function activate($license_key) {
        $response = wp_remote_post($this->api_url . 'activate', [
            'body' => [
                'license_key' => $license_key,
                'domain' => home_url(),
                'product' => 'wp-cloud-media-offload',
            ],
            'timeout' => 15,
        ]);
        
        if (is_wp_error($response)) {
            return [
                'success' => false,
                'message' => $response->get_error_message(),
            ];
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['success']) && $body['success']) {
            update_option('wpcmo_license_key', $license_key);
            update_option('wpcmo_license_status', 'active');
            update_option('wpcmo_license_data', $body['data']);
            
            return [
                'success' => true,
                'message' => __('License activated successfully!', 'wp-cloud-media-offload'),
                'data' => $body['data'],
            ];
        }
        
        return [
            'success' => false,
            'message' => $body['message'] ?? __('License activation failed.', 'wp-cloud-media-offload'),
        ];
    }
    
    public function deactivate() {
        $license_key = get_option('wpcmo_license_key', '');
        
        if (empty($license_key)) {
            return false;
        }
        
        $response = wp_remote_post($this->api_url . 'deactivate', [
            'body' => [
                'license_key' => $license_key,
                'domain' => home_url(),
            ],
            'timeout' => 15,
        ]);
        
        update_option('wpcmo_license_status', 'inactive');
        delete_option('wpcmo_license_data');
        
        return true;
    }
    
    public function check_license() {
        $last_check = get_option('wpcmo_license_last_check', 0);
        
        // Check once per day
        if (time() - $last_check < DAY_IN_SECONDS) {
            return;
        }
        
        $license_key = get_option('wpcmo_license_key', '');
        
        if (empty($license_key)) {
            return;
        }
        
        $response = wp_remote_post($this->api_url . 'check', [
            'body' => [
                'license_key' => $license_key,
                'domain' => home_url(),
            ],
            'timeout' => 15,
        ]);
        
        if (!is_wp_error($response)) {
            $body = json_decode(wp_remote_retrieve_body($response), true);
            
            if (isset($body['valid']) && $body['valid']) {
                update_option('wpcmo_license_status', 'active');
            } else {
                update_option('wpcmo_license_status', 'inactive');
            }
        }
        
        update_option('wpcmo_license_last_check', time());
    }
    
    public function is_active() {
        return get_option('wpcmo_license_status', 'inactive') === 'active';
    }
}

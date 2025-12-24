<?php
namespace WPCMO\Core;

class Activator {
    public static function activate() {
        // Create database tables
        self::create_tables();
        
        // Set default options
        self::set_default_options();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    private static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        $table_name = $wpdb->prefix . 'wpcmo_uploads';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            attachment_id bigint(20) NOT NULL,
            s3_key varchar(500) NOT NULL,
            s3_url varchar(1000) NOT NULL,
            cloudfront_url varchar(1000) DEFAULT NULL,
            file_size bigint(20) DEFAULT NULL,
            uploaded_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY attachment_id (attachment_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    private static function set_default_options() {
        $default_settings = [
            'provider' => 'aws_s3',
            'aws_access_key' => '',
            'aws_secret_key' => '',
            'aws_region' => 'us-east-1',
            'aws_bucket' => '',
            'cloudfront_enabled' => false,
            'cloudfront_domain' => '',
            'auto_upload' => true,
            'remove_local_files' => false,
            'file_path_prefix' => 'wp-content/uploads/',
        ];
        
        add_option('wpcmo_settings', $default_settings);
        add_option('wpcmo_license_key', '');
        add_option('wpcmo_license_status', 'inactive');
    }
}

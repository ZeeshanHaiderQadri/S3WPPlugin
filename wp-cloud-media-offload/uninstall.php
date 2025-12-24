<?php
/**
 * Uninstall script
 * Fired when the plugin is uninstalled
 */

// Exit if accessed directly
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete options
delete_option('wpcmo_settings');
delete_option('wpcmo_license_key');
delete_option('wpcmo_license_status');
delete_option('wpcmo_license_data');
delete_option('wpcmo_license_last_check');

// Drop custom table
global $wpdb;
$table_name = $wpdb->prefix . 'wpcmo_uploads';
$wpdb->query("DROP TABLE IF EXISTS $table_name");

// Clear scheduled events
wp_clear_scheduled_hook('wpcmo_bulk_upload_cron');

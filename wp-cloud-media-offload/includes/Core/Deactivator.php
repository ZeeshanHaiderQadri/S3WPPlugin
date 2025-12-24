<?php
namespace WPCMO\Core;

class Deactivator {
    public static function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Clear scheduled events
        wp_clear_scheduled_hook('wpcmo_bulk_upload_cron');
    }
}

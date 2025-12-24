<?php
/**
 * Plugin Name: WP Cloud Media Offload
 * Plugin URI: https://offloadmedia.hntechs.com
 * Description: Professional cloud media offloading solution for WordPress. Supports AWS S3, Wasabi, and more. Upload and manage unlimited media files with CDN integration. Automatically uploads thumbnails to save server inodes.
 * Version: 1.0.1
 * Author: HaiderNama Technologies Limited
 * Author URI: https://offloadmedia.hntechs.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-cloud-media-offload
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WPCMO_VERSION', '1.0.1');
define('WPCMO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WPCMO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WPCMO_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('WPCMO_PLUGIN_FILE', __FILE__);

// Load Composer autoloader for AWS SDK and other dependencies
if (file_exists(WPCMO_PLUGIN_DIR . 'vendor/autoload.php')) {
    require_once WPCMO_PLUGIN_DIR . 'vendor/autoload.php';
} else {
    // Show admin notice if vendor directory is missing
    add_action('admin_notices', function() {
        echo '<div class="notice notice-error"><p>';
        echo '<strong>WP Cloud Media Offload:</strong> Missing dependencies. Please run <code>composer install</code> in the plugin directory.';
        echo '</p></div>';
    });
    return;
}

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'WPCMO\\';
    $base_dir = WPCMO_PLUGIN_DIR . 'includes/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Initialize the plugin
function wpcmo_init() {
    error_log('WPCMO: Plugin initialization started');
    
    // Load text domain
    load_plugin_textdomain('wp-cloud-media-offload', false, dirname(WPCMO_PLUGIN_BASENAME) . '/languages');
    
    // Initialize main plugin class
    \WPCMO\Core\Plugin::instance();
    
    error_log('WPCMO: Plugin initialization completed');
}
add_action('plugins_loaded', 'wpcmo_init');

// Activation hook
register_activation_hook(__FILE__, function() {
    \WPCMO\Core\Activator::activate();
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    \WPCMO\Core\Deactivator::deactivate();
});

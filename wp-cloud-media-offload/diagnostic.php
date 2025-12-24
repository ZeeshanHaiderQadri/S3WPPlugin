<?php
/**
 * Diagnostic script for WP Cloud Media Offload
 * Access this file directly to check if dependencies are loaded correctly
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('Unauthorized');
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>WP Cloud Media Offload - Diagnostics</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #0073aa; padding-bottom: 10px; }
        .test { margin: 20px 0; padding: 15px; border-left: 4px solid #ccc; background: #f9f9f9; }
        .test.pass { border-left-color: #46b450; background: #f0f9f0; }
        .test.fail { border-left-color: #dc3232; background: #fff0f0; }
        .test h3 { margin: 0 0 10px 0; }
        .test p { margin: 5px 0; }
        code { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        .success { color: #46b450; font-weight: bold; }
        .error { color: #dc3232; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 WP Cloud Media Offload - Diagnostics</h1>
        
        <?php
        $tests = [];
        
        // Test 1: Check if plugin is active
        $tests[] = [
            'name' => 'Plugin Active',
            'pass' => defined('WPCMO_VERSION'),
            'message' => defined('WPCMO_VERSION') ? 'Plugin is active (v' . WPCMO_VERSION . ')' : 'Plugin is not active'
        ];
        
        // Test 2: Check if vendor directory exists
        $vendor_path = plugin_dir_path(__FILE__) . 'vendor/';
        $tests[] = [
            'name' => 'Vendor Directory',
            'pass' => is_dir($vendor_path),
            'message' => is_dir($vendor_path) ? 'Vendor directory exists' : 'Vendor directory not found at: ' . $vendor_path
        ];
        
        // Test 3: Check if autoload.php exists
        $autoload_path = $vendor_path . 'autoload.php';
        $tests[] = [
            'name' => 'Composer Autoloader',
            'pass' => file_exists($autoload_path),
            'message' => file_exists($autoload_path) ? 'Autoloader file exists' : 'Autoloader not found. Run: composer install'
        ];
        
        // Test 4: Check if AWS SDK is available
        $tests[] = [
            'name' => 'AWS SDK',
            'pass' => class_exists('Aws\S3\S3Client'),
            'message' => class_exists('Aws\S3\S3Client') ? 'AWS SDK is loaded' : 'AWS SDK not found. Run: composer install'
        ];
        
        // Test 5: Check plugin classes
        $tests[] = [
            'name' => 'Plugin Classes',
            'pass' => class_exists('WPCMO\Core\Plugin'),
            'message' => class_exists('WPCMO\Core\Plugin') ? 'Plugin classes loaded' : 'Plugin classes not found'
        ];
        
        // Test 6: Check S3Handler
        $tests[] = [
            'name' => 'S3Handler Class',
            'pass' => class_exists('WPCMO\AWS\S3Handler'),
            'message' => class_exists('WPCMO\AWS\S3Handler') ? 'S3Handler class available' : 'S3Handler class not found'
        ];
        
        // Test 7: Check WasabiHandler
        $tests[] = [
            'name' => 'WasabiHandler Class',
            'pass' => class_exists('WPCMO\Wasabi\WasabiHandler'),
            'message' => class_exists('WPCMO\Wasabi\WasabiHandler') ? 'WasabiHandler class available' : 'WasabiHandler class not found'
        ];
        
        // Test 8: Check settings
        $settings = get_option('wpcmo_settings', []);
        $tests[] = [
            'name' => 'Plugin Settings',
            'pass' => !empty($settings),
            'message' => !empty($settings) ? 'Settings found (Provider: ' . ($settings['provider'] ?? 'not set') . ')' : 'No settings saved yet'
        ];
        
        // Test 9: Check PHP version
        $php_version = phpversion();
        $tests[] = [
            'name' => 'PHP Version',
            'pass' => version_compare($php_version, '7.4', '>='),
            'message' => 'PHP ' . $php_version . ' (' . (version_compare($php_version, '7.4', '>=') ? 'OK' : 'Requires 7.4+') . ')'
        ];
        
        // Test 10: Check WordPress version
        global $wp_version;
        $tests[] = [
            'name' => 'WordPress Version',
            'pass' => version_compare($wp_version, '5.8', '>='),
            'message' => 'WordPress ' . $wp_version . ' (' . (version_compare($wp_version, '5.8', '>=') ? 'OK' : 'Requires 5.8+') . ')'
        ];
        
        // Display results
        $passed = 0;
        $failed = 0;
        
        foreach ($tests as $test) {
            $class = $test['pass'] ? 'pass' : 'fail';
            $status = $test['pass'] ? '✅ PASS' : '❌ FAIL';
            
            if ($test['pass']) {
                $passed++;
            } else {
                $failed++;
            }
            
            echo '<div class="test ' . $class . '">';
            echo '<h3>' . $status . ' - ' . htmlspecialchars($test['name']) . '</h3>';
            echo '<p>' . htmlspecialchars($test['message']) . '</p>';
            echo '</div>';
        }
        
        // Summary
        echo '<div style="margin-top: 30px; padding: 20px; background: #f0f0f0; border-radius: 8px;">';
        echo '<h2>Summary</h2>';
        echo '<p><span class="success">Passed: ' . $passed . '</span> | <span class="error">Failed: ' . $failed . '</span></p>';
        
        if ($failed === 0) {
            echo '<p class="success">✅ All tests passed! The plugin should work correctly.</p>';
        } else {
            echo '<p class="error">⚠️ Some tests failed. Please fix the issues above.</p>';
            
            if (!class_exists('Aws\S3\S3Client')) {
                echo '<p><strong>To fix AWS SDK issue:</strong></p>';
                echo '<ol>';
                echo '<li>Open terminal/command prompt</li>';
                echo '<li>Navigate to plugin directory: <code>cd ' . plugin_dir_path(__FILE__) . '</code></li>';
                echo '<li>Run: <code>composer install</code></li>';
                echo '</ol>';
            }
        }
        echo '</div>';
        
        // Additional info
        echo '<div style="margin-top: 20px; padding: 15px; background: #e7f3ff; border-radius: 8px;">';
        echo '<h3>📋 Additional Information</h3>';
        echo '<p><strong>Plugin Directory:</strong> <code>' . plugin_dir_path(__FILE__) . '</code></p>';
        echo '<p><strong>Plugin URL:</strong> <code>' . plugin_dir_url(__FILE__) . '</code></p>';
        echo '<p><strong>WordPress Debug:</strong> ' . (defined('WP_DEBUG') && WP_DEBUG ? 'Enabled' : 'Disabled') . '</p>';
        echo '</div>';
        ?>
        
        <div style="margin-top: 20px; text-align: center;">
            <a href="<?php echo admin_url('admin.php?page=wpcmo-settings'); ?>" style="display: inline-block; padding: 10px 20px; background: #0073aa; color: white; text-decoration: none; border-radius: 4px;">
                Go to Plugin Settings
            </a>
        </div>
    </div>
</body>
</html>

<?php
/**
 * Auto Upload Diagnostic Tool
 * 
 * Upload this file to your WordPress root directory and access it via:
 * https://artstationers.com/check-auto-upload.php
 * 
 * This will help diagnose why auto-upload isn't working
 */

// Load WordPress
require_once('wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('Access denied. You must be an administrator.');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Auto Upload Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #0073aa; padding-bottom: 10px; }
        h2 { color: #0073aa; margin-top: 30px; }
        .status { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 4px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        table td, table th { padding: 8px; border: 1px solid #ddd; text-align: left; }
        table th { background: #0073aa; color: white; }
        .code { font-family: monospace; background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 WP Cloud Media Offload - Auto Upload Diagnostic</h1>
        <p><strong>Website:</strong> <?php echo home_url(); ?></p>
        <p><strong>Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>

        <?php
        // 1. Check if plugin is active
        echo '<h2>1. Plugin Status</h2>';
        if (class_exists('WPCMO\Core\Plugin')) {
            echo '<div class="status success">✅ Plugin is active and loaded</div>';
        } else {
            echo '<div class="status error">❌ Plugin is NOT active or not loaded properly</div>';
            echo '<p>Please activate the WP Cloud Media Offload plugin first.</p>';
            exit;
        }

        // 2. Check settings
        echo '<h2>2. Plugin Settings</h2>';
        $settings = get_option('wpcmo_settings', []);
        
        if (empty($settings)) {
            echo '<div class="status error">❌ No settings found! Please configure the plugin.</div>';
        } else {
            echo '<div class="status success">✅ Settings found</div>';
            echo '<table>';
            echo '<tr><th>Setting</th><th>Value</th><th>Status</th></tr>';
            
            // Provider
            $provider = $settings['provider'] ?? 'not set';
            $provider_status = !empty($settings['provider']) ? '✅' : '❌';
            echo "<tr><td>Provider</td><td><code>$provider</code></td><td>$provider_status</td></tr>";
            
            // Auto Upload
            $auto_upload = !empty($settings['auto_upload']) ? 'Enabled' : 'Disabled';
            $auto_upload_status = !empty($settings['auto_upload']) ? '✅' : '❌ DISABLED';
            echo "<tr><td><strong>Auto Upload</strong></td><td><strong>$auto_upload</strong></td><td><strong>$auto_upload_status</strong></td></tr>";
            
            // AWS/Wasabi credentials
            if ($provider === 'aws_s3') {
                $access_key = !empty($settings['aws_access_key']) ? '✅ Set' : '❌ Not set';
                $secret_key = !empty($settings['aws_secret_key']) ? '✅ Set' : '❌ Not set';
                $bucket = $settings['aws_bucket'] ?? 'not set';
                $region = $settings['aws_region'] ?? 'not set';
                
                echo "<tr><td>AWS Access Key</td><td>$access_key</td><td>" . (!empty($settings['aws_access_key']) ? '✅' : '❌') . "</td></tr>";
                echo "<tr><td>AWS Secret Key</td><td>$secret_key</td><td>" . (!empty($settings['aws_secret_key']) ? '✅' : '❌') . "</td></tr>";
                echo "<tr><td>AWS Bucket</td><td><code>$bucket</code></td><td>" . (!empty($settings['aws_bucket']) ? '✅' : '❌') . "</td></tr>";
                echo "<tr><td>AWS Region</td><td><code>$region</code></td><td>" . (!empty($settings['aws_region']) ? '✅' : '❌') . "</td></tr>";
            } elseif ($provider === 'wasabi') {
                $access_key = !empty($settings['wasabi_access_key']) ? '✅ Set' : '❌ Not set';
                $secret_key = !empty($settings['wasabi_secret_key']) ? '✅ Set' : '❌ Not set';
                $bucket = $settings['wasabi_bucket'] ?? 'not set';
                $region = $settings['wasabi_region'] ?? 'not set';
                
                echo "<tr><td>Wasabi Access Key</td><td>$access_key</td><td>" . (!empty($settings['wasabi_access_key']) ? '✅' : '❌') . "</td></tr>";
                echo "<tr><td>Wasabi Secret Key</td><td>$secret_key</td><td>" . (!empty($settings['wasabi_secret_key']) ? '✅' : '❌') . "</td></tr>";
                echo "<tr><td>Wasabi Bucket</td><td><code>$bucket</code></td><td>" . (!empty($settings['wasabi_bucket']) ? '✅' : '❌') . "</td></tr>";
                echo "<tr><td>Wasabi Region</td><td><code>$region</code></td><td>" . (!empty($settings['wasabi_region']) ? '✅' : '❌') . "</td></tr>";
            }
            
            // Other settings
            $remove_local = !empty($settings['remove_local_files']) ? 'Yes' : 'No';
            $file_prefix = $settings['file_path_prefix'] ?? 'wp-content/uploads/';
            
            echo "<tr><td>Remove Local Files</td><td>$remove_local</td><td>ℹ️</td></tr>";
            echo "<tr><td>File Path Prefix</td><td><code>$file_prefix</code></td><td>ℹ️</td></tr>";
            
            echo '</table>';
        }

        // 3. Check if auto upload is enabled
        echo '<h2>3. Auto Upload Status</h2>';
        if (empty($settings['auto_upload'])) {
            echo '<div class="status error">❌ <strong>AUTO UPLOAD IS DISABLED!</strong></div>';
            echo '<p>This is likely the problem. To fix:</p>';
            echo '<ol>';
            echo '<li>Go to <strong>Cloud Media → Settings</strong></li>';
            echo '<li>Check the <strong>"Auto Upload"</strong> checkbox</li>';
            echo '<li>Click <strong>"Save Settings"</strong></li>';
            echo '</ol>';
        } else {
            echo '<div class="status success">✅ Auto Upload is ENABLED</div>';
        }

        // 4. Check WordPress hooks
        echo '<h2>4. WordPress Hooks</h2>';
        global $wp_filter;
        
        $hooks_to_check = [
            'wp_handle_upload' => 'Handles file uploads',
            'add_attachment' => 'Triggered when attachment is added',
            'wp_generate_attachment_metadata' => 'Handles thumbnail generation',
        ];
        
        echo '<table>';
        echo '<tr><th>Hook</th><th>Description</th><th>Status</th></tr>';
        
        foreach ($hooks_to_check as $hook => $description) {
            $registered = isset($wp_filter[$hook]) && !empty($wp_filter[$hook]);
            $status = $registered ? '✅ Registered' : '❌ Not registered';
            echo "<tr><td><code>$hook</code></td><td>$description</td><td>$status</td></tr>";
        }
        
        echo '</table>';

        // 5. Check database table
        echo '<h2>5. Database Table</h2>';
        global $wpdb;
        $table_name = $wpdb->prefix . 'wpcmo_uploads';
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
        
        if ($table_exists) {
            echo '<div class="status success">✅ Database table exists: <code>' . $table_name . '</code></div>';
            
            $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
            echo "<p>Total uploads in database: <strong>$count</strong></p>";
            
            if ($count > 0) {
                echo '<p>Recent uploads:</p>';
                $recent = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC LIMIT 5");
                echo '<table>';
                echo '<tr><th>ID</th><th>Attachment ID</th><th>S3 Key</th><th>Created</th></tr>';
                foreach ($recent as $row) {
                    echo "<tr>";
                    echo "<td>{$row->id}</td>";
                    echo "<td>{$row->attachment_id}</td>";
                    echo "<td><code>" . esc_html($row->s3_key) . "</code></td>";
                    echo "<td>{$row->created_at}</td>";
                    echo "</tr>";
                }
                echo '</table>';
            }
        } else {
            echo '<div class="status error">❌ Database table does NOT exist: <code>' . $table_name . '</code></div>';
            echo '<p>Try deactivating and reactivating the plugin to create the table.</p>';
        }

        // 6. Check AWS SDK
        echo '<h2>6. AWS SDK</h2>';
        if (class_exists('Aws\S3\S3Client')) {
            echo '<div class="status success">✅ AWS SDK is loaded</div>';
        } else {
            echo '<div class="status error">❌ AWS SDK is NOT loaded</div>';
            echo '<p>The vendor folder might be missing. Please ensure composer dependencies are installed.</p>';
        }

        // 7. Test connection
        echo '<h2>7. Connection Test</h2>';
        if (!empty($settings['auto_upload']) && !empty($settings['provider'])) {
            try {
                $provider = $settings['provider'];
                
                if ($provider === 'aws_s3' && class_exists('\WPCMO\AWS\S3Handler')) {
                    $handler = new \WPCMO\AWS\S3Handler();
                    $result = $handler->test_connection();
                    
                    if ($result) {
                        echo '<div class="status success">✅ Connection to AWS S3 successful!</div>';
                    } else {
                        echo '<div class="status error">❌ Connection to AWS S3 failed!</div>';
                        echo '<p>Check your credentials and bucket permissions.</p>';
                    }
                } elseif ($provider === 'wasabi' && class_exists('\WPCMO\Wasabi\WasabiHandler')) {
                    $handler = new \WPCMO\Wasabi\WasabiHandler();
                    $result = $handler->test_connection();
                    
                    if ($result) {
                        echo '<div class="status success">✅ Connection to Wasabi successful!</div>';
                    } else {
                        echo '<div class="status error">❌ Connection to Wasabi failed!</div>';
                        echo '<p>Check your credentials and bucket permissions.</p>';
                    }
                } else {
                    echo '<div class="status warning">⚠️ Could not test connection - handler class not found</div>';
                }
            } catch (Exception $e) {
                echo '<div class="status error">❌ Connection test error: ' . esc_html($e->getMessage()) . '</div>';
            }
        } else {
            echo '<div class="status warning">⚠️ Cannot test connection - auto upload is disabled or provider not set</div>';
        }

        // 8. Check debug log
        echo '<h2>8. Debug Information</h2>';
        echo '<p>WordPress Debug Mode: ' . (WP_DEBUG ? '<span class="code">Enabled</span>' : '<span class="code">Disabled</span>') . '</p>';
        
        if (WP_DEBUG) {
            echo '<div class="status info">ℹ️ Debug mode is enabled. Check your <code>wp-content/debug.log</code> file for detailed error messages.</div>';
            echo '<p>Look for lines starting with <code>WPCMO:</code></p>';
        } else {
            echo '<div class="status warning">⚠️ Debug mode is disabled. Enable it to see detailed error messages:</div>';
            echo '<pre>define(\'WP_DEBUG\', true);
define(\'WP_DEBUG_LOG\', true);
define(\'WP_DEBUG_DISPLAY\', false);</pre>';
            echo '<p>Add these lines to your <code>wp-config.php</code> file.</p>';
        }

        // 9. Comparison with working site
        echo '<h2>9. Troubleshooting Steps</h2>';
        echo '<div class="status info">';
        echo '<p><strong>Since it works on flexiology.store but not here, check:</strong></p>';
        echo '<ol>';
        echo '<li><strong>Auto Upload Setting:</strong> Make sure "Auto Upload" is checked in Settings</li>';
        echo '<li><strong>Save Settings:</strong> After checking Auto Upload, click "Save Settings"</li>';
        echo '<li><strong>Clear Cache:</strong> Clear any caching plugins (WP Rocket, W3 Total Cache, etc.)</li>';
        echo '<li><strong>Test Upload:</strong> Try uploading a new image to Media Library</li>';
        echo '<li><strong>Check Debug Log:</strong> Enable WP_DEBUG and check debug.log for WPCMO messages</li>';
        echo '<li><strong>Compare Settings:</strong> Export settings from flexiology.store and compare</li>';
        echo '<li><strong>Plugin Conflicts:</strong> Temporarily disable other plugins to test</li>';
        echo '<li><strong>File Permissions:</strong> Check that WordPress can write to wp-content/uploads/</li>';
        echo '</ol>';
        echo '</div>';

        // 10. Quick fix
        echo '<h2>10. Quick Fix</h2>';
        echo '<div class="status warning">';
        echo '<p><strong>Most Common Issue:</strong> Auto Upload checkbox is not checked!</p>';
        echo '<p><strong>Solution:</strong></p>';
        echo '<ol>';
        echo '<li>Go to <strong>WordPress Admin → Cloud Media → Settings</strong></li>';
        echo '<li>Scroll down to <strong>"Upload Settings"</strong></li>';
        echo '<li>Check the <strong>"Auto Upload"</strong> checkbox</li>';
        echo '<li>Click <strong>"Save Settings"</strong> button</li>';
        echo '<li>Try uploading a test image</li>';
        echo '</ol>';
        echo '</div>';

        // Summary
        echo '<h2>Summary</h2>';
        $issues = [];
        
        if (empty($settings)) {
            $issues[] = 'Plugin not configured';
        }
        if (empty($settings['auto_upload'])) {
            $issues[] = 'Auto Upload is DISABLED (most likely cause!)';
        }
        if (empty($settings['provider'])) {
            $issues[] = 'No provider selected';
        }
        if ($settings['provider'] === 'aws_s3' && empty($settings['aws_bucket'])) {
            $issues[] = 'AWS bucket not configured';
        }
        if ($settings['provider'] === 'wasabi' && empty($settings['wasabi_bucket'])) {
            $issues[] = 'Wasabi bucket not configured';
        }
        if (!$table_exists) {
            $issues[] = 'Database table missing';
        }
        
        if (empty($issues)) {
            echo '<div class="status success">✅ <strong>Everything looks good!</strong> If uploads still don\'t work, check the debug log.</div>';
        } else {
            echo '<div class="status error">';
            echo '<p><strong>Issues found:</strong></p>';
            echo '<ul>';
            foreach ($issues as $issue) {
                echo '<li>' . esc_html($issue) . '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }
        ?>

        <hr style="margin: 30px 0;">
        <p style="text-align: center; color: #666;">
            <small>WP Cloud Media Offload v<?php echo WPCMO_VERSION; ?> | 
            <a href="<?php echo admin_url('admin.php?page=wpcmo-settings'); ?>">Go to Settings</a> | 
            <a href="<?php echo admin_url('admin.php?page=wp-cloud-media-offload'); ?>">Go to Dashboard</a>
            </small>
        </p>
    </div>
</body>
</html>

<?php
/**
 * Wasabi Connection Debug Script
 * Upload this file to your WordPress root and access it directly
 * URL: https://yoursite.com/debug-wasabi.php
 */

// Load WordPress
require_once('wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('Access denied. You must be an administrator.');
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Wasabi Debug</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .section { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .pass { color: green; font-weight: bold; }
        .fail { color: red; font-weight: bold; }
        .info { color: blue; }
        pre { background: #f0f0f0; padding: 10px; border-radius: 4px; overflow-x: auto; }
        h2 { border-bottom: 2px solid #333; padding-bottom: 10px; }
    </style>
</head>
<body>
    <h1>🧪 Wasabi Connection Debug</h1>
    
    <?php
    echo '<div class="section">';
    echo '<h2>1. PHP Environment</h2>';
    echo '<p>PHP Version: <strong>' . phpversion() . '</strong></p>';
    echo '<p>WordPress Version: <strong>' . get_bloginfo('version') . '</strong></p>';
    echo '</div>';
    
    echo '<div class="section">';
    echo '<h2>2. AWS SDK Check</h2>';
    
    // Check if AWS SDK is available
    $composer_autoload = WP_CONTENT_DIR . '/plugins/wp-cloud-media-offload/vendor/autoload.php';
    if (file_exists($composer_autoload)) {
        echo '<p class="pass">✅ Composer autoload found</p>';
        require_once($composer_autoload);
        
        if (class_exists('Aws\S3\S3Client')) {
            echo '<p class="pass">✅ AWS SDK S3Client class available</p>';
        } else {
            echo '<p class="fail">❌ AWS SDK S3Client class NOT found</p>';
            echo '<p>Run: <code>cd wp-content/plugins/wp-cloud-media-offload && composer install</code></p>';
        }
    } else {
        echo '<p class="fail">❌ Composer autoload NOT found</p>';
        echo '<p>Run: <code>cd wp-content/plugins/wp-cloud-media-offload && composer install</code></p>';
    }
    echo '</div>';
    
    echo '<div class="section">';
    echo '<h2>3. Plugin Settings</h2>';
    $settings = get_option('wpcmo_settings', []);
    
    echo '<p>Provider: <strong>' . ($settings['provider'] ?? 'NOT SET') . '</strong></p>';
    echo '<p>Wasabi Access Key: ' . (!empty($settings['wasabi_access_key']) ? '<span class="pass">✅ SET (' . substr($settings['wasabi_access_key'], 0, 8) . '...)</span>' : '<span class="fail">❌ NOT SET</span>') . '</p>';
    echo '<p>Wasabi Secret Key: ' . (!empty($settings['wasabi_secret_key']) ? '<span class="pass">✅ SET (length: ' . strlen($settings['wasabi_secret_key']) . ')</span>' : '<span class="fail">❌ NOT SET</span>') . '</p>';
    echo '<p>Wasabi Region: <strong>' . ($settings['wasabi_region'] ?? 'NOT SET') . '</strong></p>';
    echo '<p>Wasabi Bucket: <strong>' . ($settings['wasabi_bucket'] ?? 'NOT SET') . '</strong></p>';
    
    echo '<details><summary>View Full Settings (click to expand)</summary>';
    echo '<pre>' . print_r($settings, true) . '</pre>';
    echo '</details>';
    echo '</div>';
    
    // Only proceed if AWS SDK is available
    if (class_exists('Aws\S3\S3Client')) {
        echo '<div class="section">';
        echo '<h2>4. Wasabi Endpoint Test</h2>';
        
        $region = $settings['wasabi_region'] ?? 'us-east-1';
        $endpoints = [
            'us-east-1' => 's3.wasabisys.com',
            'us-east-2' => 's3.us-east-2.wasabisys.com',
            'us-west-1' => 's3.us-west-1.wasabisys.com',
            'eu-central-1' => 's3.eu-central-1.wasabisys.com',
            'eu-west-1' => 's3.eu-west-1.wasabisys.com',
            'eu-west-2' => 's3.eu-west-2.wasabisys.com',
            'ap-northeast-1' => 's3.ap-northeast-1.wasabisys.com',
            'ap-northeast-2' => 's3.ap-northeast-2.wasabisys.com',
            'ap-southeast-1' => 's3.ap-southeast-1.wasabisys.com',
            'ap-southeast-2' => 's3.ap-southeast-2.wasabisys.com',
        ];
        
        $endpoint = $endpoints[$region] ?? 's3.wasabisys.com';
        echo '<p>Region: <strong>' . $region . '</strong></p>';
        echo '<p>Endpoint: <strong>https://' . $endpoint . '</strong></p>';
        
        // Test HTTP connectivity
        $response = wp_remote_get('https://' . $endpoint, ['timeout' => 10]);
        if (is_wp_error($response)) {
            echo '<p class="fail">❌ Cannot reach endpoint: ' . $response->get_error_message() . '</p>';
        } else {
            $code = wp_remote_retrieve_response_code($response);
            echo '<p class="pass">✅ Endpoint reachable (HTTP ' . $code . ')</p>';
        }
        echo '</div>';
        
        echo '<div class="section">';
        echo '<h2>5. S3 Client Initialization</h2>';
        
        if (empty($settings['wasabi_access_key']) || empty($settings['wasabi_secret_key'])) {
            echo '<p class="fail">❌ Cannot test - credentials not set</p>';
        } else {
            try {
                $s3_client = new Aws\S3\S3Client([
                    'version' => 'latest',
                    'region' => $region,
                    'endpoint' => 'https://' . $endpoint,
                    'use_path_style_endpoint' => false,
                    'credentials' => [
                        'key' => trim($settings['wasabi_access_key']),
                        'secret' => trim($settings['wasabi_secret_key']),
                    ],
                    'signature_version' => 'v4',
                    'http' => [
                        'timeout' => 30,
                        'connect_timeout' => 10,
                    ],
                ]);
                
                echo '<p class="pass">✅ S3 Client initialized successfully</p>';
                
                echo '<h3>6. List Buckets Test</h3>';
                try {
                    $result = $s3_client->listBuckets();
                    $buckets = $result['Buckets'];
                    echo '<p class="pass">✅ Successfully listed buckets (found ' . count($buckets) . ')</p>';
                    
                    if (count($buckets) > 0) {
                        echo '<p>Your buckets:</p><ul>';
                        foreach ($buckets as $bucket) {
                            $bucketName = $bucket['Name'];
                            $isTarget = ($bucketName === ($settings['wasabi_bucket'] ?? ''));
                            echo '<li>' . $bucketName . ($isTarget ? ' <strong class="pass">← TARGET BUCKET</strong>' : '') . '</li>';
                        }
                        echo '</ul>';
                    }
                } catch (Aws\Exception\AwsException $e) {
                    echo '<p class="fail">❌ List Buckets Failed</p>';
                    echo '<p>Error Code: <strong>' . $e->getAwsErrorCode() . '</strong></p>';
                    echo '<p>Error Message: <strong>' . $e->getMessage() . '</strong></p>';
                    
                    // Specific error guidance
                    switch ($e->getAwsErrorCode()) {
                        case 'InvalidAccessKeyId':
                            echo '<p class="info">💡 Your access key is incorrect or not recognized by Wasabi.</p>';
                            break;
                        case 'SignatureDoesNotMatch':
                            echo '<p class="info">💡 Your secret key is incorrect.</p>';
                            break;
                        default:
                            echo '<p class="info">💡 Check your credentials in the Wasabi console.</p>';
                    }
                }
                
                if (!empty($settings['wasabi_bucket'])) {
                    echo '<h3>7. Bucket Access Test</h3>';
                    try {
                        $s3_client->headBucket(['Bucket' => $settings['wasabi_bucket']]);
                        echo '<p class="pass">✅ Can access bucket: ' . $settings['wasabi_bucket'] . '</p>';
                        
                        // Try to get bucket location
                        try {
                            $location = $s3_client->getBucketLocation(['Bucket' => $settings['wasabi_bucket']]);
                            $bucketRegion = $location['LocationConstraint'] ?: 'us-east-1';
                            echo '<p>Bucket Region: <strong>' . $bucketRegion . '</strong></p>';
                            
                            if ($bucketRegion !== $region) {
                                echo '<p class="fail">⚠️ WARNING: Bucket is in region "' . $bucketRegion . '" but you selected "' . $region . '"</p>';
                                echo '<p class="info">💡 Change your region setting to match the bucket region!</p>';
                            }
                        } catch (Exception $e) {
                            echo '<p class="info">ℹ️ Could not determine bucket region</p>';
                        }
                        
                    } catch (Aws\Exception\AwsException $e) {
                        echo '<p class="fail">❌ Cannot access bucket</p>';
                        echo '<p>Error Code: <strong>' . $e->getAwsErrorCode() . '</strong></p>';
                        echo '<p>Error Message: <strong>' . $e->getMessage() . '</strong></p>';
                        
                        switch ($e->getAwsErrorCode()) {
                            case 'NoSuchBucket':
                                echo '<p class="info">💡 Bucket "' . $settings['wasabi_bucket'] . '" does not exist or is in a different region.</p>';
                                break;
                            case 'AccessDenied':
                                echo '<p class="info">💡 Your access key does not have permission to access this bucket.</p>';
                                break;
                        }
                    }
                }
                
            } catch (Exception $e) {
                echo '<p class="fail">❌ Failed to initialize S3 Client</p>';
                echo '<p>Error: ' . $e->getMessage() . '</p>';
            }
        }
        echo '</div>';
    }
    
    echo '<div class="section">';
    echo '<h2>8. Recommendations</h2>';
    echo '<ul>';
    
    if (!class_exists('Aws\S3\S3Client')) {
        echo '<li class="fail">Install AWS SDK: <code>cd wp-content/plugins/wp-cloud-media-offload && composer install</code></li>';
    }
    
    if (empty($settings['wasabi_access_key'])) {
        echo '<li class="fail">Set Wasabi Access Key in plugin settings</li>';
    }
    
    if (empty($settings['wasabi_secret_key'])) {
        echo '<li class="fail">Set Wasabi Secret Key in plugin settings</li>';
    }
    
    if (empty($settings['wasabi_bucket'])) {
        echo '<li class="fail">Set Wasabi Bucket Name in plugin settings</li>';
    }
    
    if (($settings['provider'] ?? '') !== 'wasabi') {
        echo '<li class="fail">Select Wasabi as provider in plugin settings</li>';
    }
    
    echo '</ul>';
    echo '</div>';
    
    echo '<div class="section">';
    echo '<h2>9. Next Steps</h2>';
    echo '<ol>';
    echo '<li>Review the test results above</li>';
    echo '<li>Fix any issues marked with ❌</li>';
    echo '<li>Go to <a href="' . admin_url('admin.php?page=wpcmo-settings') . '">Plugin Settings</a> to update configuration</li>';
    echo '<li>Refresh this page to re-test</li>';
    echo '<li>Once all tests pass, try uploading a test image</li>';
    echo '</ol>';
    echo '</div>';
    
    echo '<div class="section">';
    echo '<p><strong>⚠️ Security Note:</strong> Delete this file after debugging!</p>';
    echo '<p><code>rm ' . __FILE__ . '</code></p>';
    echo '</div>';
    ?>
</body>
</html>

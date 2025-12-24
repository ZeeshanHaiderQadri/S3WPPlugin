<?php
/**
 * Simple connection test script
 * Access this directly to test Wasabi connection
 */

// Load WordPress
$wp_load_paths = [
    '../../../wp-load.php',
    '../../../../wp-load.php',
    '../../../../../wp-load.php',
];

$wp_loaded = false;
foreach ($wp_load_paths as $path) {
    if (file_exists(__DIR__ . '/' . $path)) {
        require_once(__DIR__ . '/' . $path);
        $wp_loaded = true;
        break;
    }
}

if (!$wp_loaded) {
    die('Could not load WordPress');
}

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('Unauthorized');
}

header('Content-Type: application/json');

try {
    // Get settings
    $settings = get_option('wpcmo_settings', []);
    
    $result = [
        'success' => false,
        'checks' => [],
        'settings' => [
            'provider' => $settings['provider'] ?? 'not set',
            'has_wasabi_key' => !empty($settings['wasabi_access_key']),
            'has_wasabi_secret' => !empty($settings['wasabi_secret_key']),
            'wasabi_bucket' => $settings['wasabi_bucket'] ?? 'not set',
            'wasabi_region' => $settings['wasabi_region'] ?? 'not set',
        ]
    ];
    
    // Check 1: AWS SDK
    if (!class_exists('Aws\S3\S3Client')) {
        $result['checks'][] = '❌ AWS SDK not found';
        echo json_encode($result, JSON_PRETTY_PRINT);
        exit;
    }
    $result['checks'][] = '✅ AWS SDK loaded';
    
    // Check 2: WasabiHandler class
    if (!class_exists('WPCMO\Wasabi\WasabiHandler')) {
        $result['checks'][] = '❌ WasabiHandler class not found';
        echo json_encode($result, JSON_PRETTY_PRINT);
        exit;
    }
    $result['checks'][] = '✅ WasabiHandler class available';
    
    // Check 3: Credentials
    if (empty($settings['wasabi_access_key']) || empty($settings['wasabi_secret_key'])) {
        $result['checks'][] = '❌ Wasabi credentials not configured';
        echo json_encode($result, JSON_PRETTY_PRINT);
        exit;
    }
    $result['checks'][] = '✅ Wasabi credentials configured';
    
    // Check 4: Bucket name
    if (empty($settings['wasabi_bucket'])) {
        $result['checks'][] = '❌ Wasabi bucket not configured';
        echo json_encode($result, JSON_PRETTY_PRINT);
        exit;
    }
    $result['checks'][] = '✅ Wasabi bucket configured';
    
    // Check 5: Test connection
    $handler = new \WPCMO\Wasabi\WasabiHandler();
    
    if (method_exists($handler, 'test_connection_detailed')) {
        $detailed = $handler->test_connection_detailed();
        $result['detailed_tests'] = $detailed;
        
        // Check if all passed
        $all_passed = true;
        foreach ($detailed as $test => $status) {
            if (strpos($status, 'FAIL') === 0) {
                $all_passed = false;
                break;
            }
        }
        
        if ($all_passed) {
            $result['success'] = true;
            $result['checks'][] = '✅ All connection tests passed';
        } else {
            $result['checks'][] = '❌ Some connection tests failed';
        }
    } else {
        $connection_test = $handler->test_connection();
        if ($connection_test) {
            $result['success'] = true;
            $result['checks'][] = '✅ Connection test passed';
        } else {
            $result['checks'][] = '❌ Connection test failed';
        }
    }
    
    echo json_encode($result, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT);
}

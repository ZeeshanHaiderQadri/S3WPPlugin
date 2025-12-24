<?php
namespace WPCMO\Tests;

use WPCMO\Wasabi\WasabiHandler;

/**
 * Simple test class for Wasabi integration
 * This is not a full PHPUnit test, just a basic verification
 */
class WasabiTest {
    
    public static function run_basic_tests() {
        $results = [];
        
        // Test 1: Class instantiation
        try {
            $handler = new WasabiHandler();
            $results['instantiation'] = 'PASS';
        } catch (Exception $e) {
            $results['instantiation'] = 'FAIL: ' . $e->getMessage();
        }
        
        // Test 2: Endpoint mapping
        $handler = new WasabiHandler();
        $reflection = new \ReflectionClass($handler);
        $property = $reflection->getProperty('wasabi_endpoints');
        $property->setAccessible(true);
        $endpoints = $property->getValue($handler);
        
        if (isset($endpoints['us-east-1']) && $endpoints['us-east-1'] === 's3.wasabisys.com') {
            $results['endpoints'] = 'PASS';
        } else {
            $results['endpoints'] = 'FAIL: Endpoint mapping incorrect';
        }
        
        // Test 3: URL generation
        try {
            // Mock settings for testing
            update_option('wpcmo_settings', [
                'wasabi_bucket' => 'test-bucket',
                'wasabi_region' => 'us-east-1'
            ]);
            
            $handler = new WasabiHandler();
            $url = $handler->get_file_url('test/file.jpg');
            $expected = 'https://test-bucket.s3.wasabisys.com/test/file.jpg';
            
            if ($url === $expected) {
                $results['url_generation'] = 'PASS';
            } else {
                $results['url_generation'] = 'FAIL: Expected ' . $expected . ', got ' . $url;
            }
        } catch (Exception $e) {
            $results['url_generation'] = 'FAIL: ' . $e->getMessage();
        }
        
        return $results;
    }
    
    public static function display_test_results() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        $results = self::run_basic_tests();
        
        echo '<div class="notice notice-info">';
        echo '<h3>Wasabi Integration Test Results</h3>';
        echo '<ul>';
        
        foreach ($results as $test => $result) {
            $status = strpos($result, 'PASS') === 0 ? '✅' : '❌';
            echo '<li>' . $status . ' ' . ucfirst(str_replace('_', ' ', $test)) . ': ' . $result . '</li>';
        }
        
        echo '</ul>';
        echo '</div>';
    }
}
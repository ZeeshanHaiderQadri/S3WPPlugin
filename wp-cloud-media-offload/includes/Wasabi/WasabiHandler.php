<?php
namespace WPCMO\Wasabi;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Exception;

class WasabiHandler {
    private $s3_client;
    private $settings;
    
    // Wasabi endpoint regions
    private $wasabi_endpoints = [
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
    
    public function __construct() {
        $this->settings = get_option('wpcmo_settings', []);
        
        // Debug logging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('WPCMO Wasabi: Constructor called with settings: ' . print_r($this->settings, true));
        }
        
        $this->init_wasabi_client();
    }
    
    private function init_wasabi_client() {
        if (empty($this->settings['wasabi_access_key']) || empty($this->settings['wasabi_secret_key'])) {
            error_log('WPCMO Wasabi: Missing credentials');
            return false;
        }
        
        // Check if AWS SDK is available
        if (!class_exists('Aws\S3\S3Client')) {
            error_log('WPCMO Wasabi: AWS SDK not found. Please run composer install.');
            throw new Exception('AWS SDK not found. Please run composer install in the plugin directory.');
        }
        
        $region = $this->settings['wasabi_region'] ?? 'us-east-1';
        $endpoint = $this->wasabi_endpoints[$region] ?? 's3.wasabisys.com';
        
        try {
            $this->s3_client = new S3Client([
                'version' => 'latest',
                'region' => $region,
                'endpoint' => 'https://' . $endpoint,
                'use_path_style_endpoint' => false,
                'credentials' => [
                    'key' => trim($this->settings['wasabi_access_key']),
                    'secret' => trim($this->settings['wasabi_secret_key']),
                ],
                // Wasabi-specific configurations
                'signature_version' => 'v4',
                'http' => [
                    'timeout' => 30,
                    'connect_timeout' => 10,
                ],
                // Disable SSL verification for development (remove in production)
                // 'http' => ['verify' => false],
            ]);
            
            error_log('WPCMO Wasabi: Client initialized successfully for region: ' . $region);
            return true;
        } catch (AwsException $e) {
            error_log('WPCMO Wasabi Client Error: ' . $e->getMessage());
            throw $e;
        } catch (Exception $e) {
            error_log('WPCMO Wasabi Client General Error: ' . $e->getMessage());
            throw $e;
        }
    }
    
    public function test_connection() {
        // Check if credentials are provided
        if (empty($this->settings['wasabi_access_key']) || empty($this->settings['wasabi_secret_key'])) {
            error_log('WPCMO Wasabi: Missing access key or secret key');
            return false;
        }
        
        // Check if bucket name is provided
        if (empty($this->settings['wasabi_bucket'])) {
            error_log('WPCMO Wasabi: Missing bucket name');
            return false;
        }
        
        // Initialize client if not already done
        if (!$this->s3_client) {
            $this->init_wasabi_client();
        }
        
        if (!$this->s3_client) {
            error_log('WPCMO Wasabi: Failed to initialize S3 client');
            return false;
        }
        
        try {
            // First test: List buckets to verify credentials
            $this->s3_client->listBuckets();
            
            // Second test: Check if specific bucket exists and is accessible
            $this->s3_client->headBucket([
                'Bucket' => $this->settings['wasabi_bucket']
            ]);
            
            // Third test: Try to get bucket location to verify region
            $location = $this->s3_client->getBucketLocation([
                'Bucket' => $this->settings['wasabi_bucket']
            ]);
            
            error_log('WPCMO Wasabi: Connection test successful for bucket: ' . $this->settings['wasabi_bucket']);
            return true;
            
        } catch (AwsException $e) {
            $error_code = $e->getAwsErrorCode();
            $error_message = $e->getMessage();
            
            // Log specific error details
            error_log('WPCMO Wasabi Connection Test Error: ' . $error_code . ' - ' . $error_message);
            
            // Handle specific error cases
            switch ($error_code) {
                case 'InvalidAccessKeyId':
                    error_log('WPCMO Wasabi: Invalid access key ID');
                    break;
                case 'SignatureDoesNotMatch':
                    error_log('WPCMO Wasabi: Invalid secret access key');
                    break;
                case 'NoSuchBucket':
                    error_log('WPCMO Wasabi: Bucket does not exist: ' . $this->settings['wasabi_bucket']);
                    break;
                case 'AccessDenied':
                    error_log('WPCMO Wasabi: Access denied to bucket: ' . $this->settings['wasabi_bucket']);
                    break;
                default:
                    error_log('WPCMO Wasabi: Unknown error: ' . $error_code);
            }
            
            return false;
        }
    }
    
    public function upload_file($file_path, $s3_key) {
        if (!$this->s3_client || !file_exists($file_path)) {
            return false;
        }
        
        try {
            $result = $this->s3_client->putObject([
                'Bucket' => $this->settings['wasabi_bucket'],
                'Key' => $s3_key,
                'SourceFile' => $file_path,
                'ACL' => 'public-read',
                'ContentType' => mime_content_type($file_path),
                // Wasabi-specific optimizations
                'StorageClass' => 'STANDARD',
            ]);
            
            return [
                'success' => true,
                's3_url' => $this->get_file_url($s3_key),
                's3_key' => $s3_key,
            ];
        } catch (AwsException $e) {
            error_log('WPCMO Wasabi Upload Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
    
    public function delete_file($s3_key) {
        if (!$this->s3_client) {
            return false;
        }
        
        try {
            $this->s3_client->deleteObject([
                'Bucket' => $this->settings['wasabi_bucket'],
                'Key' => $s3_key,
            ]);
            return true;
        } catch (AwsException $e) {
            error_log('WPCMO Wasabi Delete Error: ' . $e->getMessage());
            return false;
        }
    }
    
    public function get_file_url($s3_key) {
        $region = $this->settings['wasabi_region'] ?? 'us-east-1';
        $endpoint = $this->wasabi_endpoints[$region] ?? 's3.wasabisys.com';
        
        return 'https://' . $this->settings['wasabi_bucket'] . '.' . $endpoint . '/' . $s3_key;
    }
    
    public function list_buckets() {
        if (!$this->s3_client) {
            return false;
        }
        
        try {
            $result = $this->s3_client->listBuckets();
            return $result['Buckets'];
        } catch (AwsException $e) {
            error_log('WPCMO Wasabi List Buckets Error: ' . $e->getMessage());
            return false;
        }
    }
    
    public function get_bucket_location($bucket_name) {
        if (!$this->s3_client) {
            return false;
        }
        
        try {
            $result = $this->s3_client->getBucketLocation([
                'Bucket' => $bucket_name
            ]);
            return $result['LocationConstraint'] ?: 'us-east-1';
        } catch (AwsException $e) {
            error_log('WPCMO Wasabi Get Bucket Location Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get detailed connection status for debugging
     */
    public function get_connection_status() {
        $status = [
            'credentials_provided' => !empty($this->settings['wasabi_access_key']) && !empty($this->settings['wasabi_secret_key']),
            'bucket_provided' => !empty($this->settings['wasabi_bucket']),
            'region' => $this->settings['wasabi_region'] ?? 'us-east-1',
            'endpoint' => $this->wasabi_endpoints[$this->settings['wasabi_region'] ?? 'us-east-1'] ?? 's3.wasabisys.com',
            'client_initialized' => $this->s3_client !== null,
        ];
        
        return $status;
    }
    
    /**
     * Test individual components of the connection
     */
    public function test_connection_detailed() {
        $results = [];
        
        // Test 0: Check AWS SDK availability
        if (!$this->check_aws_sdk()) {
            $results['aws_sdk'] = 'FAIL: AWS SDK not available - run composer install';
            return $results;
        }
        $results['aws_sdk'] = 'PASS: AWS SDK available';
        
        // Test 1: Check credentials
        if (empty($this->settings['wasabi_access_key']) || empty($this->settings['wasabi_secret_key'])) {
            $results['credentials'] = 'FAIL: Missing access key or secret key';
            return $results;
        }
        $results['credentials'] = 'PASS: Credentials provided';
        
        // Test 2: Check bucket name
        if (empty($this->settings['wasabi_bucket'])) {
            $results['bucket_name'] = 'FAIL: Missing bucket name';
            return $results;
        }
        $results['bucket_name'] = 'PASS: Bucket name provided';
        
        // Test 3: HTTP connectivity test
        $http_test = $this->test_http_connectivity();
        if (!$http_test['success']) {
            $results['http_connectivity'] = 'FAIL: Cannot reach Wasabi endpoint - ' . $http_test['error'];
            return $results;
        }
        $results['http_connectivity'] = 'PASS: Wasabi endpoint reachable (HTTP ' . $http_test['response_code'] . ')';
        
        // Test 4: Initialize client
        if (!$this->s3_client) {
            $this->init_wasabi_client();
        }
        
        if (!$this->s3_client) {
            $results['client_init'] = 'FAIL: Could not initialize Wasabi client';
            return $results;
        }
        $results['client_init'] = 'PASS: Client initialized';
        
        // Test 5: List buckets (test credentials)
        try {
            $this->s3_client->listBuckets();
            $results['list_buckets'] = 'PASS: Can list buckets (credentials valid)';
        } catch (AwsException $e) {
            $results['list_buckets'] = 'FAIL: ' . $e->getAwsErrorCode() . ' - ' . $e->getMessage();
            return $results;
        }
        
        // Test 6: Check specific bucket
        try {
            $this->s3_client->headBucket(['Bucket' => $this->settings['wasabi_bucket']]);
            $results['bucket_access'] = 'PASS: Can access bucket "' . $this->settings['wasabi_bucket'] . '"';
        } catch (AwsException $e) {
            $results['bucket_access'] = 'FAIL: ' . $e->getAwsErrorCode() . ' - ' . $e->getMessage();
            return $results;
        }
        
        return $results;
    }
    
    /**
     * Simple HTTP test to verify endpoint connectivity
     */
    public function test_http_connectivity() {
        $region = $this->settings['wasabi_region'] ?? 'us-east-1';
        $endpoint = $this->wasabi_endpoints[$region] ?? 's3.wasabisys.com';
        $url = 'https://' . $endpoint;
        
        $response = wp_remote_get($url, [
            'timeout' => 10,
            'sslverify' => true,
        ]);
        
        if (is_wp_error($response)) {
            return [
                'success' => false,
                'error' => $response->get_error_message()
            ];
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        
        return [
            'success' => $response_code < 500, // Any response under 500 means endpoint is reachable
            'response_code' => $response_code,
            'endpoint' => $endpoint
        ];
    }
    
    /**
     * Check if AWS SDK is available
     */
    public function check_aws_sdk() {
        return class_exists('Aws\S3\S3Client');
    }
}
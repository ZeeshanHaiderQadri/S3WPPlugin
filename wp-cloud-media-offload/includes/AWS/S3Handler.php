<?php
namespace WPCMO\AWS;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class S3Handler {
    private $s3_client;
    private $settings;
    
    public function __construct() {
        $this->settings = get_option('wpcmo_settings', []);
        $this->init_s3_client();
    }
    
    private function init_s3_client() {
        if (empty($this->settings['aws_access_key']) || empty($this->settings['aws_secret_key'])) {
            return false;
        }
        
        try {
            $this->s3_client = new S3Client([
                'version' => 'latest',
                'region' => $this->settings['aws_region'] ?? 'us-east-1',
                'credentials' => [
                    'key' => $this->settings['aws_access_key'],
                    'secret' => $this->settings['aws_secret_key'],
                ],
            ]);
            return true;
        } catch (AwsException $e) {
            error_log('WPCMO S3 Client Error: ' . $e->getMessage());
            return false;
        }
    }
    
    public function test_connection() {
        if (!$this->s3_client) {
            return false;
        }
        
        try {
            $this->s3_client->headBucket([
                'Bucket' => $this->settings['aws_bucket']
            ]);
            return true;
        } catch (AwsException $e) {
            error_log('WPCMO Connection Test Error: ' . $e->getMessage());
            return false;
        }
    }
    
    public function upload_file($file_path, $s3_key) {
        if (!$this->s3_client || !file_exists($file_path)) {
            return false;
        }
        
        try {
            $result = $this->s3_client->putObject([
                'Bucket' => $this->settings['aws_bucket'],
                'Key' => $s3_key,
                'SourceFile' => $file_path,
                'ACL' => 'public-read',
                'ContentType' => mime_content_type($file_path),
            ]);
            
            return [
                'success' => true,
                's3_url' => $result['ObjectURL'],
                's3_key' => $s3_key,
            ];
        } catch (AwsException $e) {
            error_log('WPCMO Upload Error: ' . $e->getMessage());
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
                'Bucket' => $this->settings['aws_bucket'],
                'Key' => $s3_key,
            ]);
            return true;
        } catch (AwsException $e) {
            error_log('WPCMO Delete Error: ' . $e->getMessage());
            return false;
        }
    }
    
    public function get_file_url($s3_key) {
        if (!empty($this->settings['cloudfront_enabled']) && !empty($this->settings['cloudfront_domain'])) {
            return 'https://' . $this->settings['cloudfront_domain'] . '/' . $s3_key;
        }
        
        return 'https://' . $this->settings['aws_bucket'] . '.s3.' . $this->settings['aws_region'] . '.amazonaws.com/' . $s3_key;
    }
}

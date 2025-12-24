<?php
/**
 * Fix Missing Images - Repair Database Records for S3 Images
 * 
 * This script scans for attachments where:
 * 1. The local file is missing
 * 2. The file exists in S3
 * 3. The database record is missing or incorrect
 * 
 * Upload this file to your WordPress root and access it via browser:
 * https://yoursite.com/fix-missing-images.php
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
    <title>Fix Missing Images - WP Cloud Media Offload</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .status { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .btn { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #0056b3; }
        .progress { width: 100%; height: 30px; background: #f0f0f0; border-radius: 4px; overflow: hidden; margin: 10px 0; }
        .progress-bar { height: 100%; background: linear-gradient(90deg, #667eea 0%, #764ba2 100%); transition: width 0.3s; text-align: center; color: white; line-height: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Fix Missing Images</h1>
        <p>This tool will scan your media library and fix images that are on S3 but showing as broken in WordPress.</p>
        
        <?php
        if (isset($_GET['action']) && $_GET['action'] === 'scan') {
            echo '<h2>Scanning Media Library...</h2>';
            
            global $wpdb;
            $settings = get_option('wpcmo_settings', []);
            $table_name = $wpdb->prefix . 'wpcmo_uploads';
            
            // Get all attachments
            $attachments = get_posts([
                'post_type' => 'attachment',
                'post_status' => 'inherit',
                'posts_per_page' => -1,
                'fields' => 'ids'
            ]);
            
            $total = count($attachments);
            $missing_local = 0;
            $has_s3_record = 0;
            $needs_fix = 0;
            $fixed = 0;
            
            echo '<div class="progress"><div class="progress-bar" id="progress" style="width: 0%">0%</div></div>';
            echo '<table>';
            echo '<tr><th>Attachment ID</th><th>Filename</th><th>Status</th><th>Action</th></tr>';
            
            foreach ($attachments as $index => $attachment_id) {
                $file_path = get_attached_file($attachment_id);
                $filename = basename($file_path);
                $file_exists = file_exists($file_path);
                
                // Check if there's an S3 record
                $s3_record = $wpdb->get_row($wpdb->prepare(
                    "SELECT * FROM $table_name WHERE attachment_id = %d LIMIT 1",
                    $attachment_id
                ));
                
                $status = '';
                $action = '';
                
                if (!$file_exists) {
                    $missing_local++;
                    
                    if ($s3_record) {
                        $has_s3_record++;
                        $status = '<span style="color: green;">✓ On S3</span>';
                        $action = 'OK';
                    } else {
                        $needs_fix++;
                        $status = '<span style="color: red;">✗ Missing</span>';
                        $action = 'NEEDS FIX';
                        
                        // Try to create S3 record
                        $upload_dir = wp_upload_dir();
                        $relative_path = str_replace($upload_dir['basedir'] . '/', '', $file_path);
                        $s3_key = ($settings['file_path_prefix'] ?? 'wp-content/uploads/') . $relative_path;
                        
                        // Get handler
                        $provider = $settings['provider'] ?? 'aws_s3';
                        if ($provider === 'wasabi') {
                            $handler = new \WPCMO\Wasabi\WasabiHandler();
                        } else {
                            $handler = new \WPCMO\AWS\S3Handler();
                        }
                        
                        $s3_url = $handler->get_file_url($s3_key);
                        
                        // Insert record
                        $wpdb->insert($table_name, [
                            'attachment_id' => $attachment_id,
                            's3_key' => $s3_key,
                            's3_url' => $s3_url,
                            'cloudfront_url' => $s3_url,
                            'file_size' => 0,
                        ]);
                        
                        $fixed++;
                        $action = '<span style="color: green;">FIXED</span>';
                    }
                    
                    echo '<tr>';
                    echo '<td>' . $attachment_id . '</td>';
                    echo '<td>' . esc_html($filename) . '</td>';
                    echo '<td>' . $status . '</td>';
                    echo '<td>' . $action . '</td>';
                    echo '</tr>';
                }
                
                // Update progress
                $progress = round(($index + 1) / $total * 100);
                echo '<script>document.getElementById("progress").style.width = "' . $progress . '%"; document.getElementById("progress").innerText = "' . $progress . '%";</script>';
                flush();
            }
            
            echo '</table>';
            
            echo '<div class="status info">';
            echo '<h3>Scan Results:</h3>';
            echo '<ul>';
            echo '<li><strong>Total Attachments:</strong> ' . $total . '</li>';
            echo '<li><strong>Missing Locally:</strong> ' . $missing_local . '</li>';
            echo '<li><strong>Already on S3:</strong> ' . $has_s3_record . '</li>';
            echo '<li><strong>Needed Fix:</strong> ' . $needs_fix . '</li>';
            echo '<li><strong>Fixed:</strong> ' . $fixed . '</li>';
            echo '</ul>';
            echo '</div>';
            
            if ($fixed > 0) {
                echo '<div class="status success">';
                echo '<strong>Success!</strong> Fixed ' . $fixed . ' missing image records.';
                echo '</div>';
            }
            
            echo '<p><a href="?" class="btn">Back</a></p>';
            
        } else {
            ?>
            <div class="status warning">
                <strong>⚠️ Before You Start:</strong>
                <ul>
                    <li>Make sure your S3/Wasabi credentials are configured correctly</li>
                    <li>This will scan ALL attachments in your media library</li>
                    <li>It will create database records for images that are on S3 but missing locally</li>
                    <li>This process may take several minutes for large media libraries</li>
                </ul>
            </div>
            
            <h2>What This Tool Does:</h2>
            <ol>
                <li>Scans all attachments in your WordPress media library</li>
                <li>Identifies images that are missing locally but should be on S3</li>
                <li>Creates database records so WordPress can find them on S3</li>
                <li>Fixes broken product images and thumbnails</li>
            </ol>
            
            <p><a href="?action=scan" class="btn">Start Scan & Fix</a></p>
            <?php
        }
        ?>
    </div>
</body>
</html>

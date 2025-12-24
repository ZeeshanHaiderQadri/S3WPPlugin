<?php
if (!defined('ABSPATH')) exit;

$settings = get_option('wpcmo_settings', []);
$is_wasabi = ($settings['provider'] ?? 'aws_s3') === 'wasabi';
?>

<div class="wpcmo-wrap" data-theme="light">
    <div class="wpcmo-header">
        <button class="wpcmo-theme-toggle">🌙 Dark Mode</button>
        <div class="wpcmo-header-content">
            <h1>🧪 Wasabi Connection Test</h1>
            <p>Detailed testing and debugging for Wasabi integration</p>
        </div>
    </div>

    <div class="wpcmo-container">
        <div class="wpcmo-alerts"></div>

        <?php if (!$is_wasabi): ?>
        <div class="wpcmo-card">
            <div class="wpcmo-card-body">
                <div class="wpcmo-alert wpcmo-alert-warning">
                    <strong>⚠️ Wasabi Not Selected:</strong> Please go to Settings and select Wasabi as your storage provider first.
                </div>
                <a href="<?php echo admin_url('admin.php?page=wpcmo-settings'); ?>" class="wpcmo-btn wpcmo-btn-primary">
                    Go to Settings
                </a>
            </div>
        </div>
        <?php else: ?>

        <div class="wpcmo-card">
            <div class="wpcmo-card-header">
                <div class="wpcmo-card-icon">📋</div>
                <h2 class="wpcmo-card-title">Current Configuration</h2>
            </div>
            <div class="wpcmo-card-body">
                <table class="wpcmo-config-table">
                    <tr>
                        <td><strong>Provider:</strong></td>
                        <td>Wasabi</td>
                    </tr>
                    <tr>
                        <td><strong>Access Key:</strong></td>
                        <td><?php echo !empty($settings['wasabi_access_key']) ? substr($settings['wasabi_access_key'], 0, 8) . '...' : '<span style="color: red;">Not set</span>'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Secret Key:</strong></td>
                        <td><?php echo !empty($settings['wasabi_secret_key']) ? '••••••••••••••••' : '<span style="color: red;">Not set</span>'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Region:</strong></td>
                        <td><?php echo esc_html($settings['wasabi_region'] ?? 'us-east-1'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Bucket:</strong></td>
                        <td><?php echo !empty($settings['wasabi_bucket']) ? esc_html($settings['wasabi_bucket']) : '<span style="color: red;">Not set</span>'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Endpoint:</strong></td>
                        <td><?php 
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
                            echo esc_html($endpoints[$region] ?? 's3.wasabisys.com');
                        ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="wpcmo-card">
            <div class="wpcmo-card-header">
                <div class="wpcmo-card-icon">🔍</div>
                <h2 class="wpcmo-card-title">Connection Tests</h2>
            </div>
            <div class="wpcmo-card-body">
                <button type="button" class="wpcmo-btn wpcmo-btn-primary wpcmo-run-detailed-test">
                    🧪 Run Detailed Tests
                </button>
                
                <div id="wpcmo-test-results" style="margin-top: 20px; display: none;">
                    <h3>Test Results:</h3>
                    <div id="wpcmo-test-output"></div>
                </div>
            </div>
        </div>

        <div class="wpcmo-card">
            <div class="wpcmo-card-header">
                <div class="wpcmo-card-icon">📚</div>
                <h2 class="wpcmo-card-title">Troubleshooting Guide</h2>
            </div>
            <div class="wpcmo-card-body">
                <div class="wpcmo-troubleshooting">
                    <h4>Common Issues:</h4>
                    
                    <div class="wpcmo-issue">
                        <h5>❌ Invalid Access Key ID</h5>
                        <p><strong>Solution:</strong> Check that your access key is correct and active in the Wasabi console.</p>
                    </div>
                    
                    <div class="wpcmo-issue">
                        <h5>❌ Signature Does Not Match</h5>
                        <p><strong>Solution:</strong> Verify your secret key is correct. Regenerate keys if necessary.</p>
                    </div>
                    
                    <div class="wpcmo-issue">
                        <h5>❌ No Such Bucket</h5>
                        <p><strong>Solution:</strong> Ensure the bucket exists in the selected region. Create it in the Wasabi console if needed.</p>
                    </div>
                    
                    <div class="wpcmo-issue">
                        <h5>❌ Access Denied</h5>
                        <p><strong>Solution:</strong> Check bucket permissions. Your access key needs read/write permissions for the bucket.</p>
                    </div>
                    
                    <h4>Helpful Links:</h4>
                    <ul>
                        <li><a href="https://console.wasabisys.com/" target="_blank">Wasabi Console</a></li>
                        <li><a href="https://docs.wasabi.com/docs/getting-started" target="_blank">Wasabi Getting Started</a></li>
                        <li><a href="https://docs.wasabi.com/docs/access-keys-1" target="_blank">Managing Access Keys</a></li>
                        <li><a href="https://docs.wasabi.com/docs/creating-a-bucket" target="_blank">Creating Buckets</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <?php endif; ?>
    </div>
</div>

<style>
.wpcmo-config-table {
    width: 100%;
    border-collapse: collapse;
}

.wpcmo-config-table td {
    padding: 8px 12px;
    border-bottom: 1px solid var(--wpcmo-border-light);
}

.wpcmo-config-table td:first-child {
    width: 150px;
    font-weight: 500;
}

.wpcmo-troubleshooting .wpcmo-issue {
    margin-bottom: 15px;
    padding: 10px;
    background: var(--wpcmo-bg-secondary-light);
    border-radius: 6px;
}

.wpcmo-troubleshooting h5 {
    margin: 0 0 5px 0;
    color: var(--wpcmo-text-light);
}

.wpcmo-troubleshooting p {
    margin: 0;
    color: var(--wpcmo-text-secondary-light);
}

.wpcmo-troubleshooting ul {
    margin: 10px 0 0 0;
    padding-left: 20px;
}

.wpcmo-troubleshooting a {
    color: var(--wpcmo-purple);
    text-decoration: none;
}

.wpcmo-troubleshooting a:hover {
    text-decoration: underline;
}

#wpcmo-test-output {
    font-family: monospace;
    background: var(--wpcmo-bg-secondary-light);
    padding: 15px;
    border-radius: 6px;
    border: 1px solid var(--wpcmo-border-light);
}

.test-result {
    margin: 5px 0;
    padding: 5px 10px;
    border-radius: 4px;
}

.test-pass {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.test-fail {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>

<script>
jQuery(document).ready(function($) {
    $('.wpcmo-run-detailed-test').on('click', function(e) {
        e.preventDefault();
        
        const $button = $(this);
        const originalText = $button.html();
        const $results = $('#wpcmo-test-results');
        const $output = $('#wpcmo-test-output');
        
        $button.prop('disabled', true).html('<span class="wpcmo-spinner"></span> Running Tests...');
        $results.show();
        $output.html('Starting detailed connection tests...<br>');
        
        $.ajax({
            url: wpcmoData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'wpcmo_wasabi_detailed_test',
                nonce: wpcmoData.nonce
            },
            success: function(response) {
                if (response.success && response.data.details) {
                    let html = '';
                    $.each(response.data.details, function(test, result) {
                        const isPass = result.indexOf('PASS') === 0;
                        const cssClass = isPass ? 'test-pass' : 'test-fail';
                        const icon = isPass ? '✅' : '❌';
                        html += '<div class="test-result ' + cssClass + '">' + icon + ' ' + test.replace('_', ' ').toUpperCase() + ': ' + result + '</div>';
                    });
                    $output.html(html);
                } else {
                    $output.html('<div class="test-fail">❌ Test failed: ' + (response.data.message || 'Unknown error') + '</div>');
                }
            },
            error: function() {
                $output.html('<div class="test-fail">❌ Test request failed</div>');
            },
            complete: function() {
                $button.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
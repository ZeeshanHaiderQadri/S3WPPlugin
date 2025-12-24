<?php
if (!defined('ABSPATH')) exit;

$settings = get_option('wpcmo_settings', []);
?>

<div class="wpcmo-wrap" data-theme="light">
    <div class="wpcmo-header">
        <button class="wpcmo-theme-toggle">🌙 Dark Mode</button>
        <div class="wpcmo-header-content">
            <h1>⚙️ Settings</h1>
            <p>Configure your cloud storage settings</p>
        </div>
    </div>

    <div class="wpcmo-container">
        <div class="wpcmo-alerts"></div>

        <form id="wpcmo-settings-form">
            <div class="wpcmo-card">
                <div class="wpcmo-card-header">
                    <div class="wpcmo-card-icon">☁️</div>
                    <h2 class="wpcmo-card-title">Storage Provider</h2>
                </div>
                <div class="wpcmo-card-body">
                    <div class="wpcmo-provider-grid">
                        <div class="wpcmo-provider-card <?php echo ($settings['provider'] ?? 'aws_s3') === 'aws_s3' ? 'selected' : ''; ?>" data-provider="aws_s3">
                            <div class="wpcmo-provider-icon">🔶</div>
                            <div class="wpcmo-provider-name">Amazon S3</div>
                        </div>
                        <div class="wpcmo-provider-card <?php echo ($settings['provider'] ?? '') === 'wasabi' ? 'selected' : ''; ?>" data-provider="wasabi">
                            <div class="wpcmo-provider-icon">🗄️</div>
                            <div class="wpcmo-provider-name">Wasabi</div>
                        </div>
                        <div class="wpcmo-provider-card" data-provider="digitalocean">
                            <div class="wpcmo-provider-icon">🌊</div>
                            <div class="wpcmo-provider-name">DigitalOcean Spaces</div>
                        </div>
                        <div class="wpcmo-provider-card" data-provider="google_cloud">
                            <div class="wpcmo-provider-icon">☁️</div>
                            <div class="wpcmo-provider-name">Google Cloud</div>
                        </div>
                    </div>
                    <input type="hidden" name="provider" id="wpcmo-provider-input" value="<?php echo esc_attr($settings['provider'] ?? 'aws_s3'); ?>">
                </div>
            </div>

            <div class="wpcmo-card wpcmo-provider-settings" data-provider="aws_s3">
                <div class="wpcmo-card-header">
                    <div class="wpcmo-card-icon">🔑</div>
                    <h2 class="wpcmo-card-title">AWS Credentials</h2>
                </div>
                <div class="wpcmo-card-body">
                    <div class="wpcmo-form-group">
                        <label class="wpcmo-form-label">Access Key ID</label>
                        <input type="text" name="wpcmo_aws_access_key" class="wpcmo-input" value="<?php echo esc_attr($settings['aws_access_key'] ?? ''); ?>" placeholder="AKIAIOSFODNN7EXAMPLE">
                        <p class="wpcmo-form-description">Your AWS access key ID</p>
                    </div>

                    <div class="wpcmo-form-group">
                        <label class="wpcmo-form-label">Secret Access Key</label>
                        <input type="password" name="wpcmo_aws_secret_key" class="wpcmo-input" value="<?php echo esc_attr($settings['aws_secret_key'] ?? ''); ?>" placeholder="wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY">
                        <p class="wpcmo-form-description">Your AWS secret access key</p>
                    </div>

                    <div class="wpcmo-form-group">
                        <label class="wpcmo-form-label">Region</label>
                        <select name="wpcmo_aws_region" class="wpcmo-select">
                            <option value="us-east-1" <?php selected($settings['aws_region'] ?? 'us-east-1', 'us-east-1'); ?>>US East (N. Virginia)</option>
                            <option value="us-east-2" <?php selected($settings['aws_region'] ?? '', 'us-east-2'); ?>>US East (Ohio)</option>
                            <option value="us-west-1" <?php selected($settings['aws_region'] ?? '', 'us-west-1'); ?>>US West (N. California)</option>
                            <option value="us-west-2" <?php selected($settings['aws_region'] ?? '', 'us-west-2'); ?>>US West (Oregon)</option>
                            <option value="eu-west-1" <?php selected($settings['aws_region'] ?? '', 'eu-west-1'); ?>>EU (Ireland)</option>
                            <option value="eu-central-1" <?php selected($settings['aws_region'] ?? '', 'eu-central-1'); ?>>EU (Frankfurt)</option>
                            <option value="ap-south-1" <?php selected($settings['aws_region'] ?? '', 'ap-south-1'); ?>>Asia Pacific (Mumbai)</option>
                            <option value="ap-southeast-1" <?php selected($settings['aws_region'] ?? '', 'ap-southeast-1'); ?>>Asia Pacific (Singapore)</option>
                        </select>
                    </div>

                    <div class="wpcmo-form-group">
                        <label class="wpcmo-form-label">Bucket Name</label>
                        <input type="text" name="wpcmo_aws_bucket" class="wpcmo-input" value="<?php echo esc_attr($settings['aws_bucket'] ?? ''); ?>" placeholder="my-bucket-name">
                        <p class="wpcmo-form-description">Your S3 bucket name</p>
                    </div>

                    <button type="button" class="wpcmo-btn wpcmo-btn-secondary wpcmo-test-connection">
                        🔌 Test Connection
                    </button>
                </div>
            </div>

            <div class="wpcmo-card wpcmo-provider-settings" data-provider="wasabi" style="display: <?php echo ($settings['provider'] ?? 'aws_s3') === 'wasabi' ? 'block' : 'none'; ?>;">
                <div class="wpcmo-card-header">
                    <div class="wpcmo-card-icon">🔑</div>
                    <h2 class="wpcmo-card-title">Wasabi Credentials</h2>
                </div>
                <div class="wpcmo-card-body">
                    <div class="wpcmo-alert wpcmo-alert-info" style="margin-bottom: 20px;">
                        <strong>💡 Wasabi Setup:</strong> Create access keys in your Wasabi console under "Access Keys". 
                        <a href="https://console.wasabisys.com/" target="_blank">Open Wasabi Console</a>
                    </div>
                    
                    <div class="wpcmo-form-group">
                        <label class="wpcmo-form-label">Access Key <span style="color: red;">*</span></label>
                        <input type="text" name="wpcmo_wasabi_access_key" class="wpcmo-input" value="<?php echo esc_attr($settings['wasabi_access_key'] ?? ''); ?>" placeholder="AKIAIOSFODNN7EXAMPLE">
                        <p class="wpcmo-form-description">Your Wasabi access key ID (starts with AKIA...)</p>
                    </div>

                    <div class="wpcmo-form-group">
                        <label class="wpcmo-form-label">Secret Key <span style="color: red;">*</span></label>
                        <input type="password" name="wpcmo_wasabi_secret_key" class="wpcmo-input" value="<?php echo esc_attr($settings['wasabi_secret_key'] ?? ''); ?>" placeholder="wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY">
                        <p class="wpcmo-form-description">Your Wasabi secret access key (40 characters)</p>
                    </div>

                    <div class="wpcmo-form-group">
                        <label class="wpcmo-form-label">Region</label>
                        <select name="wpcmo_wasabi_region" class="wpcmo-select">
                            <option value="us-east-1" <?php selected($settings['wasabi_region'] ?? 'us-east-1', 'us-east-1'); ?>>US East 1 (N. Virginia)</option>
                            <option value="us-east-2" <?php selected($settings['wasabi_region'] ?? '', 'us-east-2'); ?>>US East 2 (N. Virginia)</option>
                            <option value="us-west-1" <?php selected($settings['wasabi_region'] ?? '', 'us-west-1'); ?>>US West 1 (Oregon)</option>
                            <option value="eu-central-1" <?php selected($settings['wasabi_region'] ?? '', 'eu-central-1'); ?>>EU Central 1 (Amsterdam)</option>
                            <option value="eu-west-1" <?php selected($settings['wasabi_region'] ?? '', 'eu-west-1'); ?>>EU West 1 (London)</option>
                            <option value="eu-west-2" <?php selected($settings['wasabi_region'] ?? '', 'eu-west-2'); ?>>EU West 2 (Paris)</option>
                            <option value="ap-northeast-1" <?php selected($settings['wasabi_region'] ?? '', 'ap-northeast-1'); ?>>AP Northeast 1 (Tokyo)</option>
                            <option value="ap-northeast-2" <?php selected($settings['wasabi_region'] ?? '', 'ap-northeast-2'); ?>>AP Northeast 2 (Osaka)</option>
                            <option value="ap-southeast-1" <?php selected($settings['wasabi_region'] ?? '', 'ap-southeast-1'); ?>>AP Southeast 1 (Singapore)</option>
                            <option value="ap-southeast-2" <?php selected($settings['wasabi_region'] ?? '', 'ap-southeast-2'); ?>>AP Southeast 2 (Sydney)</option>
                        </select>
                    </div>

                    <div class="wpcmo-form-group">
                        <label class="wpcmo-form-label">Bucket Name <span style="color: red;">*</span></label>
                        <input type="text" name="wpcmo_wasabi_bucket" class="wpcmo-input" value="<?php echo esc_attr($settings['wasabi_bucket'] ?? ''); ?>" placeholder="my-wasabi-bucket">
                        <p class="wpcmo-form-description">Your Wasabi bucket name (must exist in the selected region)</p>
                    </div>

                    <div class="wpcmo-form-group">
                        <div style="background: #f0f8ff; padding: 15px; border-radius: 8px; border-left: 4px solid #0073aa;">
                            <h4 style="margin: 0 0 10px 0;">🚀 Wasabi Benefits:</h4>
                            <ul style="margin: 0; padding-left: 20px;">
                                <li><strong>80% cheaper</strong> than AWS S3</li>
                                <li><strong>No egress fees</strong> for downloads</li>
                                <li><strong>S3-compatible API</strong> for easy migration</li>
                                <li><strong>11 9's durability</strong> (99.999999999%)</li>
                            </ul>
                        </div>
                    </div>

                    <button type="button" class="wpcmo-btn wpcmo-btn-secondary wpcmo-test-connection">
                        🔌 Test Wasabi Connection
                    </button>
                </div>
            </div>

            <div class="wpcmo-card">
                <div class="wpcmo-card-header">
                    <div class="wpcmo-card-icon">🚀</div>
                    <h2 class="wpcmo-card-title">CloudFront CDN</h2>
                </div>
                <div class="wpcmo-card-body">
                    <div class="wpcmo-form-group">
                        <div class="wpcmo-checkbox-wrapper">
                            <input type="checkbox" name="wpcmo_cloudfront_enabled" class="wpcmo-checkbox" <?php checked(!empty($settings['cloudfront_enabled'])); ?>>
                            <label class="wpcmo-form-label" style="margin: 0;">Enable CloudFront</label>
                        </div>
                        <p class="wpcmo-form-description">Serve media files through CloudFront CDN for faster delivery</p>
                    </div>

                    <div class="wpcmo-form-group">
                        <label class="wpcmo-form-label">CloudFront Domain</label>
                        <input type="text" name="wpcmo_cloudfront_domain" class="wpcmo-input" value="<?php echo esc_attr($settings['cloudfront_domain'] ?? ''); ?>" placeholder="d111111abcdef8.cloudfront.net">
                        <p class="wpcmo-form-description">Your CloudFront distribution domain</p>
                    </div>
                </div>
            </div>

            <div class="wpcmo-card">
                <div class="wpcmo-card-header">
                    <div class="wpcmo-card-icon">📁</div>
                    <h2 class="wpcmo-card-title">Upload Settings</h2>
                </div>
                <div class="wpcmo-card-body">
                    <div class="wpcmo-form-group">
                        <div class="wpcmo-checkbox-wrapper">
                            <input type="checkbox" name="wpcmo_auto_upload" class="wpcmo-checkbox" <?php checked(!empty($settings['auto_upload'])); ?>>
                            <label class="wpcmo-form-label" style="margin: 0;">Auto Upload New Media</label>
                        </div>
                        <p class="wpcmo-form-description">Automatically upload new media files to S3</p>
                    </div>

                    <div class="wpcmo-form-group">
                        <div class="wpcmo-checkbox-wrapper">
                            <input type="checkbox" name="wpcmo_remove_local_files" class="wpcmo-checkbox" <?php checked(!empty($settings['remove_local_files'])); ?>>
                            <label class="wpcmo-form-label" style="margin: 0;">Remove Local Files</label>
                        </div>
                        <p class="wpcmo-form-description">Delete local files after successful upload to save server space</p>
                    </div>

                    <div class="wpcmo-form-group">
                        <label class="wpcmo-form-label">File Path Prefix</label>
                        <input type="text" name="wpcmo_file_path_prefix" class="wpcmo-input" value="<?php echo esc_attr($settings['file_path_prefix'] ?? 'wp-content/uploads/'); ?>" placeholder="wp-content/uploads/">
                        <p class="wpcmo-form-description">Path prefix for uploaded files in S3</p>
                    </div>
                </div>
            </div>

            <div style="text-align: right;">
                <button type="submit" class="wpcmo-btn wpcmo-btn-primary">
                    💾 Save Settings
                </button>
            </div>
        </form>
    </div>
</div>

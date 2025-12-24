<?php
if (!defined('ABSPATH')) exit;

$settings = get_option('wpcmo_settings', []);
$license_status = get_option('wpcmo_license_status', 'inactive');

global $wpdb;
$table_name = $wpdb->prefix . 'wpcmo_uploads';
$total_uploads = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
$total_size = $wpdb->get_var("SELECT SUM(file_size) FROM $table_name");
$total_attachments = wp_count_posts('attachment')->inherit;
?>

<div class="wpcmo-wrap" data-theme="light">
    <div class="wpcmo-header">
        <button class="wpcmo-theme-toggle">🌙 Dark Mode</button>
        <div class="wpcmo-header-content">
            <h1>☁️ Cloud Media Offload</h1>
            <p>Manage your media files with AWS S3 and CloudFront</p>
        </div>
    </div>

    <div class="wpcmo-container">
        <div class="wpcmo-alerts"></div>

        <?php if ($license_status !== 'active'): ?>
        <div class="wpcmo-alert wpcmo-alert-warning">
            <span>⚠️</span>
            <div>
                <strong>License Required</strong><br>
                Please activate your license to use all features. <a href="<?php echo admin_url('admin.php?page=wpcmo-license'); ?>">Activate License</a>
            </div>
        </div>
        <?php endif; ?>

        <div class="wpcmo-grid">
            <div class="wpcmo-card">
                <div class="wpcmo-card-header">
                    <div class="wpcmo-card-icon">📊</div>
                    <h2 class="wpcmo-card-title">Statistics</h2>
                </div>
                <div class="wpcmo-card-body">
                    <div class="wpcmo-stat">
                        <span class="wpcmo-stat-label">Total Uploads</span>
                        <span class="wpcmo-stat-value"><?php echo number_format($total_uploads); ?></span>
                    </div>
                    <div class="wpcmo-stat">
                        <span class="wpcmo-stat-label">Total Size</span>
                        <span class="wpcmo-stat-value"><?php echo size_format($total_size ?: 0); ?></span>
                    </div>
                    <div class="wpcmo-stat">
                        <span class="wpcmo-stat-label">Total Media Files</span>
                        <span class="wpcmo-stat-value"><?php echo number_format($total_attachments); ?></span>
                    </div>
                    <div class="wpcmo-stat">
                        <span class="wpcmo-stat-label">Offload Progress</span>
                        <span class="wpcmo-stat-value">
                            <?php 
                            $percentage = $total_attachments > 0 ? round(($total_uploads / $total_attachments) * 100) : 0;
                            echo $percentage . '%';
                            ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="wpcmo-card">
                <div class="wpcmo-card-header">
                    <div class="wpcmo-card-icon">⚙️</div>
                    <h2 class="wpcmo-card-title">Configuration</h2>
                </div>
                <div class="wpcmo-card-body">
                    <div class="wpcmo-stat">
                        <span class="wpcmo-stat-label">Storage Provider</span>
                        <span class="wpcmo-stat-value">
                            <?php echo !empty($settings['provider']) ? strtoupper($settings['provider']) : 'Not Set'; ?>
                        </span>
                    </div>
                    <div class="wpcmo-stat">
                        <span class="wpcmo-stat-label">S3 Bucket</span>
                        <span class="wpcmo-stat-value">
                            <?php echo !empty($settings['aws_bucket']) ? esc_html($settings['aws_bucket']) : 'Not Set'; ?>
                        </span>
                    </div>
                    <div class="wpcmo-stat">
                        <span class="wpcmo-stat-label">CloudFront</span>
                        <span class="wpcmo-stat-value">
                            <?php echo !empty($settings['cloudfront_enabled']) ? '✅ Enabled' : '❌ Disabled'; ?>
                        </span>
                    </div>
                    <div class="wpcmo-stat">
                        <span class="wpcmo-stat-label">Auto Upload</span>
                        <span class="wpcmo-stat-value">
                            <?php echo !empty($settings['auto_upload']) ? '✅ Enabled' : '❌ Disabled'; ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="wpcmo-card">
                <div class="wpcmo-card-header">
                    <div class="wpcmo-card-icon">🔑</div>
                    <h2 class="wpcmo-card-title">License Status</h2>
                </div>
                <div class="wpcmo-card-body">
                    <div class="wpcmo-stat">
                        <span class="wpcmo-stat-label">Status</span>
                        <span class="wpcmo-stat-value">
                            <?php if ($license_status === 'active'): ?>
                                <span class="wpcmo-badge wpcmo-badge-success">Active</span>
                            <?php else: ?>
                                <span class="wpcmo-badge wpcmo-badge-inactive">Inactive</span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <?php if ($license_status === 'active'): ?>
                        <?php $license_data = get_option('wpcmo_license_data', []); ?>
                        <div class="wpcmo-stat">
                            <span class="wpcmo-stat-label">Plan</span>
                            <span class="wpcmo-stat-value"><?php echo esc_html($license_data['plan'] ?? 'Standard'); ?></span>
                        </div>
                        <div class="wpcmo-stat">
                            <span class="wpcmo-stat-label">Expires</span>
                            <span class="wpcmo-stat-value"><?php echo esc_html($license_data['expires'] ?? 'Never'); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="wpcmo-card">
            <div class="wpcmo-card-header">
                <div class="wpcmo-card-icon">🚀</div>
                <h2 class="wpcmo-card-title">Quick Actions</h2>
            </div>
            <div class="wpcmo-card-body">
                <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                    <a href="<?php echo admin_url('admin.php?page=wpcmo-settings'); ?>" class="wpcmo-btn wpcmo-btn-primary">
                        ⚙️ Configure Settings
                    </a>
                    <a href="<?php echo admin_url('admin.php?page=wpcmo-bulk-upload'); ?>" class="wpcmo-btn wpcmo-btn-primary">
                        📤 Bulk Upload
                    </a>
                    <?php if ($license_status !== 'active'): ?>
                    <a href="<?php echo admin_url('admin.php?page=wpcmo-license'); ?>" class="wpcmo-btn wpcmo-btn-secondary">
                        🔑 Activate License
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

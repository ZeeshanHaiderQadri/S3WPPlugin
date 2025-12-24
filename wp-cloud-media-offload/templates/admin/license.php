<?php
if (!defined('ABSPATH')) exit;

$license_key = get_option('wpcmo_license_key', '');
$license_status = get_option('wpcmo_license_status', 'inactive');
$license_data = get_option('wpcmo_license_data', []);
?>

<div class="wpcmo-wrap" data-theme="light">
    <div class="wpcmo-header">
        <button class="wpcmo-theme-toggle">🌙 Dark Mode</button>
        <div class="wpcmo-header-content">
            <h1>🔑 License Activation</h1>
            <p>Activate your license to unlock all features</p>
        </div>
    </div>

    <div class="wpcmo-container">
        <div class="wpcmo-alerts"></div>

        <?php if ($license_status === 'active'): ?>
        <div class="wpcmo-card">
            <div class="wpcmo-card-header">
                <div class="wpcmo-card-icon">✅</div>
                <h2 class="wpcmo-card-title">License Active</h2>
            </div>
            <div class="wpcmo-card-body">
                <div class="wpcmo-alert wpcmo-alert-success">
                    <span>✅</span>
                    <div>
                        <strong>Your license is active!</strong><br>
                        You have full access to all features of WP Cloud Media Offload.
                    </div>
                </div>

                <div class="wpcmo-stat">
                    <span class="wpcmo-stat-label">License Key</span>
                    <span class="wpcmo-stat-value"><?php echo esc_html(substr($license_key, 0, 8) . '••••••••' . substr($license_key, -4)); ?></span>
                </div>
                <div class="wpcmo-stat">
                    <span class="wpcmo-stat-label">Plan</span>
                    <span class="wpcmo-stat-value"><?php echo esc_html($license_data['plan'] ?? 'Standard'); ?></span>
                </div>
                <div class="wpcmo-stat">
                    <span class="wpcmo-stat-label">Status</span>
                    <span class="wpcmo-stat-value"><span class="wpcmo-badge wpcmo-badge-success">Active</span></span>
                </div>
                <?php if (!empty($license_data['expires'])): ?>
                <div class="wpcmo-stat">
                    <span class="wpcmo-stat-label">Expires</span>
                    <span class="wpcmo-stat-value"><?php echo esc_html($license_data['expires']); ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php else: ?>
        <div class="wpcmo-card">
            <div class="wpcmo-card-header">
                <div class="wpcmo-card-icon">🔑</div>
                <h2 class="wpcmo-card-title">Activate Your License</h2>
            </div>
            <div class="wpcmo-card-body">
                <form id="wpcmo-license-form">
                    <div class="wpcmo-form-group">
                        <label class="wpcmo-form-label">License Key</label>
                        <input type="text" name="license_key" class="wpcmo-input" placeholder="XXXX-XXXX-XXXX-XXXX" required>
                        <p class="wpcmo-form-description">Enter your license key to activate the plugin</p>
                    </div>

                    <button type="submit" class="wpcmo-btn wpcmo-btn-primary">
                        🔓 Activate License
                    </button>
                </form>
            </div>
        </div>

        <div class="wpcmo-grid">
            <div class="wpcmo-card">
                <div class="wpcmo-card-header">
                    <div class="wpcmo-card-icon">💎</div>
                    <h2 class="wpcmo-card-title">Premium Features</h2>
                </div>
                <div class="wpcmo-card-body">
                    <ul style="color: var(--wpcmo-text-secondary-light); line-height: 1.8;">
                        <li>✅ Unlimited media uploads</li>
                        <li>✅ CloudFront CDN integration</li>
                        <li>✅ Automatic background uploads</li>
                        <li>✅ Bulk upload tools</li>
                        <li>✅ Priority email support</li>
                        <li>✅ Regular updates</li>
                    </ul>
                </div>
            </div>

            <div class="wpcmo-card">
                <div class="wpcmo-card-header">
                    <div class="wpcmo-card-icon">🛒</div>
                    <h2 class="wpcmo-card-title">Get a License</h2>
                </div>
                <div class="wpcmo-card-body">
                    <p style="color: var(--wpcmo-text-secondary-light); margin-bottom: 16px;">
                        Choose the plan that fits your needs:
                    </p>
                    <ul style="color: var(--wpcmo-text-secondary-light); line-height: 1.8; margin-bottom: 20px;">
                        <li><strong>Bronze:</strong> Up to 2,000 files - $39/year</li>
                        <li><strong>Silver:</strong> Up to 6,000 files - $59/year</li>
                        <li><strong>Gold:</strong> Up to 20,000 files - $149/year</li>
                        <li><strong>Platinum:</strong> Up to 40,000 files - $199/year</li>
                        <li><strong>Unlimited:</strong> Unlimited files - $1,199/year</li>
                    </ul>
                    <a href="https://yoursite.com/pricing" target="_blank" class="wpcmo-btn wpcmo-btn-primary">
                        🛒 View Pricing
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="wpcmo-card">
            <div class="wpcmo-card-header">
                <div class="wpcmo-card-icon">❓</div>
                <h2 class="wpcmo-card-title">Need Help?</h2>
            </div>
            <div class="wpcmo-card-body">
                <p style="color: var(--wpcmo-text-secondary-light); margin-bottom: 16px;">
                    If you're having trouble activating your license, please check:
                </p>
                <ul style="color: var(--wpcmo-text-secondary-light); line-height: 1.8;">
                    <li>Make sure you're entering the correct license key</li>
                    <li>Verify that your license hasn't expired</li>
                    <li>Check that you haven't exceeded the activation limit</li>
                    <li>Ensure your server can connect to our license server</li>
                </ul>
                <div style="margin-top: 20px;">
                    <a href="https://yoursite.com/support" target="_blank" class="wpcmo-btn wpcmo-btn-secondary">
                        📧 Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

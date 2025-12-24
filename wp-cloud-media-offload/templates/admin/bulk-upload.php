<?php
if (!defined('ABSPATH')) exit;

global $wpdb;
$table_name = $wpdb->prefix . 'wpcmo_uploads';
$uploaded_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
$total_attachments = wp_count_posts('attachment')->inherit;
$remaining = max(0, $total_attachments - $uploaded_count);
?>

<div class="wpcmo-wrap" data-theme="light">
    <div class="wpcmo-header">
        <button class="wpcmo-theme-toggle">🌙 Dark Mode</button>
        <div class="wpcmo-header-content">
            <h1>📤 Bulk Upload</h1>
            <p>Upload existing media files to cloud storage</p>
        </div>
    </div>

    <div class="wpcmo-container">
        <div class="wpcmo-alerts"></div>

        <div class="wpcmo-card">
            <div class="wpcmo-card-header">
                <div class="wpcmo-card-icon">📊</div>
                <h2 class="wpcmo-card-title">Upload Progress</h2>
            </div>
            <div class="wpcmo-card-body">
                <div class="wpcmo-stat">
                    <span class="wpcmo-stat-label">Total Media Files</span>
                    <span class="wpcmo-stat-value wpcmo-total-count"><?php echo number_format($total_attachments); ?></span>
                </div>
                <div class="wpcmo-stat">
                    <span class="wpcmo-stat-label">Already Uploaded</span>
                    <span class="wpcmo-stat-value wpcmo-uploaded-count"><?php echo number_format($uploaded_count); ?></span>
                </div>
                <div class="wpcmo-stat">
                    <span class="wpcmo-stat-label">Remaining</span>
                    <span class="wpcmo-stat-value wpcmo-processed-count"><?php echo number_format($remaining); ?></span>
                </div>
                <div class="wpcmo-stat">
                    <span class="wpcmo-stat-label">Failed</span>
                    <span class="wpcmo-stat-value wpcmo-failed-count">0</span>
                </div>

                <div class="wpcmo-progress">
                    <div class="wpcmo-progress-bar" style="width: <?php echo $total_attachments > 0 ? ($uploaded_count / $total_attachments * 100) : 0; ?>%"></div>
                </div>
            </div>
        </div>

        <div class="wpcmo-card">
            <div class="wpcmo-card-header">
                <div class="wpcmo-card-icon">⚙️</div>
                <h2 class="wpcmo-card-title">Bulk Upload Options</h2>
            </div>
            <div class="wpcmo-card-body">
                <div class="wpcmo-alert wpcmo-alert-info">
                    <span>ℹ️</span>
                    <div>
                        <strong>Before You Start</strong><br>
                        Make sure you have configured your AWS S3 credentials in the Settings page. Choose between Quick Upload (browser-based) or Background Upload (server-based cron).
                    </div>
                </div>

                <div style="margin-top: 24px;">
                    <h3 style="margin-bottom: 12px; color: var(--wpcmo-text-light);">What will be uploaded?</h3>
                    <ul style="color: var(--wpcmo-text-secondary-light); line-height: 1.8;">
                        <li>✅ All image attachments + thumbnails in your media library</li>
                        <li>✅ Files will be uploaded to your configured S3 bucket</li>
                        <li>✅ Original files can be kept or removed based on your settings</li>
                        <li>✅ Already uploaded files will be skipped</li>
                    </ul>
                </div>

                <div style="margin-top: 24px;">
                    <h3 style="margin-bottom: 12px; color: var(--wpcmo-text-light);">Upload Method</h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                        <div style="border: 2px solid var(--wpcmo-border-light); border-radius: 8px; padding: 16px;">
                            <h4 style="margin: 0 0 8px 0; color: var(--wpcmo-text-light);">⚡ Quick Upload</h4>
                            <p style="margin: 0 0 12px 0; font-size: 14px; color: var(--wpcmo-text-secondary-light);">
                                Fast browser-based upload. Requires keeping browser tab open.
                            </p>
                            <ul style="font-size: 13px; color: var(--wpcmo-text-secondary-light); margin: 0; padding-left: 20px;">
                                <li>Best for: &lt;10,000 images</li>
                                <li>Speed: 50-100 images/min</li>
                                <li>Keep browser open</li>
                            </ul>
                        </div>
                        
                        <div style="border: 2px solid #10b981; border-radius: 8px; padding: 16px; background: rgba(16, 185, 129, 0.05);">
                            <h4 style="margin: 0 0 8px 0; color: var(--wpcmo-text-light);">🤖 Background Upload (Recommended)</h4>
                            <p style="margin: 0 0 12px 0; font-size: 14px; color: var(--wpcmo-text-secondary-light);">
                                Server-based cron job. Runs automatically in background.
                            </p>
                            <ul style="font-size: 13px; color: var(--wpcmo-text-secondary-light); margin: 0; padding-left: 20px;">
                                <li>Best for: 10,000+ images</li>
                                <li>Speed: 10 images/min (gentle on CPU)</li>
                                <li>Close browser anytime</li>
                                <li>No server overload</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 24px; display: flex; gap: 12px; flex-wrap: wrap;">
                    <button type="button" class="wpcmo-btn wpcmo-btn-primary wpcmo-start-bulk-upload">
                        ⚡ Quick Upload
                    </button>
                    <button type="button" class="wpcmo-btn wpcmo-btn-success wpcmo-start-background-upload">
                        🤖 Background Upload
                    </button>
                    <button type="button" class="wpcmo-btn wpcmo-btn-danger wpcmo-stop-bulk-upload" style="display: none;">
                        ⏹️ Stop Upload
                    </button>
                    <button type="button" class="wpcmo-btn wpcmo-btn-danger wpcmo-stop-background-upload" style="display: none;">
                        ⏹️ Stop Background Upload
                    </button>
                </div>
                
                <div class="wpcmo-background-status" style="display: none; margin-top: 20px; padding: 16px; background: rgba(16, 185, 129, 0.1); border-radius: 8px; border: 1px solid #10b981;">
                    <h4 style="margin: 0 0 8px 0; color: #10b981;">🤖 Background Upload Active</h4>
                    <p style="margin: 0; font-size: 14px; color: var(--wpcmo-text-secondary-light);">
                        Upload is running automatically in the background via WP-Cron. You can close this page safely.
                    </p>
                    <p style="margin: 8px 0 0 0; font-size: 13px; color: var(--wpcmo-text-secondary-light);">
                        Processing <strong class="wpcmo-bg-batch-size">10</strong> images every minute. Refresh page to see updated progress.
                    </p>
                </div>
            </div>
        </div>

        <div class="wpcmo-card">
            <div class="wpcmo-card-header">
                <div class="wpcmo-card-icon">⚙️</div>
                <h2 class="wpcmo-card-title">Advanced: Real Cron Setup (Optional)</h2>
            </div>
            <div class="wpcmo-card-body">
                <div class="wpcmo-alert wpcmo-alert-info">
                    <span>ℹ️</span>
                    <div>
                        <strong>When do you need this?</strong><br>
                        Only if your hosting provider has disabled WordPress WP-Cron. Most users don't need this - WP-Cron works automatically!
                    </div>
                </div>

                <div style="margin-top: 20px;">
                    <h3 style="margin-bottom: 12px; color: var(--wpcmo-text-light);">How to Check if WP-Cron is Disabled</h3>
                    <ol style="color: var(--wpcmo-text-secondary-light); line-height: 1.8;">
                        <li>Check your <code>wp-config.php</code> file</li>
                        <li>Look for: <code>define('DISABLE_WP_CRON', true);</code></li>
                        <li>If you see this line, WP-Cron is disabled</li>
                        <li>If you don't see it, WP-Cron is enabled (you're good!)</li>
                    </ol>
                </div>

                <div style="margin-top: 24px;">
                    <h3 style="margin-bottom: 12px; color: var(--wpcmo-text-light);">Setup Real Cron Job (If WP-Cron is Disabled)</h3>
                    
                    <div style="margin-bottom: 20px;">
                        <h4 style="margin: 0 0 8px 0; color: var(--wpcmo-text-light); font-size: 14px;">📋 Your Cron URL:</h4>
                        <div style="position: relative;">
                            <input 
                                type="text" 
                                readonly 
                                value="<?php echo esc_attr(home_url('/wp-cron.php?doing_wp_cron')); ?>" 
                                id="wpcmo-cron-url"
                                style="width: 100%; padding: 12px; border: 2px solid var(--wpcmo-border-light); border-radius: 6px; font-family: monospace; font-size: 13px; background: var(--wpcmo-bg-secondary-light); color: var(--wpcmo-text-light);"
                            />
                            <button 
                                type="button" 
                                class="wpcmo-copy-btn" 
                                data-copy="wpcmo-cron-url"
                                style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); padding: 6px 12px; background: var(--wpcmo-primary); color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;"
                            >
                                📋 Copy
                            </button>
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <h4 style="margin: 0 0 12px 0; color: var(--wpcmo-text-light); font-size: 14px;">🔧 Cron Commands (Choose One):</h4>
                        
                        <div style="margin-bottom: 16px;">
                            <p style="margin: 0 0 8px 0; font-size: 13px; color: var(--wpcmo-text-secondary-light);"><strong>Option 1: Using CURL (Recommended)</strong></p>
                            <div style="position: relative;">
                                <textarea 
                                    readonly 
                                    id="wpcmo-cron-curl"
                                    rows="2"
                                    style="width: 100%; padding: 12px; border: 2px solid var(--wpcmo-border-light); border-radius: 6px; font-family: monospace; font-size: 12px; background: var(--wpcmo-bg-secondary-light); color: var(--wpcmo-text-light); resize: none;"
                                >*/1 * * * * curl -s <?php echo esc_attr(home_url('/wp-cron.php?doing_wp_cron')); ?> >/dev/null 2>&1</textarea>
                                <button 
                                    type="button" 
                                    class="wpcmo-copy-btn" 
                                    data-copy="wpcmo-cron-curl"
                                    style="position: absolute; right: 8px; top: 8px; padding: 6px 12px; background: var(--wpcmo-primary); color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;"
                                >
                                    📋 Copy
                                </button>
                            </div>
                        </div>

                        <div style="margin-bottom: 16px;">
                            <p style="margin: 0 0 8px 0; font-size: 13px; color: var(--wpcmo-text-secondary-light);"><strong>Option 2: Using WGET</strong></p>
                            <div style="position: relative;">
                                <textarea 
                                    readonly 
                                    id="wpcmo-cron-wget"
                                    rows="2"
                                    style="width: 100%; padding: 12px; border: 2px solid var(--wpcmo-border-light); border-radius: 6px; font-family: monospace; font-size: 12px; background: var(--wpcmo-bg-secondary-light); color: var(--wpcmo-text-light); resize: none;"
                                >*/1 * * * * wget -q -O - <?php echo esc_attr(home_url('/wp-cron.php?doing_wp_cron')); ?> >/dev/null 2>&1</textarea>
                                <button 
                                    type="button" 
                                    class="wpcmo-copy-btn" 
                                    data-copy="wpcmo-cron-wget"
                                    style="position: absolute; right: 8px; top: 8px; padding: 6px 12px; background: var(--wpcmo-primary); color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;"
                                >
                                    📋 Copy
                                </button>
                            </div>
                        </div>

                        <div style="margin-bottom: 16px;">
                            <p style="margin: 0 0 8px 0; font-size: 13px; color: var(--wpcmo-text-secondary-light);"><strong>Option 3: Using PHP CLI</strong></p>
                            <div style="position: relative;">
                                <textarea 
                                    readonly 
                                    id="wpcmo-cron-php"
                                    rows="2"
                                    style="width: 100%; padding: 12px; border: 2px solid var(--wpcmo-border-light); border-radius: 6px; font-family: monospace; font-size: 12px; background: var(--wpcmo-bg-secondary-light); color: var(--wpcmo-text-light); resize: none;"
                                >*/1 * * * * php <?php echo esc_attr(ABSPATH); ?>wp-cron.php >/dev/null 2>&1</textarea>
                                <button 
                                    type="button" 
                                    class="wpcmo-copy-btn" 
                                    data-copy="wpcmo-cron-php"
                                    style="position: absolute; right: 8px; top: 8px; padding: 6px 12px; background: var(--wpcmo-primary); color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;"
                                >
                                    📋 Copy
                                </button>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 24px; padding: 16px; background: rgba(59, 130, 246, 0.1); border-radius: 8px; border-left: 4px solid #3b82f6;">
                        <h4 style="margin: 0 0 12px 0; color: #3b82f6; font-size: 14px;">📖 Step-by-Step Instructions</h4>
                        <ol style="margin: 0; padding-left: 20px; color: var(--wpcmo-text-secondary-light); line-height: 1.8; font-size: 13px;">
                            <li><strong>Log into your hosting control panel</strong> (cPanel, Plesk, etc.)</li>
                            <li><strong>Find "Cron Jobs"</strong> section (usually under Advanced)</li>
                            <li><strong>Click "Add New Cron Job"</strong></li>
                            <li><strong>Set frequency to:</strong> Every minute (*/1 * * * *)</li>
                            <li><strong>Copy one of the commands above</strong> (click 📋 Copy button)</li>
                            <li><strong>Paste into "Command" field</strong></li>
                            <li><strong>Save the cron job</strong></li>
                            <li><strong>Done!</strong> Your cron will run every minute</li>
                        </ol>
                    </div>

                    <div style="margin-top: 16px; padding: 12px; background: rgba(16, 185, 129, 0.1); border-radius: 6px; border-left: 4px solid #10b981;">
                        <p style="margin: 0; font-size: 13px; color: var(--wpcmo-text-secondary-light);">
                            <strong>💡 Tip:</strong> After setting up the cron job, start Background Upload and wait 2-3 minutes. Then refresh this page to see if progress is updating.
                        </p>
                    </div>

                    <div style="margin-top: 16px;">
                        <details style="cursor: pointer;">
                            <summary style="padding: 12px; background: var(--wpcmo-bg-secondary-light); border-radius: 6px; font-weight: 500; color: var(--wpcmo-text-light);">
                                🎥 Video Tutorials (Click to expand)
                            </summary>
                            <div style="padding: 16px; border: 1px solid var(--wpcmo-border-light); border-top: none; border-radius: 0 0 6px 6px;">
                                <ul style="margin: 0; padding-left: 20px; color: var(--wpcmo-text-secondary-light); line-height: 1.8;">
                                    <li><strong>cPanel:</strong> Search YouTube for "cPanel cron job setup"</li>
                                    <li><strong>Plesk:</strong> Search YouTube for "Plesk cron job setup"</li>
                                    <li><strong>Hostinger:</strong> Search YouTube for "Hostinger cron job setup"</li>
                                    <li><strong>SiteGround:</strong> Search YouTube for "SiteGround cron job setup"</li>
                                    <li><strong>Bluehost:</strong> Search YouTube for "Bluehost cron job setup"</li>
                                </ul>
                            </div>
                        </details>
                    </div>
                </div>
            </div>
        </div>

        <div class="wpcmo-card">
            <div class="wpcmo-card-header">
                <div class="wpcmo-card-icon">💡</div>
                <h2 class="wpcmo-card-title">Upload Time Estimates</h2>
            </div>
            <div class="wpcmo-card-body">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--wpcmo-border-light);">
                            <th style="padding: 12px; text-align: left; color: var(--wpcmo-text-light);">Images</th>
                            <th style="padding: 12px; text-align: left; color: var(--wpcmo-text-light);">Quick Upload</th>
                            <th style="padding: 12px; text-align: left; color: var(--wpcmo-text-light);">Background Upload</th>
                        </tr>
                    </thead>
                    <tbody style="color: var(--wpcmo-text-secondary-light);">
                        <tr style="border-bottom: 1px solid var(--wpcmo-border-light);">
                            <td style="padding: 12px;">1,000</td>
                            <td style="padding: 12px;">~10-20 minutes</td>
                            <td style="padding: 12px;">~1.5 hours</td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--wpcmo-border-light);">
                            <td style="padding: 12px;">10,000</td>
                            <td style="padding: 12px;">~2-3 hours</td>
                            <td style="padding: 12px;">~16 hours</td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--wpcmo-border-light);">
                            <td style="padding: 12px;"><strong>50,000</strong></td>
                            <td style="padding: 12px;">~10-15 hours</td>
                            <td style="padding: 12px;"><strong>~3.5 days (Recommended)</strong></td>
                        </tr>
                        <tr>
                            <td style="padding: 12px;">100,000</td>
                            <td style="padding: 12px;">~20-30 hours</td>
                            <td style="padding: 12px;">~7 days</td>
                        </tr>
                    </tbody>
                </table>
                
                <div style="margin-top: 20px; padding: 12px; background: rgba(59, 130, 246, 0.1); border-radius: 6px; border-left: 4px solid #3b82f6;">
                    <p style="margin: 0; font-size: 14px; color: var(--wpcmo-text-secondary-light);">
                        <strong>💡 Tip for 50k+ images:</strong> Use Background Upload! It runs automatically via WP-Cron without overloading your server CPU. You can close your browser and let it run for days.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

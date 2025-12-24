(function($) {
    'use strict';

    const WPCMO = {
        init: function() {
            this.themeToggle();
            this.settingsForm();
            this.testConnection();
            this.licenseActivation();
            this.bulkUpload();
            this.providerSelection();
        },

        themeToggle: function() {
            const $toggle = $('.wpcmo-theme-toggle');
            const currentTheme = localStorage.getItem('wpcmo-theme') || 'light';
            
            $('body').attr('data-theme', currentTheme);
            this.updateToggleText($toggle, currentTheme);

            $toggle.on('click', function() {
                const newTheme = $('body').attr('data-theme') === 'light' ? 'dark' : 'light';
                $('body').attr('data-theme', newTheme);
                localStorage.setItem('wpcmo-theme', newTheme);
                WPCMO.updateToggleText($toggle, newTheme);
            });
        },

        updateToggleText: function($toggle, theme) {
            $toggle.html(theme === 'light' ? '🌙 Dark Mode' : '☀️ Light Mode');
        },

        settingsForm: function() {
            console.log('WPCMO: settingsForm() called');
            console.log('WPCMO: Looking for form #wpcmo-settings-form');
            console.log('WPCMO: Form found:', $('#wpcmo-settings-form').length);
            
            $('#wpcmo-settings-form').on('submit', function(e) {
                console.log('WPCMO: Form submit event triggered!');
                e.preventDefault();
                
                const $form = $(this);
                const $button = $form.find('button[type="submit"]');
                const originalText = $button.html();
                
                console.log('WPCMO: Button found:', $button.length);
                console.log('WPCMO: Original button text:', originalText);
                
                $button.prop('disabled', true).html('<span class="wpcmo-spinner"></span> ' + wpcmoData.strings.saving);
                
                const formData = new FormData(this);
                const settings = {};
                
                formData.forEach((value, key) => {
                    if (key.startsWith('wpcmo_')) {
                        const settingKey = key.replace('wpcmo_', '');
                        settings[settingKey] = value;
                    } else if (key === 'provider') {
                        // Handle provider field (doesn't have wpcmo_ prefix)
                        settings[key] = value;
                    }
                });
                
                // Handle checkboxes
                $form.find('input[type="checkbox"]').each(function() {
                    const name = $(this).attr('name');
                    if (name && name.startsWith('wpcmo_')) {
                        const settingKey = name.replace('wpcmo_', '');
                        settings[settingKey] = $(this).is(':checked');
                    }
                });
                
                // Ensure provider is set
                if (!settings.provider) {
                    settings.provider = $('input[name="provider"]').val() || 'aws_s3';
                }
                
                console.log('WPCMO: Saving settings:', settings);
                console.log('WPCMO: AJAX URL:', wpcmoData.ajaxUrl);
                console.log('WPCMO: Nonce:', wpcmoData.nonce);
                
                console.log('WPCMO: Starting AJAX request...');
                
                $.ajax({
                    url: wpcmoData.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'wpcmo_save_settings',
                        nonce: wpcmoData.nonce,
                        settings: settings
                    },
                    beforeSend: function() {
                        console.log('WPCMO: AJAX beforeSend');
                    },
                    success: function(response) {
                        console.log('WPCMO: AJAX success!');
                        console.log('WPCMO: Response:', response);
                        console.log('WPCMO: Response type:', typeof response);
                        console.log('WPCMO: Response.success:', response.success);
                        
                        if (response.success) {
                            console.log('WPCMO: Showing success alert');
                            WPCMO.showAlert('success', response.data.message || wpcmoData.strings.saved);
                        } else {
                            console.log('WPCMO: Showing error alert');
                            WPCMO.showAlert('error', response.data.message || wpcmoData.strings.error);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('WPCMO: AJAX error!');
                        console.error('WPCMO: XHR:', xhr);
                        console.error('WPCMO: Status:', status);
                        console.error('WPCMO: Error:', error);
                        console.error('WPCMO: Response text:', xhr.responseText);
                        WPCMO.showAlert('error', wpcmoData.strings.error);
                    },
                    complete: function() {
                        console.log('WPCMO: AJAX complete');
                        $button.prop('disabled', false).html(originalText);
                    }
                });
            });
        },

        testConnection: function() {
            $('.wpcmo-test-connection').on('click', function(e) {
                e.preventDefault();
                
                const $button = $(this);
                const originalText = $button.html();
                const $form = $('#wpcmo-settings-form');
                
                // Get current form data for testing
                const formData = new FormData($form[0]);
                const settings = {};
                
                formData.forEach((value, key) => {
                    if (key.startsWith('wpcmo_')) {
                        const settingKey = key.replace('wpcmo_', '');
                        settings[settingKey] = value;
                    } else if (key === 'provider') {
                        settings[key] = value;
                    }
                });
                
                // Handle checkboxes
                $form.find('input[type="checkbox"]').each(function() {
                    const name = $(this).attr('name');
                    if (name && name.startsWith('wpcmo_')) {
                        const settingKey = name.replace('wpcmo_', '');
                        settings[settingKey] = $(this).is(':checked');
                    }
                });
                
                // Ensure provider is set - get from hidden input or selected card
                if (!settings.provider) {
                    settings.provider = $('input[name="provider"]').val();
                }
                
                // If still not set, get from selected provider card
                if (!settings.provider) {
                    const $selectedCard = $('.wpcmo-provider-card.selected');
                    if ($selectedCard.length) {
                        settings.provider = $selectedCard.data('provider');
                    }
                }
                
                // Final fallback
                if (!settings.provider) {
                    settings.provider = 'aws_s3';
                }
                
                console.log('Testing connection with provider:', settings.provider);
                console.log('Settings:', settings);
                
                // Validate required fields based on provider
                let missingFields = [];
                if (settings.provider === 'wasabi') {
                    if (!settings.wasabi_access_key) missingFields.push('Access Key');
                    if (!settings.wasabi_secret_key) missingFields.push('Secret Key');
                    if (!settings.wasabi_bucket) missingFields.push('Bucket Name');
                } else if (settings.provider === 'aws_s3') {
                    if (!settings.aws_access_key) missingFields.push('Access Key');
                    if (!settings.aws_secret_key) missingFields.push('Secret Key');
                    if (!settings.aws_bucket) missingFields.push('Bucket Name');
                }
                
                if (missingFields.length > 0) {
                    WPCMO.showAlert('error', '⚠️ Please fill in required fields: ' + missingFields.join(', '));
                    return;
                }
                
                $button.prop('disabled', true).html('<span class="wpcmo-spinner"></span> ' + wpcmoData.strings.testing);
                
                $.ajax({
                    url: wpcmoData.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'wpcmo_test_connection',
                        nonce: wpcmoData.nonce,
                        settings: settings
                    },
                    success: function(response) {
                        console.log('Test response:', response);
                        if (response.success) {
                            WPCMO.showAlert('success', response.data.message || wpcmoData.strings.connected);
                            
                            // Show detailed results if available
                            if (response.data.details) {
                                console.log('Detailed test results:', response.data.details);
                            }
                        } else {
                            WPCMO.showAlert('error', response.data.message || '❌ Connection failed');
                            
                            // Show detailed results if available
                            if (response.data.details) {
                                console.log('Detailed test results:', response.data.details);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Test error:', xhr, status, error);
                        console.error('Response:', xhr.responseText);
                        
                        let errorMessage = wpcmoData.strings.error;
                        
                        // Try to parse JSON response
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.data && xhr.responseJSON.data.message) {
                                errorMessage = xhr.responseJSON.data.message;
                            } else if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                        } else if (xhr.responseText) {
                            // Try to parse the response text as JSON
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.data && response.data.message) {
                                    errorMessage = response.data.message;
                                } else if (response.message) {
                                    errorMessage = response.message;
                                }
                            } catch (e) {
                                // If not JSON, show the status text
                                errorMessage = '❌ Server error (' + xhr.status + '): ' + xhr.statusText;
                                if (xhr.responseText.length < 200) {
                                    errorMessage += ' - ' + xhr.responseText;
                                }
                            }
                        }
                        
                        WPCMO.showAlert('error', errorMessage);
                    },
                    complete: function() {
                        $button.prop('disabled', false).html(originalText);
                    }
                });
            });
        },

        licenseActivation: function() {
            $('#wpcmo-license-form').on('submit', function(e) {
                e.preventDefault();
                
                const $form = $(this);
                const $button = $form.find('button[type="submit"]');
                const $input = $form.find('input[name="license_key"]');
                const originalText = $button.html();
                
                $button.prop('disabled', true).html('<span class="wpcmo-spinner"></span> Activating...');
                
                $.ajax({
                    url: wpcmoData.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'wpcmo_activate_license',
                        nonce: wpcmoData.nonce,
                        license_key: $input.val()
                    },
                    success: function(response) {
                        if (response.success) {
                            WPCMO.showAlert('success', response.data.message);
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            WPCMO.showAlert('error', response.data.message);
                        }
                    },
                    error: function() {
                        WPCMO.showAlert('error', wpcmoData.strings.error);
                    },
                    complete: function() {
                        $button.prop('disabled', false).html(originalText);
                    }
                });
            });
        },

        bulkUpload: function() {
            let isUploading = false;
            let offset = 0;
            
            $('.wpcmo-start-bulk-upload').on('click', function(e) {
                e.preventDefault();
                
                if (isUploading) {
                    return;
                }
                
                isUploading = true;
                offset = 0;
                
                const $button = $(this);
                $button.prop('disabled', true).html('<span class="wpcmo-spinner"></span> ' + wpcmoData.strings.uploading);
                
                WPCMO.processBulkUploadBatch();
            });
            
            $('.wpcmo-stop-bulk-upload').on('click', function(e) {
                e.preventDefault();
                isUploading = false;
                $('.wpcmo-start-bulk-upload').prop('disabled', false).html('Start Upload');
            });
        },

        processBulkUploadBatch: function() {
            $.ajax({
                url: wpcmoData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'wpcmo_bulk_upload',
                    nonce: wpcmoData.nonce,
                    offset: offset
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        
                        // Update progress
                        const progress = (data.processed / data.total) * 100;
                        $('.wpcmo-progress-bar').css('width', progress + '%');
                        
                        // Update stats
                        $('.wpcmo-uploaded-count').text(data.uploaded);
                        $('.wpcmo-failed-count').text(data.failed);
                        $('.wpcmo-processed-count').text(data.processed);
                        $('.wpcmo-total-count').text(data.total);
                        
                        if (!data.complete) {
                            offset = data.processed;
                            setTimeout(function() {
                                WPCMO.processBulkUploadBatch();
                            }, 500);
                        } else {
                            WPCMO.showAlert('success', 'Bulk upload completed!');
                            $('.wpcmo-start-bulk-upload').prop('disabled', false).html('Start Upload');
                        }
                    } else {
                        WPCMO.showAlert('error', 'Upload failed');
                        $('.wpcmo-start-bulk-upload').prop('disabled', false).html('Start Upload');
                    }
                },
                error: function() {
                    WPCMO.showAlert('error', wpcmoData.strings.error);
                    $('.wpcmo-start-bulk-upload').prop('disabled', false).html('Start Upload');
                }
            });
        },

        providerSelection: function() {
            $('.wpcmo-provider-card').on('click', function() {
                $('.wpcmo-provider-card').removeClass('selected');
                $(this).addClass('selected');
                
                const provider = $(this).data('provider');
                const $providerInput = $('input[name="provider"]');
                $providerInput.val(provider);
                
                console.log('Provider selected:', provider);
                console.log('Provider input value:', $providerInput.val());
                
                // Show/hide provider-specific settings
                $('.wpcmo-provider-settings').hide();
                $('.wpcmo-provider-settings[data-provider="' + provider + '"]').show();
                
                // Update CDN section visibility based on provider
                if (provider === 'aws_s3') {
                    $('.wpcmo-card:has(.wpcmo-card-title:contains("CloudFront"))').show();
                } else {
                    $('.wpcmo-card:has(.wpcmo-card-title:contains("CloudFront"))').hide();
                }
            });
            
            // Ensure correct provider is shown on page load
            const currentProvider = $('input[name="provider"]').val() || 'aws_s3';
            $('.wpcmo-provider-settings').hide();
            $('.wpcmo-provider-settings[data-provider="' + currentProvider + '"]').show();
            
            if (currentProvider !== 'aws_s3') {
                $('.wpcmo-card:has(.wpcmo-card-title:contains("CloudFront"))').hide();
            }
        },

        showAlert: function(type, message) {
            const alertClass = 'wpcmo-alert-' + type;
            const $alert = $('<div class="wpcmo-alert ' + alertClass + '">' + message + '</div>');
            
            $('.wpcmo-alerts').html($alert);
            
            setTimeout(function() {
                $alert.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        }
    };

    $(document).ready(function() {
        
        // Background Upload handlers
        backgroundUpload: function() {
            const self = this;
            let statusInterval = null;
            
            // Start background upload
            $('.wpcmo-start-background-upload').on('click', function() {
                if (!confirm('Start background upload? This will run automatically via WP-Cron. You can close your browser.')) {
                    return;
                }
                
                const $button = $(this);
                $button.prop('disabled', true).html('<span class="wpcmo-spinner"></span> Starting...');
                
                $.ajax({
                    url: wpcmoData.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'wpcmo_start_background_upload',
                        nonce: wpcmoData.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            self.showAlert('success', response.data.message);
                            $('.wpcmo-start-background-upload').hide();
                            $('.wpcmo-start-bulk-upload').hide();
                            $('.wpcmo-stop-background-upload').show();
                            $('.wpcmo-background-status').show();
                            
                            // Start polling for status
                            self.startBackgroundStatusPolling();
                        } else {
                            self.showAlert('error', response.data.message || 'Failed to start background upload');
                            $button.prop('disabled', false).html('🤖 Background Upload');
                        }
                    },
                    error: function() {
                        self.showAlert('error', 'Failed to start background upload');
                        $button.prop('disabled', false).html('🤖 Background Upload');
                    }
                });
            });
            
            // Stop background upload
            $('.wpcmo-stop-background-upload').on('click', function() {
                if (!confirm('Stop background upload?')) {
                    return;
                }
                
                const $button = $(this);
                $button.prop('disabled', true).html('<span class="wpcmo-spinner"></span> Stopping...');
                
                $.ajax({
                    url: wpcmoData.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'wpcmo_stop_background_upload',
                        nonce: wpcmoData.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            self.showAlert('success', response.data.message);
                            $('.wpcmo-stop-background-upload').hide();
                            $('.wpcmo-start-background-upload').show();
                            $('.wpcmo-start-bulk-upload').show();
                            $('.wpcmo-background-status').hide();
                            
                            // Stop polling
                            if (statusInterval) {
                                clearInterval(statusInterval);
                            }
                        }
                        $button.prop('disabled', false).html('⏹️ Stop Background Upload');
                    }
                });
            });
            
            // Check if background upload is already running on page load
            this.checkBackgroundStatus();
        },
        
        checkBackgroundStatus: function() {
            const self = this;
            $.ajax({
                url: wpcmoData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'wpcmo_background_upload_status',
                    nonce: wpcmoData.nonce
                },
                success: function(response) {
                    if (response.success && response.data.active) {
                        $('.wpcmo-start-background-upload').hide();
                        $('.wpcmo-start-bulk-upload').hide();
                        $('.wpcmo-stop-background-upload').show();
                        $('.wpcmo-background-status').show();
                        
                        // Update progress
                        self.updateBackgroundProgress(response.data);
                        
                        // Start polling
                        self.startBackgroundStatusPolling();
                    }
                }
            });
        },
        
        startBackgroundStatusPolling: function() {
            const self = this;
            
            // Poll every 10 seconds
            statusInterval = setInterval(function() {
                $.ajax({
                    url: wpcmoData.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'wpcmo_background_upload_status',
                        nonce: wpcmoData.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            if (!response.data.active) {
                                // Upload completed
                                clearInterval(statusInterval);
                                $('.wpcmo-stop-background-upload').hide();
                                $('.wpcmo-start-background-upload').show();
                                $('.wpcmo-start-bulk-upload').show();
                                $('.wpcmo-background-status').hide();
                                self.showAlert('success', '✅ Background upload completed!');
                            } else {
                                // Update progress
                                self.updateBackgroundProgress(response.data);
                            }
                        }
                    }
                });
            }, 10000); // Every 10 seconds
        },
        
        updateBackgroundProgress: function(data) {
            if (data.stats) {
                $('.wpcmo-uploaded-count').text(data.stats.uploaded || 0);
                $('.wpcmo-failed-count').text(data.stats.failed || 0);
            }
            
            if (data.progress) {
                $('.wpcmo-progress-bar').css('width', data.progress + '%');
            }
            
            if (data.remaining !== undefined) {
                $('.wpcmo-processed-count').text(data.remaining);
            }
        },
        
        // Copy to clipboard functionality
        copyToClipboard: function() {
            $('.wpcmo-copy-btn').on('click', function() {
                const targetId = $(this).data('copy');
                const $target = $('#' + targetId);
                const $button = $(this);
                const originalText = $button.html();
                
                // Select and copy text
                $target.select();
                $target[0].setSelectionRange(0, 99999); // For mobile
                
                try {
                    // Try modern clipboard API first
                    if (navigator.clipboard && window.isSecureContext) {
                        navigator.clipboard.writeText($target.val()).then(function() {
                            // Success feedback
                            $button.html('✅ Copied!');
                            $button.css('background', '#10b981');
                            
                            setTimeout(function() {
                                $button.html(originalText);
                                $button.css('background', '');
                            }, 2000);
                        }).catch(function() {
                            // Fallback to execCommand
                            fallbackCopy();
                        });
                    } else {
                        // Fallback for older browsers
                        fallbackCopy();
                    }
                } catch (err) {
                    fallbackCopy();
                }
                
                function fallbackCopy() {
                    try {
                        document.execCommand('copy');
                        $button.html('✅ Copied!');
                        $button.css('background', '#10b981');
                        
                        setTimeout(function() {
                            $button.html(originalText);
                            $button.css('background', '');
                        }, 2000);
                    } catch (err) {
                        $button.html('❌ Failed');
                        setTimeout(function() {
                            $button.html(originalText);
                        }, 2000);
                    }
                }
            });
        }
    };

    $(document).ready(function() {
        WPCMO.init();
        WPCMO.backgroundUpload();
        WPCMO.copyToClipboard();
    });

})(jQuery);

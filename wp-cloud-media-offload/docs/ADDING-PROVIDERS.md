# Adding New Cloud Storage Providers

This guide explains how to add support for new cloud storage providers to the WP Cloud Media Offload plugin.

## Architecture Overview

The plugin uses a provider-based architecture where each cloud storage service is implemented as a separate handler class. This makes it easy to add new providers without modifying existing code.

## Required Components

To add a new provider, you need to create:

1. **Handler Class** - Implements the storage operations
2. **Settings Fields** - Configuration options in admin
3. **Provider Selection** - UI for choosing the provider
4. **Documentation** - Setup guide for users

## Step 1: Create Handler Class

Create a new handler class in `includes/[ProviderName]/[ProviderName]Handler.php`:

```php
<?php
namespace WPCMO\[ProviderName];

class [ProviderName]Handler {
    private $settings;
    
    public function __construct() {
        $this->settings = get_option('wpcmo_settings', []);
        $this->init_client();
    }
    
    private function init_client() {
        // Initialize the provider's SDK/client
    }
    
    public function test_connection() {
        // Test if credentials and configuration are valid
        // Return true on success, false on failure
    }
    
    public function upload_file($file_path, $key) {
        // Upload file to cloud storage
        // Return array with 'success', 's3_url', 's3_key'
    }
    
    public function delete_file($key) {
        // Delete file from cloud storage
        // Return true on success, false on failure
    }
    
    public function get_file_url($key) {
        // Generate public URL for the file
        // Return the full URL string
    }
}
```

## Step 2: Update Settings Class

Add new settings fields in `includes/Admin/Settings.php`:

```php
public function sanitize_settings($input) {
    $sanitized = [];
    
    // Existing settings...
    
    // New provider settings
    $sanitized['[provider]_access_key'] = sanitize_text_field($input['[provider]_access_key'] ?? '');
    $sanitized['[provider]_secret_key'] = sanitize_text_field($input['[provider]_secret_key'] ?? '');
    $sanitized['[provider]_region'] = sanitize_text_field($input['[provider]_region'] ?? '');
    $sanitized['[provider]_bucket'] = sanitize_text_field($input['[provider]_bucket'] ?? '');
    
    return $sanitized;
}
```

## Step 3: Update Settings Template

Add provider card and settings section in `templates/admin/settings.php`:

```php
<!-- Add to provider grid -->
<div class="wpcmo-provider-card <?php echo ($settings['provider'] ?? '') === '[provider]' ? 'selected' : ''; ?>" data-provider="[provider]">
    <div class="wpcmo-provider-icon">🔷</div>
    <div class="wpcmo-provider-name">[Provider Name]</div>
</div>

<!-- Add settings section -->
<div class="wpcmo-card wpcmo-provider-settings" data-provider="[provider]" style="display: <?php echo ($settings['provider'] ?? 'aws_s3') === '[provider]' ? 'block' : 'none'; ?>;">
    <div class="wpcmo-card-header">
        <div class="wpcmo-card-icon">🔑</div>
        <h2 class="wpcmo-card-title">[Provider Name] Credentials</h2>
    </div>
    <div class="wpcmo-card-body">
        <!-- Add form fields for provider-specific settings -->
    </div>
</div>
```

## Step 4: Update Handler Factory

Add the new provider to the `get_storage_handler` method in both `MediaHandler.php` and `Plugin.php`:

```php
private function get_storage_handler($provider) {
    switch ($provider) {
        case 'aws_s3':
            return new \WPCMO\AWS\S3Handler();
        case 'wasabi':
            return new \WPCMO\Wasabi\WasabiHandler();
        case '[provider]':
            return new \WPCMO\[ProviderName]\[ProviderName]Handler();
        default:
            return new \WPCMO\AWS\S3Handler();
    }
}
```

## Step 5: Create Documentation

Create a setup guide in `docs/[PROVIDER]-SETUP-GUIDE.md` with:

- Account creation instructions
- API key generation
- Bucket/container setup
- Permission configuration
- Plugin configuration steps
- Troubleshooting tips

## Example: DigitalOcean Spaces

Here's how you would add DigitalOcean Spaces support:

### 1. Handler Class (`includes/DigitalOcean/SpacesHandler.php`)

```php
<?php
namespace WPCMO\DigitalOcean;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class SpacesHandler {
    private $s3_client;
    private $settings;
    
    private $spaces_endpoints = [
        'nyc3' => 'nyc3.digitaloceanspaces.com',
        'ams3' => 'ams3.digitaloceanspaces.com',
        'sgp1' => 'sgp1.digitaloceanspaces.com',
        'fra1' => 'fra1.digitaloceanspaces.com',
        'sfo3' => 'sfo3.digitaloceanspaces.com',
    ];
    
    public function __construct() {
        $this->settings = get_option('wpcmo_settings', []);
        $this->init_spaces_client();
    }
    
    private function init_spaces_client() {
        if (empty($this->settings['do_access_key']) || empty($this->settings['do_secret_key'])) {
            return false;
        }
        
        $region = $this->settings['do_region'] ?? 'nyc3';
        $endpoint = $this->spaces_endpoints[$region] ?? 'nyc3.digitaloceanspaces.com';
        
        try {
            $this->s3_client = new S3Client([
                'version' => 'latest',
                'region' => $region,
                'endpoint' => 'https://' . $endpoint,
                'use_path_style_endpoint' => false,
                'credentials' => [
                    'key' => $this->settings['do_access_key'],
                    'secret' => $this->settings['do_secret_key'],
                ],
            ]);
            return true;
        } catch (AwsException $e) {
            error_log('WPCMO DigitalOcean Spaces Error: ' . $e->getMessage());
            return false;
        }
    }
    
    // Implement other required methods...
}
```

### 2. Settings Update

```php
// Add to sanitize_settings method
$sanitized['do_access_key'] = sanitize_text_field($input['do_access_key'] ?? '');
$sanitized['do_secret_key'] = sanitize_text_field($input['do_secret_key'] ?? '');
$sanitized['do_region'] = sanitize_text_field($input['do_region'] ?? 'nyc3');
$sanitized['do_space'] = sanitize_text_field($input['do_space'] ?? '');
```

### 3. Template Update

```php
<div class="wpcmo-provider-card" data-provider="digitalocean">
    <div class="wpcmo-provider-icon">🌊</div>
    <div class="wpcmo-provider-name">DigitalOcean Spaces</div>
</div>
```

## Best Practices

### Error Handling
- Always wrap API calls in try-catch blocks
- Log errors with descriptive messages
- Return consistent response formats

### Security
- Sanitize all input data
- Use secure credential storage
- Validate permissions before operations

### Performance
- Implement connection pooling if available
- Cache frequently accessed data
- Use appropriate timeouts

### Testing
- Create basic test methods
- Test connection validation
- Test file upload/download operations
- Test URL generation

### Documentation
- Provide clear setup instructions
- Include troubleshooting section
- Document any provider-specific limitations
- Add cost comparison information

## Common Patterns

### S3-Compatible Providers
Many providers use S3-compatible APIs (like Wasabi). For these:
- Use AWS SDK with custom endpoints
- Follow S3 authentication patterns
- Implement similar error handling

### Native APIs
For providers with native APIs:
- Use provider's official SDK if available
- Implement custom HTTP client if needed
- Handle provider-specific authentication

### URL Generation
- Support custom domains/CDNs
- Handle regional endpoints correctly
- Ensure URLs are publicly accessible

## Testing Your Implementation

1. **Unit Tests**: Test individual methods
2. **Integration Tests**: Test with real provider
3. **UI Tests**: Verify admin interface works
4. **Performance Tests**: Check upload/download speeds

## Submitting Your Provider

If you've created a new provider integration:

1. Follow the coding standards
2. Include comprehensive documentation
3. Add test cases
4. Submit a pull request
5. Include setup guide and examples

## Support

For questions about adding new providers:
- Check existing provider implementations
- Review the plugin architecture
- Contact the development team
- Join the developer community

This modular approach makes it easy to support any cloud storage provider while maintaining code quality and user experience.
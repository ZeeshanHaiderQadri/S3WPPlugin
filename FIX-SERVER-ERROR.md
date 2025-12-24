# Fix: "Server error: error" Issue

## Problem
Your WordPress plugin is showing a generic "Server error: error" message when testing the Wasabi connection, even though all credentials are correct.

## Root Cause
The main issue was that the **AWS SDK was not being loaded** by the plugin. The plugin requires the AWS SDK (installed via Composer) to communicate with Wasabi, but the main plugin file wasn't including the Composer autoloader.

## What I Fixed

### 1. Added Composer Autoloader Loading
**File:** `wp-cloud-media-offload/wp-cloud-media-offload.php`

Added code to load the Composer autoloader before initializing the plugin:
```php
// Load Composer autoloader for AWS SDK and other dependencies
if (file_exists(WPCMO_PLUGIN_DIR . 'vendor/autoload.php')) {
    require_once WPCMO_PLUGIN_DIR . 'vendor/autoload.php';
} else {
    // Show admin notice if vendor directory is missing
    add_action('admin_notices', function() {
        echo '<div class="notice notice-error"><p>';
        echo '<strong>WP Cloud Media Offload:</strong> Missing dependencies. Please run <code>composer install</code> in the plugin directory.';
        echo '</p></div>';
    });
    return;
}
```

### 2. Enhanced Error Handling
**File:** `wp-cloud-media-offload/includes/Core/Plugin.php`

- Added AWS SDK availability check before testing connection
- Added better exception handling with detailed error messages
- Added logging for debugging
- Added class existence checks before instantiating handlers

### 3. Improved JavaScript Error Display
**File:** `wp-cloud-media-offload/assets/js/admin.js`

- Enhanced error parsing to show actual error messages
- Added console logging for debugging
- Better handling of different error response formats

### 4. Added SDK Check in WasabiHandler
**File:** `wp-cloud-media-offload/includes/Wasabi/WasabiHandler.php`

- Added check for AWS SDK availability
- Throws proper exceptions instead of returning false
- Better error messages

### 5. Created Diagnostic Tools

**New Files:**
- `diagnostic.php` - Web-based diagnostic page
- `test-connection.php` - JSON API for testing connection
- `TROUBLESHOOTING-GUIDE.md` - Comprehensive troubleshooting guide

## How to Test the Fix

### Option 1: Use the Diagnostic Page
1. Access: `https://your-site.com/wp-content/plugins/wp-cloud-media-offload/diagnostic.php`
2. Check if all tests pass
3. If AWS SDK test fails, run `composer install`

### Option 2: Use the Test Connection Script
1. Access: `https://your-site.com/wp-content/plugins/wp-cloud-media-offload/test-connection.php`
2. Check the JSON response
3. Look for specific error messages

### Option 3: Test in WordPress Admin
1. Go to Cloud Media → Settings
2. Select Wasabi as provider
3. Enter your credentials
4. Click "Test Wasabi Connection"
5. You should now see specific error messages instead of "Server error: error"

## Verification Steps

### Step 1: Verify Vendor Directory
```bash
ls -la wp-content/plugins/wp-cloud-media-offload/vendor/
```
Should show:
- `autoload.php`
- `aws/` directory
- `guzzlehttp/` directory

### Step 2: Check if AWS SDK is Loaded
Add this to a test file:
```php
<?php
require_once('wp-load.php');
var_dump(class_exists('Aws\S3\S3Client'));
// Should output: bool(true)
?>
```

### Step 3: Enable Debug Mode
In `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Then check `wp-content/debug.log` for detailed errors.

## Common Issues After Fix

### Issue 1: Still Getting Errors
**Solution:** Clear your browser cache and WordPress cache

### Issue 2: "Composer not found"
**Solution:** Install Composer first:
```bash
# On Mac
brew install composer

# On Ubuntu/Debian
sudo apt-get install composer

# Or download from getcomposer.org
```

### Issue 3: "Permission denied"
**Solution:** Fix file permissions:
```bash
chmod -R 755 wp-content/plugins/wp-cloud-media-offload/
```

### Issue 4: "Class not found"
**Solution:** Re-run composer install:
```bash
cd wp-content/plugins/wp-cloud-media-offload/
composer install --no-dev
```

## What You Should See Now

### Before Fix:
```
❌ Server error: error
```

### After Fix (with proper error messages):
```
✅ Wasabi connection successful! All tests passed.
```

Or if there's an actual issue:
```
❌ Wasabi connection failed: Invalid access key ID
```

Or:
```
❌ AWS SDK not found. Please run composer install in the plugin directory.
```

## Next Steps

1. **Upload the fixed plugin** to your WordPress site
2. **Run the diagnostic page** to verify everything is working
3. **Test the connection** in the WordPress admin
4. **Check the debug log** if you still see errors

## Files Modified

1. `wp-cloud-media-offload/wp-cloud-media-offload.php` - Added autoloader
2. `wp-cloud-media-offload/includes/Core/Plugin.php` - Enhanced error handling
3. `wp-cloud-media-offload/assets/js/admin.js` - Better error display
4. `wp-cloud-media-offload/includes/Wasabi/WasabiHandler.php` - Added SDK check

## Files Created

1. `diagnostic.php` - Diagnostic page
2. `test-connection.php` - Connection test API
3. `TROUBLESHOOTING-GUIDE.md` - Troubleshooting guide
4. `FIX-SERVER-ERROR.md` - This file

## Important Notes

- The vendor directory MUST be uploaded to your server
- If you're using Git, make sure vendor/ is not in .gitignore for production
- The AWS SDK is required for the plugin to work
- All credentials must be correct for the connection to succeed

## Support

If you're still experiencing issues:
1. Check the diagnostic page results
2. Review the debug.log file
3. Verify your Wasabi credentials are correct
4. Ensure your server can reach Wasabi endpoints
5. Check that your hosting allows outbound HTTPS connections

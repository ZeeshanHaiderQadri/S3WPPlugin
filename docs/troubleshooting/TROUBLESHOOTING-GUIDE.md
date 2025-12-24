# Troubleshooting Guide - "Server error: error"

## Quick Fix Steps

### Step 1: Run Diagnostics
Access the diagnostic page to identify the issue:
```
https://your-site.com/wp-content/plugins/wp-cloud-media-offload/diagnostic.php
```

### Step 2: Check if AWS SDK is Loaded
The most common cause of "Server error: error" is that the AWS SDK is not loaded.

**Fix:**
1. Open terminal/SSH to your server
2. Navigate to the plugin directory:
   ```bash
   cd /path/to/wordpress/wp-content/plugins/wp-cloud-media-offload
   ```
3. Run composer install:
   ```bash
   composer install
   ```

### Step 3: Verify Vendor Directory
Make sure the `vendor` directory exists and contains the AWS SDK:
```bash
ls -la wp-content/plugins/wp-cloud-media-offload/vendor/
```

You should see:
- `autoload.php`
- `aws/` directory
- `guzzlehttp/` directory
- Other dependencies

### Step 4: Check File Permissions
Ensure the plugin files are readable by the web server:
```bash
chmod -R 755 wp-content/plugins/wp-cloud-media-offload/
```

### Step 5: Enable WordPress Debug Mode
Add these lines to your `wp-config.php` file:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Then check the error log at:
```
wp-content/debug.log
```

### Step 6: Check Browser Console
1. Open your browser's Developer Tools (F12)
2. Go to the Console tab
3. Try the connection test again
4. Look for detailed error messages

## Common Issues and Solutions

### Issue 1: "AWS SDK not found"
**Cause:** Composer dependencies not installed
**Solution:** Run `composer install` in the plugin directory

### Issue 2: "Class 'Aws\S3\S3Client' not found"
**Cause:** Autoloader not loaded
**Solution:** 
- Check if `vendor/autoload.php` exists
- Verify the main plugin file loads the autoloader
- Re-upload the plugin with vendor directory

### Issue 3: "Invalid credentials"
**Cause:** Wrong access key or secret key
**Solution:**
- Double-check your Wasabi credentials
- Make sure there are no extra spaces
- Verify the keys are for the correct region

### Issue 4: "Bucket not found"
**Cause:** Bucket doesn't exist or wrong region
**Solution:**
- Log into Wasabi console
- Verify the bucket name is correct
- Check the bucket region matches your settings

### Issue 5: "Connection timeout"
**Cause:** Firewall or network issue
**Solution:**
- Check if your server can reach Wasabi endpoints
- Test with: `curl https://s3.wasabisys.com`
- Contact your hosting provider about firewall rules

## Manual Testing

### Test 1: Check if Plugin is Active
```php
// Add to a test PHP file
<?php
require_once('wp-load.php');
echo defined('WPCMO_VERSION') ? 'Plugin Active' : 'Plugin Inactive';
?>
```

### Test 2: Check AWS SDK
```php
<?php
require_once('wp-load.php');
echo class_exists('Aws\S3\S3Client') ? 'AWS SDK Loaded' : 'AWS SDK Missing';
?>
```

### Test 3: Test Wasabi Connection Manually
```php
<?php
require_once('wp-load.php');

$handler = new \WPCMO\Wasabi\WasabiHandler();
$result = $handler->test_connection_detailed();
print_r($result);
?>
```

## What Changed

I've made the following fixes to help resolve the "Server error: error" issue:

1. **Added Composer Autoloader** - The main plugin file now loads the AWS SDK
2. **Better Error Messages** - More specific error messages instead of generic "error"
3. **Diagnostic Page** - New diagnostic.php file to check system status
4. **Enhanced Logging** - Better error logging for debugging
5. **SDK Check** - Added checks to verify AWS SDK is available before use

## Next Steps

1. Run the diagnostic page first
2. If AWS SDK is missing, run `composer install`
3. If credentials are wrong, update them in settings
4. Check the debug.log for detailed errors
5. Test the connection again

## Need More Help?

If you're still seeing errors:
1. Check `wp-content/debug.log` for detailed errors
2. Run the diagnostic page and share the results
3. Check browser console for JavaScript errors
4. Verify your hosting environment supports the plugin requirements

# Quick Fix Steps - "Server error: error"

## 🚀 Immediate Actions (5 minutes)

### Step 1: Upload Fixed Files
Upload these modified files to your WordPress site:
```
wp-cloud-media-offload/
├── wp-cloud-media-offload.php (MODIFIED - loads AWS SDK)
├── includes/Core/Plugin.php (MODIFIED - better errors)
├── includes/Wasabi/WasabiHandler.php (MODIFIED - SDK check)
├── assets/js/admin.js (MODIFIED - error display)
├── diagnostic.php (NEW - for testing)
└── test-connection.php (NEW - for testing)
```

### Step 2: Verify Vendor Directory
Make sure the `vendor/` directory is uploaded:
```
wp-cloud-media-offload/vendor/
├── autoload.php
├── aws/
├── guzzlehttp/
└── ... (other dependencies)
```

**Important:** The vendor directory contains the AWS SDK and is REQUIRED!

### Step 3: Run Diagnostics
Access this URL in your browser:
```
https://your-site.com/wp-content/plugins/wp-cloud-media-offload/diagnostic.php
```

Look for:
- ✅ All tests should pass
- ❌ If "AWS SDK" test fails, see Step 4

### Step 4: Install Dependencies (if needed)
If AWS SDK is missing, SSH to your server and run:
```bash
cd /path/to/wordpress/wp-content/plugins/wp-cloud-media-offload/
composer install --no-dev
```

Don't have Composer? Install it:
```bash
# Mac
brew install composer

# Ubuntu/Debian
sudo apt-get install composer

# Or download
curl -sS https://getcomposer.org/installer | php
php composer.phar install --no-dev
```

### Step 5: Test Connection
1. Go to WordPress Admin → Cloud Media → Settings
2. Select Wasabi
3. Enter your credentials
4. Click "Test Wasabi Connection"

## ✅ Expected Results

### Before Fix:
```
❌ Server error: error
```

### After Fix:
```
✅ Wasabi connection successful! All tests passed.
```

Or if there's a real issue:
```
❌ Wasabi connection failed: Invalid access key ID
```

## 🔍 Troubleshooting

### Problem: "AWS SDK not found"
**Solution:**
```bash
cd wp-content/plugins/wp-cloud-media-offload/
composer install --no-dev
```

### Problem: "Permission denied"
**Solution:**
```bash
chmod -R 755 wp-content/plugins/wp-cloud-media-offload/
chown -R www-data:www-data wp-content/plugins/wp-cloud-media-offload/
```

### Problem: Still seeing "Server error: error"
**Solution:**
1. Clear browser cache (Ctrl+Shift+R)
2. Clear WordPress cache
3. Check browser console (F12) for JavaScript errors
4. Check `wp-content/debug.log` for PHP errors

### Problem: "Composer not installed"
**Solution:**
Either:
1. Install Composer on your server
2. OR run `composer install` locally and upload the vendor/ directory

## 📋 Checklist

- [ ] Upload modified plugin files
- [ ] Verify vendor/ directory exists
- [ ] Run diagnostic.php
- [ ] All diagnostic tests pass
- [ ] Test connection in WordPress admin
- [ ] Connection successful or shows specific error

## 🎯 What Changed

The main issue was that the plugin wasn't loading the AWS SDK. I fixed:

1. **Main plugin file** - Now loads Composer autoloader
2. **Error handling** - Shows specific errors instead of generic "error"
3. **Diagnostics** - Added tools to identify issues
4. **Logging** - Better error logging for debugging

## 💡 Pro Tips

1. **Always upload vendor/** - Don't rely on server-side composer install
2. **Enable debug mode** - Helps identify issues quickly
3. **Check browser console** - Shows JavaScript errors
4. **Use diagnostic page** - Quick way to verify setup

## 🆘 Still Need Help?

If you're still seeing errors:

1. **Check diagnostic page** - Shows exactly what's wrong
2. **Check debug.log** - Located at `wp-content/debug.log`
3. **Check browser console** - Press F12, go to Console tab
4. **Test connection API** - Access `test-connection.php` for JSON response

## 📞 Support Information

The plugin now provides much better error messages. Instead of "Server error: error", you'll see:
- "AWS SDK not found" - Run composer install
- "Invalid access key ID" - Check your Wasabi credentials
- "Bucket not found" - Verify bucket name and region
- "Connection timeout" - Check firewall/network settings

Each error message tells you exactly what to fix!

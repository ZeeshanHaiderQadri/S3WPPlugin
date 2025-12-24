# Action Checklist - Fix "Server error: error"

## ✅ Step-by-Step Checklist

### Phase 1: Upload Fixed Files (5 minutes)

- [ ] **Upload modified plugin files:**
  - [ ] `wp-cloud-media-offload/wp-cloud-media-offload.php`
  - [ ] `wp-cloud-media-offload/includes/Core/Plugin.php`
  - [ ] `wp-cloud-media-offload/includes/Wasabi/WasabiHandler.php`
  - [ ] `wp-cloud-media-offload/assets/js/admin.js`

- [ ] **Upload new diagnostic files:**
  - [ ] `wp-cloud-media-offload/diagnostic.php`
  - [ ] `wp-cloud-media-offload/test-connection.php`

- [ ] **Verify vendor directory exists:**
  - [ ] `wp-cloud-media-offload/vendor/autoload.php` exists
  - [ ] `wp-cloud-media-offload/vendor/aws/` directory exists

### Phase 2: Run Diagnostics (2 minutes)

- [ ] **Access diagnostic page:**
  ```
  https://your-site.com/wp-content/plugins/wp-cloud-media-offload/diagnostic.php
  ```

- [ ] **Check results:**
  - [ ] Plugin Active: ✅ PASS
  - [ ] Vendor Directory: ✅ PASS
  - [ ] Composer Autoloader: ✅ PASS
  - [ ] AWS SDK: ✅ PASS
  - [ ] Plugin Classes: ✅ PASS
  - [ ] S3Handler Class: ✅ PASS
  - [ ] WasabiHandler Class: ✅ PASS

### Phase 3: Test Connection (2 minutes)

- [ ] **Go to WordPress Admin:**
  - [ ] Navigate to Cloud Media → Settings
  - [ ] Select Wasabi as provider
  - [ ] Enter your credentials:
    - [ ] Access Key
    - [ ] Secret Key
    - [ ] Region
    - [ ] Bucket Name

- [ ] **Click "Test Wasabi Connection"**

- [ ] **Verify result:**
  - [ ] See specific message (not "Server error: error")
  - [ ] If successful: "✅ Wasabi connection successful!"
  - [ ] If failed: See specific error (e.g., "Invalid access key ID")

### Phase 4: Troubleshooting (if needed)

If AWS SDK test fails:
- [ ] SSH to server
- [ ] Navigate to plugin directory:
  ```bash
  cd /path/to/wordpress/wp-content/plugins/wp-cloud-media-offload/
  ```
- [ ] Run composer install:
  ```bash
  composer install --no-dev
  ```
- [ ] Re-run diagnostic page

If permission errors:
- [ ] Fix file permissions:
  ```bash
  chmod -R 755 wp-content/plugins/wp-cloud-media-offload/
  ```

If still seeing errors:
- [ ] Clear browser cache (Ctrl+Shift+R)
- [ ] Clear WordPress cache
- [ ] Enable debug mode in wp-config.php:
  ```php
  define('WP_DEBUG', true);
  define('WP_DEBUG_LOG', true);
  ```
- [ ] Check `wp-content/debug.log`

### Phase 5: Verification (1 minute)

- [ ] **Confirm fix is working:**
  - [ ] No more generic "Server error: error"
  - [ ] Seeing specific error messages
  - [ ] Connection test works with correct credentials
  - [ ] Can see detailed error messages in browser console

## 🎯 Success Indicators

You'll know the fix is working when:

✅ **Instead of:**
```
❌ Server error: error
```

✅ **You see:**
```
✅ Wasabi connection successful! All tests passed.
```

✅ **Or specific errors like:**
```
❌ AWS SDK not found. Please run composer install
❌ Invalid access key ID
❌ Bucket not found
❌ Connection timeout
```

## 📊 Quick Status Check

| Check | Status | Action if Failed |
|-------|--------|------------------|
| Files uploaded | ⬜ | Upload modified files |
| Vendor directory exists | ⬜ | Run composer install |
| Diagnostic page passes | ⬜ | Check specific failed test |
| Connection test works | ⬜ | Check credentials |
| No generic errors | ⬜ | Clear cache, check logs |

## 🚨 Common Issues

### Issue: "AWS SDK not found"
- [ ] Run `composer install` in plugin directory
- [ ] OR upload vendor/ directory from local machine

### Issue: "Permission denied"
- [ ] Run `chmod -R 755` on plugin directory
- [ ] Check file ownership

### Issue: Still seeing "Server error: error"
- [ ] Clear browser cache
- [ ] Clear WordPress cache
- [ ] Check browser console (F12)
- [ ] Check debug.log

### Issue: "Composer not installed"
- [ ] Install Composer on server
- [ ] OR run locally and upload vendor/

## 📝 Notes

- The vendor/ directory is **required** - don't skip it!
- Clear cache after uploading files
- Use diagnostic page to verify setup
- Check browser console for JavaScript errors
- Check debug.log for PHP errors

## ✨ Final Check

After completing all steps:
- [ ] Diagnostic page shows all green checkmarks
- [ ] Connection test shows specific messages
- [ ] No more generic "Server error: error"
- [ ] Plugin works as expected

## 🎉 Done!

Once all checkboxes are checked, your plugin should be working correctly with proper error messages!

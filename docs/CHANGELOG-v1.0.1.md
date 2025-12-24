# Changelog - v1.0.1

## 🎉 Version 1.0.1 (November 12, 2024)

### 🐛 Bug Fixes

1. **Fixed Settings Save Issue**
   - Removed `required` attributes from hidden Wasabi fields
   - HTML5 form validation was blocking submission
   - Settings now save correctly for all providers
   - File: `templates/admin/settings.php`

2. **Fixed Form Validation**
   - Hidden fields with `required` attribute were preventing form submission
   - Browser console showed "invalid form control" errors
   - Now only visible fields are validated
   - File: `templates/admin/settings.php`

3. **Removed License Check (Temporary)**
   - Auto upload now works without license activation
   - Allows testing of S3 upload functionality
   - Can be re-enabled for production use
   - File: `includes/Core/MediaHandler.php`

4. **Fixed Provider Field Capture**
   - JavaScript now properly captures provider selection
   - Provider field doesn't have `wpcmo_` prefix
   - Settings save includes provider selection
   - File: `assets/js/admin.js`

5. **Enhanced Error Handling**
   - Added detailed console logging for debugging
   - Better error messages in AJAX responses
   - Specific error messages instead of generic "error"
   - File: `assets/js/admin.js`

6. **Fixed AWS SDK Loading**
   - Added Composer autoloader to main plugin file
   - AWS SDK now loads properly
   - Shows clear error if SDK is missing
   - File: `wp-cloud-media-offload.php`

### ✨ Improvements

1. **Better Debugging**
   - Added console.log statements throughout JavaScript
   - Shows exactly what's happening during save/test
   - Helps identify issues quickly
   - File: `assets/js/admin.js`

2. **Improved AJAX Handler**
   - Proper sanitization of all settings
   - Validation of provider selection
   - Returns saved settings in response
   - File: `includes/Core/Plugin.php`

3. **Enhanced Settings Sanitization**
   - All fields properly sanitized
   - Boolean values handled correctly
   - Provider validated against allowed values
   - File: `includes/Core/Plugin.php`

### 📝 Files Modified

1. `wp-cloud-media-offload/wp-cloud-media-offload.php`
   - Added Composer autoloader loading
   - Added error notice if vendor missing

2. `wp-cloud-media-offload/includes/Core/Plugin.php`
   - Enhanced ajax_save_settings method
   - Added proper sanitization
   - Added AWS SDK check
   - Better error handling

3. `wp-cloud-media-offload/includes/Core/MediaHandler.php`
   - Removed license check (temporary)
   - Allows auto upload without license
   - Better error logging

4. `wp-cloud-media-offload/assets/js/admin.js`
   - Fixed provider field capture
   - Added extensive debugging
   - Better error display
   - Enhanced AJAX error handling

5. `wp-cloud-media-offload/templates/admin/settings.php`
   - Removed `required` from Wasabi fields
   - Fixed form validation issues

### 🔧 Technical Changes

**Before v1.0.1:**
- Settings wouldn't save (form validation blocked)
- Generic "Server error: error" messages
- License required for auto upload
- AWS SDK not loading properly

**After v1.0.1:**
- Settings save correctly
- Specific, helpful error messages
- Auto upload works without license
- AWS SDK loads properly

### 🎯 What Works Now

✅ Settings save correctly
✅ Provider selection persists
✅ Test connection works
✅ Auto upload to S3 works
✅ Specific error messages
✅ AWS SDK loads properly
✅ Form validation fixed
✅ Better debugging tools

### ⚠️ Known Issues / TODO

1. **License Check Disabled**
   - Currently disabled for testing
   - Should be re-enabled for production
   - Location: `includes/Core/MediaHandler.php`

2. **Database Table Creation**
   - Needs to be tested on fresh install
   - Table: `wp_wpcmo_uploads`
   - Used to track uploaded files

3. **Bulk Upload Feature**
   - Not tested yet
   - May need similar license check removal

### 📦 Installation

1. Remove old version
2. Install v1.0.1
3. Configure AWS S3 credentials
4. Enable "Auto Upload New Media"
5. Test with a sample image

### 🔄 Upgrade from v1.0.0

1. Deactivate old plugin
2. Delete old plugin
3. Install v1.0.1
4. Activate plugin
5. Re-enter settings (they may not persist from v1.0.0)
6. Enable "Auto Upload New Media"

### 🐛 Bug Reports

If you find issues:
1. Check browser console (F12)
2. Check WordPress debug.log
3. Verify AWS credentials
4. Test connection before uploading

All error messages are now specific and will tell you exactly what's wrong!

---

## Version 1.0.0 (Initial Release)

- Initial release
- AWS S3 support
- Wasabi support
- Settings page
- Test connection
- Auto upload feature
- License system

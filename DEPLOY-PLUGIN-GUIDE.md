# Deploy Plugin Guide - Zip & Install

## ✅ Yes, Zipping and Re-installing Will Work!

The plugin is ready to be zipped and installed on your WordPress site. All fixes are in place:
- ✅ AWS SDK autoloader is loaded
- ✅ Settings save functionality works
- ✅ Test connection works for both S3 and Wasabi
- ✅ Better error messages

## 📦 What to Include in the Zip

### ✅ MUST Include (Required):
```
wp-cloud-media-offload/
├── vendor/                    ⚠️ CRITICAL - Contains AWS SDK
├── assets/
├── includes/
├── templates/
├── wp-cloud-media-offload.php
├── composer.json
├── composer.lock
├── readme.txt
├── LICENSE.txt
└── uninstall.php
```

### ❌ Exclude (Not Needed):
```
- .git/
- .gitignore
- .DS_Store
- node_modules/
- *.md files (documentation)
- diagnostic.php (optional - can include for testing)
- test-connection.php (optional - can include for testing)
- debug-wasabi.php (optional - can include for testing)
```

## 🚀 Quick Deploy Steps

### Option 1: Create Zip with Vendor (Recommended)

```bash
# Navigate to parent directory
cd /path/to/your/project

# Create zip including vendor directory
zip -r wp-cloud-media-offload.zip wp-cloud-media-offload/ \
  -x "*.git*" \
  -x "*.DS_Store" \
  -x "*.md" \
  -x "node_modules/*"
```

### Option 2: Using GUI (Mac/Windows)

**Mac:**
1. Right-click on `wp-cloud-media-offload` folder
2. Select "Compress"
3. Rename to `wp-cloud-media-offload.zip`

**Windows:**
1. Right-click on `wp-cloud-media-offload` folder
2. Select "Send to" → "Compressed (zipped) folder"
3. Rename to `wp-cloud-media-offload.zip`

⚠️ **IMPORTANT:** Make sure the `vendor/` directory is included!

## 📥 Installation Steps

### Step 1: Backup Current Plugin
1. Go to WordPress Admin → Plugins
2. Deactivate "WP Cloud Media Offload"
3. (Optional) Export settings if you want to keep them

### Step 2: Remove Old Plugin
1. Delete the old plugin
2. Or use FTP to remove the old folder

### Step 3: Install New Plugin
1. Go to Plugins → Add New → Upload Plugin
2. Choose your `wp-cloud-media-offload.zip` file
3. Click "Install Now"
4. Click "Activate Plugin"

### Step 4: Configure Settings
1. Go to Cloud Media → Settings
2. Select your provider (AWS S3 or Wasabi)
3. Enter your credentials:
   - **For AWS S3:**
     - Access Key ID
     - Secret Access Key
     - Region
     - Bucket Name
   - **For Wasabi:**
     - Access Key
     - Secret Key
     - Region
     - Bucket Name
4. Click "Save Settings"
5. Click "Test Connection" to verify

## 🧪 Verify Installation

### Check 1: Plugin Activated
- [ ] Plugin appears in Plugins list
- [ ] No error messages on activation
- [ ] Menu item "Cloud Media" appears

### Check 2: Dependencies Loaded
- [ ] No "AWS SDK not found" error
- [ ] Settings page loads correctly
- [ ] No PHP errors in debug.log

### Check 3: Settings Work
- [ ] Can save settings
- [ ] Settings persist after refresh
- [ ] Success message appears

### Check 4: Connection Works
- [ ] Test connection button works
- [ ] Shows specific error messages (not generic "error")
- [ ] Connection succeeds with correct credentials

## 🔍 Troubleshooting

### Issue: "AWS SDK not found"
**Cause:** Vendor directory not included in zip
**Solution:** 
1. Re-create zip including vendor/ directory
2. Or SSH to server and run `composer install`

### Issue: "Plugin could not be activated"
**Cause:** PHP version too old or missing dependencies
**Solution:**
1. Check PHP version (requires 7.4+)
2. Verify vendor/ directory exists
3. Check error logs

### Issue: Settings don't save
**Cause:** Old JavaScript cached
**Solution:**
1. Clear browser cache (Ctrl+Shift+R)
2. Try incognito mode
3. Check browser console for errors

### Issue: Connection test fails
**Cause:** Wrong credentials or network issue
**Solution:**
1. Verify credentials are correct
2. Check bucket exists in correct region
3. Verify server can reach AWS/Wasabi endpoints

## 📋 Pre-Deployment Checklist

Before creating the zip:
- [ ] All fixes applied (autoloader, settings save, error handling)
- [ ] Vendor directory exists and contains AWS SDK
- [ ] No syntax errors in PHP files
- [ ] JavaScript files are updated
- [ ] Test files can be included or excluded (your choice)

## 🎯 What's Fixed in This Version

### 1. AWS SDK Loading
- ✅ Composer autoloader is now loaded
- ✅ AWS SDK available for S3 and Wasabi
- ✅ Shows clear error if SDK missing

### 2. Settings Save
- ✅ All settings save correctly
- ✅ Provider selection persists
- ✅ Proper sanitization and validation
- ✅ Debug logging for troubleshooting

### 3. Error Messages
- ✅ Specific error messages instead of generic "error"
- ✅ Better error handling in JavaScript
- ✅ Detailed error logging

### 4. Connection Testing
- ✅ Works for both AWS S3 and Wasabi
- ✅ Detailed test results for Wasabi
- ✅ Clear success/failure messages

## 💾 Backup Recommendations

Before installing:
1. **Backup your database** (contains settings)
2. **Backup wp-content/uploads/** (your media files)
3. **Export plugin settings** (if possible)

After installing:
1. Test with a small file first
2. Verify uploads work correctly
3. Check that URLs are correct

## 🔐 Security Notes

- Keep your AWS/Wasabi credentials secure
- Don't commit credentials to Git
- Use IAM roles with minimal permissions
- Enable CloudFront for better security (optional)

## 📊 File Size

Expected zip size:
- **With vendor:** ~5-10 MB (includes AWS SDK)
- **Without vendor:** ~500 KB (won't work!)

⚠️ **Always include vendor directory!**

## ✨ Ready to Deploy!

Your plugin is ready to be zipped and deployed. The fixes ensure:
- Settings save correctly
- Connection tests work
- Error messages are helpful
- Both S3 and Wasabi are supported

Just create the zip (including vendor/) and install it on your WordPress site!

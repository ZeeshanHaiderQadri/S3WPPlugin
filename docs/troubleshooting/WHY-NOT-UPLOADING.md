# 🔍 Why Plugin Works on One Site But Not Another

## Your Situation
- ✅ **flexiology.store** - Plugin working, uploads to S3
- ❌ **artstationers.com** - Plugin NOT uploading, but test connection works

## Most Common Causes (In Order)

### 1. ⚠️ Auto Upload is Disabled (90% of cases)
**Problem**: The "Auto Upload" checkbox is not checked in settings.

**Solution**:
```
1. Go to: Cloud Media → Settings
2. Scroll to: "Upload Settings" section
3. Check: ☑ Auto Upload
4. Click: Save Settings
5. Test: Upload a new image
```

**Why this happens**: 
- Settings are stored per-site in the database
- Even with same credentials, each site has its own settings
- The checkbox might not be checked on artstationers.com

---

### 2. 🔄 Settings Not Saved Properly
**Problem**: Settings appear to be saved but aren't actually in the database.

**Solution**:
```
1. Go to: Cloud Media → Settings
2. Re-enter ALL credentials:
   - Provider (click the card)
   - Access Key
   - Secret Key
   - Region
   - Bucket Name
3. Check: ☑ Auto Upload
4. Click: Save Settings
5. Wait for success message
6. Refresh the page
7. Verify settings are still there
```

---

### 3. 🗄️ Database Table Missing
**Problem**: The plugin's database table wasn't created during activation.

**Solution**:
```
1. Deactivate the plugin
2. Reactivate the plugin
3. Check if table exists (use diagnostic tool)
```

**Manual check**:
```sql
SHOW TABLES LIKE 'wp_wpcmo_uploads';
```

---

### 4. 🔌 Plugin Conflict
**Problem**: Another plugin is interfering with the upload process.

**Solution**:
```
1. Disable all other plugins temporarily
2. Test upload
3. If it works, re-enable plugins one by one
4. Find the conflicting plugin
```

**Common conflicts**:
- Other S3/cloud storage plugins
- Image optimization plugins
- Security plugins blocking uploads
- Caching plugins

---

### 5. 📝 WordPress Debug Log Shows Errors
**Problem**: There's an error during upload that's not visible.

**Solution**:
```
1. Enable debug mode in wp-config.php:
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   define('WP_DEBUG_DISPLAY', false);

2. Upload a test image

3. Check: wp-content/debug.log

4. Look for lines starting with: WPCMO:
```

**What to look for**:
- `WPCMO: Auto upload is disabled` → Enable auto upload
- `WPCMO: Failed to get storage handler` → Check provider settings
- `WPCMO: Upload failed:` → Check credentials/permissions
- `WPCMO: File not found:` → File path issue

---

### 6. 🔐 Different S3 Bucket Permissions
**Problem**: The artstationers S3 bucket has different permissions than flexiology bucket.

**Solution**:
```
1. Check S3 bucket policy
2. Verify IAM user permissions
3. Ensure bucket allows PutObject
```

**Required S3 permissions**:
```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
                "s3:PutObject",
                "s3:PutObjectAcl",
                "s3:GetObject",
                "s3:DeleteObject",
                "s3:ListBucket"
            ],
            "Resource": [
                "arn:aws:s3:::your-bucket-name/*",
                "arn:aws:s3:::your-bucket-name"
            ]
        }
    ]
}
```

---

### 7. 🌐 Different PHP/WordPress Environment
**Problem**: Server configuration differences between sites.

**Check**:
- PHP version (should be 7.4+)
- Memory limit (should be 256M+)
- Max upload size
- File permissions on wp-content/uploads/

**Solution**:
```php
// Add to wp-config.php temporarily to check
define('WP_MEMORY_LIMIT', '256M');
ini_set('upload_max_filesize', '64M');
ini_set('post_max_size', '64M');
```

---

## 🔧 Diagnostic Tool

Upload the `check-auto-upload.php` file to your WordPress root:

```
1. Upload: check-auto-upload.php to WordPress root
2. Access: https://artstationers.com/check-auto-upload.php
3. Review: All diagnostic checks
4. Fix: Any issues found
```

This will show you EXACTLY what's different between the two sites.

---

## 📊 Step-by-Step Comparison

### On flexiology.store (working):
```
1. Go to: Cloud Media → Settings
2. Take screenshot of ALL settings
3. Note which checkboxes are checked
4. Note the exact provider, region, bucket
```

### On artstationers.com (not working):
```
1. Go to: Cloud Media → Settings
2. Compare with flexiology.store screenshot
3. Make settings IDENTICAL
4. Pay special attention to:
   - ☑ Auto Upload checkbox
   - Provider selection (card clicked)
   - Bucket name (exact match)
   - Region (exact match)
```

---

## 🧪 Test Upload Process

After fixing settings, test the upload:

```
1. Go to: Media → Add New
2. Upload a small test image (test.jpg)
3. Check if it appears in Media Library
4. Check S3 bucket for the file
5. Check debug.log for WPCMO messages
```

**Expected debug.log output** (when working):
```
WPCMO: MediaHandler constructor called
WPCMO: MediaHandler hooks registered
WPCMO: handle_new_attachment called for ID: 123
WPCMO: Auto upload is enabled
WPCMO: File exists: /path/to/file.jpg
WPCMO: S3 key: wp-content/uploads/2025/11/file.jpg
WPCMO: Provider: aws_s3
WPCMO: Starting S3 upload...
WPCMO: Upload result: Array ( [success] => 1 [s3_url] => ... )
WPCMO: Stored in database
```

---

## 🎯 Quick Fix Checklist

Try these in order:

- [ ] 1. Go to Settings, check "Auto Upload", save
- [ ] 2. Clear browser cache (Ctrl+Shift+R)
- [ ] 3. Clear WordPress cache (if using caching plugin)
- [ ] 4. Re-enter all credentials and save
- [ ] 5. Test connection (should succeed)
- [ ] 6. Upload test image
- [ ] 7. Check S3 bucket for file
- [ ] 8. If still not working, run diagnostic tool
- [ ] 9. Enable WP_DEBUG and check debug.log
- [ ] 10. Compare settings with working site

---

## 💡 Why Test Connection Works But Upload Doesn't

**Test Connection**:
- Only checks if credentials are valid
- Only checks if bucket is accessible
- Doesn't check if auto-upload is enabled
- Doesn't check WordPress hooks

**Auto Upload**:
- Requires "Auto Upload" checkbox to be checked
- Requires WordPress hooks to be registered
- Requires database table to exist
- Requires proper file permissions

**This is why test can succeed but uploads fail!**

---

## 🚨 Most Likely Solution

Based on your description, the most likely issue is:

### ☑ Auto Upload Checkbox is NOT Checked

**Fix**:
1. Go to **Cloud Media → Settings** on artstationers.com
2. Scroll to **"Upload Settings"**
3. Check the **☑ Auto Upload** checkbox
4. Click **"Save Settings"**
5. Upload a test image

This is the #1 reason why a plugin works on one site but not another with the same credentials.

---

## 📞 Need More Help?

1. Run the diagnostic tool: `check-auto-upload.php`
2. Enable WP_DEBUG and check debug.log
3. Compare exact settings between both sites
4. Check for plugin conflicts
5. Verify S3 bucket permissions

The diagnostic tool will tell you EXACTLY what's different!

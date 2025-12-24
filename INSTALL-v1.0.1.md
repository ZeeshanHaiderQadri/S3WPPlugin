# WP Cloud Media Offload v1.0.1 - Installation Guide

## ✅ What's Fixed in v1.0.1

1. **Settings Save** ✅ - Settings now save correctly
2. **Form Validation** ✅ - Fixed hidden required fields blocking submission
3. **License Check Removed** ✅ - Auto upload works without license (for testing)
4. **AWS SDK Loading** ✅ - Composer autoloader properly loaded
5. **Better Error Messages** ✅ - Specific errors instead of generic "error"

## 📦 File Info

- **File:** `wp-cloud-media-offload-v1.0.1.zip`
- **Size:** 6.2 MB
- **Includes:** All files + AWS SDK (vendor directory)

## 🚀 Installation Steps

### Step 1: Remove Old Plugin
1. Go to WordPress Admin → Plugins
2. Deactivate "WP Cloud Media Offload" (if active)
3. Delete the old plugin

### Step 2: Install New Version
1. Go to Plugins → Add New → Upload Plugin
2. Choose `wp-cloud-media-offload-v1.0.1.zip`
3. Click "Install Now"
4. Click "Activate Plugin"

### Step 3: Configure AWS S3
1. Go to **Cloud Media → Settings**
2. Select **"Amazon S3"** as provider
3. Enter your AWS credentials:
   - **Access Key ID:** Your AWS access key
   - **Secret Access Key:** Your AWS secret key
   - **Region:** Select your S3 region (e.g., us-east-2)
   - **Bucket Name:** Your S3 bucket name (e.g., flexiology-uploads)
4. Click **"Save Settings"**
5. You should see: **"✅ Settings saved successfully!"**

### Step 4: Test Connection
1. Click **"Test Connection"** button
2. You should see: **"✅ Connection successful!"**

### Step 5: Enable Auto Upload
1. Scroll down to **"Upload Settings"** section
2. **Check** the box for **"Auto Upload New Media"**
3. (Optional) Check **"Remove Local Files"** to save server space
4. Click **"Save Settings"**

### Step 6: Test Upload
1. Go to **Media → Add New**
2. Upload a test image
3. Check your S3 bucket - the image should appear there!
4. The image URL in WordPress should point to S3

## ✅ Verification Checklist

After installation:
- [ ] Plugin activated successfully
- [ ] No error messages
- [ ] "Cloud Media" menu appears
- [ ] Settings page loads
- [ ] Can save settings
- [ ] Settings persist after refresh
- [ ] Test connection works
- [ ] "Auto Upload New Media" is checked
- [ ] Test image uploads to S3
- [ ] Image URL points to S3

## 🎯 Expected Behavior

### When You Upload an Image:
1. Image uploads to WordPress normally
2. Plugin automatically uploads to S3
3. Image URL in WordPress points to S3
4. (Optional) Local file is deleted if "Remove Local Files" is enabled

### Image URL Format:
```
https://flexiology-uploads.s3.us-east-2.amazonaws.com/wp-content/uploads/2024/11/image.jpg
```

## 🔍 Troubleshooting

### Issue: Settings don't save
**Solution:**
- Hard refresh (Ctrl+Shift+R or Cmd+Shift+R)
- Try incognito mode
- Check browser console for errors

### Issue: Images not uploading to S3
**Solution:**
- Verify "Auto Upload New Media" is checked
- Check AWS credentials are correct
- Verify S3 bucket exists and is accessible
- Check WordPress debug.log for errors

### Issue: "Connection failed"
**Solution:**
- Verify Access Key ID is correct
- Verify Secret Access Key is correct
- Check bucket name matches exactly
- Verify bucket region matches settings
- Check IAM permissions allow s3:PutObject

### Issue: Images upload but URLs still local
**Solution:**
- Check if upload actually succeeded in S3
- Verify the wpcmo_uploads table exists in database
- Check WordPress debug.log for errors

## 📊 What's Different from v1.0.0

| Feature | v1.0.0 | v1.0.1 |
|---------|--------|--------|
| Settings Save | ❌ Broken | ✅ Works |
| Form Validation | ❌ Blocks submit | ✅ Fixed |
| License Required | ✅ Yes | ❌ No (for testing) |
| Auto Upload | ⚠️ Needs license | ✅ Works without |
| Error Messages | ❌ Generic | ✅ Specific |

## 💡 Important Notes

### License Check Removed
For testing purposes, the license check has been temporarily disabled. This means:
- ✅ Auto upload works without a license
- ✅ You can test all features
- ⚠️ In production, you may want to re-enable license checks

### Database Table
The plugin creates a table `wp_wpcmo_uploads` to track uploaded files. Make sure your database user has CREATE TABLE permissions.

### IAM Permissions Required
Your AWS IAM user needs these permissions:
```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
                "s3:PutObject",
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

## 🎉 Ready to Use!

Your plugin is now ready to automatically upload all media files to S3. Just:
1. Install the plugin
2. Configure AWS credentials
3. Enable "Auto Upload New Media"
4. Upload images as normal

The plugin will handle the rest!

## 📞 Need Help?

If you encounter issues:
1. Check browser console (F12) for JavaScript errors
2. Check WordPress debug.log for PHP errors
3. Verify AWS credentials and permissions
4. Test connection before uploading
5. Try uploading a small test image first

All error messages are now specific and helpful, so they'll tell you exactly what's wrong!

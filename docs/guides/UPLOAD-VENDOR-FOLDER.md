# Upload Vendor Folder to Fix Wasabi Connection

## ✅ Good News!

The AWS SDK has been installed successfully in your local `wp-cloud-media-offload/vendor/` folder.

Now you just need to upload this folder to your live WordPress site.

---

## Option 1: Upload via FTP (Recommended)

### Step 1: Connect to Your Site

1. Open **FileZilla** (or your FTP client)
2. Connect to **artstationers.com**:
   - Host: Your FTP host
   - Username: Your FTP username
   - Password: Your FTP password

### Step 2: Navigate to Plugin Directory

On the **remote site** (right side), navigate to:
```
wp-content/plugins/wp-cloud-media-offload/
```

### Step 3: Upload Vendor Folder

On your **local computer** (left side), navigate to:
```
/Users/haider/[your-path]/S3 Plugin/wp-cloud-media-offload/
```

**Drag and drop** the `vendor` folder from left to right.

⏱️ **This will take 5-10 minutes** (it's uploading ~12 packages)

### Step 4: Verify Upload

On the remote site, check that you now have:
```
wp-content/plugins/wp-cloud-media-offload/vendor/
├── autoload.php
├── aws/
│   ├── aws-crt-php/
│   └── aws-sdk-php/  ← This is the important one!
├── guzzlehttp/
└── ... (other folders)
```

### Step 5: Test Connection

1. Go to your WordPress admin
2. Navigate to **Cloud Media → Settings**
3. Make sure **Wasabi** is selected
4. Enter your credentials (if not already there):
   - Access Key: `PL920JBLUQWDQ1XRP4BM`
   - Secret Key: `w4QsvaOAD5PvqKFwZ0yheRNnUBVMf6euH3zsp3DK`
   - Region: `us-east-1`
   - Bucket: `artstationers-wp`
5. Click **"🔌 Test Wasabi Connection"**
6. Should now show: ✅ **"Wasabi connection successful!"**

---

## Option 2: Create Zip and Upload via cPanel

### Step 1: Create Zip File

I'll create a zip file for you. Run this in Terminal:

```bash
cd wp-cloud-media-offload
zip -r vendor.zip vendor/
```

This creates `vendor.zip` (about 10-15 MB).

### Step 2: Upload via cPanel

1. Log into your **cPanel**
2. Open **File Manager**
3. Navigate to: `public_html/wp-content/plugins/wp-cloud-media-offload/`
4. Click **Upload**
5. Upload `vendor.zip`
6. Right-click on `vendor.zip` → **Extract**
7. Delete `vendor.zip` after extraction

### Step 3: Test Connection

Same as Option 1, Step 5 above.

---

## Option 3: Upload Entire Plugin (Easiest)

Since you've made changes to the plugin, you can re-upload the entire plugin:

### Step 1: Create Plugin Zip

```bash
# In Terminal, from the "S3 Plugin" directory
zip -r wp-cloud-media-offload-updated.zip wp-cloud-media-offload/
```

### Step 2: Upload to WordPress

1. In WordPress admin, go to **Plugins → Add New**
2. Click **Upload Plugin**
3. Choose `wp-cloud-media-offload-updated.zip`
4. Click **Install Now**
5. When asked, choose **Replace current with uploaded**
6. Activate the plugin

### Step 3: Test Connection

Your settings should be preserved. Just test the connection again.

---

## What's in the Vendor Folder?

The vendor folder contains:

- **aws/aws-sdk-php** - AWS SDK for PHP (works with Wasabi)
- **guzzlehttp/guzzle** - HTTP client for making API requests
- **psr/** - PHP standards for HTTP messages
- **Other dependencies** - Required by AWS SDK

Total size: ~10-15 MB

---

## After Upload: Expected Result

Once the vendor folder is uploaded, when you test the connection:

### Before (Current):
```
❌ Test request failed
```

### After (Expected):
```
✅ Wasabi connection successful! All tests passed.
```

The detailed test will show:
- ✅ AWS SDK available
- ✅ Credentials provided
- ✅ Bucket name provided
- ✅ HTTP connectivity
- ✅ Client initialized
- ✅ List buckets (credentials valid)
- ✅ Bucket access (can access artstationers-wp)

---

## Troubleshooting

### Upload Fails or Times Out

If FTP upload fails:
1. Try uploading in smaller batches
2. Or use the zip method (Option 2)
3. Or re-upload entire plugin (Option 3)

### Still Getting "Test Failed" After Upload

1. **Clear WordPress cache**:
   - If using a caching plugin, clear it
   - Or deactivate and reactivate the plugin

2. **Check file permissions**:
   ```
   vendor/ should be readable (755)
   vendor/autoload.php should be readable (644)
   ```

3. **Verify vendor folder location**:
   ```
   Should be: wp-content/plugins/wp-cloud-media-offload/vendor/
   NOT: wp-content/plugins/vendor/
   ```

4. **Check PHP version on server**:
   - Needs PHP 7.4 or higher
   - Check in WordPress: Tools → Site Health

---

## Quick Checklist

Before testing:
- [ ] Vendor folder uploaded to correct location
- [ ] vendor/autoload.php exists
- [ ] vendor/aws/aws-sdk-php/ folder exists
- [ ] Wasabi provider selected in settings
- [ ] Credentials entered correctly
- [ ] Region set to us-east-1
- [ ] Bucket name: artstationers-wp

After upload, the connection test should work immediately! 🚀

---

## Need Help?

If you're having trouble uploading:

1. **Tell me your preferred method**: FTP, cPanel, or full plugin upload?
2. **Share any error messages** you see during upload
3. **Let me know** when upload is complete so I can help test

The vendor folder is ready to go - just needs to be on your live site!

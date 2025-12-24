# How to Upload and Run the Debug Script

## What You Need

The debug file is already created at:
```
wp-cloud-media-offload/debug-wasabi.php
```

You need to upload it to your **live WordPress site** (artstationers.com).

---

## Method 1: Using FTP/SFTP (Easiest)

### Step 1: Connect to Your Site

1. **Open your FTP client** (FileZilla, Cyberduck, or similar)
2. **Connect to your site**:
   - Host: `artstationers.com` (or your hosting FTP address)
   - Username: Your FTP username
   - Password: Your FTP password
   - Port: 21 (FTP) or 22 (SFTP)

### Step 2: Navigate to WordPress Root

In your FTP client, navigate to your WordPress root directory. This is usually:
- `/public_html/`
- `/www/`
- `/httpdocs/`
- Or just `/` (root)

**How to identify WordPress root:**
Look for these files:
- `wp-config.php`
- `wp-content/`
- `wp-admin/`
- `wp-includes/`

### Step 3: Upload the File

1. **On your local computer**, navigate to:
   ```
   /path/to/your/project/wp-cloud-media-offload/debug-wasabi.php
   ```

2. **Drag and drop** `debug-wasabi.php` to the WordPress root directory (same level as `wp-config.php`)

3. **Verify** the file is uploaded

### Step 4: Access the Debug Script

Open your browser and visit:
```
https://artstationers.com/debug-wasabi.php
```

You should see a detailed diagnostic page showing what's wrong.

---

## Method 2: Using cPanel File Manager

### Step 1: Log into cPanel

1. Go to your hosting cPanel (usually `https://artstationers.com/cpanel`)
2. Log in with your credentials

### Step 2: Open File Manager

1. Find and click **"File Manager"**
2. Navigate to your WordPress root directory (usually `public_html`)

### Step 3: Upload the File

1. Click **"Upload"** button at the top
2. Click **"Select File"**
3. Navigate to: `wp-cloud-media-offload/debug-wasabi.php` on your computer
4. Select and upload it
5. Make sure it's in the root directory (same level as `wp-config.php`)

### Step 4: Access the Debug Script

Visit: `https://artstationers.com/debug-wasabi.php`

---

## Method 3: Using SSH/Terminal (Advanced)

If you have SSH access:

### Step 1: Upload via SCP

From your local machine:
```bash
# Navigate to your project directory
cd /path/to/your/project

# Upload the file
scp wp-cloud-media-offload/debug-wasabi.php username@artstationers.com:/path/to/wordpress/
```

### Step 2: Or Create Directly on Server

SSH into your server:
```bash
ssh username@artstationers.com
cd /path/to/wordpress
nano debug-wasabi.php
```

Then paste the contents of the file and save.

### Step 3: Access the Debug Script

Visit: `https://artstationers.com/debug-wasabi.php`

---

## Method 4: Using WordPress Plugin (Easiest if you have access)

### Step 1: Install File Manager Plugin

1. In WordPress admin, go to **Plugins → Add New**
2. Search for **"File Manager"**
3. Install and activate **"File Manager" by mndpsingh287**

### Step 2: Upload the File

1. Go to **WP File Manager** in WordPress admin
2. Navigate to WordPress root (go up from wp-content)
3. Click **Upload** button
4. Select `debug-wasabi.php` from your computer
5. Upload it

### Step 3: Access the Debug Script

Visit: `https://artstationers.com/debug-wasabi.php`

---

## What the Debug Script Will Show

Once you access it, you'll see:

1. ✅ **PHP Environment** - PHP version, WordPress version
2. ✅ **AWS SDK Check** - Is Composer installed?
3. ✅ **Plugin Settings** - Your current configuration
4. ✅ **Wasabi Endpoint Test** - Can reach Wasabi servers?
5. ✅ **S3 Client Initialization** - Can create connection?
6. ✅ **List Buckets Test** - Can authenticate?
7. ✅ **Bucket Access Test** - Can access your specific bucket?
8. ✅ **Recommendations** - What to fix

---

## Expected Output

You should see something like:

```
🧪 Wasabi Connection Debug

1. PHP Environment
PHP Version: 8.1.0
WordPress Version: 6.3.1

2. AWS SDK Check
✅ Composer autoload found
✅ AWS SDK S3Client class available

3. Plugin Settings
Provider: wasabi
Wasabi Access Key: ✅ SET (PL920JBL...)
Wasabi Secret Key: ✅ SET (length: 40)
Wasabi Region: us-east-1
Wasabi Bucket: artstationers-wp

4. Wasabi Endpoint Test
Region: us-east-1
Endpoint: https://s3.wasabisys.com
✅ Endpoint reachable (HTTP 403)

5. S3 Client Initialization
✅ S3 Client initialized successfully

6. List Buckets Test
❌ List Buckets Failed
Error Code: InvalidAccessKeyId
Error Message: The AWS Access Key Id you provided does not exist in our records.
💡 Your access key is incorrect or not recognized by Wasabi.
```

---

## After Running the Debug Script

### If All Tests Pass ✅

Great! Your credentials work. The issue might be:
- Browser cache
- WordPress cache
- Plugin cache

**Solution**: Clear all caches and try again.

### If Tests Fail ❌

The debug script will tell you exactly what's wrong:

**"AWS SDK not available"**
→ Run: `composer install` in plugin directory

**"Invalid Access Key ID"**
→ Your access key is wrong. Check Wasabi console.

**"Signature Does Not Match"**
→ Your secret key is wrong. Check Wasabi console.

**"No Such Bucket"**
→ Bucket doesn't exist or wrong region.

**"Access Denied"**
→ Your access key doesn't have permission for this bucket.

---

## Quick Alternative: Check Browser Console

If you can't upload the file, you can check the browser console:

1. In WordPress, go to **Cloud Media → Settings**
2. Open browser Developer Tools (F12)
3. Go to **Console** tab
4. Click **"Test Wasabi Connection"**
5. Look for error messages in the console

The console will show:
```javascript
Testing connection with provider: wasabi
Settings: {wasabi_access_key: "PL920...", ...}
Test response: {success: false, data: {...}}
```

---

## Security Note

**⚠️ IMPORTANT: Delete the debug file after use!**

The debug script shows sensitive information. After you're done debugging:

### Via FTP:
1. Connect to your site
2. Delete `debug-wasabi.php`

### Via cPanel:
1. Go to File Manager
2. Find and delete `debug-wasabi.php`

### Via SSH:
```bash
rm /path/to/wordpress/debug-wasabi.php
```

### Via WordPress File Manager Plugin:
1. Go to WP File Manager
2. Find and delete `debug-wasabi.php`

---

## Still Need Help?

If you can't upload the file, you can:

1. **Share your hosting provider name** - I'll give specific instructions
2. **Check WordPress admin** - Go to Cloud Media → Wasabi Test page
3. **Check browser console** - Look for JavaScript errors
4. **Enable WordPress debug mode** - Check error logs

Let me know which method you used and what the debug script shows!

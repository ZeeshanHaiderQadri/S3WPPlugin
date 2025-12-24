# Background Upload Guide - For Large Sites (50k+ Images)

## 🎯 What is Background Upload?

Background Upload is a **server-based cron job** that automatically uploads your images to S3 in the background. Perfect for sites with 50,000+ images!

### ✅ Benefits

- **No Browser Required** - Close your browser, upload continues
- **Gentle on Server** - Only 10 images per minute (won't overload CPU)
- **Automatic** - Runs via WordPress WP-Cron every minute
- **Resumable** - Can pause and resume anytime
- **Safe** - Won't crash your server or timeout

### ⚡ Quick Upload vs 🤖 Background Upload

| Feature | Quick Upload | Background Upload |
|---------|-------------|-------------------|
| **Best For** | < 10,000 images | 50,000+ images |
| **Speed** | 50-100 images/min | 10 images/min |
| **Browser** | Must stay open | Can close anytime |
| **Server Load** | Higher | Very low |
| **Time (50k images)** | ~10-15 hours | ~3.5 days |

## 📋 Step-by-Step Instructions

### Step 1: Prepare Your Site

Before starting background upload:

1. **Configure Settings**
   - Go to **Cloud Media → Settings**
   - Enter AWS S3 or Wasabi credentials
   - Select your bucket and region
   - Enable "Auto Upload"
   - **IMPORTANT**: Keep "Remove Local Files" DISABLED initially
   - Save settings

2. **Test Connection**
   - Click "Test Connection" button
   - Make sure you see "✅ Connection successful!"
   - If failed, fix credentials before proceeding

3. **Test with One Image**
   - Upload a single test image
   - Verify it appears in S3 bucket
   - Verify it displays on your site
   - If test works, proceed to bulk upload

### Step 2: Start Background Upload

1. **Go to Bulk Upload Page**
   - Navigate to **Cloud Media → Bulk Upload**
   - You'll see two upload options

2. **Choose Background Upload**
   - Click the **"🤖 Background Upload"** button (green button)
   - A confirmation dialog will appear
   - Click "OK" to confirm

3. **Upload Started!**
   - You'll see: "✅ Background upload started!"
   - A green status box appears showing it's active
   - The upload is now running automatically

### Step 3: Monitor Progress

#### Option A: In WordPress Admin (Recommended)

1. **Stay on Bulk Upload Page**
   - Progress updates automatically every 10 seconds
   - You'll see:
     - Total images
     - Already uploaded
     - Remaining
     - Failed count
     - Progress bar

2. **Refresh Anytime**
   - You can refresh the page anytime
   - Progress is saved in database
   - Status will update automatically

#### Option B: Close Browser (Advanced)

1. **You Can Close Browser!**
   - Background upload runs on server
   - No need to keep browser open
   - Come back later to check progress

2. **Check Progress Later**
   - Open **Cloud Media → Bulk Upload**
   - You'll see current status
   - Upload continues even when you're away

### Step 4: Wait for Completion

#### Time Estimates

- **10,000 images**: ~16 hours
- **50,000 images**: ~3.5 days
- **100,000 images**: ~7 days

#### What's Happening?

Every minute, the plugin:
1. Processes 10 images
2. Uploads main image to S3
3. Uploads all thumbnails to S3
4. Updates database records
5. Waits 60 seconds
6. Repeats

This gentle pace prevents server overload!

### Step 5: Verify Completion

When upload completes:

1. **Check Status**
   - Go to **Cloud Media → Bulk Upload**
   - You'll see "✅ Background upload completed!"
   - Progress bar will be at 100%

2. **Verify S3 Bucket**
   - Log into AWS S3 Console
   - Check your bucket
   - You should see all images + thumbnails

3. **Test Your Site**
   - Visit product pages
   - Check if images display correctly
   - Test image zoom/gallery features

4. **Check Statistics**
   - Go to **Cloud Media → Dashboard**
   - See total uploaded files
   - Review any failed uploads

## 🎛️ Advanced Options

### Pause Background Upload

If you need to pause:

1. Go to **Cloud Media → Bulk Upload**
2. Click **"⏹️ Stop Background Upload"** button
3. Upload will pause immediately
4. Progress is saved

### Resume Background Upload

To resume after pausing:

1. Go to **Cloud Media → Bulk Upload**
2. Click **"🤖 Background Upload"** button again
3. Upload resumes from where it stopped
4. Already uploaded files are skipped

### Enable Local File Removal

⚠️ **ONLY DO THIS AFTER VERIFYING EVERYTHING WORKS!**

1. Go to **Cloud Media → Settings**
2. Enable **"Remove Local Files"**
3. Save settings
4. Run background upload again
5. It will remove local files that are already on S3

## 🔧 Technical Details

### How It Works

1. **WP-Cron Based**
   - Uses WordPress built-in cron system
   - Runs every 60 seconds automatically
   - No external cron setup needed

2. **Batch Processing**
   - Processes 10 images per run
   - Each image includes all thumbnails
   - Gentle on server resources

3. **Database Tracking**
   - Stores progress in WordPress database
   - Tracks uploaded files
   - Prevents duplicate uploads

4. **Auto-Resume**
   - If server restarts, upload resumes automatically
   - Progress is never lost
   - Safe and reliable

### Server Requirements

- **WordPress**: 5.8+
- **PHP**: 7.4+
- **WP-Cron**: Must be enabled (default)
- **Memory**: 128MB+ recommended
- **Execution Time**: 60 seconds+ recommended

### WP-Cron Configuration

**Default Setup (No Action Needed)**

WordPress WP-Cron works automatically. The plugin will:
- Schedule cron job when you start background upload
- Run every minute automatically
- Stop when upload completes

**If WP-Cron is Disabled on Your Server**

Some hosts disable WP-Cron for performance. If so:

1. **Check if WP-Cron is Disabled**
   - Look in `wp-config.php`
   - If you see: `define('DISABLE_WP_CRON', true);`
   - Then WP-Cron is disabled

2. **Setup Real Cron Job**
   - Log into cPanel or hosting control panel
   - Go to "Cron Jobs" section
   - Add new cron job:
   
   ```bash
   */1 * * * * wget -q -O - https://yoursite.com/wp-cron.php?doing_wp_cron >/dev/null 2>&1
   ```
   
   Or using curl:
   
   ```bash
   */1 * * * * curl https://yoursite.com/wp-cron.php?doing_wp_cron >/dev/null 2>&1
   ```

3. **Verify Cron is Running**
   - Start background upload
   - Wait 2-3 minutes
   - Refresh bulk upload page
   - Progress should increase

## 🚨 Troubleshooting

### Upload Not Starting

**Problem**: Clicked button but nothing happens

**Solutions**:
1. Check browser console for errors (F12)
2. Verify you're logged in as admin
3. Try refreshing the page
4. Check if WP-Cron is enabled

### Progress Not Updating

**Problem**: Progress bar stuck at same percentage

**Solutions**:
1. Wait 2-3 minutes (cron runs every minute)
2. Refresh the page manually
3. Check if WP-Cron is working:
   - Install "WP Crontrol" plugin
   - Check if `wpcmo_background_upload_cron` is scheduled
4. Check server error logs

### Upload Too Slow

**Problem**: Taking longer than expected

**Solutions**:
1. This is normal! Background upload is intentionally slow
2. It processes 10 images/minute to avoid server overload
3. For 50k images, expect ~3.5 days
4. If you need faster, use Quick Upload instead

### Some Images Failed

**Problem**: Failed count is increasing

**Solutions**:
1. Check which images failed:
   - Look at WordPress error logs
   - Check file permissions
2. Common causes:
   - File doesn't exist locally
   - File permissions issue
   - S3 connection problem
3. Failed images can be re-uploaded manually

### Server CPU High

**Problem**: Server CPU usage increased

**Solutions**:
1. Background upload should use minimal CPU
2. If CPU is high, check:
   - Other plugins running
   - Other cron jobs
   - Server traffic
3. You can pause upload temporarily

## 📊 Monitoring & Logs

### Check Upload Status

**Via WordPress Admin**:
- Go to **Cloud Media → Bulk Upload**
- See real-time progress
- Updates every 10 seconds

**Via Database**:
```sql
SELECT * FROM wp_options WHERE option_name LIKE 'wpcmo_background%';
```

### Check WordPress Logs

Enable WordPress debugging:

1. Edit `wp-config.php`
2. Add these lines:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

3. Check logs at: `/wp-content/debug.log`
4. Look for lines starting with "WPCMO Background Upload"

### Check Cron Schedule

Install "WP Crontrol" plugin:
1. Go to **Tools → Cron Events**
2. Look for `wpcmo_background_upload_cron`
3. Should run every minute
4. Check "Next Run" time

## 💡 Best Practices

### For 50,000 Images

1. **Use Background Upload** (not Quick Upload)
2. **Start during off-peak hours** (night/weekend)
3. **Keep "Remove Local Files" disabled** initially
4. **Monitor first 1,000 images** to ensure it works
5. **Let it run for 3-4 days** uninterrupted
6. **Verify everything works** before enabling file removal

### For 100,000+ Images

1. **Definitely use Background Upload**
2. **Expect 7+ days** for completion
3. **Monitor server resources** periodically
4. **Consider splitting** into multiple batches
5. **Backup database** before starting

### Server Optimization

1. **Increase PHP Memory**:
   - Edit `wp-config.php`
   - Add: `define('WP_MEMORY_LIMIT', '256M');`

2. **Increase Execution Time**:
   - Edit `.htaccess` or `php.ini`
   - Set: `max_execution_time = 300`

3. **Optimize WP-Cron**:
   - Keep WP-Cron enabled
   - Or setup real cron job (see above)

## ✅ Success Checklist

Before starting:
- [ ] AWS/Wasabi credentials configured
- [ ] Test connection successful
- [ ] Test image uploaded and displays correctly
- [ ] "Remove Local Files" is DISABLED
- [ ] Backup created (optional but recommended)

During upload:
- [ ] Background upload started successfully
- [ ] Green status box visible
- [ ] Progress updating every minute
- [ ] No errors in WordPress logs

After completion:
- [ ] All images in S3 bucket
- [ ] Product pages display correctly
- [ ] Thumbnails working
- [ ] No broken images
- [ ] Statistics look correct

## 🎉 You're Done!

Once background upload completes:
1. ✅ All images are on S3
2. ✅ All thumbnails are on S3
3. ✅ Your site loads images from S3
4. ✅ You can enable "Remove Local Files" to save inodes
5. ✅ Enjoy unlimited media storage!

---

**Need Help?**
- Check WordPress error logs
- Review troubleshooting section above
- Contact support with error details

**Plugin**: WP Cloud Media Offload v1.0.5
**Company**: HaiderNama Technologies Limited
**Website**: https://offloadmedia.hntechs.com

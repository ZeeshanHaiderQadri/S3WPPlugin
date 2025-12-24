# WP Cloud Media Offload v1.0.5 - Final Installation Guide

## 🎉 What's New in v1.0.5

### 🤖 Background Upload Feature (NEW!)

Perfect for large sites with 50,000+ images!

- ✅ **Runs Automatically** - Via WordPress WP-Cron
- ✅ **Close Browser** - No need to keep browser open
- ✅ **Gentle on CPU** - Only 10 images per minute
- ✅ **Auto-Resume** - Continues even after server restart
- ✅ **Safe & Reliable** - Won't overload your server

### 📦 Complete Feature List

1. **Automatic Upload** - New images go to S3 automatically
2. **Thumbnail Management** - All thumbnails uploaded & removed
3. **Quick Upload** - Browser-based for < 10k images
4. **Background Upload** - Server-based for 50k+ images (NEW!)
5. **Smart URL Filtering** - No broken images
6. **WooCommerce Compatible** - Works with product images
7. **Inode Savings** - Zero local storage

## 📥 Installation

### Method 1: WordPress Admin (Recommended)

1. Download `wp-cloud-media-offload-v1.0.5-final.zip`
2. Go to **WordPress Admin → Plugins → Add New**
3. Click **"Upload Plugin"**
4. Choose the zip file
5. Click **"Install Now"**
6. Click **"Activate Plugin"**

### Method 2: FTP/cPanel

1. Extract `wp-cloud-media-offload-v1.0.5-final.zip`
2. Upload `wp-cloud-media-offload` folder to `/wp-content/plugins/`
3. Go to **WordPress Admin → Plugins**
4. Find "WP Cloud Media Offload"
5. Click **"Activate"**

## ⚙️ Configuration

### Step 1: Basic Settings

1. Go to **Cloud Media → Settings**
2. Select Provider: **AWS S3** or **Wasabi**
3. Enter credentials:
   - Access Key ID
   - Secret Access Key
   - Bucket Name
   - Region
4. Click **"Test Connection"**
5. Wait for "✅ Connection successful!"

### Step 2: Upload Settings

1. Enable **"Auto Upload"** ✅
2. Keep **"Remove Local Files"** ❌ DISABLED initially
3. File Path Prefix: `wp-content/uploads/` (default)
4. Click **"Save Settings"**

### Step 3: CDN Settings (Optional)

1. Enable **"CloudFront"** if you have it
2. Enter CloudFront domain
3. Click **"Save Settings"**

## 🚀 Usage

### For New Images (Automatic)

Just upload images normally:
1. Go to **Media → Add New**
2. Upload image
3. Plugin automatically:
   - Uploads to S3
   - Uploads all thumbnails
   - Removes local files (if enabled)
   - Updates database

### For Existing Images (Bulk Upload)

#### Option A: Quick Upload (< 10,000 images)

1. Go to **Cloud Media → Bulk Upload**
2. Click **"⚡ Quick Upload"**
3. Keep browser tab open
4. Wait for completion (~2-3 hours for 10k images)

#### Option B: Background Upload (50,000+ images) ⭐ RECOMMENDED

1. Go to **Cloud Media → Bulk Upload**
2. Click **"🤖 Background Upload"**
3. Confirm dialog
4. **Close browser if you want!**
5. Come back later to check progress

**Time Estimates:**
- 10,000 images: ~16 hours
- 50,000 images: ~3.5 days
- 100,000 images: ~7 days

**See detailed guide**: `BACKGROUND-UPLOAD-GUIDE.md`

## 📊 Monitoring

### Check Progress

1. Go to **Cloud Media → Bulk Upload**
2. See real-time statistics:
   - Total images
   - Already uploaded
   - Remaining
   - Failed count
   - Progress bar

### Check Dashboard

1. Go to **Cloud Media → Dashboard**
2. See overview:
   - Total uploaded files
   - Storage used
   - Recent uploads

## 🔧 Advanced Configuration

### Enable Local File Removal

⚠️ **ONLY AFTER VERIFYING EVERYTHING WORKS!**

1. Go to **Cloud Media → Settings**
2. Enable **"Remove Local Files"** ✅
3. Save Settings
4. Run bulk upload again (it will remove already-uploaded files)

### Setup Real Cron (If WP-Cron Disabled)

If your host disabled WP-Cron:

1. Log into cPanel
2. Go to **Cron Jobs**
3. Add new cron job:

```bash
*/1 * * * * curl https://yoursite.com/wp-cron.php?doing_wp_cron >/dev/null 2>&1
```

Replace `yoursite.com` with your actual domain.

## 📚 Documentation Files

All guides included in plugin folder:

1. **BACKGROUND-UPLOAD-GUIDE.md** - Complete background upload guide
2. **QUICK-START-BACKGROUND-UPLOAD.md** - Quick reference
3. **BULK-UPLOAD-GUIDE.md** - General bulk upload guide
4. **FIX-BROKEN-IMAGES-GUIDE.md** - Troubleshooting broken images
5. **INSTALL-v1.0.5-FINAL.md** - This file

## 🎯 Recommended Workflow for 50k Images

### Day 1: Setup & Test

1. Install plugin
2. Configure AWS credentials
3. Test connection
4. Upload 1 test image
5. Verify it works
6. Start background upload
7. Monitor for first hour

### Day 2-4: Let It Run

1. Check progress once per day
2. Verify no errors
3. Let it run automatically
4. No action needed!

### Day 5: Verify & Enable Removal

1. Check all images uploaded
2. Verify site works correctly
3. Test product pages
4. Enable "Remove Local Files"
5. Run background upload again
6. Local files removed

### Day 6: Done!

1. All images on S3
2. Zero local storage
3. Unlimited capacity
4. Faster page loads
5. Lower hosting costs

## 🚨 Troubleshooting

### Background Upload Not Starting

**Check**:
1. WP-Cron enabled (default)
2. Admin permissions
3. Browser console (F12)
4. WordPress error logs

**Fix**:
- Refresh page
- Try again
- Check `BACKGROUND-UPLOAD-GUIDE.md`

### Images Not Displaying

**Check**:
1. S3 bucket permissions
2. CloudFront settings
3. WordPress cache

**Fix**:
- Clear all caches
- Run `fix-missing-images.php` script
- Check `FIX-BROKEN-IMAGES-GUIDE.md`

### Upload Too Slow

**Background Upload**:
- This is normal (10 images/min)
- Designed to be gentle on server
- For faster, use Quick Upload

**Quick Upload**:
- Should be 50-100 images/min
- If slower, check server resources

## 📞 Support

### Get Help

1. Check documentation files
2. Review WordPress error logs
3. Check browser console
4. Contact support with details

### Useful Information to Provide

- Plugin version: 1.0.5
- WordPress version
- PHP version
- Number of images
- Error messages
- Server type (shared/VPS/dedicated)

## ✅ Success Checklist

### Installation
- [ ] Plugin installed and activated
- [ ] Settings link visible in plugins page
- [ ] Menu "Cloud Media" appears

### Configuration
- [ ] AWS/Wasabi credentials entered
- [ ] Test connection successful
- [ ] Auto Upload enabled
- [ ] Settings saved

### Testing
- [ ] Test image uploaded
- [ ] Image appears in S3
- [ ] Image displays on site
- [ ] Thumbnails working

### Bulk Upload
- [ ] Method chosen (Quick or Background)
- [ ] Upload started successfully
- [ ] Progress visible
- [ ] No errors

### Completion
- [ ] All images on S3
- [ ] Site working correctly
- [ ] Products displaying images
- [ ] Local files removed (optional)

## 🎉 You're All Set!

Your WordPress site now has:
- ✅ Unlimited media storage
- ✅ Zero inode usage
- ✅ Faster page loads
- ✅ Lower hosting costs
- ✅ Automatic backups (S3)
- ✅ CDN delivery (optional)

**Enjoy your cloud-powered media library!** 🚀

---

**Plugin**: WP Cloud Media Offload v1.0.5
**Company**: HaiderNama Technologies Limited
**Website**: https://offloadmedia.hntechs.com
**Support**: Check documentation files included

# Bulk Upload Existing Images to S3 - Complete Guide

## Overview

This guide will help you migrate ALL your existing WordPress media (images + thumbnails) to S3 without losing any product images.

## What the Bulk Upload Does

✅ Uploads main images to S3
✅ Uploads ALL thumbnails to S3 (thumbnail, medium, large, etc.)
✅ Creates database records for tracking
✅ Optionally removes local files to save inodes
✅ Products continue working perfectly
✅ No downtime or broken images

## Before You Start

### 1. Backup Your Site
- Backup your WordPress database
- Backup `/wp-content/uploads/` folder
- Better safe than sorry!

### 2. Check Your Settings
Go to **Cloud Media → Settings** and verify:
- ✅ AWS/Wasabi credentials are correct
- ✅ Bucket name is correct
- ✅ Region is correct
- ✅ "Auto Upload" is enabled
- ⚠️ **IMPORTANT**: Keep "Remove Local Files" DISABLED for now

### 3. Test First
- Upload one new test image
- Verify it appears in S3
- Verify it displays on your site
- If test works, proceed with bulk upload

## Step-by-Step Bulk Upload Process

### Step 1: Start Bulk Upload

1. Go to WordPress Admin
2. Navigate to **Cloud Media → Bulk Upload**
3. You'll see:
   - Total images to upload
   - Progress bar
   - Upload statistics

4. Click **"Start Bulk Upload"**

### Step 2: Monitor Progress

The upload will process in batches of 50 images at a time:
- ✅ **Uploaded**: Successfully uploaded to S3
- ⏭️ **Skipped**: Already on S3
- ❌ **Failed**: Error occurred

**Time Estimates:**
- 1,000 images: ~10-20 minutes
- 10,000 images: ~2-3 hours
- 100,000 images: ~20-30 hours

### Step 3: Verify Upload

After completion:

1. **Check S3 Bucket**
   - Log into AWS S3 Console
   - Open your bucket
   - Navigate to `wp-content/uploads/`
   - You should see folders by year/month
   - Each folder should have main images + thumbnails

2. **Check Your Website**
   - Visit product pages
   - Images should display correctly
   - Check media library
   - Everything should work normally

3. **Check Database**
   - Go to **Cloud Media → Dashboard**
   - You should see total uploaded files count
   - Should match your media library count × ~4-5 (for thumbnails)

### Step 4: Enable Local File Removal (Optional)

⚠️ **ONLY DO THIS AFTER VERIFYING EVERYTHING WORKS!**

1. Go to **Cloud Media → Settings**
2. Enable **"Remove Local Files"**
3. Save Settings
4. Run bulk upload again
5. It will remove local files that are already on S3

## Safety Features

### The Bulk Upload is Safe Because:

1. **Non-Destructive**: Doesn't delete files unless you enable "Remove Local Files"
2. **Skips Existing**: Won't re-upload files already on S3
3. **Batch Processing**: Processes in small batches to avoid timeouts
4. **Database Tracking**: Keeps records of all uploads
5. **Resumable**: Can stop and restart anytime

### What Happens to Your Products?

**Nothing changes!** Your products will:
- ✅ Keep all their images
- ✅ Display correctly on product pages
- ✅ Show thumbnails in admin
- ✅ Work in product galleries
- ✅ Function exactly as before

The only difference: Images are served from S3 instead of your server.

## Troubleshooting

### Upload Stops or Times Out

**Solution:**
- Refresh the page
- Click "Start Bulk Upload" again
- It will resume from where it stopped

### Some Images Failed

**Reasons:**
- File doesn't exist locally
- File permissions issue
- S3 connection problem

**Solution:**
- Check error logs
- Verify AWS credentials
- Try uploading those specific images manually

### Images Not Displaying After Upload

**Solution:**
- Clear WordPress cache
- Clear browser cache
- Run the fix-missing-images.php script
- Check S3 bucket permissions

### Want to Undo?

If you haven't enabled "Remove Local Files":
1. Simply deactivate the plugin
2. Images will serve from local server again
3. No data lost!

## Advanced Options

### Bulk Upload via WP-CLI (For Large Sites)

If you have 100,000+ images, use WP-CLI for better performance:

```bash
# SSH into your server
cd /path/to/wordpress

# Run bulk upload via CLI
wp eval 'do_action("wpcmo_bulk_upload");'
```

### Selective Upload

To upload only specific images:
1. Use the Media Library
2. Select images you want to upload
3. Use "Bulk Actions" → "Upload to S3"

## Post-Upload Checklist

After bulk upload completes:

- [ ] Check S3 bucket has all files
- [ ] Verify product pages display correctly
- [ ] Test image zoom/gallery features
- [ ] Check mobile responsiveness
- [ ] Verify thumbnails in admin
- [ ] Test new image uploads
- [ ] Monitor S3 costs
- [ ] Enable "Remove Local Files" (optional)
- [ ] Delete local files to save inodes (optional)

## Cost Considerations

### S3 Storage Costs (Approximate)

- **10,000 images** (~5GB): $0.12/month
- **100,000 images** (~50GB): $1.15/month
- **1,000,000 images** (~500GB): $11.50/month

### Data Transfer Costs

- First 100GB/month: FREE
- After that: $0.09/GB

**Tip**: Use CloudFront CDN to reduce transfer costs!

## Inode Savings

After bulk upload + local file removal:

| Images | Without Plugin | With Plugin | Savings |
|--------|---------------|-------------|---------|
| 10,000 | ~50,000 inodes | 0 inodes | 50,000 |
| 100,000 | ~500,000 inodes | 0 inodes | 500,000 |
| 1,000,000 | ~5,000,000 inodes | 0 inodes | 5,000,000 |

## FAQ

**Q: Will my products lose their images?**
A: No! Products keep all their images. They just load from S3 instead.

**Q: Can I undo the bulk upload?**
A: Yes, if you haven't deleted local files. Just deactivate the plugin.

**Q: How long does it take?**
A: Depends on image count. ~50-100 images per minute.

**Q: Will it affect my site performance?**
A: No! The upload runs in background. Site stays fast.

**Q: What if upload fails halfway?**
A: Just restart it. It will skip already-uploaded files.

**Q: Do I need to keep local files?**
A: No, but keep them until you verify everything works.

## Support

If you encounter issues:
1. Check WordPress error logs
2. Verify S3 credentials
3. Test with single image first
4. Contact support with error details

---

**Ready to start?** Go to **Cloud Media → Bulk Upload** and click "Start Bulk Upload"! 🚀

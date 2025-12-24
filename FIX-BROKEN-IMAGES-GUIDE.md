# Fix Broken Product Images - Quick Guide

## Problem
After importing products, some images show in WordPress media library but don't display on product pages. This happens because:
1. Images were uploaded to S3
2. Local files were deleted
3. Database records are missing or incorrect

## Solution

### Step 1: Upload Updated Plugin
1. Download `wp-cloud-media-offload-v1.0.2.zip`
2. Go to WordPress → Plugins → Add New → Upload Plugin
3. Upload and activate

### Step 2: Run Fix Script
1. Upload `fix-missing-images.php` to your WordPress root directory (same folder as wp-config.php)
2. Access it in your browser: `https://yoursite.com/fix-missing-images.php`
3. Click "Start Scan & Fix"
4. Wait for the process to complete

### Step 3: Clear Cache
1. Clear WordPress cache (if using caching plugin)
2. Clear browser cache
3. Refresh product pages

## What the Fix Does

The updated plugin now:
- ✅ Automatically constructs S3 URLs for missing files
- ✅ Filters all image URLs (main + thumbnails)
- ✅ Works with WooCommerce product images
- ✅ Handles imported products correctly

The fix script:
- ✅ Scans all attachments
- ✅ Identifies missing local files
- ✅ Creates database records for S3 files
- ✅ Fixes broken image links

## Testing

After running the fix:
1. Go to a product page that had broken images
2. Images should now load from S3
3. Check media library - thumbnails should display
4. Check product gallery - all images should work

## Prevention

For future imports:
1. Make sure plugin is activated BEFORE importing
2. Keep "Auto Upload" enabled
3. Keep "Remove Local Files" enabled
4. Images will automatically go to S3

## Troubleshooting

### Images Still Not Showing
- Check S3 bucket permissions (must be public or have CORS)
- Verify AWS credentials are correct
- Check error logs in WordPress

### Some Images Work, Others Don't
- Run the fix script again
- Check if those specific files exist in S3 bucket
- Verify file names match in database

### Thumbnails Missing
- The new version automatically handles thumbnails
- Re-upload a test image to verify
- Run fix script to repair existing thumbnails

## Technical Details

The v1.0.2 update includes:
- Improved URL filtering with fallback logic
- Automatic S3 URL construction for missing files
- Better thumbnail handling
- WooCommerce compatibility improvements

## Need Help?

If images are still broken after following these steps:
1. Check WordPress debug log
2. Verify S3 bucket has the files
3. Test with a fresh image upload
4. Contact support with error details

---

**Remember**: Delete `fix-missing-images.php` after use for security!

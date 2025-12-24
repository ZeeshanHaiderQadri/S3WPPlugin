# Quick Start Guide

Get WP Cloud Media Offload up and running in 10 minutes!

## Prerequisites

✅ WordPress 5.8 or higher  
✅ PHP 7.4 or higher  
✅ AWS account  
✅ Valid license key  

## 5-Step Setup

### Step 1: Install Plugin (2 minutes)

```bash
# Upload to WordPress
cd wp-content/plugins/
unzip wp-cloud-media-offload.zip

# Install dependencies
cd wp-cloud-media-offload
composer install

# Activate in WordPress
# Go to Plugins > Activate "WP Cloud Media Offload"
```

### Step 2: AWS S3 Setup (3 minutes)

**Create S3 Bucket:**
1. AWS Console → S3 → Create bucket
2. Name: `mysite-media`
3. Region: Choose closest to you
4. **Uncheck** "Block all public access"
5. Create bucket

**Bucket Policy:**
```json
{
    "Version": "2012-10-17",
    "Statement": [{
        "Effect": "Allow",
        "Principal": "*",
        "Action": "s3:GetObject",
        "Resource": "arn:aws:s3:::mysite-media/*"
    }]
}
```

### Step 3: IAM User (2 minutes)

**Create User:**
1. AWS Console → IAM → Users → Add user
2. Name: `wp-media-offload`
3. Access type: Programmatic access
4. Attach policy: Create custom policy

**Policy:**
```json
{
    "Version": "2012-10-17",
    "Statement": [{
        "Effect": "Allow",
        "Action": [
            "s3:PutObject",
            "s3:GetObject",
            "s3:DeleteObject",
            "s3:ListBucket"
        ],
        "Resource": [
            "arn:aws:s3:::mysite-media",
            "arn:aws:s3:::mysite-media/*"
        ]
    }]
}
```

**Save Credentials:**
- Access Key ID: `AKIAIOSFODNN7EXAMPLE`
- Secret Access Key: `wJalrXUtnFEMI/K7MDENG/bPxRfiCY`

### Step 4: Plugin Configuration (2 minutes)

**Activate License:**
1. WordPress → Cloud Media → License
2. Enter license key
3. Click "Activate License"

**Configure AWS:**
1. WordPress → Cloud Media → Settings
2. Enter AWS credentials:
   - Access Key ID
   - Secret Access Key
   - Region (e.g., `us-east-1`)
   - Bucket Name (e.g., `mysite-media`)
3. Click "Test Connection" ✅
4. Save Settings

### Step 5: Upload Media (1 minute)

**Option A: Auto Upload (New Files)**
1. Settings → Enable "Auto Upload New Media"
2. Upload images normally in WordPress
3. Done! Files automatically go to S3

**Option B: Bulk Upload (Existing Files)**
1. Cloud Media → Bulk Upload
2. Click "Start Bulk Upload"
3. Wait for completion
4. Done!

## CloudFront Setup (Optional, +5 minutes)

**Create Distribution:**
1. AWS Console → CloudFront → Create Distribution
2. Origin: Select your S3 bucket
3. Viewer Protocol: Redirect HTTP to HTTPS
4. Create distribution
5. Wait 15-20 minutes for deployment

**Configure in Plugin:**
1. Settings → Enable "CloudFront CDN"
2. Enter CloudFront domain: `d111111abcdef8.cloudfront.net`
3. Save Settings

## Verify Everything Works

### Test 1: Upload New Image
1. Media → Add New
2. Upload test image
3. Check if it appears in S3 bucket ✅

### Test 2: View Image
1. Insert image in post
2. View post
3. Right-click image → Copy image address
4. Should be S3 or CloudFront URL ✅

### Test 3: Bulk Upload
1. Cloud Media → Bulk Upload
2. Start upload
3. Monitor progress ✅

## Troubleshooting

### Connection Test Fails
- ❌ Check AWS credentials
- ❌ Verify IAM permissions
- ❌ Confirm bucket name and region

### Images Not Loading
- ❌ Check bucket policy (public read)
- ❌ Verify files uploaded to S3
- ❌ Check browser console for errors

### Bulk Upload Stops
- ❌ Refresh page and restart
- ❌ Check PHP error logs
- ❌ Increase PHP max_execution_time

## Next Steps

✅ Enable auto upload for new media  
✅ Run bulk upload for existing media  
✅ Set up CloudFront for faster delivery  
✅ Enable "Remove Local Files" to save space  
✅ Monitor AWS costs in billing dashboard  

## Support

Need help?
- 📧 Email: support@yourcompany.com
- 📚 Docs: yoursite.com/docs
- 💬 Forum: yoursite.com/forum

## Tips for Large Libraries

### 25,000 Images
- ⏱️ Time: 2-4 hours
- 💾 Storage: ~2.5 GB (avg 100KB/image)
- 💰 Cost: ~$9/month

### 250,000 Images
- ⏱️ Time: 20-40 hours
- 💾 Storage: ~25 GB (avg 100KB/image)
- 💰 Cost: ~$88/month

**Best Practices:**
- Run during off-peak hours
- Keep browser tab open
- Monitor server resources
- Test with 1,000 images first

## Quick Reference

### AWS Regions
- `us-east-1` - US East (N. Virginia)
- `us-west-2` - US West (Oregon)
- `eu-west-1` - EU (Ireland)
- `ap-southeast-1` - Asia Pacific (Singapore)

### Plugin Pages
- Dashboard: `Cloud Media`
- Settings: `Cloud Media → Settings`
- Bulk Upload: `Cloud Media → Bulk Upload`
- License: `Cloud Media → License`

### Important Files
- Settings: `wp_options.wpcmo_settings`
- Uploads: `wp_wpcmo_uploads` table
- Logs: `wp-content/debug.log`

## Success Checklist

- [x] Plugin installed and activated
- [x] License activated
- [x] S3 bucket created
- [x] IAM user created
- [x] AWS credentials configured
- [x] Connection test passed
- [x] Test image uploaded
- [x] Image loads from S3/CloudFront
- [x] Auto upload enabled
- [x] Bulk upload completed

Congratulations! You're all set! 🎉

---

**Time to Complete:** 10-15 minutes  
**Difficulty:** Easy  
**Cost:** Starting at $9/month  

For detailed instructions, see [INSTALLATION.md](INSTALLATION.md)

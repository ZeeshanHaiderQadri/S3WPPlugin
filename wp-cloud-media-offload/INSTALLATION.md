# Installation Guide - WP Cloud Media Offload

## Quick Start

### Step 1: Install the Plugin

1. Download the plugin ZIP file
2. Upload to WordPress via Plugins > Add New > Upload Plugin
3. Or extract to `/wp-content/plugins/wp-cloud-media-offload/`
4. Activate the plugin

### Step 2: Install Dependencies

The plugin requires AWS SDK for PHP. Install it using Composer:

```bash
cd wp-content/plugins/wp-cloud-media-offload
composer install
```

If you don't have Composer installed, download it from [getcomposer.org](https://getcomposer.org/)

### Step 3: AWS Setup

#### Create S3 Bucket

1. Log in to AWS Console
2. Go to S3 service
3. Click "Create bucket"
4. Choose a unique bucket name
5. Select your preferred region
6. Uncheck "Block all public access" (we need public read access)
7. Create bucket

#### Create IAM User

1. Go to IAM service in AWS Console
2. Click "Users" > "Add user"
3. Enter username (e.g., `wp-media-offload`)
4. Select "Programmatic access"
5. Click "Next: Permissions"

#### Set Permissions

Attach this policy to the user:

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

Replace `your-bucket-name` with your actual bucket name.

#### Get Access Keys

1. Complete user creation
2. Save the Access Key ID and Secret Access Key
3. You'll need these for plugin configuration

### Step 4: Configure Plugin

1. Go to WordPress Admin > Cloud Media > License
2. Enter your license key and activate
3. Go to Cloud Media > Settings
4. Enter your AWS credentials:
   - Access Key ID
   - Secret Access Key
   - Region
   - Bucket Name
5. Click "Test Connection" to verify
6. Save settings

### Step 5: CloudFront Setup (Optional but Recommended)

#### Create CloudFront Distribution

1. Go to CloudFront in AWS Console
2. Click "Create Distribution"
3. Select "Web" distribution
4. Configure:
   - **Origin Domain Name**: Select your S3 bucket
   - **Viewer Protocol Policy**: Redirect HTTP to HTTPS
   - **Compress Objects Automatically**: Yes
   - **Price Class**: Choose based on your needs
5. Create distribution
6. Wait for deployment (15-20 minutes)
7. Copy the CloudFront domain (e.g., `d111111abcdef8.cloudfront.net`)

#### Configure in Plugin

1. Go to Cloud Media > Settings
2. Enable "CloudFront CDN"
3. Enter your CloudFront domain
4. Save settings

### Step 6: Upload Media

#### For New Media

1. Enable "Auto Upload New Media" in settings
2. Upload media files normally through WordPress
3. Files will automatically upload to S3

#### For Existing Media (Bulk Upload)

1. Go to Cloud Media > Bulk Upload
2. Review the statistics
3. Click "Start Bulk Upload"
4. Keep the browser tab open
5. Monitor progress

**Important for Large Libraries:**
- For 25,000 images: Allow 2-4 hours
- For 250,000+ images: Allow 20-40 hours
- Run during off-peak hours
- Don't close the browser tab
- Progress is saved automatically

## Troubleshooting

### Connection Test Fails

**Check:**
- AWS credentials are correct
- IAM user has proper permissions
- Bucket name is correct
- Region matches bucket region
- Server can connect to AWS (firewall/proxy)

### Bulk Upload Stops

**Solutions:**
- Refresh the page and restart
- Already uploaded files are skipped
- Check PHP error logs
- Increase PHP max_execution_time
- Check server resources

### Images Not Loading

**Check:**
- CloudFront domain is correct
- S3 bucket has public read access
- Files were uploaded successfully
- Check browser console for errors

### License Activation Fails

**Check:**
- License key is correct
- License hasn't expired
- Not exceeded activation limit
- Server can connect to license server

## Server Requirements

### Minimum Requirements

- PHP 7.4 or higher
- WordPress 5.8 or higher
- MySQL 5.6 or higher
- 128 MB PHP memory limit
- cURL enabled
- OpenSSL enabled

### Recommended for Large Libraries

- PHP 8.0 or higher
- 256 MB+ PHP memory limit
- 300+ seconds max_execution_time
- Fast internet connection
- Adequate server resources

## Performance Tips

### For Amazon Affiliate Sites

1. **Use CloudFront**: Essential for fast image delivery
2. **Enable Auto Upload**: New products upload automatically
3. **Remove Local Files**: Save server space after upload
4. **Choose Nearest Region**: Select AWS region closest to your audience
5. **Batch Processing**: Bulk upload runs in optimized batches

### For 250,000+ Images

1. **Schedule Uploads**: Run during low-traffic hours
2. **Monitor Resources**: Watch CPU and memory usage
3. **Staged Migration**: Upload in phases if needed
4. **Test First**: Try with 1,000 images first
5. **Backup First**: Always backup before major changes

## Security Best Practices

1. **IAM Permissions**: Only grant necessary S3 permissions
2. **Access Keys**: Never share or commit to version control
3. **HTTPS**: Always use HTTPS for CloudFront
4. **Regular Updates**: Keep plugin updated
5. **License Protection**: Keep license key secure

## Support

Need help? Contact us:
- Email: support@yourcompany.com
- Documentation: yoursite.com/docs
- Support Portal: yoursite.com/support

## Next Steps

After installation:
1. ✅ Test with a few images first
2. ✅ Verify images load correctly
3. ✅ Check CloudFront delivery
4. ✅ Run bulk upload for existing media
5. ✅ Enable auto upload for new media
6. ✅ Monitor storage and bandwidth usage

Congratulations! Your WordPress site is now powered by AWS S3 and CloudFront! 🎉

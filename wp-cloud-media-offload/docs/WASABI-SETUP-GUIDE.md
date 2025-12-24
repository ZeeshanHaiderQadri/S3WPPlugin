# Wasabi Cloud Storage Setup Guide

This guide will help you configure Wasabi as your cloud storage provider for the WordPress Cloud Media Offload plugin.

## What is Wasabi?

Wasabi is a cloud storage service that provides S3-compatible storage at a fraction of the cost of traditional cloud providers. It offers:

- **80% less cost** than AWS S3
- **No egress fees** for data retrieval
- **S3-compatible API** for easy integration
- **11 9's of durability** (99.999999999%)
- **Global data centers** for optimal performance

## Prerequisites

1. A Wasabi account (sign up at [wasabi.com](https://wasabi.com))
2. WordPress Cloud Media Offload plugin installed and activated
3. Valid plugin license

## Step 1: Create a Wasabi Account

1. Visit [wasabi.com](https://wasabi.com) and sign up for an account
2. Choose your preferred pricing plan
3. Complete the account verification process

## Step 2: Create Access Keys

1. Log into your Wasabi console
2. Navigate to **Access Keys** in the left sidebar
3. Click **Create New Access Key**
4. Enter a descriptive name for your key (e.g., "WordPress Media Offload")
5. Save both the **Access Key** and **Secret Key** securely

## Step 3: Create a Storage Bucket

1. In the Wasabi console, go to **Buckets**
2. Click **Create Bucket**
3. Choose your bucket settings:
   - **Bucket Name**: Choose a unique name (e.g., "my-wordpress-media")
   - **Region**: Select the region closest to your users
   - **Versioning**: Enable if you want file versioning
   - **Logging**: Optional, for access logs
4. Click **Create Bucket**

## Step 4: Configure Bucket Permissions

1. Select your newly created bucket
2. Go to the **Policies** tab
3. Add a bucket policy to allow public read access for media files:

```json
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Sid": "PublicReadGetObject",
      "Effect": "Allow",
      "Principal": "*",
      "Action": "s3:GetObject",
      "Resource": "arn:aws:s3:::YOUR-BUCKET-NAME/*"
    }
  ]
}
```

Replace `YOUR-BUCKET-NAME` with your actual bucket name.

## Step 5: Configure WordPress Plugin

1. In your WordPress admin, go to **Cloud Media > Settings**
2. Select **Wasabi** as your storage provider
3. Enter your Wasabi credentials:
   - **Access Key**: Your Wasabi access key
   - **Secret Key**: Your Wasabi secret key
   - **Region**: The region where you created your bucket
   - **Bucket Name**: Your bucket name
4. Click **Test Connection** to verify the setup
5. Configure additional settings:
   - **Auto Upload New Media**: Enable to automatically upload new files
   - **Remove Local Files**: Enable to save server space (optional)
   - **File Path Prefix**: Customize the folder structure in your bucket

## Step 6: Test the Integration

1. Upload a test image to your WordPress media library
2. Check that the file appears in your Wasabi bucket
3. Verify that the image displays correctly on your website
4. Check the image URL to confirm it's being served from Wasabi

## Wasabi Regions and Endpoints

The plugin supports all Wasabi regions:

| Region | Location | Endpoint |
|--------|----------|----------|
| us-east-1 | N. Virginia | s3.wasabisys.com |
| us-east-2 | N. Virginia | s3.us-east-2.wasabisys.com |
| us-west-1 | Oregon | s3.us-west-1.wasabisys.com |
| eu-central-1 | Amsterdam | s3.eu-central-1.wasabisys.com |
| eu-west-1 | London | s3.eu-west-1.wasabisys.com |
| eu-west-2 | Paris | s3.eu-west-2.wasabisys.com |
| ap-northeast-1 | Tokyo | s3.ap-northeast-1.wasabisys.com |
| ap-northeast-2 | Osaka | s3.ap-northeast-2.wasabisys.com |
| ap-southeast-1 | Singapore | s3.ap-southeast-1.wasabisys.com |
| ap-southeast-2 | Sydney | s3.ap-southeast-2.wasabisys.com |

## Cost Optimization Tips

1. **Choose the Right Region**: Select a region close to your users for better performance
2. **Monitor Usage**: Use Wasabi's console to track storage usage and costs
3. **Lifecycle Policies**: Set up policies to automatically delete old files if needed
4. **Compression**: Enable image compression in WordPress to reduce storage costs

## Troubleshooting

### Connection Test Fails

**Check the basics first:**
1. Verify your access key and secret key are correct (no extra spaces)
2. Ensure your bucket exists in the selected region
3. Check that AWS SDK is installed (`composer install` in plugin directory)
4. Try the detailed test in **Cloud Media > Wasabi Test**

**Common Error Messages:**

**"AWS SDK not available"**
- Run `composer install` in the plugin directory
- Ensure your hosting provider allows Composer packages

**"Invalid Access Key ID"**
- Double-check your access key in Wasabi console
- Regenerate access keys if necessary
- Ensure no extra spaces or characters

**"Signature Does Not Match"**
- Verify your secret key is correct
- Check for copy/paste errors
- Regenerate keys in Wasabi console

**"No Such Bucket"**
- Verify bucket name spelling
- Ensure bucket exists in the selected region
- Check bucket name doesn't contain invalid characters

**"Access Denied"**
- Check bucket permissions in Wasabi console
- Ensure your access key has read/write permissions
- Verify bucket policy allows your operations

### Files Not Uploading

1. Check WordPress error logs (`/wp-content/debug.log`)
2. Verify your bucket has write permissions
3. Ensure your server can make outbound HTTPS connections
4. Check if your hosting provider blocks external API calls
5. Test with a small image file first

### Images Not Displaying

1. Verify your bucket policy allows public read access
2. Check that the file path prefix is configured correctly
3. Ensure your bucket name and region are correct
4. Test the direct Wasabi URL in your browser
5. Check browser developer tools for 404 or access errors

### Performance Issues

1. Choose a Wasabi region closer to your users
2. Enable WordPress caching plugins
3. Consider using a CDN in front of Wasabi for global distribution
4. Monitor upload/download speeds in different regions

### Debug Mode

Enable WordPress debug mode to get detailed error messages:

```php
// Add to wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Then check `/wp-content/debug.log` for detailed error messages.

## Security Best Practices

1. **Rotate Access Keys**: Regularly rotate your Wasabi access keys
2. **Limit Permissions**: Only grant the minimum required permissions
3. **Monitor Access**: Enable bucket logging to monitor access patterns
4. **Use HTTPS**: Always use HTTPS URLs for serving media files
5. **Backup Strategy**: Maintain backups of critical media files

## Support

If you encounter issues with the Wasabi integration:

1. Check the WordPress error logs
2. Review the Wasabi console for any error messages
3. Contact plugin support with detailed error information
4. Consult Wasabi's documentation for API-specific issues

## Migration from Other Providers

If you're migrating from AWS S3 or another provider:

1. **Backup First**: Always backup your existing media files
2. **Test Thoroughly**: Test the Wasabi integration with a few files first
3. **Bulk Migration**: Use the plugin's bulk upload feature to migrate existing files
4. **Update URLs**: The plugin will automatically handle URL updates for new uploads
5. **Verify Everything**: Check that all media files are accessible after migration

For more information about Wasabi's features and pricing, visit [wasabi.com](https://wasabi.com).
# Quick Wasabi Connection Test Guide

## Before You Start

Make sure you have:
- ✅ Wasabi account created
- ✅ Access Key and Secret Key from Wasabi console
- ✅ Bucket created in Wasabi
- ✅ Composer dependencies installed (`composer install` in plugin directory)

## Step-by-Step Testing

### 1. Navigate to Settings
Go to: **WordPress Admin → Cloud Media → Settings**

### 2. Select Wasabi Provider
Click on the **Wasabi** card (🗄️ icon)

### 3. Enter Credentials

Fill in these required fields:
- **Access Key**: Your Wasabi access key (starts with AKIA...)
- **Secret Key**: Your Wasabi secret key (40 characters)
- **Region**: Select the region where your bucket is located
- **Bucket Name**: Your bucket name (must already exist)

### 4. Test Connection

Click the **"🔌 Test Wasabi Connection"** button

### 5. Check Results

You'll see one of these messages:

#### ✅ Success
```
✅ Wasabi connection successful! All tests passed.
```
**What this means**: Your credentials are correct, bucket is accessible, and everything is configured properly.

**Next steps**: 
- Click "💾 Save Settings" to save your configuration
- Go to "Bulk Upload" to start uploading existing media
- Upload a test image to verify automatic uploads work

#### ❌ Failure
You'll see a specific error message. See "Common Errors" below.

## Common Errors & Solutions

### "AWS SDK not available"
**Problem**: Composer dependencies not installed
**Solution**: 
```bash
cd wp-content/plugins/wp-cloud-media-offload
composer install
```

### "Missing access key or secret key"
**Problem**: Required fields are empty
**Solution**: Fill in both Access Key and Secret Key fields

### "Missing bucket name"
**Problem**: Bucket name field is empty
**Solution**: Enter your Wasabi bucket name

### "Invalid Access Key ID"
**Problem**: Access key is incorrect or has typos
**Solution**: 
1. Go to Wasabi Console → Access Keys
2. Copy the access key again (watch for extra spaces)
3. Or create a new access key

### "Signature Does Not Match"
**Problem**: Secret key is incorrect
**Solution**: 
1. Go to Wasabi Console → Access Keys
2. If you can't see the secret key, create a new access key pair
3. Copy both keys carefully

### "No Such Bucket"
**Problem**: Bucket doesn't exist or wrong region
**Solution**: 
1. Go to Wasabi Console → Buckets
2. Verify the bucket name spelling
3. Check which region the bucket is in
4. Select the correct region in the plugin settings

### "Access Denied"
**Problem**: Access key doesn't have permissions for the bucket
**Solution**: 
1. Go to Wasabi Console → Access Keys
2. Check the permissions for your access key
3. Ensure it has read/write access to the bucket
4. Or create a new access key with proper permissions

### "Cannot reach Wasabi endpoint"
**Problem**: Server can't connect to Wasabi
**Solution**: 
1. Check your server's firewall settings
2. Ensure outbound HTTPS connections are allowed
3. Contact your hosting provider if needed

## Debugging Tips

### Enable WordPress Debug Mode
Add to `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Then check `/wp-content/debug.log` for detailed errors.

### Check Browser Console
1. Press F12 to open Developer Tools
2. Go to Console tab
3. Look for messages starting with "Testing connection with provider:"
4. Check for any red error messages

### Use Detailed Test Page
Go to: **Cloud Media → Wasabi Test**

This page runs a comprehensive 6-step test and shows exactly which step fails.

## What Gets Tested

When you click "Test Connection", the plugin runs these checks:

1. **AWS SDK Check**: Verifies the AWS SDK is installed
2. **Credentials Check**: Validates access key and secret key are provided
3. **Bucket Name Check**: Ensures bucket name is provided
4. **HTTP Connectivity**: Tests connection to Wasabi endpoint
5. **Client Initialization**: Creates S3-compatible client
6. **List Buckets**: Verifies credentials work
7. **Bucket Access**: Confirms you can access the specific bucket

All tests must pass for a successful connection.

## After Successful Test

Once the test passes:

1. **Save Settings**: Click "💾 Save Settings" button
2. **Test Upload**: Upload a test image in WordPress Media Library
3. **Verify**: Check that the image appears in your Wasabi bucket
4. **Check URL**: View the image and verify it's served from Wasabi
5. **Bulk Upload**: Use "Bulk Upload" to migrate existing media

## Wasabi Console Links

- **Main Console**: https://console.wasabisys.com/
- **Access Keys**: https://console.wasabisys.com/#/access_keys
- **Buckets**: https://console.wasabisys.com/#/buckets

## Region Endpoints

Make sure you select the correct region:

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

## Still Having Issues?

1. Double-check all credentials in Wasabi console
2. Try creating a new access key pair
3. Verify the bucket exists in the correct region
4. Check WordPress debug log for detailed errors
5. Test with a different bucket
6. Contact your hosting provider about firewall/connectivity
7. Review the WASABI-CONNECTION-FIX.md document for technical details

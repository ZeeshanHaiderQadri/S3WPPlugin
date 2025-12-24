# WP Cloud Media Offload

Professional WordPress plugin for cloud media offloading with support for AWS S3, Wasabi, and more. Handle 250,000+ images with ease.

## Features

- 🚀 **Unlimited Media Uploads** - Handle 25,000 to 350,000+ images with ease
- ☁️ **Multiple Cloud Providers** - AWS S3, Wasabi, and more
- 🗄️ **Wasabi Integration** - 80% cheaper than AWS S3 with no egress fees
- 🌐 **CloudFront CDN** - Serve media through CloudFront for faster delivery
- 📤 **Bulk Upload** - Migrate existing media library in batches
- 🎨 **Modern UI** - Beautiful purple-orange gradient interface
- 🌓 **Light/Dark Mode** - Toggle between themes
- 🔑 **License Management** - Secure activation system
- ⚡ **Auto Upload** - New media files automatically uploaded
- 💾 **Space Saving** - Option to remove local files after upload

## Installation

1. Upload the plugin to `/wp-content/plugins/wp-cloud-media-offload/`
2. Run `composer install` to install dependencies
3. Activate the plugin in WordPress
4. Activate your license in Cloud Media > License
5. Configure AWS credentials in Cloud Media > Settings
6. Start uploading!

## Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher
- Cloud storage account (AWS S3, Wasabi, etc.)
- Valid license key

## Supported Cloud Providers

### AWS S3
- Full S3 API support
- CloudFront CDN integration
- All AWS regions supported

### Wasabi
- S3-compatible API
- 80% cheaper than AWS S3
- No egress fees
- 11 global regions

## Configuration

### AWS S3 Setup

1. Create an S3 bucket in AWS Console
2. Create IAM user with S3 access
3. Generate access keys
4. Enter credentials in plugin settings

### Wasabi Setup

1. Create a Wasabi account at [wasabi.com](https://wasabi.com)
2. Create access keys in Wasabi console
3. Create a storage bucket
4. Configure bucket permissions for public read access
5. Enter credentials in plugin settings

See our [Wasabi Setup Guide](docs/WASABI-SETUP-GUIDE.md) for detailed instructions.

### CloudFront Setup (Optional - AWS S3 only)

1. Create CloudFront distribution
2. Point it to your S3 bucket
3. Enter CloudFront domain in plugin settings
4. Enable CloudFront in settings

## Usage

### Automatic Upload

Enable "Auto Upload New Media" in settings to automatically upload new media files to S3.

### Bulk Upload

1. Go to Cloud Media > Bulk Upload
2. Click "Start Bulk Upload"
3. Monitor progress
4. Keep browser tab open during upload

### For Large Libraries (250,000+ images)

- Run bulk upload during off-peak hours
- Monitor server resources
- Process runs in batches to prevent timeouts
- Progress is saved automatically

## Pricing

- **Bronze**: Up to 2,000 files - $39/year
- **Silver**: Up to 6,000 files - $59/year
- **Gold**: Up to 20,000 files - $149/year
- **Platinum**: Up to 40,000 files - $199/year
- **Unlimited**: Unlimited files - $1,199/year

## Support

For support, please visit [yoursite.com/support](https://yoursite.com/support)

## License

GPL v2 or later

## Credits

Developed by Your Company

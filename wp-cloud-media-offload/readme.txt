=== WP Cloud Media Offload ===
Contributors: haidernama
Tags: s3, cloudfront, cdn, media, aws, cloud storage, offload, thumbnails, inodes
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Professional AWS S3 & CloudFront media offloading solution. Upload and manage unlimited media files with CDN integration. Automatically offloads thumbnails to save server inodes.

== Description ==

WP Cloud Media Offload is a powerful WordPress plugin that seamlessly integrates your media library with Amazon S3 and CloudFront CDN. Perfect for websites with large media libraries, especially Amazon affiliate sites with thousands of product images.

= Key Features =

* **Unlimited Media Uploads** - Upload 25,000 to 350,000+ images to AWS S3
* **CloudFront CDN Integration** - Serve media files through CloudFront for lightning-fast delivery
* **Automatic Background Uploads** - New media files are automatically uploaded to S3
* **Bulk Upload Tools** - Migrate existing media library to S3 with one click
* **Smart File Management** - Option to remove local files after upload to save server space
* **Beautiful Modern UI** - Stunning purple-orange gradient interface with light/dark mode
* **License Management** - Secure license activation system
* **Multiple Storage Providers** - Support for AWS S3, DigitalOcean Spaces, and Google Cloud
* **Priority Support** - Get help when you need it

= Perfect For =

* Amazon affiliate websites with thousands of product images
* E-commerce stores with large product catalogs
* Photography and portfolio websites
* News and magazine sites with extensive media libraries
* Any WordPress site looking to offload media to the cloud

= Why Choose WP Cloud Media Offload? =

Unlike other media offload plugins, WP Cloud Media Offload is specifically designed to handle massive media libraries with ease. Whether you're managing 25,000 or 350,000+ images, our plugin ensures smooth, reliable uploads without overwhelming your server.

= Premium Features =

* Unlimited media file uploads
* CloudFront CDN integration
* Automatic background processing
* Bulk upload tools for existing media
* Priority email support
* Regular updates and new features
* Multi-site support

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/wp-cloud-media-offload/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Cloud Media > License to activate your license
4. Configure your AWS S3 credentials in Cloud Media > Settings
5. Use Cloud Media > Bulk Upload to migrate existing media files

== Frequently Asked Questions ==

= Do I need an AWS account? =

Yes, you need an AWS account with S3 access. You can create one at aws.amazon.com.

= Can I use CloudFront CDN? =

Yes! CloudFront integration is included. Simply enable it in the settings and enter your CloudFront domain.

= Will this work with my existing media files? =

Absolutely! Use the Bulk Upload feature to migrate all existing media files to S3.

= What happens to my local files? =

You can choose to keep or remove local files after upload. We recommend keeping them initially until you verify everything works correctly.

= How long does bulk upload take? =

For 25,000 images, expect 2-4 hours. For 250,000+ images, plan for 20-40 hours depending on file sizes and connection speed.

= Is my license valid for multiple sites? =

License terms depend on your plan. Check your license details or contact support.

== Screenshots ==

1. Beautiful dashboard with statistics and quick actions
2. Easy-to-use settings page with provider selection
3. Bulk upload interface with progress tracking
4. License activation page
5. Modern UI with light and dark mode support

== Changelog ==

= 1.0.5 =
* Added Background Upload via WP-Cron for large sites (50k+ images)
* Background upload runs automatically without browser open
* Gentle on server CPU (10 images/minute)
* Perfect for 50,000+ images without server overload
* Auto-polling status updates every 10 seconds
* Can pause/resume background upload anytime

= 1.0.4 =
* Enhanced bulk upload to include all thumbnails
* Bulk upload now uploads main image + all thumbnail sizes
* Improved bulk upload handler with provider support
* Safe bulk migration without losing product images
* Added comprehensive bulk upload documentation

= 1.0.3 =
* Fixed broken images for imported products
* Improved URL filtering with intelligent fallback logic
* Automatic S3 URL construction for missing local files
* Better WooCommerce product image compatibility
* Added fix-missing-images.php utility script
* Enhanced thumbnail URL filtering

= 1.0.2 =
* Added automatic thumbnail offloading to S3
* Thumbnails now automatically deleted from local server
* Saves server inodes for high-volume sites
* All thumbnail sizes served from S3/CloudFront
* Improved deletion handling for thumbnails
* Company rebranded to HaiderNama Technologies Limited

= 1.0.0 =
* Initial release
* AWS S3 integration
* CloudFront CDN support
* Bulk upload functionality
* License management system
* Modern UI with light/dark mode
* Support for 250,000+ media files

== Upgrade Notice ==

= 1.0.0 =
Initial release of WP Cloud Media Offload.

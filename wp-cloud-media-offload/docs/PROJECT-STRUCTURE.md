# Project Structure

Complete overview of the WP Cloud Media Offload plugin architecture.

## Directory Structure

```
wp-cloud-media-offload/
├── assets/                      # Frontend assets
│   ├── css/
│   │   └── admin.css           # Admin UI styles (purple-orange gradient, light/dark mode)
│   └── js/
│       └── admin.js            # Admin JavaScript (AJAX, UI interactions)
│
├── docs/                        # Documentation
│   ├── AWS-SETUP-GUIDE.md      # Complete AWS setup instructions
│   ├── LICENSE-SERVER-API.md   # License server API specification
│   └── PROJECT-STRUCTURE.md    # This file
│
├── includes/                    # PHP classes (PSR-4 autoloaded)
│   ├── Admin/
│   │   └── Settings.php        # Settings registration and sanitization
│   │
│   ├── AWS/
│   │   └── S3Handler.php       # AWS S3 operations (upload, delete, test)
│   │
│   ├── Core/
│   │   ├── Activator.php       # Plugin activation logic
│   │   ├── BulkUploadHandler.php # Batch processing for bulk uploads
│   │   ├── Deactivator.php     # Plugin deactivation logic
│   │   ├── MediaHandler.php    # WordPress media hooks integration
│   │   └── Plugin.php          # Main plugin class
│   │
│   └── License/
│       └── Manager.php         # License activation and validation
│
├── languages/                   # Translation files (empty, ready for i18n)
│
├── templates/                   # Admin page templates
│   └── admin/
│       ├── bulk-upload.php     # Bulk upload interface
│       ├── dashboard.php       # Main dashboard
│       ├── license.php         # License activation page
│       └── settings.php        # Settings configuration page
│
├── vendor/                      # Composer dependencies (AWS SDK)
│
├── .gitignore                  # Git ignore rules
├── composer.json               # Composer configuration
├── INSTALLATION.md             # Installation guide
├── LICENSE.txt                 # GPL v2 license
├── README.md                   # Plugin overview
├── readme.txt                  # WordPress.org readme
├── uninstall.php              # Uninstall cleanup script
└── wp-cloud-media-offload.php # Main plugin file
```

## Core Components

### 1. Main Plugin File
**File:** `wp-cloud-media-offload.php`

- Plugin header with metadata
- Constants definition
- PSR-4 autoloader
- Plugin initialization
- Activation/deactivation hooks

### 2. Core Classes

#### Plugin.php
Main plugin orchestrator:
- Singleton pattern
- Admin menu registration
- Asset enqueuing
- AJAX handlers
- Dependency loading

#### Activator.php
Handles plugin activation:
- Database table creation
- Default options setup
- Rewrite rules flush

#### Deactivator.php
Handles plugin deactivation:
- Cleanup scheduled events
- Rewrite rules flush

#### MediaHandler.php
WordPress media integration:
- Upload hook (`wp_handle_upload`)
- URL filter (`wp_get_attachment_url`)
- Delete hook (`delete_attachment`)
- Auto-upload functionality

#### BulkUploadHandler.php
Batch processing system:
- Processes 50 files per batch
- Tracks progress
- Handles failures
- Prevents timeouts

### 3. AWS Integration

#### S3Handler.php
AWS S3 operations:
- S3 client initialization
- File upload with ACL
- File deletion
- Connection testing
- URL generation (S3/CloudFront)

**Dependencies:**
- AWS SDK for PHP v3
- Installed via Composer

### 4. License System

#### Manager.php
License management:
- Activation via API
- Deactivation
- Daily validation checks
- Status tracking

**API Endpoints:**
- `/activate` - Activate license
- `/deactivate` - Deactivate license
- `/check` - Validate license

### 5. Admin Interface

#### Settings.php
Settings management:
- Settings registration
- Input sanitization
- Validation

#### Template Files
Modern UI with purple-orange gradient:

**dashboard.php**
- Statistics overview
- Configuration status
- License status
- Quick actions

**settings.php**
- Provider selection (S3, DigitalOcean, Google Cloud)
- AWS credentials
- CloudFront configuration
- Upload settings

**bulk-upload.php**
- Progress tracking
- Batch processing controls
- Statistics display
- Tips for large uploads

**license.php**
- License activation form
- Status display
- Pricing information
- Support links

### 6. Frontend Assets

#### admin.css
Modern UI styling:
- CSS custom properties for theming
- Purple-orange gradient (`#8B5CF6` to `#F97316`)
- Light/dark mode support
- Responsive design
- Card-based layout
- Smooth animations

**Key Features:**
- Gradient buttons and headers
- Glass morphism effects
- Smooth transitions
- Mobile-responsive grid
- Accessible color contrast

#### admin.js
Interactive functionality:
- Theme toggle (light/dark)
- AJAX form submissions
- Connection testing
- License activation
- Bulk upload processing
- Progress tracking
- Alert notifications

## Database Schema

### Table: `wp_wpcmo_uploads`

```sql
CREATE TABLE wp_wpcmo_uploads (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    attachment_id bigint(20) NOT NULL,
    s3_key varchar(500) NOT NULL,
    s3_url varchar(1000) NOT NULL,
    cloudfront_url varchar(1000) DEFAULT NULL,
    file_size bigint(20) DEFAULT NULL,
    uploaded_at datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY attachment_id (attachment_id)
);
```

**Purpose:**
- Track uploaded files
- Store S3 and CloudFront URLs
- Monitor file sizes
- Prevent duplicate uploads

### Options

Stored in `wp_options`:

```php
wpcmo_settings = [
    'provider' => 'aws_s3',
    'aws_access_key' => '',
    'aws_secret_key' => '',
    'aws_region' => 'us-east-1',
    'aws_bucket' => '',
    'cloudfront_enabled' => false,
    'cloudfront_domain' => '',
    'auto_upload' => true,
    'remove_local_files' => false,
    'file_path_prefix' => 'wp-content/uploads/',
]

wpcmo_license_key = ''
wpcmo_license_status = 'inactive'
wpcmo_license_data = []
wpcmo_license_last_check = 0
```

## Workflow

### New Media Upload

1. User uploads file in WordPress
2. `MediaHandler::handle_upload()` triggered
3. Check if auto-upload enabled
4. Check license status
5. Upload to S3 via `S3Handler`
6. Store record in database
7. Optionally remove local file
8. Return upload data

### Bulk Upload

1. User clicks "Start Bulk Upload"
2. JavaScript initiates AJAX request
3. `BulkUploadHandler::process_batch()` called
4. Fetch 50 unprocessed attachments
5. Upload each to S3
6. Update progress
7. Return statistics
8. JavaScript requests next batch
9. Repeat until complete

### URL Filtering

1. WordPress requests attachment URL
2. `MediaHandler::filter_attachment_url()` triggered
3. Check database for S3 record
4. Return CloudFront URL if enabled
5. Otherwise return S3 URL
6. Fallback to original URL

### License Validation

1. Daily cron check
2. `Manager::check_license()` called
3. API request to license server
4. Update license status
5. Store last check timestamp

## Hooks and Filters

### Actions

```php
// Admin
add_action('admin_menu', 'add_admin_menu')
add_action('admin_enqueue_scripts', 'enqueue_admin_assets')
add_action('admin_init', 'register_settings')

// AJAX
add_action('wp_ajax_wpcmo_save_settings', 'ajax_save_settings')
add_action('wp_ajax_wpcmo_test_connection', 'ajax_test_connection')
add_action('wp_ajax_wpcmo_activate_license', 'ajax_activate_license')
add_action('wp_ajax_wpcmo_bulk_upload', 'ajax_bulk_upload')

// Media
add_action('delete_attachment', 'handle_delete')
```

### Filters

```php
add_filter('wp_handle_upload', 'handle_upload')
add_filter('wp_get_attachment_url', 'filter_attachment_url')
```

## Security

### Nonce Verification

All AJAX requests verify nonce:
```php
check_ajax_referer('wpcmo_nonce', 'nonce');
```

### Capability Checks

All admin actions check capabilities:
```php
if (!current_user_can('manage_options')) {
    wp_send_json_error(['message' => 'Unauthorized']);
}
```

### Input Sanitization

All inputs sanitized:
```php
sanitize_text_field()
sanitize_url()
esc_html()
esc_attr()
```

### SQL Injection Prevention

All queries use prepared statements:
```php
$wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id)
```

## Performance Optimizations

### Batch Processing

- Processes 50 files per batch
- Prevents PHP timeouts
- Reduces memory usage
- Allows progress tracking

### Database Indexing

- Index on `attachment_id`
- Fast lookup for URL filtering
- Efficient duplicate checking

### Asset Loading

- Assets only load on plugin pages
- Minified CSS/JS (production)
- Conditional loading

### Caching

- License check cached for 24 hours
- Settings cached in memory
- CloudFront caches media files

## Extensibility

### Filters for Developers

```php
// Modify S3 key before upload
apply_filters('wpcmo_s3_key', $s3_key, $file_path);

// Modify S3 upload args
apply_filters('wpcmo_s3_upload_args', $args, $file_path);

// Modify CloudFront URL
apply_filters('wpcmo_cloudfront_url', $url, $s3_key);
```

### Actions for Developers

```php
// After successful upload
do_action('wpcmo_after_upload', $attachment_id, $s3_data);

// Before file deletion
do_action('wpcmo_before_delete', $attachment_id, $s3_key);

// After bulk upload complete
do_action('wpcmo_bulk_upload_complete', $stats);
```

## Testing

### Manual Testing Checklist

- [ ] Plugin activation
- [ ] Settings save
- [ ] Connection test
- [ ] License activation
- [ ] Single file upload
- [ ] Bulk upload (small batch)
- [ ] URL filtering
- [ ] CloudFront delivery
- [ ] File deletion
- [ ] Plugin deactivation
- [ ] Plugin uninstall

### Test Environments

- WordPress 5.8+
- PHP 7.4, 8.0, 8.1
- MySQL 5.6+
- Various hosting environments

## Deployment

### Build Process

1. Run `composer install --no-dev`
2. Remove development files
3. Create ZIP archive
4. Test on clean WordPress install

### Files to Exclude

- `.git/`
- `.gitignore`
- `node_modules/`
- `composer.lock`
- Development tools

## Support

### Debug Mode

Enable WordPress debug:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

Check logs:
- `wp-content/debug.log`
- Server error logs
- Browser console

### Common Issues

1. **Connection fails**: Check AWS credentials
2. **Upload fails**: Check IAM permissions
3. **Images not loading**: Check bucket policy
4. **License fails**: Check server connectivity

## Future Enhancements

### Planned Features

- [ ] Multi-site support
- [ ] Image optimization before upload
- [ ] Backup/restore functionality
- [ ] Advanced analytics
- [ ] WP-CLI commands
- [ ] REST API endpoints
- [ ] Webhook notifications
- [ ] Custom storage providers

### Performance Improvements

- [ ] Background processing with Action Scheduler
- [ ] Redis caching integration
- [ ] Lazy loading for admin tables
- [ ] Async JavaScript loading

## Contributing

### Code Standards

- Follow WordPress Coding Standards
- PSR-4 autoloading
- PHPDoc comments
- Meaningful variable names

### Git Workflow

1. Fork repository
2. Create feature branch
3. Make changes
4. Test thoroughly
5. Submit pull request

## License

GPL v2 or later - See LICENSE.txt

## Credits

- AWS SDK for PHP
- WordPress Plugin Boilerplate
- Modern UI inspired by Tailwind CSS

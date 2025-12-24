# Changelog

All notable changes to WP Cloud Media Offload will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-10-27

### Added
- Initial release of WP Cloud Media Offload
- AWS S3 integration for media storage
- CloudFront CDN support for fast content delivery
- Automatic upload of new media files
- Bulk upload functionality for existing media (supports 250,000+ files)
- Modern admin interface with purple-orange gradient design
- Light and dark mode theme support
- License activation and management system
- Connection testing for AWS credentials
- Progress tracking for bulk uploads
- Option to remove local files after upload
- Configurable file path prefix
- Support for multiple AWS regions
- Database tracking of uploaded files
- Statistics dashboard
- Settings page with provider selection
- Comprehensive documentation
- Installation guide
- AWS setup guide
- License server API documentation

### Features
- **Storage Providers**: AWS S3 (DigitalOcean Spaces and Google Cloud ready)
- **CDN Integration**: CloudFront support with custom domain
- **Batch Processing**: Upload 50 files per batch to prevent timeouts
- **Auto Upload**: Automatically upload new media to S3
- **URL Filtering**: Serve media from S3/CloudFront automatically
- **File Management**: Option to delete local files after upload
- **License System**: Secure license activation and validation
- **Modern UI**: Beautiful gradient design with responsive layout
- **Theme Toggle**: Switch between light and dark modes
- **Progress Tracking**: Real-time progress for bulk uploads
- **Statistics**: Track uploads, file sizes, and progress
- **Connection Test**: Verify AWS credentials before use

### Technical
- PHP 7.4+ support
- WordPress 5.8+ compatibility
- PSR-4 autoloading
- AWS SDK for PHP v3
- Composer dependency management
- Custom database table for tracking
- AJAX-powered admin interface
- Nonce verification for security
- Capability checks for authorization
- Input sanitization and validation
- Prepared SQL statements
- Responsive CSS Grid layout
- CSS custom properties for theming
- Smooth animations and transitions

### Documentation
- Complete README with features and installation
- Detailed INSTALLATION.md guide
- AWS-SETUP-GUIDE.md with step-by-step instructions
- LICENSE-SERVER-API.md for license server implementation
- PROJECT-STRUCTURE.md for developers
- WordPress.org readme.txt
- Inline code documentation

### Security
- Secure license validation
- Nonce verification for all AJAX requests
- Capability checks for admin actions
- Input sanitization
- SQL injection prevention
- Secure credential storage
- HTTPS enforcement for CloudFront

### Performance
- Batch processing to prevent timeouts
- Database indexing for fast lookups
- Conditional asset loading
- Optimized SQL queries
- Efficient file handling
- Progress caching

## [1.1.0] - 2025-10-28

### Added
- **Wasabi Cloud Storage Integration** - Full support for Wasabi's S3-compatible API
- Support for all 10 Wasabi global regions (US, EU, AP)
- Wasabi-specific optimizations and endpoint configuration
- Cost-effective alternative to AWS S3 (80% cheaper with no egress fees)
- Comprehensive Wasabi setup guide with step-by-step instructions
- Multi-provider architecture for easy addition of new storage providers

### Enhanced
- Improved provider selection interface in admin settings
- Enhanced connection testing for different cloud providers
- Updated MediaHandler to support multiple storage providers
- Better error handling and logging for different providers
- Updated documentation with Wasabi configuration instructions

### Technical
- New WasabiHandler class with S3-compatible API implementation
- Support for Wasabi's regional endpoints and URL structure
- Enhanced settings sanitization for multiple providers
- Improved JavaScript for provider switching
- Updated autoloader to handle new provider classes

## [Unreleased]

### Planned for 1.2.0
- [ ] Multi-site network support
- [ ] Image optimization before upload
- [ ] WP-CLI commands
- [ ] Advanced analytics dashboard
- [ ] Backup and restore functionality
- [ ] Webhook notifications
- [ ] REST API endpoints

### Planned for 1.2.0
- [ ] DigitalOcean Spaces full integration
- [ ] Google Cloud Storage integration
- [ ] Custom storage provider API
- [ ] Advanced file filtering
- [ ] Scheduled uploads
- [ ] Email notifications

### Planned for 2.0.0
- [ ] Background processing with Action Scheduler
- [ ] Redis caching support
- [ ] Advanced CDN features
- [ ] Image transformation API
- [ ] Video file support
- [ ] Document file support

## Version History

### Version Numbering

- **Major (X.0.0)**: Breaking changes, major new features
- **Minor (1.X.0)**: New features, backwards compatible
- **Patch (1.0.X)**: Bug fixes, minor improvements

### Support Policy

- **Latest version**: Full support and updates
- **Previous major version**: Security updates only
- **Older versions**: No support

## Upgrade Guide

### From Beta to 1.0.0

If you were using a beta version:

1. Backup your database
2. Deactivate the plugin
3. Delete the old plugin files
4. Install version 1.0.0
5. Activate the plugin
6. Reconfigure settings
7. Reactivate license

### Database Changes

Version 1.0.0 creates these database tables:
- `wp_wpcmo_uploads` - Tracks uploaded files

Version 1.0.0 creates these options:
- `wpcmo_settings` - Plugin settings
- `wpcmo_license_key` - License key
- `wpcmo_license_status` - License status
- `wpcmo_license_data` - License data
- `wpcmo_license_last_check` - Last validation check

## Known Issues

### Version 1.0.0

**Minor Issues:**
- Bulk upload requires browser tab to stay open
- Very large files (>100MB) may timeout on slow connections
- Dark mode toggle requires page refresh in some browsers

**Workarounds:**
- For large files, increase PHP max_execution_time
- For bulk uploads, use during off-peak hours
- Refresh page after theme toggle if needed

## Bug Reports

Found a bug? Please report it:
- Email: support@yourcompany.com
- Support Portal: yoursite.com/support
- Include WordPress version, PHP version, and error messages

## Feature Requests

Have an idea? We'd love to hear it:
- Email: features@yourcompany.com
- Community Forum: yoursite.com/forum
- Vote on existing requests: yoursite.com/roadmap

## Credits

### Contributors
- Your Company Development Team
- Beta testers and early adopters
- WordPress community

### Third-Party Libraries
- AWS SDK for PHP by Amazon Web Services
- WordPress Plugin Boilerplate
- Modern UI inspired by Tailwind CSS

### Special Thanks
- All our customers and supporters
- WordPress core team
- AWS team for excellent documentation

## License

WP Cloud Media Offload is licensed under GPL v2 or later.

See LICENSE.txt for full license text.

---

**Note**: This changelog follows [Keep a Changelog](https://keepachangelog.com/) format.

For detailed commit history, see the Git repository.

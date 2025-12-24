# License Server Installation Guide

Complete guide to set up the license management system.

## Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL 8.0 or higher
- Node.js 16+ (for frontend assets)
- Stripe account
- Web server (Apache/Nginx)

## Step-by-Step Installation

### 1. Clone Repository

```bash
git clone https://github.com/yourcompany/license-server.git
cd license-server
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies (if using frontend)
npm install
```

### 3. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Database

Edit `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=license_server
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Create database:

```bash
mysql -u root -p
CREATE DATABASE license_server;
EXIT;
```

### 5. Configure Stripe

Get your Stripe keys from: https://dashboard.stripe.com/apikeys

Edit `.env`:

```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

### 6. Run Migrations

```bash
# Run all migrations
php artisan migrate

# Seed database with plans and admin user
php artisan db:seed
```

This creates:
- Admin user: admin@yourcompany.com / password
- Test customer: customer@example.com / password
- All 6 plans (Free, Bronze, Silver, Gold, Platinum, Unlimited)

### 7. Configure Mail

Edit `.env` for email notifications:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourcompany.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 8. Set Up Stripe Webhook

1. Go to: https://dashboard.stripe.com/webhooks
2. Click "Add endpoint"
3. URL: `https://your-domain.com/webhook/stripe`
4. Events to listen:
   - `checkout.session.completed`
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
5. Copy webhook signing secret to `.env`

### 9. Start Server

**Development:**
```bash
php artisan serve
# Access at: http://localhost:8000
```

**Production:**
Configure your web server (Apache/Nginx) to point to `public/` directory.

### 10. Test Installation

```bash
# Test API endpoint
curl http://localhost:8000/api/v1/check \
  -H "Content-Type: application/json" \
  -d '{"license_key":"TEST-KEY","domain":"https://example.com"}'
```

## Post-Installation

### 1. Change Admin Password

```bash
php artisan tinker
$admin = User::where('email', 'admin@yourcompany.com')->first();
$admin->password = Hash::make('your-secure-password');
$admin->save();
```

### 2. Configure CORS (if needed)

Edit `config/cors.php`:

```php
'paths' => ['api/*'],
'allowed_origins' => ['*'], // Or specific domains
```

### 3. Set Up Cron Jobs

Add to crontab:

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### 4. Configure Queue Workers (Optional)

For background jobs:

```bash
php artisan queue:work
```

Or use Supervisor for production.

### 5. Enable HTTPS

Use Let's Encrypt:

```bash
sudo certbot --nginx -d your-domain.com
```

## Testing

### Test License Generation

```bash
php artisan tinker

$user = User::first();
$plan = Plan::where('slug', 'gold')->first();
$licenseService = app(\App\Services\LicenseService::class);
$license = $licenseService->createLicense($user, $plan);
echo $license->license_key;
```

### Test API Endpoints

```bash
# Activate license
curl -X POST http://localhost:8000/api/v1/activate \
  -H "Content-Type: application/json" \
  -d '{
    "license_key": "YOUR-LICENSE-KEY",
    "domain": "https://example.com",
    "product": "wp-cloud-media-offload"
  }'

# Track upload
curl -X POST http://localhost:8000/api/v1/track-upload \
  -H "Content-Type: application/json" \
  -d '{
    "license_key": "YOUR-LICENSE-KEY",
    "domain": "https://example.com",
    "file_name": "test.jpg",
    "file_size": 245760
  }'
```

### Test Payment Flow

1. Use Stripe test card: `4242 4242 4242 4242`
2. Any future expiry date
3. Any 3-digit CVC
4. Any ZIP code

## Troubleshooting

### Database Connection Error

```bash
# Check MySQL is running
sudo systemctl status mysql

# Test connection
mysql -u your_username -p
```

### Permission Issues

```bash
# Set correct permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Stripe Webhook Not Working

1. Check webhook URL is accessible
2. Verify webhook secret in `.env`
3. Check webhook logs in Stripe dashboard
4. Test with Stripe CLI:

```bash
stripe listen --forward-to localhost:8000/webhook/stripe
```

### API Returns 404

Check `.htaccess` or Nginx config:

**Apache (.htaccess):**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

**Nginx:**
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## Security Checklist

- [ ] Change default admin password
- [ ] Set strong `APP_KEY`
- [ ] Use HTTPS in production
- [ ] Set `APP_DEBUG=false` in production
- [ ] Configure firewall
- [ ] Set up regular backups
- [ ] Enable rate limiting
- [ ] Use environment variables for secrets
- [ ] Keep dependencies updated

## Backup

### Database Backup

```bash
# Manual backup
mysqldump -u username -p license_server > backup.sql

# Restore
mysql -u username -p license_server < backup.sql
```

### Automated Backups

Add to crontab:

```bash
0 2 * * * mysqldump -u username -p'password' license_server > /backups/license_server_$(date +\%Y\%m\%d).sql
```

## Monitoring

### Log Files

```bash
# Application logs
tail -f storage/logs/laravel.log

# Web server logs
tail -f /var/log/nginx/error.log
tail -f /var/log/apache2/error.log
```

### Performance Monitoring

Consider using:
- Laravel Telescope (development)
- New Relic (production)
- Sentry (error tracking)

## Scaling

### Database Optimization

```bash
# Add indexes
php artisan migrate

# Optimize tables
php artisan db:optimize
```

### Caching

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache
```

### Load Balancing

For high traffic:
1. Set up multiple app servers
2. Use Redis for session storage
3. Use CDN for static assets
4. Implement database replication

## Support

Need help?
- Email: dev@yourcompany.com
- Documentation: https://docs.yourcompany.com
- GitHub Issues: https://github.com/yourcompany/license-server/issues

## Next Steps

After installation:
1. Access admin dashboard
2. Create test license
3. Test API endpoints
4. Configure email templates
5. Set up monitoring
6. Deploy to production

Congratulations! Your license server is ready! 🎉

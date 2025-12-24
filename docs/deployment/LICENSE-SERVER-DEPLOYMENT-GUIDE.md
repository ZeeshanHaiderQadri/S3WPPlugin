# License Server Deployment Guide

Complete guide to deploy your license server to production.

## Overview

The license server is a Laravel application that manages:
- License key generation and validation
- User accounts and subscriptions
- Payment processing via Stripe
- Usage tracking for media uploads
- Admin dashboard for management

## Deployment Options

Choose the option that best fits your needs:

### Option 1: Shared Hosting (Easiest) ⭐ Recommended for Beginners
- **Cost**: $5-15/month
- **Difficulty**: Easy
- **Best for**: Small to medium projects
- **Examples**: Namecheap, Hostinger, SiteGround

### Option 2: VPS (Most Flexible)
- **Cost**: $5-20/month
- **Difficulty**: Medium
- **Best for**: Growing projects
- **Examples**: DigitalOcean, Linode, Vultr

### Option 3: Cloud Platform (Most Scalable)
- **Cost**: $10-50+/month
- **Difficulty**: Medium-Hard
- **Best for**: Large projects
- **Examples**: AWS, Google Cloud, Azure

### Option 4: Laravel-Specific Hosting (Easiest for Laravel)
- **Cost**: $12-30/month
- **Difficulty**: Very Easy
- **Best for**: Laravel projects
- **Examples**: Laravel Forge, Ploi, RunCloud

---

## Option 1: Shared Hosting (Recommended for Beginners)

### Requirements
- PHP 8.1+
- MySQL 8.0+
- Composer support
- SSH access (preferred)

### Step-by-Step: Namecheap/Hostinger/SiteGround

#### 1. Prepare Your Files Locally

```bash
cd license-server

# Install dependencies
composer install --optimize-autoloader --no-dev

# Create production .env
cp .env.example .env
```

#### 2. Configure .env for Production

Edit `.env`:

```env
APP_NAME="License Server"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

# Get from Stripe Dashboard
STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...

# Email settings (use your hosting email)
MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
```

#### 3. Generate Application Key

```bash
php artisan key:generate
```

Copy the generated key to your `.env` file.

#### 4. Upload Files

**Via FTP/SFTP:**
1. Connect to your hosting via FileZilla or similar
2. Upload entire `license-server` folder to your hosting
3. Place it outside `public_html` for security

**Directory structure:**
```
/home/username/
├── license-server/          # Upload here
│   ├── app/
│   ├── config/
│   ├── database/
│   ├── public/             # This will be your web root
│   └── ...
└── public_html/            # Don't use this
```

#### 5. Set Document Root

In your hosting control panel (cPanel):
1. Go to "Domains" or "Addon Domains"
2. Set document root to: `/home/username/license-server/public`
3. Save changes

#### 6. Create Database

In cPanel:
1. Go to "MySQL Databases"
2. Create new database: `username_license`
3. Create new user with strong password
4. Add user to database with ALL PRIVILEGES
5. Note down: database name, username, password

#### 7. Run Migrations via SSH

```bash
# SSH into your server
ssh username@yourdomain.com

# Navigate to project
cd license-server

# Run migrations
php artisan migrate --force

# Seed database
php artisan db:seed --force

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**No SSH access?** Use the web installer:
- Upload `public/setup-fixed.php` 
- Visit: `https://yourdomain.com/setup-fixed.php`
- Follow on-screen instructions

#### 8. Set Permissions

```bash
chmod -R 755 storage bootstrap/cache
chown -R username:username storage bootstrap/cache
```

#### 9. Configure .htaccess

Ensure `public/.htaccess` exists:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

#### 10. Test Installation

Visit: `https://yourdomain.com`

You should see the landing page.

Test API:
```bash
curl https://yourdomain.com/api/v1/check \
  -H "Content-Type: application/json" \
  -d '{"license_key":"TEST","domain":"https://test.com"}'
```

---

## Option 2: VPS (DigitalOcean/Linode/Vultr)

### Step-by-Step: DigitalOcean Droplet

#### 1. Create Droplet

1. Sign up at DigitalOcean
2. Create new Droplet:
   - **Image**: Ubuntu 22.04 LTS
   - **Plan**: Basic $6/month (1GB RAM)
   - **Datacenter**: Closest to your users
   - **Authentication**: SSH key (recommended)

#### 2. Initial Server Setup

```bash
# SSH into server
ssh root@your_server_ip

# Update system
apt update && apt upgrade -y

# Create new user
adduser deployer
usermod -aG sudo deployer

# Switch to new user
su - deployer
```

#### 3. Install LEMP Stack

```bash
# Install Nginx
sudo apt install nginx -y

# Install MySQL
sudo apt install mysql-server -y
sudo mysql_secure_installation

# Install PHP 8.1
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install php8.1-fpm php8.1-mysql php8.1-mbstring php8.1-xml php8.1-bcmath php8.1-curl php8.1-zip php8.1-gd -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

#### 4. Configure MySQL

```bash
sudo mysql

CREATE DATABASE license_server;
CREATE USER 'license_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON license_server.* TO 'license_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### 5. Deploy Application

```bash
# Clone or upload your code
cd /var/www
sudo git clone https://github.com/yourusername/license-server.git
# OR upload via SCP/SFTP

cd license-server

# Install dependencies
composer install --optimize-autoloader --no-dev

# Set permissions
sudo chown -R www-data:www-data /var/www/license-server
sudo chmod -R 755 /var/www/license-server/storage
sudo chmod -R 755 /var/www/license-server/bootstrap/cache
```

#### 6. Configure Environment

```bash
cp .env.example .env
nano .env
```

Update with your production settings (see Option 1 for .env example).

```bash
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 7. Configure Nginx

```bash
sudo nano /etc/nginx/sites-available/license-server
```

Add:

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/license-server/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable site:

```bash
sudo ln -s /etc/nginx/sites-available/license-server /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

#### 8. Install SSL Certificate

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y

# Get certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Auto-renewal is set up automatically
```

#### 9. Set Up Firewall

```bash
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw enable
```

#### 10. Set Up Supervisor (for queues)

```bash
sudo apt install supervisor -y

sudo nano /etc/supervisor/conf.d/license-server.conf
```

Add:

```ini
[program:license-server-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/license-server/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/license-server/storage/logs/worker.log
```

Start:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start license-server-worker:*
```

---

## Option 3: Laravel Forge (Easiest for Laravel)

### Step-by-Step

#### 1. Sign Up for Forge

Visit: https://forge.laravel.com
- Cost: $12/month
- Includes server management

#### 2. Connect Your Server Provider

- Link your DigitalOcean/Linode/AWS account
- Or use Forge's servers

#### 3. Create Server

1. Click "Create Server"
2. Choose provider and size
3. Select region
4. Choose "App Server" type
5. Wait for provisioning (5-10 minutes)

#### 4. Create Site

1. Click "New Site"
2. Domain: `yourdomain.com`
3. Project type: Laravel
4. Web directory: `/public`

#### 5. Deploy Repository

1. Go to site settings
2. Click "Git Repository"
3. Connect GitHub/GitLab/Bitbucket
4. Enter repository: `yourusername/license-server`
5. Branch: `main`
6. Click "Install Repository"

#### 6. Configure Environment

1. Go to "Environment"
2. Edit `.env` file with production settings
3. Save

#### 7. Run Deployment Script

Forge auto-creates a deployment script. Edit it:

```bash
cd /home/forge/yourdomain.com
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
```

Click "Deploy Now"

#### 8. Enable SSL

1. Go to "SSL"
2. Click "LetsEncrypt"
3. Enter email
4. Click "Obtain Certificate"

#### 9. Set Up Scheduler

Forge automatically sets up Laravel scheduler.

#### 10. Set Up Queue Worker

1. Go to "Queue"
2. Click "New Worker"
3. Connection: `database` or `redis`
4. Save

Done! Your site is live at `https://yourdomain.com`

---

## Post-Deployment Checklist

### Security

- [ ] Set `APP_DEBUG=false` in production
- [ ] Use strong database passwords
- [ ] Enable HTTPS/SSL
- [ ] Set up firewall
- [ ] Regular backups configured
- [ ] Update `.env` with production Stripe keys
- [ ] Change default admin password

### Configuration

- [ ] Configure email settings
- [ ] Set up Stripe webhook
- [ ] Test API endpoints
- [ ] Configure CORS if needed
- [ ] Set up monitoring/logging

### Testing

- [ ] Visit homepage - should load
- [ ] Visit `/admin` - should show login
- [ ] Test API: `/api/v1/check`
- [ ] Test license activation
- [ ] Test payment flow with Stripe test mode
- [ ] Check email notifications work

### Stripe Webhook Setup

1. Go to: https://dashboard.stripe.com/webhooks
2. Click "Add endpoint"
3. URL: `https://yourdomain.com/webhook/stripe`
4. Select events:
   - `checkout.session.completed`
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
5. Copy webhook secret to `.env`

---

## Connecting WordPress Plugin to License Server

After deployment, update the WordPress plugin:

### 1. Update Plugin Configuration

Edit `wp-cloud-media-offload/includes/License/Manager.php`:

```php
private $api_url = 'https://yourdomain.com/api/v1';
```

### 2. Update MediaHandler

Edit `wp-cloud-media-offload/includes/Core/MediaHandler.php`:

```php
private function track_upload_with_server($upload_data) {
    $license_key = get_option('wpcmo_license_key', '');
    
    if (empty($license_key)) {
        return;
    }
    
    $response = wp_remote_post('https://yourdomain.com/api/v1/track-upload', [
        // ... rest of code
    ]);
}
```

### 3. Test Connection

In WordPress:
1. Go to Cloud Media → License
2. Enter a test license key
3. Click "Activate License"
4. Should show success message

---

## Maintenance

### Regular Updates

```bash
# Pull latest code
git pull origin main

# Update dependencies
composer install --no-dev --optimize-autoloader

# Run migrations
php artisan migrate --force

# Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Database Backup

```bash
# Manual backup
mysqldump -u username -p license_server > backup_$(date +%Y%m%d).sql

# Automated (add to crontab)
0 2 * * * mysqldump -u username -p'password' license_server > /backups/license_$(date +\%Y\%m\%d).sql
```

### Monitor Logs

```bash
# Application logs
tail -f storage/logs/laravel.log

# Nginx logs
tail -f /var/log/nginx/error.log
```

---

## Troubleshooting

### 500 Internal Server Error

```bash
# Check logs
tail -f storage/logs/laravel.log

# Check permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 755 storage bootstrap/cache

# Clear cache
php artisan config:clear
php artisan cache:clear
```

### Database Connection Error

```bash
# Test MySQL connection
mysql -u username -p

# Check .env settings
cat .env | grep DB_
```

### API Returns 404

```bash
# Check Nginx config
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx

# Clear route cache
php artisan route:clear
```

---

## Cost Estimates

### Shared Hosting
- Hosting: $5-15/month
- Domain: $10-15/year
- SSL: Free (Let's Encrypt)
- **Total**: ~$10/month

### VPS
- Server: $6-12/month (DigitalOcean/Linode)
- Domain: $10-15/year
- SSL: Free (Let's Encrypt)
- **Total**: ~$10/month

### Laravel Forge
- Forge: $12/month
- Server: $6/month (DigitalOcean)
- Domain: $10-15/year
- **Total**: ~$20/month

### Additional Costs
- Stripe fees: 2.9% + $0.30 per transaction
- Email service (optional): $0-10/month
- Backups (optional): $1-5/month

---

## Recommended Setup for Beginners

**Best Option**: Laravel Forge + DigitalOcean

**Why?**
- ✅ One-click deployment
- ✅ Automatic SSL
- ✅ Easy updates
- ✅ Built-in monitoring
- ✅ Queue management
- ✅ Scheduler setup
- ✅ Great support

**Total Cost**: ~$20/month

**Setup Time**: 30 minutes

---

## Need Help?

- Check Laravel documentation: https://laravel.com/docs
- Laravel Forge docs: https://forge.laravel.com/docs
- DigitalOcean tutorials: https://www.digitalocean.com/community/tutorials

Your license server will be live at: `https://yourdomain.com` 🚀

# Quick Deploy: License Server in 30 Minutes

The fastest way to get your license server online.

## What You Need

- [ ] A domain name (e.g., `licenses.yourdomain.com`)
- [ ] $18/month budget ($12 Forge + $6 DigitalOcean)
- [ ] Stripe account (free to create)
- [ ] 30 minutes of time

## Why This Method?

**Laravel Forge** handles all the complex server setup automatically:
- ✅ Server provisioning
- ✅ SSL certificates
- ✅ Database setup
- ✅ Queue workers
- ✅ Automatic deployments
- ✅ One-click updates

You just click buttons and paste your code. No command line needed!

---

## Step 1: Get Your Accounts Ready (10 minutes)

### 1.1 Create DigitalOcean Account

1. Go to: https://www.digitalocean.com
2. Sign up (get $200 credit for 60 days with referral)
3. Add payment method
4. **Don't create any servers yet** - Forge will do this

### 1.2 Create Laravel Forge Account

1. Go to: https://forge.laravel.com
2. Sign up ($12/month - cancel anytime)
3. Choose "Business" plan
4. Complete signup

### 1.3 Create Stripe Account

1. Go to: https://stripe.com
2. Sign up (free)
3. Complete business verification
4. Get your API keys:
   - Go to: https://dashboard.stripe.com/apikeys
   - Copy "Publishable key" (starts with `pk_test_`)
   - Copy "Secret key" (starts with `sk_test_`)
   - Keep these safe!

---

## Step 2: Connect Forge to DigitalOcean (2 minutes)

1. In Laravel Forge, click your profile (top right)
2. Go to "Server Providers"
3. Click "DigitalOcean"
4. Click "Link DigitalOcean Account"
5. Authorize Forge to access DigitalOcean
6. Done!

---

## Step 3: Create Your Server (5 minutes)

1. In Forge, click "Servers" → "Create Server"
2. Fill in:
   - **Name**: `license-server`
   - **Provider**: DigitalOcean
   - **Region**: Choose closest to your users
   - **Server Size**: Basic ($6/month - 1GB RAM)
   - **PHP Version**: 8.2
   - **Database**: MySQL 8.0
3. Click "Create Server"
4. Wait 5-10 minutes for provisioning
5. You'll get an email when ready

---

## Step 4: Push Your Code to GitHub (3 minutes)

### 4.1 Create GitHub Repository

1. Go to: https://github.com/new
2. Repository name: `license-server`
3. Make it **Private**
4. Click "Create repository"

### 4.2 Push Your Code

```bash
cd license-server

# Initialize git (if not already)
git init

# Add all files
git add .

# Commit
git commit -m "Initial commit"

# Add remote
git remote add origin https://github.com/yourusername/license-server.git

# Push
git branch -M main
git push -u origin main
```

---

## Step 5: Create Site in Forge (3 minutes)

1. In Forge, click your server name
2. Click "New Site"
3. Fill in:
   - **Root Domain**: `licenses.yourdomain.com`
   - **Project Type**: General PHP/Laravel
   - **Web Directory**: `/public`
4. Click "Add Site"

---

## Step 6: Connect GitHub Repository (2 minutes)

1. Click on your site name
2. Go to "Git Repository" tab
3. Click "Install Repository"
4. Choose "GitHub"
5. Authorize Forge (if first time)
6. Fill in:
   - **Repository**: `yourusername/license-server`
   - **Branch**: `main`
   - **Install Composer Dependencies**: ✅ Yes
7. Click "Install Repository"
8. Wait for installation to complete

---

## Step 7: Configure Environment (3 minutes)

1. In your site, go to "Environment" tab
2. You'll see the `.env` file
3. Update these values:

```env
APP_NAME="License Server"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://licenses.yourdomain.com

# Database (already configured by Forge)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=forge
DB_USERNAME=forge
DB_PASSWORD=<already filled by Forge>

# Stripe (paste your keys from Step 1.3)
STRIPE_KEY=pk_test_your_key_here
STRIPE_SECRET=sk_test_your_secret_here
STRIPE_WEBHOOK_SECRET=<leave empty for now>

# Email (use your domain email)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="License Server"
```

4. Click "Save"

---

## Step 8: Run Database Migrations (2 minutes)

1. Still in your site, go to "Commands" tab
2. In the command box, type:

```bash
php artisan migrate --force
```

3. Click "Run Command"
4. Wait for success message
5. Run another command:

```bash
php artisan db:seed --force
```

6. This creates:
   - Admin user: `admin@yourcompany.com` / `password`
   - All 6 plans (Free, Bronze, Silver, Gold, Platinum, Unlimited)

---

## Step 9: Enable SSL Certificate (1 minute)

1. Go to "SSL" tab
2. Click "LetsEncrypt"
3. Enter your email
4. Click "Obtain Certificate"
5. Wait 30 seconds
6. SSL is now active! 🔒

---

## Step 10: Set Up Queue Worker (1 minute)

1. Go to "Queue" tab
2. Click "New Worker"
3. Fill in:
   - **Connection**: `database`
   - **Queue**: `default`
   - **Processes**: `1`
4. Click "Create Worker"

---

## Step 11: Point Your Domain (5 minutes)

### Option A: Subdomain (Recommended)

In your domain registrar (Namecheap, GoDaddy, etc.):

1. Go to DNS settings
2. Add A Record:
   - **Host**: `licenses` (or `license-server`)
   - **Value**: Your server IP (shown in Forge)
   - **TTL**: Automatic
3. Save
4. Wait 5-30 minutes for DNS propagation

### Option B: New Domain

1. Point nameservers to DigitalOcean:
   - `ns1.digitalocean.com`
   - `ns2.digitalocean.com`
   - `ns3.digitalocean.com`
2. In DigitalOcean, add domain and create A record
3. Wait for propagation

---

## Step 12: Test Your Installation (2 minutes)

### Test 1: Homepage

Visit: `https://licenses.yourdomain.com`

You should see the landing page.

### Test 2: Admin Login

Visit: `https://licenses.yourdomain.com/admin`

Login with:
- Email: `admin@yourcompany.com`
- Password: `password`

**⚠️ Change this password immediately!**

### Test 3: API

```bash
curl https://licenses.yourdomain.com/api/v1/check \
  -H "Content-Type: application/json" \
  -d '{"license_key":"TEST","domain":"https://test.com"}'
```

Should return JSON response.

---

## Step 13: Set Up Stripe Webhook (3 minutes)

1. Go to: https://dashboard.stripe.com/webhooks
2. Click "Add endpoint"
3. Fill in:
   - **Endpoint URL**: `https://licenses.yourdomain.com/webhook/stripe`
   - **Events to send**: Select these:
     - `checkout.session.completed`
     - `payment_intent.succeeded`
     - `payment_intent.payment_failed`
4. Click "Add endpoint"
5. Copy the "Signing secret" (starts with `whsec_`)
6. Go back to Forge → Environment
7. Update:
   ```env
   STRIPE_WEBHOOK_SECRET=whsec_your_secret_here
   ```
8. Save

---

## Step 14: Update WordPress Plugin (2 minutes)

Now connect your WordPress plugin to the license server:

### Edit License Manager

File: `wp-cloud-media-offload/includes/License/Manager.php`

Find:
```php
private $api_url = 'https://your-license-server.com/api/v1';
```

Change to:
```php
private $api_url = 'https://licenses.yourdomain.com/api/v1';
```

### Edit Media Handler

File: `wp-cloud-media-offload/includes/Core/MediaHandler.php`

Find:
```php
$response = wp_remote_post('https://your-license-server.com/api/v1/track-upload', [
```

Change to:
```php
$response = wp_remote_post('https://licenses.yourdomain.com/api/v1/track-upload', [
```

Save both files.

---

## Step 15: Create Your First License (2 minutes)

1. Log into admin: `https://licenses.yourdomain.com/admin`
2. Go to "Licenses" → "Create License"
3. Fill in:
   - **User**: Select admin user
   - **Plan**: Gold (or any plan)
   - **Status**: Active
4. Click "Create"
5. Copy the license key (format: `XXXX-XXXX-XXXX-XXXX`)

---

## Step 16: Test in WordPress (2 minutes)

1. Go to your WordPress site
2. Navigate to: **Cloud Media → License**
3. Paste the license key
4. Click "Activate License"
5. Should show: ✅ "License activated successfully!"

---

## You're Done! 🎉

Your license server is now live at: `https://licenses.yourdomain.com`

### What You Have:

✅ **License Server**: Fully functional and secure
✅ **Admin Dashboard**: Manage licenses and users
✅ **Payment Processing**: Stripe integration ready
✅ **API**: WordPress plugin can connect
✅ **SSL**: Secure HTTPS connection
✅ **Automatic Backups**: Forge handles this
✅ **Monitoring**: Built-in server monitoring

---

## Next Steps

### 1. Secure Your Admin Account

```bash
# In Forge, go to Commands and run:
php artisan tinker

# Then type:
$admin = App\Models\User::where('email', 'admin@yourcompany.com')->first();
$admin->password = Hash::make('your-new-secure-password');
$admin->save();
exit
```

### 2. Switch Stripe to Live Mode

When ready for production:

1. Get live API keys from Stripe
2. Update `.env` in Forge:
   ```env
   STRIPE_KEY=pk_live_...
   STRIPE_SECRET=sk_live_...
   ```
3. Update webhook to use live mode

### 3. Customize Landing Page

Edit files in: `license-server/resources/views/landing/`

Deploy changes:
```bash
git add .
git commit -m "Update landing page"
git push origin main
```

Forge auto-deploys on push!

### 4. Set Up Email

For production emails, use:
- **SendGrid**: Free tier (100 emails/day)
- **Mailgun**: Free tier (5,000 emails/month)
- **Amazon SES**: Very cheap ($0.10 per 1,000 emails)

### 5. Monitor Your Server

In Forge:
- Check "Monitoring" tab for server health
- View "Logs" for errors
- Set up "Notifications" for alerts

---

## Costs Breakdown

| Service | Cost | What It Does |
|---------|------|--------------|
| Laravel Forge | $12/month | Server management |
| DigitalOcean | $6/month | Server hosting |
| Domain | $10-15/year | Your domain name |
| Stripe | Free + 2.9% per sale | Payment processing |
| SSL Certificate | Free | HTTPS security |
| **Total** | **~$20/month** | Everything running |

---

## Troubleshooting

### Site Not Loading?

1. Check DNS propagation: https://dnschecker.org
2. Verify A record points to correct IP
3. Check Forge → Site → SSL is active

### Database Error?

1. Go to Forge → Commands
2. Run: `php artisan migrate:fresh --seed --force`
3. This resets database

### API Not Working?

1. Check `.env` has correct `APP_URL`
2. Run: `php artisan config:cache`
3. Check Nginx logs in Forge

### Need to Redeploy?

1. Go to Forge → Site → Deployments
2. Click "Deploy Now"
3. Or push to GitHub (auto-deploys)

---

## Support Resources

- **Laravel Forge Docs**: https://forge.laravel.com/docs
- **Laravel Docs**: https://laravel.com/docs
- **DigitalOcean Tutorials**: https://www.digitalocean.com/community
- **Stripe Docs**: https://stripe.com/docs

---

## Summary

You now have a professional license server running that:

1. ✅ Generates and validates license keys
2. ✅ Processes payments via Stripe
3. ✅ Tracks media upload usage
4. ✅ Provides admin dashboard
5. ✅ Connects to WordPress plugin
6. ✅ Runs securely with SSL
7. ✅ Auto-deploys from GitHub

**Total setup time**: ~30 minutes
**Monthly cost**: ~$20
**Maintenance**: Minimal (Forge handles it)

Your WordPress plugin can now activate licenses and track uploads! 🚀

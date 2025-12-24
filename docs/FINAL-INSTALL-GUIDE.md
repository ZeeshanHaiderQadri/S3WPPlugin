# ✅ FINAL INSTALLATION GUIDE - Clean URLs

## 🎯 Perfect Structure - No /public/ in URLs!

All main files are now at ROOT level for clean URLs:
- ✅ `https://s3cloudmedia.techknowledgecal.com/admin/login`
- ✅ `https://s3cloudmedia.techknowledgecal.com/setup.php`
- ✅ `https://s3cloudmedia.techknowledgecal.com/`

---

## 📦 What to Upload

Upload the **entire `license-server` folder** to:
```
/public_html/s3cloudmedia/
```

---

## 🚀 Installation (3 Simple Steps)

### Step 1: Upload Files

1. **Zip** the `license-server` folder
2. **Upload** to Hostinger File Manager
3. **Extract** in `/public_html/s3cloudmedia/`

### Step 2: Configure Database

1. **Edit:** `/public_html/s3cloudmedia/setup.php`
2. **Change lines 9-16:**
   ```php
   $db_host = 'localhost';
   $db_name = 'your_database_name';  // Your Hostinger database
   $db_user = 'your_database_user';  // Your database user
   $db_pass = 'your_password';       // Your database password
   
   $admin_email = 'admin@yourcompany.com';  // Your email
   $admin_password = 'ChangeMe123!';  // Your password
   ```
3. **Save**

### Step 3: Run Setup

Visit: **https://s3cloudmedia.techknowledgecal.com/setup.php**

You'll see:
- ✅ Connected to database
- ✅ All 7 tables created
- ✅ 8 pricing plans inserted
- ✅ Admin user created
- 🎉 Setup Complete!

---

## 🎉 Done! Access Your Dashboard

Visit: **https://s3cloudmedia.techknowledgecal.com/admin/login**

Login with the email/password you set in Step 2.

---

## 📁 File Structure (ROOT LEVEL)

```
/public_html/s3cloudmedia/
├── index.php              ← Main entry point (ROOT)
├── .htaccess              ← URL routing (ROOT)
├── setup.php              ← Database setup (ROOT)
├── app/                   ← Laravel application
├── bootstrap/             ← Laravel bootstrap
├── database/              ← Migrations & seeders
├── public/                ← Static assets only
│   ├── diagnose.php      ← Diagnostic tool
│   └── setup-fixed.php   ← Backup setup
├── resources/views/       ← Blade templates
├── routes/               ← Web & API routes
├── vendor/               ← Composer dependencies
├── .env.example          ← Environment template
└── composer.json         ← Dependencies
```

---

## ✅ Clean URLs (No /public/)

All URLs work cleanly:

**Admin:**
- Login: `https://s3cloudmedia.techknowledgecal.com/admin/login` ✅
- Dashboard: `https://s3cloudmedia.techknowledgecal.com/admin` ✅
- Users: `https://s3cloudmedia.techknowledgecal.com/admin/users` ✅
- Licenses: `https://s3cloudmedia.techknowledgecal.com/admin/licenses` ✅

**Landing Page:**
- Home: `https://s3cloudmedia.techknowledgecal.com/` ✅
- Pricing: `https://s3cloudmedia.techknowledgecal.com/pricing` ✅
- Features: `https://s3cloudmedia.techknowledgecal.com/features` ✅

**API:**
- Check: `https://s3cloudmedia.techknowledgecal.com/api/v1/check` ✅
- Activate: `https://s3cloudmedia.techknowledgecal.com/api/v1/activate` ✅

**Setup:**
- Setup: `https://s3cloudmedia.techknowledgecal.com/setup.php` ✅

---

## 🔧 How It Works

### Root Files
- **index.php** - Main Laravel entry point (moved from /public)
- **.htaccess** - Routes all requests through index.php
- **setup.php** - Database setup script

### Path Adjustments
All paths in `index.php` are adjusted to work from root:
```php
require __DIR__.'/vendor/autoload.php';  // Not ../vendor
$app = require_once __DIR__.'/bootstrap/app.php';  // Not ../bootstrap
```

### URL Routing
`.htaccess` handles all routing:
- Real files (setup.php) → Served directly
- Routes (/admin/login) → Sent to index.php → Laravel handles it

---

## 💰 8 Pricing Plans (Ready)

1. **Free** - $0/year - 2,500 files
2. **Bronze** - $39/year - 2,000 files
3. **Silver** - $59/year - 6,000 files
4. **Gold** - $149/year - 20,000 files
5. **Platinum** - $199/year - 40,000 files
6. **Gem** - $349/year - 100,000 files
7. **500K** - $799/year - 500,000 files
8. **Unlimited** - $1,199/year - Unlimited

---

## 🔐 After Installation

### 1. Security
```bash
# Delete setup.php after successful installation
rm /public_html/s3cloudmedia/setup.php
```

### 2. Change Password
- Login to admin dashboard
- Go to Settings
- Change your password

### 3. Configure Stripe
Edit `.env` file:
```env
STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

### 4. Test Everything
- ✅ Admin dashboard
- ✅ Landing page
- ✅ API endpoints
- ✅ WordPress plugin

---

## 🎨 What You Get

### Admin Dashboard
- 📊 Real-time statistics
- 👥 User management
- 🔑 License management
- 💳 Order management
- 📦 Plan management
- 📈 Analytics & reports

### Landing Page
- 🏠 Home page
- 💰 Pricing page
- ⚡ Features page
- 📚 Documentation
- 📧 Contact form

### API
- License activation
- Usage tracking
- License validation
- Media upload tracking

### WordPress Plugin
- AWS S3 integration
- CloudFront CDN
- Bulk upload (250K+ images)
- License activation
- Usage tracking

---

## 🆘 Troubleshooting

### Setup.php shows database error
- Check database credentials
- Verify database exists in Hostinger
- Test connection in phpMyAdmin

### Admin login shows 404
- Check `.htaccess` file exists in root
- Verify `index.php` is in root
- Check file permissions (644 for files, 755 for folders)

### 500 Internal Server Error
- Check `.env` file exists
- Verify storage permissions (755)
- Check error logs in Hostinger

---

## ✅ Success Checklist

- [ ] Files uploaded to `/public_html/s3cloudmedia/`
- [ ] `setup.php` edited with database credentials
- [ ] Setup completed (all green checkmarks)
- [ ] Admin login works
- [ ] Landing page loads
- [ ] API responds
- [ ] `setup.php` deleted (security)
- [ ] Admin password changed
- [ ] Stripe configured
- [ ] Everything tested

---

## 🎊 You're Live!

**Admin Dashboard:** https://s3cloudmedia.techknowledgecal.com/admin/login  
**Landing Page:** https://s3cloudmedia.techknowledgecal.com/  
**API:** https://s3cloudmedia.techknowledgecal.com/api/v1/

**Clean URLs, no /public/, everything works perfectly!** 🚀

---

## 📞 Support

**Diagnostic Tool:** https://s3cloudmedia.techknowledgecal.com/public/diagnose.php  
**Logs:** `storage/logs/laravel.log`  
**Test API:** https://s3cloudmedia.techknowledgecal.com/api/v1/check

---

**This is the final, clean version. Just upload and go!** ✅

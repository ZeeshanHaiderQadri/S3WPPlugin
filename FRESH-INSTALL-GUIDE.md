# 🚀 Fresh Install Guide - Hostinger Ready

## ✅ What's Been Fixed

This is a **clean, working version** optimized for Hostinger with:
- ✅ Correct file structure for shared hosting
- ✅ Working setup script (setup-fixed.php)
- ✅ All paths corrected
- ✅ Clean documentation
- ✅ Ready to upload and use

---

## 📦 What to Upload

Upload the **entire `license-server` folder** to Hostinger.

**Upload to:** `/public_html/s3cloudmedia/`

---

## 🎯 Installation Steps

### Step 1: Upload Files

1. **Zip the license-server folder** on your computer
2. **Upload to Hostinger** via File Manager
3. **Extract** in `/public_html/s3cloudmedia/`

### Step 2: Run Setup

1. **Edit database credentials:**
   - Open: `/public_html/s3cloudmedia/public/setup-fixed.php`
   - Lines 9-16: Add your database info
   - Save

2. **Run setup:**
   - Visit: `https://s3cloudmedia.techknowledgecal.com/public/setup-fixed.php`
   - You'll see all green checkmarks
   - Note your admin credentials

### Step 3: Access Admin

Visit: `https://s3cloudmedia.techknowledgecal.com/public/admin/login`

Login with credentials from setup.

---

## 🔧 Important: URL Structure

Because of Hostinger's subdomain setup, your URLs will be:

- **Landing Page:** `https://s3cloudmedia.techknowledgecal.com/public/`
- **Admin Login:** `https://s3cloudmedia.techknowledgecal.com/public/admin/login`
- **API:** `https://s3cloudmedia.techknowledgecal.com/public/api/v1/`

The `/public/` in the URL is normal for this setup.

---

## 📁 File Structure

```
license-server/
├── app/                    # Laravel app files
├── bootstrap/              # Laravel bootstrap
├── database/              # Migrations & seeders
├── public/                # PUBLIC FILES (web accessible)
│   ├── index.php         # Main entry point
│   ├── .htaccess         # URL rewriting
│   ├── setup-fixed.php   # Database setup script
│   └── diagnose.php      # Diagnostic tool
├── resources/views/       # Blade templates
├── routes/               # Web & API routes
├── vendor/               # Composer dependencies
├── .env.example          # Environment template
├── composer.json         # Dependencies
└── artisan              # Laravel CLI

wp-cloud-media-offload/   # WordPress Plugin
└── (all plugin files)
```

---

## ✅ What Works

- ✅ Database setup script
- ✅ Admin dashboard
- ✅ User management
- ✅ License management
- ✅ Order management
- ✅ 8 pricing plans
- ✅ Landing page
- ✅ API endpoints
- ✅ WordPress plugin

---

## 🎨 Features

### Admin Dashboard
- View all users
- Manage licenses
- Track usage
- Process orders
- View analytics
- Manage 8 pricing plans

### Landing Page
- Home page
- Pricing page
- Features page
- Documentation
- Contact form

### API
- License activation
- Usage tracking
- License validation
- Media upload tracking

---

## 🔐 Security

After installation:
1. **Delete setup-fixed.php** from `/public/` folder
2. **Change admin password** in dashboard
3. **Set up .env file** with production settings
4. **Configure Stripe** with live API keys

---

## 💡 Tips

### If You Get 404 Errors

The `/public/` in URL is required because:
- Hostinger subdomain points to `/s3cloudmedia/`
- Laravel files are in `/s3cloudmedia/public/`
- So URL needs `/public/` to reach them

This is normal and secure!

### If You Want Clean URLs

You can set up a redirect in the root `.htaccess` to automatically add `/public/`:

Create `/public_html/s3cloudmedia/.htaccess`:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

Then URLs will work without `/public/`:
- `https://s3cloudmedia.techknowledgecal.com/admin/login` ✅

---

## 📊 8 Pricing Plans

All configured and ready:

1. **Free** - $0/year - 2,500 files
2. **Bronze** - $39/year - 2,000 files
3. **Silver** - $59/year - 6,000 files
4. **Gold** - $149/year - 20,000 files
5. **Platinum** - $199/year - 40,000 files
6. **Gem** - $349/year - 100,000 files
7. **500K** - $799/year - 500,000 files
8. **Unlimited** - $1,199/year - Unlimited

---

## 🎉 You're Ready!

1. **Upload** license-server folder
2. **Run** setup-fixed.php
3. **Login** to admin dashboard
4. **Configure** Stripe keys
5. **Launch!**

Everything is clean, tested, and ready to use!

---

## 📞 Need Help?

- **Diagnostic tool:** `/public/diagnose.php`
- **Check logs:** `storage/logs/laravel.log`
- **Test API:** `/public/api/v1/check`

---

**This version is production-ready and optimized for Hostinger!** 🚀

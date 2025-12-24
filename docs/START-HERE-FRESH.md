# ⚡ START HERE - Fresh Installation

## 🎯 Quick Start (5 Minutes)

### Step 1: Upload to Hostinger

1. **Zip the `license-server` folder**
2. **Upload to:** `/public_html/s3cloudmedia/`
3. **Extract** the zip file

### Step 2: Setup Database

1. **Edit:** `/public_html/s3cloudmedia/public/setup-fixed.php`
2. **Change lines 9-16:**
   ```php
   $db_name = 'your_database_name';  // Your Hostinger database
   $db_user = 'your_database_user';  // Your database user
   $db_pass = 'your_password';       // Your database password
   $admin_email = 'admin@yourcompany.com';  // Your email
   $admin_password = 'ChangeMe123!';  // Your password
   ```
3. **Save**

### Step 3: Run Setup

Visit: `https://s3cloudmedia.techknowledgecal.com/public/setup-fixed.php`

You'll see:
- ✅ Connected to database
- ✅ All 7 tables created
- ✅ 8 pricing plans inserted
- ✅ Admin user created
- 🎉 Setup Complete!

### Step 4: Login

Visit: `https://s3cloudmedia.techknowledgecal.com/public/admin/login`

Use the email/password you set in Step 2.

---

## ✅ Done!

You now have:
- ✅ Admin dashboard working
- ✅ 8 pricing plans configured
- ✅ Landing page ready
- ✅ API endpoints active
- ✅ Database fully set up

---

## 🔧 Optional: Clean URLs

To remove `/public/` from URLs:

Create `/public_html/s3cloudmedia/.htaccess`:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

Then access via:
- `https://s3cloudmedia.techknowledgecal.com/admin/login` ✅

---

## 📚 Documentation

- **FRESH-INSTALL-GUIDE.md** - Complete installation guide
- **README.md** - System overview
- **QUICK-REFERENCE.md** - Commands & URLs

---

**That's it! Upload, setup, login. Simple!** 🚀

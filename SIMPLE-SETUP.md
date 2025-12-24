# ⚡ Simple Setup - 3 Steps

## Problem
The fancy installer is crashing with HTTP 500 error.

## Solution
Use the simple setup script instead.

---

## Step 1: Edit setup.php

1. Open file: `license-server/public/setup.php`
2. Edit these lines at the top:

```php
$db_host = 'localhost';
$db_port = '3306';
$db_name = 'your_database_name';  // CHANGE THIS
$db_user = 'your_database_user';  // CHANGE THIS
$db_pass = 'your_database_password';  // CHANGE THIS

$admin_name = 'Admin User';
$admin_email = 'admin@yourcompany.com';
$admin_password = 'admin123';  // CHANGE THIS
```

3. Save the file

---

## Step 2: Run setup.php

Visit this URL in your browser:
```
https://s3cloudmedia.techknowledgecal.com/setup.php
```

You'll see:
- ✅ Connected to database
- ✅ Users table created
- ✅ Plans table created
- ✅ Licenses table created
- ✅ 8 pricing plans inserted
- ✅ Admin user created
- 🎉 Setup Complete!

---

## Step 3: Login

Click "Go to Admin Login" or visit:
```
https://s3cloudmedia.techknowledgecal.com/admin/login
```

Login with:
- Email: (what you set in setup.php)
- Password: (what you set in setup.php)

---

## ⚠️ After Login

1. **Delete setup.php** - For security
2. **Change your password** - In admin dashboard
3. **Configure Stripe** - Add your API keys

---

## 🎯 If Still Getting 404

The issue is document root. In Hostinger:

1. Go to **Domains**
2. Click **s3cloudmedia.techknowledgecal.com**
3. Set **Document Root** to: `/public_html/s3cloudmedia/public`
4. Save
5. Wait 2 minutes
6. Try again

---

## ✅ Success!

Once you can login, you'll see:
- Dashboard with stats
- User management
- License management
- 8 pricing plans
- Analytics

Everything is ready to use!

---

**That's it! Simple and works every time.** 🚀

# How to Check and Install Composer Dependencies

## The Problem

Your test is failing with "Test request failed". This usually means the AWS SDK is not installed.

## Quick Check: Is Composer Installed?

### Method 1: Via FTP/cPanel File Manager

1. **Connect to your site** via FTP or cPanel File Manager
2. **Navigate to**: `wp-content/plugins/wp-cloud-media-offload/`
3. **Look for a folder named**: `vendor`
4. **Check the result**:
   - ✅ **Folder EXISTS** → Composer is installed, go to Step 2
   - ❌ **Folder MISSING** → You need to install Composer dependencies

### Method 2: Via SSH (If you have access)

```bash
# SSH into your server
ssh username@artstationers.com

# Navigate to plugin directory
cd /path/to/wordpress/wp-content/plugins/wp-cloud-media-offload/

# Check if vendor folder exists
ls -la | grep vendor

# If you see "vendor" folder → Composer is installed ✅
# If you don't see it → You need to install ❌
```

---

## How to Install Composer Dependencies

### Option 1: Install Locally, Then Upload (EASIEST)

This is the easiest method if you don't have SSH access to your server.

#### Step 1: Install on Your Computer

```bash
# Open Terminal on your Mac
cd /path/to/your/project/wp-cloud-media-offload

# Install dependencies
composer install --no-dev

# This creates a "vendor" folder with AWS SDK
```

#### Step 2: Upload to Your Server

1. **Via FTP**:
   - Connect to your site
   - Navigate to: `wp-content/plugins/wp-cloud-media-offload/`
   - Upload the entire `vendor` folder
   - This may take 5-10 minutes (it's a large folder)

2. **Via cPanel File Manager**:
   - Zip the vendor folder on your computer first
   - Upload the zip file
   - Extract it on the server

#### Step 3: Test Again

Go back to WordPress and test the connection.

---

### Option 2: Install Directly on Server via SSH (FASTEST)

If you have SSH access:

```bash
# SSH into your server
ssh username@artstationers.com

# Navigate to plugin directory
cd /path/to/wordpress/wp-content/plugins/wp-cloud-media-offload/

# Install Composer (if not already installed)
curl -sS https://getcomposer.org/installer | php

# Install dependencies
php composer.phar install --no-dev --optimize-autoloader

# Or if composer is globally installed:
composer install --no-dev --optimize-autoloader
```

---

### Option 3: Ask Your Hosting Provider

Some hosting providers can install Composer dependencies for you:

1. **Contact your hosting support**
2. **Tell them**: "Please run `composer install` in the directory: `wp-content/plugins/wp-cloud-media-offload/`"
3. **They should be able to do this for you**

---

## After Installing Composer

### Verify Installation

Check that these files exist:

```
wp-content/plugins/wp-cloud-media-offload/
├── vendor/
│   ├── autoload.php  ← This file must exist
│   ├── aws/
│   │   └── aws-sdk-php/  ← This folder must exist
│   └── composer/
```

### Test the Connection Again

1. Go to **Cloud Media → Settings**
2. Make sure **Wasabi** is selected
3. Enter your credentials:
   - Access Key: `PL920JBLUQWDQ1XRP4BM`
   - Secret Key: `w4QsvaOAD5PvqKFwZ0yheRNnUBVMf6euH3zsp3DK`
   - Region: `us-east-1`
   - Bucket: `artstationers-wp`
4. Click **"Test Wasabi Connection"**
5. Should now work! ✅

---

## Still Not Working?

If Composer is installed but test still fails, check:

### 1. PHP Version

Wasabi plugin requires PHP 7.4+

**Check via SSH:**
```bash
php -v
```

**Check in WordPress:**
- Go to **Tools → Site Health**
- Look for PHP version

### 2. PHP Extensions

Required extensions:
- `curl`
- `json`
- `mbstring`
- `xml`

**Check via SSH:**
```bash
php -m | grep -E 'curl|json|mbstring|xml'
```

### 3. WordPress Debug Mode

Enable debug mode to see detailed errors:

**Edit `wp-config.php`:**
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

**Then check**: `wp-content/debug.log` for errors

### 4. Browser Console Errors

The errors I see in your screenshot:
```
load-scripts.php?c=0...hookVer=6.8.3:5
```

These are WordPress core script errors, not related to Wasabi. They won't affect the connection test.

The real issue is likely the missing AWS SDK.

---

## Quick Test: Do You Have Composer on Your Computer?

```bash
# Open Terminal
composer --version

# If you see version number → Composer is installed ✅
# If you see "command not found" → Install Composer first
```

### Install Composer on Mac:

```bash
# Using Homebrew (easiest)
brew install composer

# Or download directly
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

---

## Summary: What You Need to Do

1. **Check if `vendor` folder exists** in plugin directory
2. **If NO** → Install Composer dependencies (Option 1 recommended)
3. **If YES** → Check if `vendor/aws/aws-sdk-php/` exists
4. **Test connection again** in WordPress

The "Test request failed" error is almost certainly because the AWS SDK is missing.

Once you install Composer dependencies, the test should work immediately! 🚀

---

## Need Help?

Tell me:
1. Do you have SSH access to your server?
2. Do you have Composer installed on your Mac?
3. Can you see the `vendor` folder in the plugin directory?

I'll give you exact commands based on your situation!

# 🚀 Install Composer on Mac - Step by Step

## Method 1: Automatic Installation (Easiest!)

### Step 1: Run the Installation Script

```bash
# Open Terminal (Cmd + Space, type "Terminal")
cd "/Users/haider/Desktop/S3 Plugin"

# Run the installer
bash install-composer.sh
```

This will:

1. Install Homebrew (if not installed)
2. Install PHP
3. Install Composer
4. Test everything

**Time:** 5-10 minutes

---

## Method 2: Manual Installation

### Step 1: Install Homebrew

```bash
# Open Terminal
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

Follow the prompts:

- Press Enter when asked
- Enter your Mac password when prompted
- Wait 5-10 minutes

### Step 2: Add Homebrew to PATH (Apple Silicon Macs only)

```bash
# If you have M1/M2/M3 Mac, run this:
echo 'eval "$(/opt/homebrew/bin/brew shellenv)"' >> ~/.zprofile
eval "$(/opt/homebrew/bin/brew shellenv)"
```

### Step 3: Install PHP

```bash
brew install php
```

Wait 2-3 minutes.

### Step 4: Install Composer

```bash
brew install composer
```

Wait 1-2 minutes.

### Step 5: Verify Installation

```bash
php -v
composer --version
```

You should see:

```
PHP 8.x.x
Composer version 2.x.x
```

---

## Method 3: Direct Composer Download (Alternative)

If Homebrew doesn't work:

```bash
# Download Composer installer
cd ~
curl -sS https://getcomposer.org/installer -o composer-setup.php

# Install Composer
php composer-setup.php

# Move to system path
sudo mv composer.phar /usr/local/bin/composer

# Make executable
sudo chmod +x /usr/local/bin/composer

# Clean up
rm composer-setup.php

# Test
composer --version
```

---

## Troubleshooting

### "command not found: brew"

Homebrew not installed. Run:

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

### "command not found: php"

After installing, close and reopen Terminal, then:

```bash
brew install php
```

### "command not found: composer"

After installing, close and reopen Terminal, then:

```bash
brew install composer
```

### "Permission denied"

Use sudo:

```bash
sudo brew install php
sudo brew install composer
```

### Still not working?

1. Close Terminal completely
2. Reopen Terminal
3. Try again

---

## After Installation

### Test PHP

```bash
php -v
```

Should show: `PHP 8.x.x`

### Test Composer

```bash
composer --version
```

Should show: `Composer version 2.x.x`

### Update PATH (if needed)

Add to `~/.zshrc`:

```bash
echo 'export PATH="/usr/local/bin:$PATH"' >> ~/.zshrc
source ~/.zshrc
```

---

## Next Steps

Once Composer is installed:

### Option A: Use XAMPP (Recommended for beginners)

1. Download XAMPP: https://www.apachefriends.org/download.html
2. Install XAMPP
3. Follow: `license-server/XAMPP-INSTALLATION.md`

### Option B: Deploy to Hostinger

1. Install dependencies locally:

   ```bash
   cd license-server
   composer install --no-dev
   ```

2. Upload to Hostinger
3. Follow: `license-server/HOSTINGER-DEPLOYMENT.md`

---

## Quick Reference

### Check if installed:

```bash
which php
which composer
```

### Update Composer:

```bash
composer self-update
```

### Update PHP:

```bash
brew upgrade php
```

---

## Common Commands

```bash
# Install dependencies
composer install

# Update dependencies
composer update

# Show version
composer --version

# Get help
composer help
```

---

## Success!

If you see version numbers for both PHP and Composer, you're ready!

**Next:** Follow `START-HERE.md` to set up the license server.

---

## Need Help?

**Homebrew Issues:**

- Visit: https://brew.sh
- Check: `brew doctor`

**Composer Issues:**

- Visit: https://getcomposer.org
- Check: `composer diagnose`

**Still Stuck?**

- Try Method 3 (Direct Download)
- Or use Hostinger (no local install needed)

---

**Ready?** Run the installation script or follow Method 2! 🚀

# 📝 Exact Steps to Fix artstationers.com

## The Issue
Plugin works on flexiology.store but not on artstationers.com, even though test connection succeeds on both.

## The Cause
**Auto Upload checkbox is not checked on artstationers.com**

## The Fix

### Step 1: Login to artstationers.com
```
URL: https://artstationers.com/wp-admin
Username: [your admin username]
Password: [your admin password]
```

### Step 2: Navigate to Plugin Settings
```
Click: Dashboard (left sidebar)
Click: Cloud Media (in sidebar)
Click: Settings (submenu)
```

### Step 3: Verify Provider Selection
```
Look at: Provider cards at the top
Ensure: AWS S3 card is highlighted/selected
If not: Click on the AWS S3 card
```

### Step 4: Check Credentials
```
Verify these fields are filled:
- Access Key: [should show your key]
- Secret Key: [should show your key]
- Region: [should match flexiology - probably us-east-1]
- Bucket Name: [should show your bucket name]
```

### Step 5: Enable Auto Upload (THE FIX!)
```
Scroll down to: "Upload Settings" section
Look for: ☐ Auto Upload checkbox
Current state: Probably UNCHECKED
Action: CHECK THE BOX → ☑ Auto Upload
```

### Step 6: Save Settings
```
Scroll to bottom
Click: "Save Settings" button (blue button)
Wait for: "✅ Settings saved successfully!" message
Important: Don't close the page immediately
```

### Step 7: Verify Settings Saved
```
Refresh the page (F5 or Ctrl+R)
Check: Auto Upload checkbox is still checked
If not: Repeat steps 5-6
```

### Step 8: Test Connection (Optional)
```
Click: "Test Connection" button
Should show: "✅ Connection successful!"
If fails: Check credentials again
```

### Step 9: Test Upload
```
Go to: Media → Add New (in sidebar)
Click: "Select Files" button
Choose: A small test image (test.jpg)
Click: "Upload"
Wait: For upload to complete
```

### Step 10: Verify Upload to S3
```
Option A: Check S3 Bucket
- Login to AWS Console
- Go to S3
- Open your bucket
- Navigate to: wp-content/uploads/2025/11/
- Look for: Your test image

Option B: Check Plugin Dashboard
- Go to: Cloud Media → Dashboard
- Look at: Upload statistics
- Should show: 1 new upload
```

---

## If It Still Doesn't Work

### Use the Diagnostic Tool

#### Step 1: Get the Diagnostic File
```
Location: wp-cloud-media-offload/check-auto-upload.php
(It's inside the plugin folder)
```

#### Step 2: Upload to WordPress Root
```
Using FTP or cPanel File Manager:
1. Connect to artstationers.com
2. Navigate to: public_html (or www or httpdocs)
3. Upload: check-auto-upload.php
4. Place it in: Root directory (same level as wp-config.php)
```

#### Step 3: Access the Diagnostic Tool
```
URL: https://artstationers.com/check-auto-upload.php
Browser: Open in new tab
Login: You must be logged in as admin
```

#### Step 4: Review Results
```
Look for: Red ❌ marks
These indicate: Problems that need fixing
Common issues:
- ❌ Auto Upload is disabled
- ❌ Settings not found
- ❌ Database table missing
- ❌ Provider not set
```

#### Step 5: Fix Issues
```
For each red ❌ mark:
1. Read the description
2. Follow the suggested fix
3. Go back to Settings
4. Make the change
5. Save Settings
6. Refresh diagnostic page
7. Verify it's now green ✅
```

---

## Enable Debug Mode (If Needed)

### Step 1: Access wp-config.php
```
Using FTP or cPanel File Manager:
1. Connect to artstationers.com
2. Navigate to: Root directory
3. Find: wp-config.php
4. Download: Backup copy first!
5. Edit: The file
```

### Step 2: Add Debug Lines
```
Find this line:
/* That's all, stop editing! Happy publishing. */

Add BEFORE that line:
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

### Step 3: Save and Upload
```
Save: The file
Upload: Back to server (overwrite)
```

### Step 4: Test Upload
```
Go to: Media → Add New
Upload: A test image
```

### Step 5: Check Debug Log
```
Using FTP or cPanel File Manager:
1. Navigate to: wp-content/
2. Find: debug.log
3. Download: The file
4. Open: In text editor
5. Search for: "WPCMO:"
```

### Step 6: Interpret Log
```
If you see:
"WPCMO: Auto upload is disabled"
→ Go enable Auto Upload in Settings

"WPCMO: Failed to get storage handler"
→ Check provider selection

"WPCMO: Upload failed: [error]"
→ Check credentials and permissions

"WPCMO: Upload result: Array ( [success] => 1 )"
→ It's working! Check S3 bucket
```

---

## Comparison with Working Site

### On flexiology.store (working):
```
1. Login to flexiology.store/wp-admin
2. Go to: Cloud Media → Settings
3. Take screenshot of entire page
4. Note especially:
   - Which provider card is selected
   - Auto Upload checkbox state: ☑ CHECKED
   - All credential fields
   - All other checkboxes
```

### On artstationers.com (not working):
```
1. Login to artstationers.com/wp-admin
2. Go to: Cloud Media → Settings
3. Compare with flexiology screenshot
4. Make EVERYTHING identical:
   - Same provider selected
   - Same Auto Upload state: ☑ CHECKED
   - Same credentials (but for different bucket)
   - Same other settings
```

---

## Quick Checklist

Before testing upload, verify:

- [ ] Plugin is activated
- [ ] Went to Cloud Media → Settings
- [ ] Provider card is selected (AWS S3)
- [ ] Access Key is entered
- [ ] Secret Key is entered
- [ ] Region is correct
- [ ] Bucket name is correct
- [ ] **Auto Upload is CHECKED** ← Most important!
- [ ] Clicked "Save Settings"
- [ ] Saw success message
- [ ] Refreshed page to verify
- [ ] Test connection succeeds

---

## Expected Result

After checking Auto Upload and saving:

1. **Upload a test image**
   - Go to Media → Add New
   - Upload test.jpg
   - Should complete normally

2. **Check S3 bucket**
   - Login to AWS Console
   - Go to your bucket
   - Navigate to wp-content/uploads/2025/11/
   - Should see test.jpg and its thumbnails

3. **Check Media Library**
   - Go to Media → Library
   - Should see the test image
   - Image should display properly

4. **Check Plugin Dashboard**
   - Go to Cloud Media → Dashboard
   - Should show upload statistics
   - Should show recent uploads

---

## Still Having Issues?

If after following ALL these steps it still doesn't work:

1. **Run the diagnostic tool** - It will tell you exactly what's wrong
2. **Enable debug mode** - Check debug.log for error messages
3. **Compare with flexiology** - Make sure settings are identical
4. **Check for conflicts** - Disable other plugins temporarily
5. **Verify S3 permissions** - Make sure bucket allows uploads

---

## Summary

**The fix is simple**: Check the Auto Upload box in Settings and save.

**Why it happens**: Each WordPress site has its own database and settings. The checkbox state is not shared between sites.

**How to verify**: Use the diagnostic tool or enable debug mode.

**Expected time**: 30 seconds to fix, if it's just the checkbox.

---

## Contact Info

If you need help:
1. Run the diagnostic tool first
2. Enable debug mode
3. Check debug.log for WPCMO messages
4. Take screenshots of Settings page
5. Note any error messages

The diagnostic tool will show you exactly what's different between the two sites!

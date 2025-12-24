# Complete Workflow: From Setup to First Upload

End-to-end guide showing how everything works together.

## Overview

This guide shows the complete flow from deploying the license server to uploading your first media file to Wasabi.

---

## Phase 1: Deploy License Server

**Time**: 30 minutes  
**Cost**: $20/month

### Quick Steps:

1. ✅ Sign up for Laravel Forge ($12/month)
2. ✅ Connect DigitalOcean account ($6/month server)
3. ✅ Create server in Forge
4. ✅ Push code to GitHub
5. ✅ Deploy site in Forge
6. ✅ Configure environment variables
7. ✅ Run database migrations
8. ✅ Enable SSL certificate
9. ✅ Point domain to server

**Result**: License server live at `https://licenses.yourdomain.com`

📖 **Detailed Guide**: See `QUICK-DEPLOY-LICENSE-SERVER.md`

---

## Phase 2: Configure Wasabi Storage

**Time**: 10 minutes  
**Cost**: $6.99/month (1TB storage)

### Steps:

1. ✅ Create Wasabi account at https://wasabi.com
2. ✅ Create access keys in Wasabi console
3. ✅ Create storage bucket
4. ✅ Configure bucket permissions (public read)
5. ✅ Note down:
   - Access Key
   - Secret Key
   - Region
   - Bucket Name

**Result**: Wasabi bucket ready to receive files

📖 **Detailed Guide**: See `wp-cloud-media-offload/docs/WASABI-SETUP-GUIDE.md`

---

## Phase 3: Install WordPress Plugin

**Time**: 5 minutes  
**Cost**: Free (your plugin)

### Steps:

1. ✅ Upload plugin to WordPress:
   ```
   wp-content/plugins/wp-cloud-media-offload/
   ```

2. ✅ Install Composer dependencies:
   ```bash
   cd wp-content/plugins/wp-cloud-media-offload
   composer install
   ```

3. ✅ Activate plugin in WordPress admin

4. ✅ Update license server URL in code:
   - Edit `includes/License/Manager.php`
   - Edit `includes/Core/MediaHandler.php`
   - Change to: `https://licenses.yourdomain.com/api/v1`

**Result**: Plugin installed and ready to configure

---

## Phase 4: Create License Key

**Time**: 2 minutes  
**Cost**: Free (you control pricing)

### Steps:

1. ✅ Log into license server admin:
   ```
   https://licenses.yourdomain.com/admin
   Email: admin@yourcompany.com
   Password: password (change this!)
   ```

2. ✅ Go to "Licenses" → "Create License"

3. ✅ Fill in:
   - **User**: Select admin user
   - **Plan**: Gold (20,000 uploads/year)
   - **Status**: Active
   - **Expires**: 1 year from now

4. ✅ Click "Create"

5. ✅ Copy license key: `XXXX-XXXX-XXXX-XXXX`

**Result**: Valid license key ready to use

---

## Phase 5: Activate License in WordPress

**Time**: 1 minute

### Steps:

1. ✅ In WordPress, go to: **Cloud Media → License**

2. ✅ Paste license key

3. ✅ Click "Activate License"

4. ✅ Should see: ✅ "License activated successfully!"

5. ✅ Verify license info displays:
   - Plan: Gold
   - Status: Active
   - Uploads: 0 / 20,000
   - Expires: [date]

**Result**: WordPress plugin connected to license server

---

## Phase 6: Configure Wasabi in WordPress

**Time**: 3 minutes

### Steps:

1. ✅ Go to: **Cloud Media → Settings**

2. ✅ Click **Wasabi** provider card

3. ✅ Enter credentials:
   - **Access Key**: Your Wasabi access key
   - **Secret Key**: Your Wasabi secret key
   - **Region**: Your bucket region
   - **Bucket Name**: Your bucket name

4. ✅ Click **"🔌 Test Wasabi Connection"**

5. ✅ Should see: ✅ "Wasabi connection successful! All tests passed."

6. ✅ Enable settings:
   - ☑️ **Auto Upload New Media**: Checked
   - ☐ **Remove Local Files**: Unchecked (for now)

7. ✅ Click **"💾 Save Settings"**

**Result**: WordPress connected to Wasabi, ready to upload

📖 **Troubleshooting**: See `WASABI-TEST-GUIDE.md`

---

## Phase 7: Test First Upload

**Time**: 2 minutes

### Steps:

1. ✅ Go to: **Media → Add New**

2. ✅ Upload a test image (e.g., `test-image.jpg`)

3. ✅ Wait for upload to complete

4. ✅ Check WordPress Media Library:
   - Image should display normally
   - Right-click image → "Copy image address"
   - URL should be: `https://your-bucket.s3.wasabisys.com/...`

5. ✅ Check Wasabi Console:
   - Log into https://console.wasabisys.com
   - Go to your bucket
   - Should see: `wp-content/uploads/2024/11/test-image.jpg`

6. ✅ Check License Server:
   - Log into admin dashboard
   - Go to "Licenses"
   - Your license should show: 1 / 20,000 uploads

**Result**: First file successfully uploaded to Wasabi! 🎉

---

## Phase 8: Migrate Existing Media (Optional)

**Time**: Varies (depends on media count)

### Steps:

1. ✅ Go to: **Cloud Media → Bulk Upload**

2. ✅ Review stats:
   - Total media files: [count]
   - Estimated time: [time]

3. ✅ Click **"Start Upload"**

4. ✅ Monitor progress:
   - Progress bar shows completion
   - Uploaded count increases
   - Failed count (if any)

5. ✅ Wait for completion

6. ✅ Verify in Wasabi bucket:
   - All files should be present
   - Organized by date: `wp-content/uploads/YYYY/MM/`

**Result**: All existing media migrated to Wasabi

---

## Complete System Flow

Here's how everything works together:

### Upload Flow:

```
1. User uploads image in WordPress
   ↓
2. WordPress receives file
   ↓
3. Plugin intercepts upload
   ↓
4. Plugin checks license status
   ├─ Invalid → Upload blocked
   └─ Valid → Continue
   ↓
5. Plugin uploads to Wasabi
   ├─ Success → Continue
   └─ Fail → Keep local copy
   ↓
6. Plugin tracks upload with license server
   ├─ Increments usage counter
   └─ Checks if limit reached
   ↓
7. Plugin updates WordPress database
   ├─ Stores Wasabi URL
   └─ Links to attachment
   ↓
8. (Optional) Plugin deletes local file
   ↓
9. WordPress displays image from Wasabi
```

### License Validation Flow:

```
1. WordPress plugin needs to validate license
   ↓
2. Plugin sends API request to license server
   POST https://licenses.yourdomain.com/api/v1/check
   {
     "license_key": "XXXX-XXXX-XXXX-XXXX",
     "domain": "https://yoursite.com"
   }
   ↓
3. License server checks database
   ├─ License exists?
   ├─ Status is active?
   ├─ Not expired?
   ├─ Domain matches?
   └─ Under usage limit?
   ↓
4. License server responds
   {
     "valid": true,
     "plan": "Gold",
     "uploads_used": 150,
     "uploads_limit": 20000,
     "expires_at": "2025-11-01"
   }
   ↓
5. Plugin caches result (24 hours)
   ↓
6. Plugin allows/blocks upload based on response
```

### Usage Tracking Flow:

```
1. File uploaded to Wasabi successfully
   ↓
2. Plugin sends tracking request
   POST https://licenses.yourdomain.com/api/v1/track-upload
   {
     "license_key": "XXXX-XXXX-XXXX-XXXX",
     "domain": "https://yoursite.com",
     "file_name": "image.jpg",
     "file_size": 245760,
     "file_type": "image/jpeg"
   }
   ↓
3. License server records upload
   ├─ Increments usage counter
   ├─ Stores upload metadata
   └─ Checks if limit reached
   ↓
4. License server responds
   {
     "success": true,
     "uploads_used": 151,
     "uploads_remaining": 19849,
     "limit_reached": false
   }
   ↓
5. Plugin updates local cache
   ↓
6. If limit reached, plugin shows admin notice
```

---

## Architecture Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                     WordPress Site                          │
│  ┌────────────────────────────────────────────────────┐    │
│  │         WP Cloud Media Offload Plugin              │    │
│  │  ┌──────────────┐  ┌──────────────┐               │    │
│  │  │   License    │  │    Wasabi    │               │    │
│  │  │   Manager    │  │   Handler    │               │    │
│  │  └──────┬───────┘  └──────┬───────┘               │    │
│  │         │                  │                        │    │
│  │         │                  │                        │    │
│  └─────────┼──────────────────┼────────────────────────┘    │
│            │                  │                              │
└────────────┼──────────────────┼──────────────────────────────┘
             │                  │
             │                  │
    ┌────────▼────────┐  ┌──────▼──────────┐
    │                 │  │                  │
    │ License Server  │  │  Wasabi Storage  │
    │                 │  │                  │
    │ ┌─────────────┐ │  │ ┌──────────────┐│
    │ │   Laravel   │ │  │ │   S3 Bucket  ││
    │ │     API     │ │  │ │              ││
    │ └─────────────┘ │  │ │  - Images    ││
    │                 │  │ │  - Videos    ││
    │ ┌─────────────┐ │  │ │  - Files     ││
    │ │   MySQL     │ │  │ └──────────────┘│
    │ │  Database   │ │  │                  │
    │ └─────────────┘ │  └──────────────────┘
    │                 │
    │ ┌─────────────┐ │
    │ │   Stripe    │ │
    │ │  Payments   │ │
    │ └─────────────┘ │
    │                 │
    └─────────────────┘
```

---

## Verification Checklist

After completing all phases, verify everything works:

### License Server:
- [ ] Admin dashboard accessible
- [ ] Can create licenses
- [ ] API endpoints respond
- [ ] Stripe webhook configured
- [ ] SSL certificate active
- [ ] Email notifications work

### WordPress Plugin:
- [ ] Plugin activated
- [ ] License activated successfully
- [ ] License info displays correctly
- [ ] Settings saved properly

### Wasabi Integration:
- [ ] Connection test passes
- [ ] Test upload succeeds
- [ ] File appears in Wasabi bucket
- [ ] Image displays from Wasabi URL
- [ ] Usage tracked in license server

### Complete Flow:
- [ ] Upload image in WordPress
- [ ] File goes to Wasabi
- [ ] Usage counter increments
- [ ] Image displays on website
- [ ] No errors in logs

---

## Monitoring & Maintenance

### Daily:
- Check WordPress error logs
- Monitor upload success rate
- Review license server logs

### Weekly:
- Check Wasabi storage usage
- Review license activations
- Monitor server performance

### Monthly:
- Review usage statistics
- Check for failed uploads
- Update dependencies
- Backup databases

---

## Costs Summary

| Service | Monthly Cost | Annual Cost |
|---------|--------------|-------------|
| Laravel Forge | $12 | $144 |
| DigitalOcean Server | $6 | $72 |
| Wasabi Storage (1TB) | $6.99 | $83.88 |
| Domain Name | ~$1 | $12 |
| SSL Certificate | Free | Free |
| **Total** | **~$26** | **~$312** |

**Plus**: Stripe fees (2.9% + $0.30 per transaction)

---

## Scaling Considerations

### When You Grow:

**100+ Customers:**
- Upgrade DigitalOcean to $12/month (2GB RAM)
- Enable Redis caching
- Set up database backups

**500+ Customers:**
- Upgrade to $24/month server (4GB RAM)
- Add load balancer
- Use managed database
- Implement CDN

**1000+ Customers:**
- Multiple app servers
- Database replication
- Queue workers on separate server
- Professional monitoring

---

## Troubleshooting Common Issues

### Upload Fails:
1. Check license is active
2. Verify Wasabi credentials
3. Check usage limit not reached
4. Review WordPress error log

### License Won't Activate:
1. Check license server is online
2. Verify API URL in plugin code
3. Check license key is valid
4. Review license server logs

### Images Don't Display:
1. Check Wasabi bucket permissions
2. Verify URL in database
3. Check CORS settings
4. Test direct Wasabi URL

---

## Success Metrics

Track these to measure success:

- **Upload Success Rate**: Should be >99%
- **API Response Time**: Should be <500ms
- **License Activation Rate**: Track conversions
- **Customer Satisfaction**: Monitor support tickets
- **Server Uptime**: Should be >99.9%
- **Storage Costs**: Monitor Wasabi usage

---

## Next Steps

Now that everything is working:

1. **Customize branding** on license server
2. **Set up email templates** for notifications
3. **Create documentation** for customers
4. **Test payment flow** with Stripe test mode
5. **Switch to live mode** when ready
6. **Market your plugin** to customers
7. **Monitor and optimize** performance

---

## Support & Resources

- **License Server**: `LICENSE-SERVER-DEPLOYMENT-GUIDE.md`
- **Wasabi Setup**: `WASABI-SETUP-GUIDE.md`
- **Connection Testing**: `WASABI-TEST-GUIDE.md`
- **Upload Behavior**: `UPLOAD-BEHAVIOR-EXPLAINED.md`

---

## Congratulations! 🎉

You now have a complete, professional WordPress plugin with:

✅ Cloud storage (Wasabi)
✅ License management system
✅ Payment processing (Stripe)
✅ Usage tracking
✅ Admin dashboard
✅ Automatic uploads
✅ Secure infrastructure

Your plugin is ready to sell and scale! 🚀

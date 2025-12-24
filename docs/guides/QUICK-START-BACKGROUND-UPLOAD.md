# Quick Start: Background Upload (50k Images)

## 🚀 5-Minute Setup

### Step 1: Configure (2 minutes)
```
WordPress Admin → Cloud Media → Settings
✅ Enter AWS credentials
✅ Select bucket & region
✅ Enable "Auto Upload"
❌ Keep "Remove Local Files" DISABLED
💾 Save Settings
🧪 Click "Test Connection"
```

### Step 2: Start Upload (1 minute)
```
WordPress Admin → Cloud Media → Bulk Upload
🤖 Click "Background Upload" button
✅ Confirm dialog
🎉 Done! Upload started
```

### Step 3: Close Browser (optional)
```
✅ You can close browser now
✅ Upload runs automatically on server
✅ Come back later to check progress
```

### Step 4: Check Progress (anytime)
```
WordPress Admin → Cloud Media → Bulk Upload
📊 See progress bar
🔄 Auto-updates every 10 seconds
```

## ⏱️ Time Estimates

| Images | Time |
|--------|------|
| 10,000 | ~16 hours |
| 50,000 | ~3.5 days |
| 100,000 | ~7 days |

## 🎛️ Controls

**Start**: Click "🤖 Background Upload"
**Stop**: Click "⏹️ Stop Background Upload"
**Resume**: Click "🤖 Background Upload" again
**Monitor**: Refresh Bulk Upload page

## ⚙️ How It Works

- Runs via WordPress WP-Cron
- Processes 10 images every minute
- Uploads main image + all thumbnails
- Gentle on server CPU
- Auto-resumes if server restarts

## 🔧 If WP-Cron is Disabled

Add to cPanel Cron Jobs:
```bash
*/1 * * * * curl https://yoursite.com/wp-cron.php?doing_wp_cron >/dev/null 2>&1
```

## ✅ Success Indicators

- Green status box visible
- Progress bar moving
- Numbers updating
- No errors in logs

## 🚨 Troubleshooting

**Not starting?**
- Check WP-Cron is enabled
- Verify admin permissions
- Check browser console (F12)

**Not progressing?**
- Wait 2-3 minutes
- Refresh page
- Check error logs

**Too slow?**
- This is normal (10 images/min)
- For faster, use Quick Upload

## 📞 Support

**Logs**: `/wp-content/debug.log`
**Plugin**: v1.0.5
**Website**: https://offloadmedia.hntechs.com

---

**That's it!** Your 50k images will upload automatically in ~3.5 days. 🎉

# AWS Setup Guide for WP Cloud Media Offload

Complete guide to setting up AWS S3 and CloudFront for your WordPress media files.

## Prerequisites

- AWS Account (create at aws.amazon.com)
- Credit card for AWS billing
- Basic understanding of AWS services

## Part 1: S3 Bucket Setup

### Step 1: Create S3 Bucket

1. Log in to AWS Console
2. Navigate to S3 service
3. Click "Create bucket"

### Step 2: Configure Bucket

**Basic Settings:**
- **Bucket name**: Choose unique name (e.g., `mysite-media-files`)
- **Region**: Choose closest to your audience
  - US East (N. Virginia) - `us-east-1`
  - US West (Oregon) - `us-west-2`
  - EU (Ireland) - `eu-west-1`
  - Asia Pacific (Singapore) - `ap-southeast-1`

**Block Public Access:**
- ⚠️ **UNCHECK** "Block all public access"
- Check the acknowledgment box
- We need public read access for media files

**Bucket Versioning:**
- Optional: Enable for file version history

**Tags:**
- Optional: Add tags for organization

**Default Encryption:**
- Optional: Enable for security

Click "Create bucket"

### Step 3: Configure Bucket Policy

1. Select your bucket
2. Go to "Permissions" tab
3. Scroll to "Bucket policy"
4. Click "Edit"
5. Paste this policy:

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "PublicReadGetObject",
            "Effect": "Allow",
            "Principal": "*",
            "Action": "s3:GetObject",
            "Resource": "arn:aws:s3:::YOUR-BUCKET-NAME/*"
        }
    ]
}
```

Replace `YOUR-BUCKET-NAME` with your actual bucket name.

### Step 4: Configure CORS (Optional)

If you'll access files from different domains:

1. Go to "Permissions" tab
2. Scroll to "Cross-origin resource sharing (CORS)"
3. Click "Edit"
4. Paste:

```json
[
    {
        "AllowedHeaders": ["*"],
        "AllowedMethods": ["GET", "HEAD"],
        "AllowedOrigins": ["*"],
        "ExposeHeaders": []
    }
]
```

## Part 2: IAM User Setup

### Step 1: Create IAM User

1. Navigate to IAM service
2. Click "Users" in sidebar
3. Click "Add users"
4. Enter username: `wp-media-offload`
5. Select "Access key - Programmatic access"
6. Click "Next: Permissions"

### Step 2: Set Permissions

**Option A: Attach Policy Directly**

1. Click "Attach existing policies directly"
2. Click "Create policy"
3. Choose JSON tab
4. Paste this policy:

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "WPMediaOffloadPermissions",
            "Effect": "Allow",
            "Action": [
                "s3:PutObject",
                "s3:PutObjectAcl",
                "s3:GetObject",
                "s3:GetObjectAcl",
                "s3:DeleteObject",
                "s3:ListBucket",
                "s3:GetBucketLocation"
            ],
            "Resource": [
                "arn:aws:s3:::YOUR-BUCKET-NAME",
                "arn:aws:s3:::YOUR-BUCKET-NAME/*"
            ]
        }
    ]
}
```

5. Click "Next: Tags"
6. Click "Next: Review"
7. Name: `WPMediaOffloadPolicy`
8. Click "Create policy"
9. Go back to user creation
10. Refresh policies and select `WPMediaOffloadPolicy`

**Option B: Use Existing Policy (Less Secure)**

1. Search for `AmazonS3FullAccess`
2. Check the box
3. ⚠️ Note: This gives full S3 access, not recommended for production

### Step 3: Review and Create

1. Click "Next: Tags" (optional)
2. Click "Next: Review"
3. Review settings
4. Click "Create user"

### Step 4: Save Credentials

⚠️ **IMPORTANT**: This is your only chance to see the secret key!

1. Copy **Access key ID**
2. Copy **Secret access key**
3. Store securely (password manager recommended)
4. Click "Download .csv" for backup

## Part 3: CloudFront Setup (Recommended)

### Why CloudFront?

- Faster global content delivery
- Reduced S3 costs
- Better performance
- HTTPS support
- Custom domain support

### Step 1: Create Distribution

1. Navigate to CloudFront service
2. Click "Create Distribution"
3. Click "Get Started" under Web

### Step 2: Configure Origin

**Origin Settings:**
- **Origin Domain Name**: Select your S3 bucket from dropdown
- **Origin Path**: Leave empty
- **Origin ID**: Auto-filled
- **Restrict Bucket Access**: No (we have bucket policy)
- **Origin Custom Headers**: Leave empty

### Step 3: Configure Default Cache Behavior

**Viewer:**
- **Viewer Protocol Policy**: Redirect HTTP to HTTPS
- **Allowed HTTP Methods**: GET, HEAD
- **Cached HTTP Methods**: GET, HEAD

**Cache Settings:**
- **Cache Policy**: CachingOptimized
- **Origin Request Policy**: None
- **Compress Objects Automatically**: Yes

### Step 4: Configure Distribution Settings

**Settings:**
- **Price Class**: Choose based on budget
  - All Edge Locations (Best Performance)
  - US, Canada, Europe
  - US, Canada, Europe, Asia
- **Alternate Domain Names (CNAMEs)**: Optional custom domain
- **SSL Certificate**: Default CloudFront Certificate
- **Default Root Object**: Leave empty
- **Logging**: Optional (recommended for analytics)

Click "Create Distribution"

### Step 5: Wait for Deployment

- Status will show "In Progress"
- Takes 15-20 minutes
- Status changes to "Deployed" when ready
- Copy the **Domain Name** (e.g., `d111111abcdef8.cloudfront.net`)

### Step 6: Test CloudFront

After deployment, test with:
```
https://YOUR-CLOUDFRONT-DOMAIN/test-file.jpg
```

## Part 4: Plugin Configuration

### Step 1: Enter AWS Credentials

In WordPress:
1. Go to Cloud Media > Settings
2. Enter:
   - **Access Key ID**: From IAM user
   - **Secret Access Key**: From IAM user
   - **Region**: Your bucket region
   - **Bucket Name**: Your bucket name

### Step 2: Test Connection

1. Click "Test Connection"
2. Should show "Connection successful"
3. If fails, check credentials and permissions

### Step 3: Configure CloudFront

1. Enable "CloudFront CDN"
2. Enter CloudFront domain (without https://)
3. Example: `d111111abcdef8.cloudfront.net`

### Step 4: Save Settings

Click "Save Settings"

## Cost Estimation

### S3 Costs (us-east-1)

**Storage:**
- First 50 TB: $0.023 per GB/month
- 25,000 images (avg 100KB): ~2.5 GB = $0.06/month
- 250,000 images (avg 100KB): ~25 GB = $0.58/month

**Requests:**
- PUT requests: $0.005 per 1,000
- GET requests: $0.0004 per 1,000

**Data Transfer:**
- First 1 GB: Free
- Next 9.999 TB: $0.09 per GB

### CloudFront Costs

**Data Transfer:**
- First 10 TB: $0.085 per GB
- Next 40 TB: $0.080 per GB

**Requests:**
- HTTP: $0.0075 per 10,000
- HTTPS: $0.010 per 10,000

### Example Monthly Cost

**Small Site (25,000 images, 100K visitors):**
- S3 Storage: $0.06
- S3 Requests: $0.50
- CloudFront Transfer: $8.50
- **Total: ~$9/month**

**Large Site (250,000 images, 1M visitors):**
- S3 Storage: $0.58
- S3 Requests: $2.00
- CloudFront Transfer: $85.00
- **Total: ~$88/month**

## Security Best Practices

### 1. IAM User Security

- ✅ Use dedicated IAM user
- ✅ Minimum required permissions
- ✅ Rotate access keys regularly
- ✅ Enable MFA on AWS account
- ❌ Never use root account credentials

### 2. Bucket Security

- ✅ Enable bucket versioning
- ✅ Enable server-side encryption
- ✅ Use bucket policy for public access
- ✅ Enable access logging
- ❌ Don't make entire bucket public

### 3. CloudFront Security

- ✅ Use HTTPS only
- ✅ Enable access logs
- ✅ Use signed URLs for private content
- ✅ Enable WAF for DDoS protection

### 4. Credential Management

- ✅ Store in password manager
- ✅ Never commit to version control
- ✅ Use environment variables
- ❌ Never share credentials

## Troubleshooting

### Connection Test Fails

**Check:**
1. Access key ID is correct
2. Secret access key is correct
3. Bucket name is correct
4. Region matches bucket region
5. IAM user has permissions
6. Server can reach AWS (firewall)

### Images Not Loading

**Check:**
1. Bucket policy allows public read
2. Files uploaded successfully
3. CloudFront domain is correct
4. CloudFront distribution deployed
5. Browser console for errors

### Slow Upload Speed

**Solutions:**
1. Choose closer AWS region
2. Check internet connection
3. Reduce batch size
4. Check server resources

## Monitoring

### CloudWatch Metrics

Monitor in AWS Console:
- S3 bucket size
- Number of objects
- Request count
- Data transfer

### CloudFront Reports

View in CloudFront Console:
- Cache statistics
- Popular objects
- Top referrers
- Viewer locations

## Support Resources

- AWS Documentation: docs.aws.amazon.com
- AWS Support: aws.amazon.com/support
- Plugin Support: yoursite.com/support
- Community Forum: yoursite.com/forum

## Next Steps

After setup:
1. ✅ Test with sample images
2. ✅ Verify CloudFront delivery
3. ✅ Run small bulk upload test
4. ✅ Monitor costs in AWS billing
5. ✅ Set up billing alerts
6. ✅ Enable full auto-upload

Congratulations! Your AWS infrastructure is ready! 🎉

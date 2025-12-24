# License Server API Documentation

This document describes the API endpoints your license server should implement to work with WP Cloud Media Offload.

## Base URL

```
https://your-license-server.com/api/v1/
```

## Authentication

All requests include the license key and domain in the request body.

## Endpoints

### 1. Activate License

**Endpoint:** `POST /activate`

**Request Body:**
```json
{
    "license_key": "XXXX-XXXX-XXXX-XXXX",
    "domain": "https://example.com",
    "product": "wp-cloud-media-offload"
}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "License activated successfully",
    "data": {
        "plan": "Gold",
        "expires": "2025-12-31",
        "max_sites": 1,
        "features": {
            "unlimited_uploads": true,
            "cloudfront": true,
            "priority_support": true
        }
    }
}
```

**Error Response (400):**
```json
{
    "success": false,
    "message": "Invalid license key"
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "License already activated on maximum number of sites"
}
```

### 2. Deactivate License

**Endpoint:** `POST /deactivate`

**Request Body:**
```json
{
    "license_key": "XXXX-XXXX-XXXX-XXXX",
    "domain": "https://example.com"
}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "License deactivated successfully"
}
```

### 3. Check License

**Endpoint:** `POST /check`

**Request Body:**
```json
{
    "license_key": "XXXX-XXXX-XXXX-XXXX",
    "domain": "https://example.com"
}
```

**Success Response (200):**
```json
{
    "valid": true,
    "status": "active",
    "expires": "2025-12-31",
    "plan": "Gold"
}
```

**Invalid Response (200):**
```json
{
    "valid": false,
    "status": "expired",
    "message": "License has expired"
}
```

## Implementation Notes

### Security

1. Use HTTPS for all API calls
2. Implement rate limiting
3. Validate domain format
4. Log all activation attempts
5. Implement IP-based fraud detection

### Database Schema

Suggested license table structure:

```sql
CREATE TABLE licenses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    license_key VARCHAR(255) UNIQUE NOT NULL,
    email VARCHAR(255) NOT NULL,
    plan VARCHAR(50) NOT NULL,
    status ENUM('active', 'inactive', 'expired') DEFAULT 'active',
    max_sites INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    INDEX idx_license_key (license_key),
    INDEX idx_email (email)
);

CREATE TABLE license_activations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    license_id INT NOT NULL,
    domain VARCHAR(255) NOT NULL,
    activated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_checked TIMESTAMP NULL,
    FOREIGN KEY (license_id) REFERENCES licenses(id),
    UNIQUE KEY unique_activation (license_id, domain)
);
```

### License Key Generation

Example PHP function:

```php
function generate_license_key() {
    $segments = [];
    for ($i = 0; $i < 4; $i++) {
        $segments[] = strtoupper(substr(md5(random_bytes(16)), 0, 4));
    }
    return implode('-', $segments);
}
```

### Validation Rules

1. **License Key Format**: XXXX-XXXX-XXXX-XXXX (16 characters, 4 segments)
2. **Domain Format**: Must be valid URL
3. **Expiration**: Check if license has expired
4. **Activation Limit**: Check if max_sites exceeded
5. **Status**: Must be 'active'

## Example Implementation (PHP/Laravel)

```php
// routes/api.php
Route::post('/v1/activate', [LicenseController::class, 'activate']);
Route::post('/v1/deactivate', [LicenseController::class, 'deactivate']);
Route::post('/v1/check', [LicenseController::class, 'check']);

// app/Http/Controllers/LicenseController.php
class LicenseController extends Controller
{
    public function activate(Request $request)
    {
        $validated = $request->validate([
            'license_key' => 'required|string',
            'domain' => 'required|url',
            'product' => 'required|string'
        ]);
        
        $license = License::where('license_key', $validated['license_key'])
            ->where('status', 'active')
            ->first();
            
        if (!$license) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid license key'
            ], 400);
        }
        
        if ($license->expires_at && $license->expires_at < now()) {
            return response()->json([
                'success' => false,
                'message' => 'License has expired'
            ], 403);
        }
        
        $activationCount = $license->activations()->count();
        if ($activationCount >= $license->max_sites) {
            return response()->json([
                'success' => false,
                'message' => 'License already activated on maximum number of sites'
            ], 403);
        }
        
        $license->activations()->updateOrCreate(
            ['domain' => $validated['domain']],
            ['last_checked' => now()]
        );
        
        return response()->json([
            'success' => true,
            'message' => 'License activated successfully',
            'data' => [
                'plan' => $license->plan,
                'expires' => $license->expires_at?->format('Y-m-d'),
                'max_sites' => $license->max_sites
            ]
        ]);
    }
}
```

## Testing

Use these test license keys for development:

- `TEST-1234-5678-9ABC` - Valid, never expires
- `TEST-EXPIRED-KEY1` - Expired license
- `TEST-MAXED-OUT-1` - Already at activation limit

## Support

For license server implementation support:
- Email: dev@yourcompany.com
- Documentation: yoursite.com/docs/license-api

<?php

namespace App\Services;

use App\Models\License;
use App\Models\LicenseActivation;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Str;

class LicenseService
{
    /**
     * Generate a unique license key
     */
    public function generateLicenseKey(): string
    {
        do {
            $segments = [];
            for ($i = 0; $i < 4; $i++) {
                $segments[] = strtoupper(Str::random(4));
            }
            $licenseKey = implode('-', $segments);
        } while (License::where('license_key', $licenseKey)->exists());

        return $licenseKey;
    }

    /**
     * Create a new license for a user
     */
    public function createLicense(User $user, Plan $plan, ?int $duration = 365): License
    {
        $license = License::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'license_key' => $this->generateLicenseKey(),
            'status' => 'active',
            'max_activations' => $plan->max_sites,
            'activated_at' => now(),
            'expires_at' => $plan->isFree() ? null : now()->addDays($duration),
        ]);

        // Send email notification
        // event(new LicenseCreated($license));

        return $license;
    }

    /**
     * Activate a license on a domain
     */
    public function activateLicense(
        string $licenseKey,
        string $domain,
        array $metadata = []
    ): array {
        $license = License::where('license_key', $licenseKey)->first();

        if (!$license) {
            return [
                'success' => false,
                'message' => 'Invalid license key',
            ];
        }

        if (!$license->isActive()) {
            return [
                'success' => false,
                'message' => $license->isExpired() ? 'License has expired' : 'License is not active',
            ];
        }

        // Check if already activated on this domain
        $existingActivation = $license->activations()
            ->where('domain', $domain)
            ->where('is_active', true)
            ->first();

        if ($existingActivation) {
            $existingActivation->updateLastChecked();
            
            return [
                'success' => true,
                'message' => 'License already activated on this domain',
                'data' => $this->getLicenseData($license),
            ];
        }

        // Check activation limit
        if (!$license->canActivate()) {
            return [
                'success' => false,
                'message' => 'License activation limit reached. Maximum ' . $license->max_activations . ' site(s) allowed.',
            ];
        }

        // Create new activation
        $activation = LicenseActivation::create([
            'license_id' => $license->id,
            'domain' => $domain,
            'site_url' => $metadata['site_url'] ?? null,
            'ip_address' => $metadata['ip_address'] ?? request()->ip(),
            'wordpress_version' => $metadata['wordpress_version'] ?? null,
            'plugin_version' => $metadata['plugin_version'] ?? null,
            'activated_at' => now(),
            'last_checked_at' => now(),
            'is_active' => true,
        ]);

        $license->update(['last_checked_at' => now()]);

        return [
            'success' => true,
            'message' => 'License activated successfully',
            'data' => $this->getLicenseData($license),
        ];
    }

    /**
     * Deactivate a license from a domain
     */
    public function deactivateLicense(string $licenseKey, string $domain): array
    {
        $license = License::where('license_key', $licenseKey)->first();

        if (!$license) {
            return [
                'success' => false,
                'message' => 'Invalid license key',
            ];
        }

        $activation = $license->activations()
            ->where('domain', $domain)
            ->where('is_active', true)
            ->first();

        if (!$activation) {
            return [
                'success' => false,
                'message' => 'License not activated on this domain',
            ];
        }

        $activation->deactivate();

        return [
            'success' => true,
            'message' => 'License deactivated successfully',
        ];
    }

    /**
     * Check license status
     */
    public function checkLicense(string $licenseKey, string $domain): array
    {
        $license = License::where('license_key', $licenseKey)->first();

        if (!$license) {
            return [
                'valid' => false,
                'status' => 'invalid',
                'message' => 'Invalid license key',
            ];
        }

        $activation = $license->activations()
            ->where('domain', $domain)
            ->where('is_active', true)
            ->first();

        if (!$activation) {
            return [
                'valid' => false,
                'status' => 'not_activated',
                'message' => 'License not activated on this domain',
            ];
        }

        $activation->updateLastChecked();
        $license->update(['last_checked_at' => now()]);

        if ($license->isExpired()) {
            return [
                'valid' => false,
                'status' => 'expired',
                'message' => 'License has expired',
                'expires_at' => $license->expires_at->toDateString(),
            ];
        }

        if (!$license->isActive()) {
            return [
                'valid' => false,
                'status' => $license->status,
                'message' => 'License is ' . $license->status,
            ];
        }

        return [
            'valid' => true,
            'status' => 'active',
            'data' => $this->getLicenseData($license),
        ];
    }

    /**
     * Get license data for API response
     */
    protected function getLicenseData(License $license): array
    {
        return [
            'plan' => $license->plan->name,
            'media_limit' => $license->plan->media_limit,
            'media_limit_display' => $license->plan->media_limit_display,
            'media_used' => $license->getTotalMediaUploads(),
            'media_remaining' => $license->getRemainingMediaLimit(),
            'usage_percentage' => round($license->getUsagePercentage(), 2),
            'max_sites' => $license->max_activations,
            'active_sites' => $license->getActiveActivationsCount(),
            'expires_at' => $license->expires_at?->toDateString(),
            'features' => $license->plan->features,
        ];
    }

    /**
     * Suspend a license
     */
    public function suspendLicense(License $license, string $reason = null): void
    {
        $license->update([
            'status' => 'suspended',
            'metadata' => array_merge($license->metadata ?? [], [
                'suspended_at' => now()->toDateTimeString(),
                'suspension_reason' => $reason,
            ]),
        ]);

        // Deactivate all activations
        $license->activations()->where('is_active', true)->each(function ($activation) {
            $activation->deactivate();
        });
    }

    /**
     * Reactivate a suspended license
     */
    public function reactivateLicense(License $license): void
    {
        $license->update(['status' => 'active']);
    }

    /**
     * Renew a license
     */
    public function renewLicense(License $license, int $duration = 365): void
    {
        $newExpiryDate = $license->expires_at && $license->expires_at->isFuture()
            ? $license->expires_at->addDays($duration)
            : now()->addDays($duration);

        $license->update([
            'expires_at' => $newExpiryDate,
            'status' => 'active',
        ]);
    }
}

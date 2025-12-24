<?php

namespace App\Services;

use App\Models\License;
use App\Models\LicenseActivation;
use App\Models\MediaUpload;
use App\Models\UsageStat;
use Carbon\Carbon;

class UsageTrackingService
{
    /**
     * Track a media upload
     */
    public function trackUpload(
        string $licenseKey,
        string $domain,
        array $uploadData
    ): array {
        $license = License::where('license_key', $licenseKey)->first();

        if (!$license || !$license->isActive()) {
            return [
                'success' => false,
                'message' => 'Invalid or inactive license',
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

        // Check if media limit reached
        if ($license->hasReachedMediaLimit()) {
            return [
                'success' => false,
                'message' => 'Media upload limit reached for your plan',
                'limit_reached' => true,
                'upgrade_required' => true,
            ];
        }

        // Create media upload record
        $mediaUpload = MediaUpload::create([
            'license_id' => $license->id,
            'license_activation_id' => $activation->id,
            'domain' => $domain,
            'attachment_id' => $uploadData['attachment_id'] ?? null,
            'file_name' => $uploadData['file_name'] ?? null,
            'file_type' => $uploadData['file_type'] ?? null,
            'file_size' => $uploadData['file_size'] ?? 0,
            's3_key' => $uploadData['s3_key'] ?? null,
            's3_url' => $uploadData['s3_url'] ?? null,
            'uploaded_at' => now(),
        ]);

        // Update usage stats
        $this->updateUsageStats($license, $uploadData['file_size'] ?? 0);

        return [
            'success' => true,
            'message' => 'Upload tracked successfully',
            'data' => [
                'total_uploads' => $license->getTotalMediaUploads(),
                'remaining' => $license->getRemainingMediaLimit(),
                'usage_percentage' => round($license->getUsagePercentage(), 2),
            ],
        ];
    }

    /**
     * Update daily usage statistics
     */
    protected function updateUsageStats(License $license, int $fileSize): void
    {
        $today = Carbon::today();

        $stat = UsageStat::firstOrCreate(
            [
                'license_id' => $license->id,
                'date' => $today,
            ],
            [
                'uploads_count' => 0,
                'total_size' => 0,
                'api_calls' => 0,
            ]
        );

        $stat->incrementUploads($fileSize);
    }

    /**
     * Get usage statistics for a license
     */
    public function getUsageStats(License $license, ?int $days = 30): array
    {
        $startDate = Carbon::today()->subDays($days);

        $stats = $license->usageStats()
            ->where('date', '>=', $startDate)
            ->orderBy('date')
            ->get();

        $totalUploads = $license->getTotalMediaUploads();
        $totalSize = $license->mediaUploads()->sum('file_size');

        return [
            'total_uploads' => $totalUploads,
            'total_size' => $totalSize,
            'total_size_formatted' => $this->formatBytes($totalSize),
            'media_limit' => $license->plan->media_limit,
            'media_remaining' => $license->getRemainingMediaLimit(),
            'usage_percentage' => round($license->getUsagePercentage(), 2),
            'daily_stats' => $stats->map(function ($stat) {
                return [
                    'date' => $stat->date->toDateString(),
                    'uploads' => $stat->uploads_count,
                    'size' => $stat->total_size,
                    'size_formatted' => $stat->formatted_total_size,
                    'api_calls' => $stat->api_calls,
                ];
            }),
        ];
    }

    /**
     * Get usage statistics for all licenses (admin)
     */
    public function getGlobalUsageStats(?int $days = 30): array
    {
        $startDate = Carbon::today()->subDays($days);

        $totalUploads = MediaUpload::where('uploaded_at', '>=', $startDate)->count();
        $totalSize = MediaUpload::where('uploaded_at', '>=', $startDate)->sum('file_size');
        $activeLicenses = License::where('status', 'active')->count();
        $totalApiCalls = UsageStat::where('date', '>=', $startDate)->sum('api_calls');

        $dailyStats = UsageStat::where('date', '>=', $startDate)
            ->selectRaw('date, SUM(uploads_count) as uploads, SUM(total_size) as size, SUM(api_calls) as api_calls')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'total_uploads' => $totalUploads,
            'total_size' => $totalSize,
            'total_size_formatted' => $this->formatBytes($totalSize),
            'active_licenses' => $activeLicenses,
            'total_api_calls' => $totalApiCalls,
            'daily_stats' => $dailyStats->map(function ($stat) {
                return [
                    'date' => $stat->date,
                    'uploads' => $stat->uploads,
                    'size' => $stat->size,
                    'size_formatted' => $this->formatBytes($stat->size),
                    'api_calls' => $stat->api_calls,
                ];
            }),
        ];
    }

    /**
     * Format bytes to human readable format
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Track API call
     */
    public function trackApiCall(License $license): void
    {
        $today = Carbon::today();

        $stat = UsageStat::firstOrCreate(
            [
                'license_id' => $license->id,
                'date' => $today,
            ],
            [
                'uploads_count' => 0,
                'total_size' => 0,
                'api_calls' => 0,
            ]
        );

        $stat->incrementApiCalls();
    }
}

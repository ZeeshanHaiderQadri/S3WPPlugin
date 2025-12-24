<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LicenseService;
use App\Services\UsageTrackingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    protected LicenseService $licenseService;
    protected UsageTrackingService $usageService;

    public function __construct(
        LicenseService $licenseService,
        UsageTrackingService $usageService
    ) {
        $this->licenseService = $licenseService;
        $this->usageService = $usageService;
    }

    /**
     * Activate a license
     */
    public function activate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'license_key' => 'required|string',
            'domain' => 'required|url',
            'product' => 'required|string',
            'site_url' => 'nullable|url',
            'wordpress_version' => 'nullable|string',
            'plugin_version' => 'nullable|string',
        ]);

        $metadata = [
            'site_url' => $validated['site_url'] ?? null,
            'wordpress_version' => $validated['wordpress_version'] ?? null,
            'plugin_version' => $validated['plugin_version'] ?? null,
            'ip_address' => $request->ip(),
        ];

        $result = $this->licenseService->activateLicense(
            $validated['license_key'],
            $validated['domain'],
            $metadata
        );

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Deactivate a license
     */
    public function deactivate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'license_key' => 'required|string',
            'domain' => 'required|url',
        ]);

        $result = $this->licenseService->deactivateLicense(
            $validated['license_key'],
            $validated['domain']
        );

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Check license status
     */
    public function check(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'license_key' => 'required|string',
            'domain' => 'required|url',
        ]);

        $result = $this->licenseService->checkLicense(
            $validated['license_key'],
            $validated['domain']
        );

        return response()->json($result);
    }

    /**
     * Track media upload
     */
    public function trackUpload(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'license_key' => 'required|string',
            'domain' => 'required|url',
            'attachment_id' => 'nullable|integer',
            'file_name' => 'nullable|string',
            'file_type' => 'nullable|string',
            'file_size' => 'nullable|integer',
            's3_key' => 'nullable|string',
            's3_url' => 'nullable|url',
        ]);

        $result = $this->usageService->trackUpload(
            $validated['license_key'],
            $validated['domain'],
            $validated
        );

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Get usage statistics
     */
    public function usage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'license_key' => 'required|string',
            'days' => 'nullable|integer|min:1|max:365',
        ]);

        $license = \App\Models\License::where('license_key', $validated['license_key'])->first();

        if (!$license) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid license key',
            ], 404);
        }

        $stats = $this->usageService->getUsageStats(
            $license,
            $validated['days'] ?? 30
        );

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}

<?php

use App\Http\Controllers\Api\LicenseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // License Management
    Route::post('/activate', [LicenseController::class, 'activate']);
    Route::post('/deactivate', [LicenseController::class, 'deactivate']);
    Route::post('/check', [LicenseController::class, 'check']);
    
    // Usage Tracking
    Route::post('/track-upload', [LicenseController::class, 'trackUpload']);
    Route::get('/usage', [LicenseController::class, 'usage']);
});

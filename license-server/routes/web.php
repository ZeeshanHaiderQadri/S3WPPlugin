<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\LicenseController as AdminLicenseController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\CheckoutController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing Page (Public)
Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/pricing', [LandingController::class, 'pricing'])->name('pricing');
Route::get('/features', [LandingController::class, 'features'])->name('features');
Route::get('/docs', [LandingController::class, 'docs'])->name('docs');
Route::get('/contact', [LandingController::class, 'contact'])->name('contact');
Route::post('/contact', [LandingController::class, 'submitContact'])->name('contact.submit');

// Checkout & Payment
Route::post('/checkout/create', [CheckoutController::class, 'create'])->name('checkout.create');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
Route::post('/webhook/stripe', [CheckoutController::class, 'webhook'])->name('webhook.stripe');

// Admin Authentication
Route::prefix('admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
});

// Admin Dashboard (Protected)
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard.index');
    
    // Users Management
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('admin.users.show');
    Route::post('/users/{id}/suspend', [UserController::class, 'suspend'])->name('admin.users.suspend');
    Route::post('/users/{id}/activate', [UserController::class, 'activate'])->name('admin.users.activate');
    
    // Licenses Management
    Route::get('/licenses', [AdminLicenseController::class, 'index'])->name('admin.licenses.index');
    Route::get('/licenses/{id}', [AdminLicenseController::class, 'show'])->name('admin.licenses.show');
    Route::post('/licenses/{id}/suspend', [AdminLicenseController::class, 'suspend'])->name('admin.licenses.suspend');
    Route::post('/licenses/{id}/activate', [AdminLicenseController::class, 'activate'])->name('admin.licenses.activate');
    Route::post('/licenses/{id}/extend', [AdminLicenseController::class, 'extend'])->name('admin.licenses.extend');
    Route::post('/licenses/create', [AdminLicenseController::class, 'create'])->name('admin.licenses.create');
    
    // Orders Management
    Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('admin.orders.show');
    Route::post('/orders/{id}/refund', [OrderController::class, 'refund'])->name('admin.orders.refund');
    
    // Plans Management
    Route::get('/plans', [PlanController::class, 'index'])->name('admin.plans.index');
    Route::get('/plans/{id}/edit', [PlanController::class, 'edit'])->name('admin.plans.edit');
    Route::post('/plans/{id}', [PlanController::class, 'update'])->name('admin.plans.update');
    Route::post('/plans/{id}/toggle', [PlanController::class, 'toggle'])->name('admin.plans.toggle');
    
    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('admin.analytics.index');
    Route::get('/analytics/revenue', [AnalyticsController::class, 'revenue'])->name('admin.analytics.revenue');
    Route::get('/analytics/usage', [AnalyticsController::class, 'usage'])->name('admin.analytics.usage');
});

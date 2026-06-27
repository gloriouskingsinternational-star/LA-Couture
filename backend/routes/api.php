<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\EnquiryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| L.A. Couture API Routes
|--------------------------------------------------------------------------
*/

// Public endpoints
Route::prefix('auth')->group(function () {
    Route::post('/admin/login',  [AuthController::class, 'adminLogin']);
    Route::post('/client/login', [AuthController::class, 'clientLogin']);
    Route::post('/register',     [AuthController::class, 'clientRegister']);
});

Route::get('/products',           [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

Route::post('/contact', [ContactController::class, 'store']);

// Authenticated endpoints (any valid token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me',      [AuthController::class, 'me']);

    // Orders
    Route::get('/orders',                         [OrderController::class, 'index']);
    Route::post('/orders',                        [OrderController::class, 'store']);
    Route::get('/orders/{order}',                 [OrderController::class, 'show']);

    // Enquiries
    Route::get('/enquiries',                           [EnquiryController::class, 'index']);
    Route::post('/enquiries',                          [EnquiryController::class, 'store']);
    Route::get('/enquiries/{enquiry}',                 [EnquiryController::class, 'show']);
    Route::post('/enquiries/{enquiry}/messages',       [EnquiryController::class, 'sendMessage']);

    // Admin-only routes
    Route::middleware('admin')->group(function () {
        // Dashboard stats
        Route::get('/admin/stats',  [AdminController::class, 'stats']);

        // Client management
        Route::get('/admin/clients',                      [AdminController::class, 'clients']);
        Route::post('/admin/clients/{user}/approve',      [AdminController::class, 'approveClient']);
        Route::post('/admin/clients/{user}/reject',       [AdminController::class, 'rejectClient']);

        // Order management
        Route::put('/admin/orders/{order}/status',        [OrderController::class, 'updateStatus']);

        // Enquiry management
        Route::put('/admin/enquiries/{enquiry}/status',   [EnquiryController::class, 'updateStatus']);

        // Products (admin CRUD)
        Route::post('/admin/products',             [ProductController::class, 'store']);
        Route::put('/admin/products/{product}',    [ProductController::class, 'update']);
        Route::delete('/admin/products/{product}', [ProductController::class, 'destroy']);

        // Contact forms
        Route::get('/admin/contact-forms',                        [AdminController::class, 'contactForms']);
        Route::put('/admin/contact-forms/{contactForm}/status',   [AdminController::class, 'updateContactForm']);
    });
});

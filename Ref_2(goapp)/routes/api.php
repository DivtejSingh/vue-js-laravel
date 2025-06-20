<?php

use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CartitemController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CompanyAddressController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\WishlistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| These routes are loaded by the RouteServiceProvider within the
| group which is assigned the "api" middleware group.
|
*/

// Example Sanctum route (only if needed):
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// -------------------------------
// JWT ROUTES
// -------------------------------

// Public route for login
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (requires valid JWT via 'auth.api' middleware)
Route::middleware(['auth.api'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // Brands
    Route::get('/brands', [BrandController::class, 'index']);

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);

    // Sliders Banner
    Route::get('/round-banner', [BannerController::class, 'roundBanner']);
    Route::get('/big-banner', [BannerController::class, 'largeBanner']);
    Route::get('/small-banner', [BannerController::class, 'smallBanner']);
    Route::get('/deals-banner', [BannerController::class, 'dealBanner']);
    Route::get('/fruit-banner', [BannerController::class, 'fruitBanner']);
    Route::get('/browse-banner', [BannerController::class, 'browseBanner']);

    // Wishlist
    Route::get  ('/wishlist',      [WishlistController::class, 'index']);
    Route::post ('/wishlist/add',[WishlistController::class, 'store']);

    // Cart Item
    Route::get('/cart-item', [CartitemController::class, 'index']);
    Route::post('/cart-item/update', [CartitemController::class, 'store']);

    // Company Address
    Route::get('/company-address', [CompanyAddressController::class, 'index']);
    Route::post('/company-address/update', [CompanyAddressController::class, 'store']);
    // Company Address
    Route::get('/delivery-methods', [CompanyAddressController::class, 'deliveryMethod']);
    // Service & Solution
    Route::get('/service-solutions', [CompanyAddressController::class, 'serviceAndSolution']);

    // Order Routes (CRUD)
    Route::apiResource('orders', OrderController::class);

    // Coupon Routes (CRUD)
    Route::post('/coupons',            [CouponController::class, 'store']); 
    Route::get('/coupons',             [CouponController::class, 'index']); 
    Route::get('/coupons/{coupon_id}', [CouponController::class, 'show']);   
    Route::put('/coupons/{coupon_id}', [CouponController::class, 'update']); 
    Route::delete('/coupons/{coupon_id}', [CouponController::class, 'destroy']);

    // Cart coupon apply
    Route::post('/cart/apply-coupon', [CartitemController::class, 'applyCoupon']);
});



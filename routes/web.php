<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\{
    ProductSearchController, ProductController, AdminController, 
    WishlistController, PriceAlertController, VendorController, 
    VendorDashboardController, AdminAgencyController
};
use App\Http\Controllers\Agency\{DashboardController, TripController as AgencyTripController}; 
use App\Http\Controllers\Auth\{RegisteredUserController, LogoutController, LoginController};

// =========================================================
// PUBLIC & USER ROUTES (Unified Search)
// =========================================================
Route::middleware(['web'])->group(function () {
    
    // Unified Search Route
    Route::get('/', [ProductSearchController::class, 'search'])->name('product.search');
    Route::get('/search', [ProductSearchController::class, 'search'])->name('search.results');

   
  // Product & Trip Details
    Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
    Route::get('/trips/{id}', [App\Http\Controllers\Agency\TripController::class, 'show'])->name('trips.show');
    Route::get('/about', fn() => view('about'))->name('about');
    
  Route::post('/trips/{trip}/rate', [App\Http\Controllers\Agency\TripController::class, 'rate'])
         ->name('trip.rate');

    Route::delete('/trips/{id}', [App\Http\Controllers\Agency\TripController::class, 'destroy'])->name('agency.trip.destroy');
    Route::put('/trips/{id}/prices', [App\Http\Controllers\Agency\TripController::class, 'updatePrices'])->name('agency.trip.updatePrices');

    // Route::post('/agencies/{agency}/rate', [App\Http\Controllers\Agency\TripController::class, 'rate'])
    //      ->name('agencies.rate');

    // Standard User Auth
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::middleware('auth')->group(function () {
        Route::post('/product/{product}/rate', [App\Http\Controllers\RatingController::class, 'store'])->name('product.rate');
        
        // Product Wishlist & Alerts (Existing)
        Route::post('/wishlist/toggle/{productId}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
        Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
        Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy'])->name('wishlist.destroy'); // Added Delete Route
        Route::post('/alerts/toggle/{productId}', [PriceAlertController::class, 'toggle'])->name('price.alert.toggle');

        // Trip Wishlist & Alerts (New)
        Route::post('/wishlist/trip/toggle/{tripId}', [WishlistController::class, 'toggleTrip'])->name('wishlist.trip.toggle');
        Route::post('/price-alerts/trip/toggle/{tripId}', [PriceAlertController::class, 'toggleTripAlert'])->name('price.alert.trip.toggle');

        Route::post('/logout', [LogoutController::class, 'logout'])->name('logout'); 
    });

    // =========================================================
    // VENDOR ROUTES
    // =========================================================
    Route::get('/vendor/login', [VendorController::class, 'showLoginForm'])->name('vendor.login.form');
    Route::post('/vendor/login', [VendorController::class, 'login'])->name('vendor.login');
    Route::get('/vendor/register', [VendorController::class, 'showRegisterForm'])->name('vendor.register.form');
    Route::post('/vendor/register', [VendorController::class, 'register'])->name('register.submit');

    Route::middleware('auth:vendor')->group(function () {
        Route::get('/vendor/dashboard', [VendorDashboardController::class, 'show'])->name('vendor.dashboard');
        Route::post('/vendor/dashboard/update', [VendorDashboardController::class, 'updatePrice'])->name('vendor.update_price');
        Route::get('/vendor/edit-price/{id}', [VendorController::class, 'editPrice'])->name('vendor.edit_price');
        Route::put('/vendor/update-price/{id}', [VendorController::class, 'updateExistingPrice'])->name('vendor.update_existing_price');
        Route::delete('/vendor/remove-product/{id}', [VendorController::class, 'removeProduct'])->name('vendor.remove_product');
        Route::get('/vendor/price-history/{id}', [VendorController::class, 'viewPriceHistory'])->name('vendor.price_history');
        Route::delete('/vendor/destroy', [VendorController::class, 'destroy'])->name('vendor.destroy');
        Route::post('/vendor/logout', function (Request $request) {
            Auth::guard('vendor')->logout();
            $request->session()->invalidate();
            return redirect()->route('vendor.login.form');
        })->name('vendor.logout');
    });

    // =========================================================
    // AGENCY ROUTES
    // =========================================================
    Route::get('/agency/login', [LoginController::class, 'showLoginForm'])->name('agency.login.form');
    Route::post('/agency/login', [LoginController::class, 'login'])->name('agency.login');

    Route::middleware('auth:agency')->group(function () {
        Route::get('/agency/dashboard', [DashboardController::class, 'index'])->name('agency.dashboard');
        Route::get('/agency/trips/create', [AgencyTripController::class, 'create'])->name('agency.trip.create');
        Route::post('/agency/trips', [AgencyTripController::class, 'store'])->name('agency.trip.store');
        Route::delete('/agency/trips/{id}', [AgencyTripController::class, 'destroy'])->name('agency.trip.destroy');
        Route::get('/agency/trip/{id}/history', [DashboardController::class, 'showHistory'])->name('agency.trip.history');
    });

    // =========================================================
    // ADMIN ROUTES
    // =========================================================
    Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/vendors', [AdminController::class, 'manageVendors'])->name('vendors');
        Route::get('/products', [AdminController::class, 'manageProducts'])->name('products');
        Route::get('/agencies', [AdminController::class, 'manageAgencies'])->name('agencies');
        
        Route::post('/approve-agency/{id}', [AdminController::class, 'approveAgency'])->name('approve-agency');
        Route::get('/agencies/{id}', [AdminController::class, 'showAgency'])->name('agency.show');
        Route::delete('/agencies/{id}', [AdminController::class, 'rejectAgency'])->name('reject-agency');
        Route::post('/agencies/{id}/toggle-status', [AdminController::class, 'toggleAgencyStatus'])->name('toggle-agency-status');

        Route::post('/approve-vendor/{vendor}', [AdminController::class, 'approveVendor'])->name('approve-vendor');
        Route::post('/vendors/verify/{vendor}', [AdminController::class, 'verifyVendor'])->name('verify-vendor');
        Route::get('/vendors/{id}', [AdminController::class, 'showVendor'])->name('vendor.show');
        Route::delete('/vendors/{id}', [AdminController::class, 'rejectVendor'])->name('reject-vendor');
        Route::post('/vendors/{id}/toggle-status', [AdminController::class, 'toggleVendorStatus'])->name('toggle-vendor-status');

        Route::post('/logout', function () {
            Auth::logout();
            return redirect('/login');
        })->name('logout');
    });
    Route::view('/faq', 'faq')->name('faq');
    Route::view('/help', 'help')->name('help');
});
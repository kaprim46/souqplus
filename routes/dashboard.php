<?php

use App\Http\Controllers\Api\Dashboard\{ExploreSliderController,
    SplashScreensController,
    SettingController,
    CountryController,
    CustomerController,
    DashboardController,
    PageController,
    ContactUsController,
    CategoryController as DashboardCategoryController,
    SubCategoryController,
    FilterController,
    BadgeController,
    AdvertisementController as DashboardAdvertisementController};
use App\Http\Controllers\Api\{CommentController};
use Illuminate\Support\Facades\Route;

/**
 * Analytics routes
 */
Route::get('/analytics', [DashboardController::class, 'index'])
    ->name('analytics.index');

/**
 * Explore slider
 */
Route::prefix('explore-slider')
    ->group(function () {
        Route::post('/', [ExploreSliderController::class, 'store'])
            ->name('explore-slider.store');

        Route::put('/{slider}', [ExploreSliderController::class, 'update'])
            ->name('explore-slider.update');

        Route::delete('/{slider}', [ExploreSliderController::class, 'destroy'])
            ->name('explore-slider.destroy');
    });

/**
 * Global notifications
 */
Route::prefix('notifications')
    ->group(function () {
        //Route::get('/', [DashboardController::class, 'notifications'])->name('notifications.index');
        Route::post('/send', [DashboardController::class, 'sendNotification'])->name('notifications.send');
    });

/**
 * Settings routes
 */
Route::prefix('settings')
    ->group(function () {
        Route::put('/', [SettingController::class, 'update'])->name('settings.update');
    });

/**
 * Splash Screens routes
 */
Route::prefix('splash-screens')
    ->group(function () {
        Route::post('/', [SplashScreensController::class, 'store'])->name('splash-screens.store');
        Route::put('/{splashScreen}', [SplashScreensController::class, 'update'])->name('splash-screens.update');
        Route::delete('/{splashScreen}', [SplashScreensController::class, 'destroy'])->name('splash-screens.destroy');
    });

/**
 * Customer routes
 */
Route::prefix('customers')
    ->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/{customer}', [CustomerController::class, 'show'])->name('customers.show');
        Route::post('/', [CustomerController::class, 'store'])->name('customers.store');
        Route::put('/{customer}', [CustomerController::class, 'update'])->name('customers.update');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    });

/**
 * Categories routes
 */
Route::prefix('categories')
    ->group(function () {
        Route::get('/', [DashboardCategoryController::class, 'index'])->name('categories.index');
        Route::get('/{category}', [DashboardCategoryController::class, 'show'])->name('categories.show');
        Route::post('/', [DashboardCategoryController::class, 'store'])->name('categories.store');
        Route::put('/{category}', [DashboardCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/{category}', [DashboardCategoryController::class, 'destroy'])->name('categories.destroy');
    });

/**
 * Sub categories routes
 */
Route::prefix('sub/categories')
    ->group(function () {
        Route::get('/', [SubCategoryController::class, 'index'])->name('sub/categories.index');
        Route::get('/{subCategory}', [SubCategoryController::class, 'show'])->name('sub/categories.show');
        Route::post('/', [SubCategoryController::class, 'store'])->name('sub/categories.store');
        Route::put('/{subCategory}', [SubCategoryController::class, 'update'])->name('sub/categories.update');
        Route::delete('/{subCategory}', [SubCategoryController::class, 'destroy'])->name('sub/categories.destroy');
    });

/**
 * Pages routes
 */
Route::prefix('pages')
    ->group(function () {
        Route::get('/', [PageController::class, 'index'])->name('pages.index');
        Route::get('/{page}', [PageController::class, 'show'])->name('pages.show');
        Route::post('/', [PageController::class, 'store'])->name('pages.store');
        Route::put('/{page}', [PageController::class, 'update'])->name('pages.update');
        Route::delete('/{page}', [PageController::class, 'destroy'])->name('pages.destroy');
    });

/**
 * Contact us routes
 */
Route::prefix('contact-us')
    ->group(function () {
        Route::get('/', [ContactUsController::class, 'index'])->name('contact-us.index');
        Route::get('/{contactUs}', [ContactUsController::class, 'show'])->name('contact-us.show');
        Route::post('/{contactUs}/reply', [ContactUsController::class, 'reply'])->name('contact-us.reply');
        Route::delete('/{contactUs}', [ContactUsController::class, 'destroy'])->name('contact-us.destroy');
    });

/**
 * Advertisements routes
 */
Route::prefix('advertisements')
    ->group(function () {
        Route::get('/', [DashboardAdvertisementController::class, 'index'])->name('advertisements.index');
        Route::get('/{advertisement}', [DashboardAdvertisementController::class, 'show'])->name('advertisements.show');
        Route::post('/', [DashboardAdvertisementController::class, 'store'])->name('advertisements.store');
        Route::put('/{advertisement}', [DashboardAdvertisementController::class, 'update'])->name('advertisements.update');
        Route::delete('/{advertisement}', [DashboardAdvertisementController::class, 'destroy'])->name('advertisements.destroy');
    });

/**
 * Countries routes
 */
Route::prefix('countries')
    ->group(function () {
        Route::post('/', [CountryController::class, 'store'])->name('countries.store');
        Route::put('/{country}', [CountryController::class, 'update'])->name('countries.update');
        Route::delete('/{country}', [CountryController::class, 'destroy'])->name('countries.destroy');
    });

/**
 * Cities routes
 */
Route::prefix('cities')
    ->group(function () {
        Route::post('/', [CountryController::class, 'storeCity'])->name('countries.storeCity');
        Route::put('/{city}', [CountryController::class, 'updateCity'])->name('countries.updateCity');
        Route::delete('/{city}', [CountryController::class, 'destroyCity'])->name('countries.destroyCity');
    });

/**
 * Route comments on advertisements
 */
Route::prefix('advertisements/{advertisement}/comments')
    ->group(function () {
        Route::put('/{comment}', [CommentController::class, 'updateComment'])->name('advertisements.comments.update');
        Route::delete('/{comment}', [CommentController::class, 'destroyComment'])->name('advertisements.comments.destroy');
    });

/**
 * Route badges
 */
Route::resource('/badges', BadgeController::class)->only(['index', 'store', 'update', 'destroy']);


/**
 * Route filters
 */
Route::resource('/filters', FilterController::class)->only(['index', 'store', 'update', 'destroy']);

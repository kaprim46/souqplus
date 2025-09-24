<?php

use App\Http\Controllers\Api\Dashboard\{
    ExploreSliderController,
    SplashScreensController,
    SettingController,
    SubSubCategoryController,
    CountryController,
    CategoryController as DashboardCategoryController,
    FilterController
};
use App\Http\Controllers\Api\{
    ChatController,
    CommentController,
    StoreController,
    UserController,
    CategoryController,
    PageController as ApiPageController,
    ContactUsController as ApiContactUsController,
    AdvertisementController,
    MediaController,
    Mobile\AdvertisementController as MobileAdvertisementController
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Settings routes
 */
Route::prefix('settings')
    ->group(function () {
        Route::get('/splash-screens', [SplashScreensController::class, 'index'])->name('splash-screens.index');
        Route::get('/', [SettingController::class, 'index'])->name('settings.index');
    });

/**
 * Localization routes
 */
Route::post('/locale/{locale}', function (string $locale) {
    if (!in_array($locale, ['ar', 'en'])) {
        return response()->json([
            'message' => 'Language not supported',
            'ok' => false,
        ]);
    }

    session()->put('locale', $locale);

    \Carbon\Carbon::setLocale($locale);

    return response()->json([
        'message' => 'Language changed successfully',
        'lang' => $locale,
        'ok' => true,
    ]);
})
    ->name('lang');

/**
 * Auth Routes
 */
Route::post('/register', [UserController::class, 'register'])
    ->name('register');

Route::post('/login', [UserController::class, 'login'])
    ->name('login');

Route::post('/forgot-password', [UserController::class, 'forgotPassword'])
    ->name('forgot-password');

Route::post('/reset-password', [UserController::class, 'resetPassword'])
    ->name('reset-password');

Route::post('/send/verification', [UserController::class, 'sendVerificationCode'])
    ->name('customer.me.sendVerificationCode');

Route::post('/verify', [UserController::class, 'verify'])
    ->name('customer.me.verify');

/*
 * Fetch Stories
 */
Route::get('/stories', [AdvertisementController::class, 'fetchStories'])
    ->middleware(['optional.auth'])
    ->name('stories.index');

/*
 * View Story
 */
Route::post('/stories/{story}/view', [AdvertisementController::class, 'addViewToStory'])
    ->middleware(['optional.auth'])
    ->name('stories.view');

/**
 * Categories Routes
 */
Route::prefix('categories')
    ->group(function () {
        Route::get('/', [CategoryController::class, 'index'])
            ->name('customer.categories.index');

        Route::get('/{slug}', [CategoryController::class, 'show'])
            ->name('customer.categories.show');

        Route::get('/{category}/sub/categories', [DashboardCategoryController::class, 'subCategories'])
            ->name('categories.subCategories');
    });

Route::prefix('sub-categories')
    ->group(function () {
        Route::get('/{subCategory}/sub-sub-categories', [DashboardCategoryController::class, 'subSubCategories'])
            ->name('sub-categories.subSubCategories');
    });

/**
 * Pages Routes
 */
Route::prefix('pages')
    ->group(function () {
        Route::get('/', [ApiPageController::class, 'index'])
            ->name('customer.pages.index');

        Route::get('/{slug}', [ApiPageController::class, 'show'])
            ->name('customer.pages.show');
    });

/**
 * Contact Us Routes
 */
Route::prefix('contact-us')
    ->group(function () {
        Route::post('/', [ApiContactUsController::class, 'store'])
            ->name('customer.contact-us.store');
    });

/**
 * Advertisements Routes
 */
Route::prefix('advertisements')
    ->group(function () {
        Route::middleware(['optional.auth'])->group(function () {
            Route::get('/', [AdvertisementController::class, 'index'])
                ->name('customer.advertisements.index');

            Route::get('/explore', [AdvertisementController::class, 'exploreAdvertisements'])
                ->name('customer.advertisements.explore');

            Route::get('/{advertisement}/comments', [CommentController::class, 'indexComments'])
                ->name('customer.advertisements.comments.index');

            Route::post('/{advertisement}/comments', [CommentController::class, 'storeComment'])
                ->name('customer.advertisements.comments');

            Route::post('/{advertisement}/favorite', [AdvertisementController::class, 'toggleFavorite'])
                ->name('customer.advertisements.favorite');

            Route::get('/{advertisement}', [AdvertisementController::class, 'show'])
                ->where('advertisement', '[0-9]+')
                ->name('customer.advertisements.show');
        });
    });

/**
 * Countries routes
 */
Route::get('/countries', [CountryController::class, 'index'])
    ->middleware(['optional.auth'])
    ->name('countries.index');

/**
 * Cities routes
 */
Route::get('cities/{country?}', [CountryController::class, 'cities'])
    ->middleware(['optional.auth'])
    ->name('countries.cities');

/**
 * Get Customer
 */
Route::get('/customers/{customer}', [UserController::class, 'show'])
    ->middleware(['optional.auth']);

/**
 * followers, following
 */
Route::get('/customers/{customer}/followers', [UserController::class, 'followers'])
    ->middleware(['optional.auth'])
    ->name('customers.followers');

Route::get('/customers/{customer}/following', [UserController::class, 'followings'])
    ->middleware(['optional.auth'])
    ->name('customers.following');

/**
 * Get Store
 */
Route::get('/stores/{store}', [StoreController::class, 'show'])
    ->middleware(['optional.auth'])
    ->name('stores.show');

/**
 * Route filters
 */
Route::prefix('filters')
    ->group(function () {
        Route::get('/category/{id}', [FilterController::class, 'getFiltersByCategory']);
        Route::get('/sub/category/{id}', [FilterController::class, 'getFiltersBySubCategory']);
    });





/**
 * Authenticated routes
 */
Route::middleware(['auth:sanctum'])
    ->group(function () {
        /**
         * Logout route
         */


        Route::prefix('dashboard')
            ->group(function() {

                // Route to get all sub-sub-categories for a given sub-category
                Route::get('/sub/categories/{subCategory}/sub-sub-categories', [SubSubCategoryController::class, 'index']);

                // Standard resource routes for creating, showing, updating, deleting sub-sub-categories
                Route::apiResource('/sub-sub-categories', SubSubCategoryController::class)->except(['index']);

            });

        Route::post('/logout', function (Request $request) {
            $request->user()->tokens()->delete();
            return response()->json([
                'message' => 'Logged out successfully',
                'ok' => true,
            ]);
        })
            ->name('logout');

        /**
         * Me route
         */
        Route::prefix('me')
            ->group(function () {
                Route::put('/notifications', [UserController::class, 'markAsRead'])
                    ->name('customer.me.notifications.markAsRead');

                Route::put('/password', [UserController::class, 'updatePassword'])
                    ->name('customer.me.updatePassword');

                Route::put('/profile', [UserController::class, 'updateProfile'])
                    ->name('customer.me.updateProfile');

                Route::get('/notifications', [UserController::class, 'notifications'])
                    ->name('customer.me.notifications');

                Route::get('/', [UserController::class, 'me'])
                    ->name('customer.me');

                /*Route::delete('/', [UserController::class, 'destroy'])
                    ->name('customer.me.destroy');*/

                Route::put('/store', [StoreController::class, 'onUpdate'])
                    ->name('customer.me.store.update');
            });

        /**
         * Advertisement routes (userAdvertisements)
         */
        Route::prefix('my/advertisements')
            ->group(function () {
                Route::get('/', [AdvertisementController::class, 'userAdvertisements'])
                    ->name('customer.advertisements.userAdvertisements');

                Route::post('/', [AdvertisementController::class, 'store'])
                    ->name('customer.advertisements.store');

                Route::post('/mobile', [MobileAdvertisementController::class, 'store'])
                    ->name('customer.mobile.advertisements.store');

                Route::put('/mobile/{advertisement}', [MobileAdvertisementController::class, 'update'])
                    ->name('customer.mobile.advertisements.update');

                Route::put('/{advertisement}', [AdvertisementController::class, 'update'])
                    ->name('customer.advertisements.update');

                Route::delete('/{advertisement}', [AdvertisementController::class, 'destroy'])
                    ->name('customer.advertisements.destroy');

                Route::get('/favorites', [AdvertisementController::class, 'favoritesAdvertisements'])
                    ->name('customer.advertisements.favorites');

                Route::post('/{advertisement}/story', [AdvertisementController::class, 'storeStory'])
                    ->name('customer.advertisements.story');

                Route::delete('/{advertisement}/story', [AdvertisementController::class, 'deleteStory'])
                    ->name('customer.advertisements.story.delete');
            });

        /**
         * Media routes
         */
        Route::prefix('media')
            ->group(function () {
                Route::get('/', [MediaController::class, 'index'])
                    ->name('media.index');

                Route::post('/', [MediaController::class, 'upload'])
                    ->name('media.upload');

                Route::delete('/{id}', [MediaController::class, 'destroy'])
                    ->name('media.destroy');
            });

        /**
         * Follow & unfollow routes use just one
         */
        Route::post('/follow/{customer}', [UserController::class, 'followOrUnfollow'])
            ->name('customer.follow');

        /**
         * Chat Routes
         */
        Route::prefix('chat')
            ->group(function () {
                Route::post('/open', [ChatController::class, 'openConversation'])
                    ->name('chat.open');

                Route::get('/messages', [ChatController::class, 'getMessages'])
                    ->name('chat.messages');

                Route::post('/send', [ChatController::class, 'sendMessage'])
                    ->name('chat.send');

                Route::post('/close', [ChatController::class, 'closeConversation'])
                    ->name('chat.close');

                Route::get('/conversations', [ChatController::class, 'getConversations'])
                    ->name('chat.conversations');

                Route::post('/mark-as-read', [ChatController::class, 'markAsRead'])
                    ->name('chat.markAsRead');
            });

        Route::middleware('auth:sanctum')->post('/user/update-fcm-token', [UserController::class, 'updateFcmToken']);
    });


/**
 * Explore Sliders routes
 */
Route::prefix('explore-slider')
    ->group(function () {

        Route::get('/', [ExploreSliderController::class, 'index'])
            ->name('explore-slider.index');
    });

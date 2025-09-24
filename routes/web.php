<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

Route::get('/{any?}', function () {
    return view('layout');
})
    ->where('any', '^(?!api).*$')
    ->name('home');

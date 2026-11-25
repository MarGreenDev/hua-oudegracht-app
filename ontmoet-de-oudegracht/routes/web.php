<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PostController;
use App\Models\Post;

Route::get('/', function () {
    return view('app', [
        'latestPost' => Post::latest('id')->first(),
        'posts' => Post::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('id')
            ->get(),
    ]);
});

Route::get('cms', function () {
    return view('CMS.cms');
});

Route::post('cms/posts', [PostController::class, 'store']);

Route::get('cms-login', function () {
    return view('CMS.login');
});

Route::post('cms-login', [LoginController::class, 'login']);

//Route::get('login-test', function () {
//    return view('logintest');
//});

Route::middleware('admin')->group(function () {
    Route::get('/logintest', function () {
        return view('logintest');
    })->name('logintest');
});

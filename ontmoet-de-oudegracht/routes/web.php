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

Route::get('login', function () {
    return view('CMS.login');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::post('cms-login', [LoginController::class, 'login']);

Route::middleware('admin')->group(function () {
    Route::get('/logintest', function () {
        return view('logintest');
    })->name('logintest');

    Route::get('/cms', function () {
        return view('CMS.cms');
    });

    Route::post('cms/posts', [PostController::class, 'store']);
});

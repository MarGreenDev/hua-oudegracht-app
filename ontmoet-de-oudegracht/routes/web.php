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

    Route::get('/cms', [PostController::class, 'index'])->name('cms.index');
    Route::post('cms/posts', [PostController::class, 'store'])->name('cms.posts.store');
    Route::patch('cms/posts/{post}', [PostController::class, 'update'])->name('cms.posts.update');
    Route::delete('cms/posts/{post}', [PostController::class, 'destroy'])->name('cms.posts.destroy');
});

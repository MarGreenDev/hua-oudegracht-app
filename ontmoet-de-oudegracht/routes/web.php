<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PostController;

Route::get('/', function () {
    return view('app');
});

Route::get('cms', function () {
    return view('CMS.cms');
});

Route::post('cms/posts', [PostController::class, 'store']);

Route::get('cms-login', function () {
    return view('CMS.login');
});

Route::post('cms-login', [LoginController::class, 'login']);

Route::get('login-test', function () {
    return view('logintest');
});

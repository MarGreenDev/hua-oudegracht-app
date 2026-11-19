<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app');
});

Route::get('cms', function () {
    return view('CMS.cms');
});

Route::get('cms-login', function () {
    return view('CMS.login');
});

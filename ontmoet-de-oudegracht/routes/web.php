<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\VideoController;
use App\Models\Post;
use App\Support\VideoRoute;

Route::get('/', function () {
    $posts = Post::whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->orderBy('id')
        ->get()
        ->each(function (Post $post): void {
            $post->video_timestamp = VideoRoute::timestamp(
                (float) $post->latitude,
                (float) $post->longitude,
                (float) config('video.duration'),
            );
        });

    return view('app', [
        'latestPost' => Post::latest('id')->first(),
        'videoRoute' => config('video.route'),
        'posts' => $posts,
    ]);
});

Route::get('/video/filmopname_oudegracht.mp4', VideoController::class)->name('video.stream');

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

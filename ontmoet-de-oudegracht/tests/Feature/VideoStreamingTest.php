<?php

test('video supports byte range requests for seeking', function () {
    $fileSize = filesize(public_path('assets/videos/filmopname_oudegracht.mp4'));

    $this->get(route('video.stream'), ['Range' => 'bytes=0-1'])
        ->assertStatus(206)
        ->assertHeader('Accept-Ranges', 'bytes')
        ->assertHeader('Content-Range', "bytes 0-1/{$fileSize}")
        ->assertHeader('Content-Length', '2');
});

test('video rejects ranges outside the file', function () {
    $fileSize = filesize(public_path('assets/videos/filmopname_oudegracht.mp4'));

    $this->get(route('video.stream'), ['Range' => "bytes={$fileSize}-"])
        ->assertStatus(416)
        ->assertHeader('Content-Range', "bytes */{$fileSize}");
});

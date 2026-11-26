<?php

use App\Support\VideoRoute;

uses(Tests\TestCase::class);

test('route endpoints map to the start and end of the video', function () {
    $route = config('video.route');
    $start = $route[0];
    $end = $route[count($route) - 1];

    expect(VideoRoute::progress($start[0], $start[1]))->toBe(0.0)
        ->and(VideoRoute::progress($end[0], $end[1]))->toBe(1.0);
});

test('coordinates along the route map to a video timestamp', function () {
    $route = config('video.route');
    $start = $route[0];
    $end = $route[1];
    $midpoint = [
        ($start[0] + $end[0]) / 2,
        ($start[1] + $end[1]) / 2,
    ];

    $progress = VideoRoute::progress($midpoint[0], $midpoint[1]);

    expect($progress)->toBeGreaterThan(0)
        ->toBeLessThan(1)
        ->and(VideoRoute::timestamp($midpoint[0], $midpoint[1], 120.0))
        ->toBe(round($progress * 120, 3));
});

test('existing post coordinates calculate a timestamp after the start of the video', function () {
    expect(VideoRoute::timestamp(52.089744, 5.1213025, 182.912))
        ->toBe(36.246);
});

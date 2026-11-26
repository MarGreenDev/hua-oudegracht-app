# How the map and video stay in sync

This application connects three things:

1. A post's latitude and longitude.
2. The post's position along the Oudegracht route.
3. The video time corresponding to that position.

The key files are:

| File | Responsibility |
| --- | --- |
| `config/video.php` | Defines the video duration and ordered route coordinates. |
| `resources/js/video-route.js` | Projects coordinates onto the route and returns route progress. |
| `app/Support/VideoRoute.php` | Server-side equivalent that calculates a timestamp in seconds. |
| `app/Http/Controllers/PostController.php` | Calculates and saves the timestamp when a post is created or edited. |
| `routes/web.php` and `resources/views/app.blade.php` | Pass posts and route coordinates to the map page. |
| `resources/js/app.js` | Creates map markers, seeks the video when a marker is clicked, and moves the boat with video playback. |
| `app/Http/Controllers/VideoController.php` | Streams the MP4 with HTTP byte-range support, which browsers need to seek inside it. |

## Route and video duration

`config/video.php` holds both values used by the calculation:

```php
return [
    'duration' => 182.912,
    'route' => [
        [52.0891988, 5.1211682],
        [52.0894500, 5.1209800],
        // More [latitude, longitude] points, in video-travel order.
        [52.0915523, 5.1197017],
    ],
];
```

The first coordinate is the beginning of the trip in the video; the last is
the end. Keep these coordinates in travel order and keep `duration` in sync
with the actual MP4 duration. The same route is supplied to the browser as
`data-video-route` on `#post-map`.

## How coordinates become a timestamp

`VideoRoute::progress()` and `getVideoRouteProgress()` perform the same
calculation:

1. Treat each pair of consecutive route coordinates as a line segment.
2. Project the post coordinate onto every segment and clamp the projection
   between the segment endpoints.
3. Choose the closest projected point.
4. Divide the distance from the route start to that point by the total route
   length. The result is a progress value from `0` to `1`.

The server turns progress into seconds and rounds to milliseconds:

```php
$timestamp = round(
    VideoRoute::progress($latitude, $longitude) * $videoDuration,
    3,
);
```

`PostController` runs this calculation from the validated coordinates and
stores the result in `posts.video_timestamp`. On the homepage, `routes/web.php`
recalculates the timestamp for each post before passing it to the view. This
also means the map does not depend on old or incorrect saved timestamp values.

## Clicking a map marker

`resources/js/app.js` creates one Leaflet marker for each post. Its click
handler updates the post details and seeks the video to the post timestamp:

```js
marker.on('click', () => {
    renderPost(post);
    seekVideoToPost(post);
});
```

The seek waits for video metadata so the browser knows the video's duration.
It then pauses, assigns the selected time to `video.currentTime`, waits for
the browser's `seeked` event, and resumes playback. Selecting a different
marker while a seek is pending invalidates the previous seek.

The video element intentionally has no `autoplay` or `loop` attributes. Its
source is Laravel's `/video/filmopname_oudegracht.mp4` route rather than a
direct static-file URL.

## Moving the boat with the video

`syncBoat()` maps playback time to route distance:

```js
const progress = Math.max(0, Math.min(1, video.currentTime / video.duration));
const targetDistance = progress * routeLength;
```

It finds the route segment containing `targetDistance`, interpolates a
latitude/longitude on that segment, and moves the Leaflet boat marker there.
The map calls `syncBoat()` on `loadedmetadata`, `timeupdate`, and `seeked`, so
the boat updates during playback and immediately after a marker seek.

## Why MP4 byte ranges matter

Seeking into a video requires the browser to request only the relevant bytes
of the media file. `VideoController` handles `Range: bytes=...` requests and
returns `206 Partial Content` with `Content-Range` and `Accept-Ranges` headers.
Serving the MP4 as an ordinary full-file `200 OK` response can make browser
seeking fail or restart from the beginning.

## Working with Laravel Sail

The application is served from the Sail container, so run frontend builds
there:

```sh
./vendor/bin/sail npm run build
```

During development, Sail's `artisan dev` command runs Vite. Check the browser's
Network panel to confirm the page loads the current `resources/js/app.js` from
the Vite server (or the current manifest asset in production). Also check that
the video request returns `206 Partial Content` when it includes a `Range`
header.

To check the timestamp calculation tests and video-range tests:

```sh
./vendor/bin/sail artisan test tests/Unit/VideoRouteTest.php tests/Feature/VideoStreamingTest.php
```

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VideoController extends Controller
{
    public function __invoke(Request $request): StreamedResponse
    {
        $path = public_path('assets/videos/filmopname_oudegracht.mp4');
        $fileSize = filesize($path);
        $start = 0;
        $end = $fileSize - 1;
        $status = 200;
        $range = $request->header('Range');

        if ($range !== null) {
            if (! preg_match('/^bytes=(\d*)-(\d*)$/', $range, $matches)) {
                return response()->stream(
                    static fn () => null,
                    416,
                    [
                        'Accept-Ranges' => 'bytes',
                        'Content-Range' => "bytes */{$fileSize}",
                    ],
                );
            }

            if ($matches[1] === '') {
                $suffixLength = (int) $matches[2];
                if ($suffixLength <= 0) {
                    return response()->stream(
                        static fn () => null,
                        416,
                        [
                            'Accept-Ranges' => 'bytes',
                            'Content-Range' => "bytes */{$fileSize}",
                        ],
                    );
                }

                $start = max(0, $fileSize - $suffixLength);
            } else {
                $start = (int) $matches[1];
                if ($matches[2] !== '') {
                    $end = (int) $matches[2];
                }
            }

            if ($start >= $fileSize || $start > $end) {
                return response()->stream(
                    static fn () => null,
                    416,
                    [
                        'Accept-Ranges' => 'bytes',
                        'Content-Range' => "bytes */{$fileSize}",
                    ],
                );
            }

            $end = min($end, $fileSize - 1);
            $status = 206;
        }

        $contentLength = $end - $start + 1;
        $headers = [
            'Accept-Ranges' => 'bytes',
            'Content-Length' => (string) $contentLength,
            'Content-Type' => 'video/mp4',
        ];

        if ($status === 206) {
            $headers['Content-Range'] = "bytes {$start}-{$end}/{$fileSize}";
        }

        return response()->stream(
            static function () use ($path, $start, $contentLength): void {
                $file = fopen($path, 'rb');
                fseek($file, $start);

                $remaining = $contentLength;
                while ($remaining > 0 && ! feof($file)) {
                    $chunk = fread($file, min(8192, $remaining));
                    if ($chunk === false || $chunk === '') {
                        break;
                    }

                    echo $chunk;
                    $remaining -= strlen($chunk);
                    flush();
                }

                fclose($file);
            },
            $status,
            $headers,
        );
    }
}

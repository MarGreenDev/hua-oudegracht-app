<?php

use App\Support\VideoRoute;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $duration = (float) config('video.duration');

        DB::table('posts')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select(['id', 'latitude', 'longitude'])
            ->orderBy('id')
            ->each(function (object $post) use ($duration): void {
                DB::table('posts')
                    ->where('id', $post->id)
                    ->update([
                        'video_timestamp' => VideoRoute::timestamp(
                            (float) $post->latitude,
                            (float) $post->longitude,
                            $duration,
                        ),
                    ]);
            });
    }

    public function down(): void
    {
        // Corrected timestamps cannot be rolled back to their previous values.
    }
};

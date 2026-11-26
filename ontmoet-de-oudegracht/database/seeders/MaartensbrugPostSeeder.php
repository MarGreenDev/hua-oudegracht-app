<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Support\VideoRoute;
use Illuminate\Database\Seeder;

class MaartensbrugPostSeeder extends Seeder
{
    public function run(): void
    {
        Post::firstOrCreate(
            ['title' => 'Maartensbrug'],
            [
                'place' => 'Maartensbrug, Utrecht',
                'text_content' => 'The Maartensbrug crosses the Oudegracht in the historic centre of Utrecht.',
                'date' => now()->toDateString(),
                'latitude' => 52.0904111,
                'longitude' => 5.1204,
                'video_timestamp' => VideoRoute::timestamp(
                    52.0904111,
                    5.1204,
                    (float) config('video.duration'),
                ),
            ],
        );
    }
}

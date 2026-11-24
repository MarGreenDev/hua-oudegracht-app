<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('date');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->decimal('video_timestamp', 10, 3)->nullable()->after('longitude');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'video_timestamp']);
        });
    }
};

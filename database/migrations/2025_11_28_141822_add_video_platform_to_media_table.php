<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->enum('video_platform', ['youtube', 'dailymotion', 'server'])
                ->default('youtube')
                ->after('url');
            $table->string('video_file_path')->nullable()->after('video_platform');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn(['video_platform', 'video_file_path']);
        });
    }
};

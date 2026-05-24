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
        Schema::table('lecture_pages', function (Blueprint $table) {
            $table->string('youtube_url')->nullable()->after('thumbnail_path');
            $table->string('youtube_video_id')->nullable()->after('youtube_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lecture_pages', function (Blueprint $table) {
            $table->dropColumn(['youtube_url', 'youtube_video_id']);
        });
    }
};

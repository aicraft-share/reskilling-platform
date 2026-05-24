<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $header) {
            $header->id();
            $header->string('title');
            $header->text('description')->nullable();
            $header->string('thumbnail_path')->nullable();
            $header->string('status')->default('draft'); // draft, published, archived
            $header->integer('sort_order')->default(0);
            $header->timestamps();
            $header->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};

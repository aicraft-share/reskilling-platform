<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Course;
use App\Models\LecturePage;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if there are any existing lectures
        $exists = LecturePage::exists();

        if ($exists) {
            // Create a default course
            $defaultCourse = Course::create([
                'title' => '既定のコース',
                'description' => '移行前に登録されていた教材がまとめられたコースです。',
                'status' => 'published',
                'sort_order' => 1,
            ]);

            // Associate all existing lectures with this course
            // Also set a default section name
            LecturePage::whereNull('course_id')->update([
                'course_id' => $defaultCourse->id,
                'section_name' => '基本',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We probably don't want to delete everything in down()
        // but we can at least clear course_id
        LecturePage::query()->update(['course_id' => null, 'section_name' => null]);
        Course::where('title', '既定のコース')->delete();
    }
};

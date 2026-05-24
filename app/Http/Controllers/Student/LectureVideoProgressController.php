<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LecturePage;
use App\Models\LectureVideoProgress;
use Illuminate\Support\Facades\Auth;

class LectureVideoProgressController extends Controller
{
    public function store(Request $request, LecturePage $lecturePage)
    {
        $request->validate([
            'current_time' => 'required|numeric|min:0',
            'duration' => 'required|numeric|min:0',
        ]);

        $progress = LectureVideoProgress::firstOrCreate(
            ['user_id' => Auth::id(), 'lecture_page_id' => $lecturePage->id],
            ['max_position_seconds' => 0, 'progress_percent' => 0]
        );

        $currentTime = floor($request->current_time);
        $duration = floor($request->duration);

        if ($currentTime > $progress->max_position_seconds) {
            $progress->max_position_seconds = $currentTime;
        }

        $progress->last_position_seconds = $currentTime;
        $progress->last_watched_at = now();

        if ($duration > 0) {
            $percent = floor(($progress->max_position_seconds / $duration) * 100);
            $progress->progress_percent = min(100, $percent);
        }

        $progress->save();

        return response()->json([
            'success' => true,
            'max_position_seconds' => $progress->max_position_seconds,
            'progress_percent' => $progress->progress_percent,
        ]);
    }
}

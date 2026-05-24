<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LecturePage;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LectureController extends Controller
{
    public function show(LecturePage $lecturePage)
    {
        if (!$lecturePage->is_active) {
            abort(404);
        }

        $existingSubmissions = Submission::where('user_id', Auth::id())
            ->where('lecture_page_id', $lecturePage->id)
            ->latest()
            ->with(['items', 'reviewer'])
            ->get();

        $videoProgress = \App\Models\LectureVideoProgress::firstOrCreate(
            ['user_id' => Auth::id(), 'lecture_page_id' => $lecturePage->id],
            ['max_position_seconds' => 0, 'progress_percent' => 0]
        );

        return view('student.lectures.show', compact('lecturePage', 'existingSubmissions', 'videoProgress'));
    }
}

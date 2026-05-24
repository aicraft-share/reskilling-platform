<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LecturePage;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Fetch all submissions for the current student, including related lecture page and reviewer
        $feedbacks = \App\Models\Submission::where('user_id', $user->id)
            ->with(['lecturePage', 'reviewer.profile'])
            ->latest() // Standardize to created_at
            ->get();

        return view('student.feedbacks.index', compact('feedbacks'));
    }
}

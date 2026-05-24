<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LecturePage;
use App\Models\LectureVideoProgress;
use Illuminate\Support\Facades\Auth;

class StudentProgressController extends Controller
{
    public function show(User $student)
    {
        // Must be a student
        if (!$student->isStudent()) {
            abort(404);
        }

        // Must belong to a company assigned to the logged-in teacher
        $teacher = Auth::user();
        $isAssigned = $teacher->assignedCompanies()->where('id', $student->company_id)->exists();

        if (!$isAssigned) {
            abort(403, 'Unauthorized access to this student.');
        }

        // Fetch all active lectures and their progress
        $lecturePages = LecturePage::active()->orderBy('sort_order')->get();
        $progresses = LectureVideoProgress::where('user_id', $student->id)->get()->keyBy('lecture_page_id');

        return view('teacher.students.progress', compact('student', 'lecturePages', 'progresses'));
    }
}

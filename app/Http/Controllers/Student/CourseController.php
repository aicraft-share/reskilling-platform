<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\LecturePage;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::published()
            ->orderBy('sort_order', 'asc')
            ->withCount(['lecturePages' => function($query) {
                $query->where('is_active', true);
            }])
            ->get();

        return view('student.courses.index', compact('courses'));
    }

    public function show(Course $course)
    {
        if ($course->status !== 'published') {
            abort(404);
        }

        $user = auth()->user();

        $lecturePages = $course->lecturePages()
            ->active()
            ->with([
                'submissions' => function ($query) use ($user) {
                    $query->where('user_id', $user->id)->latest();
                },
                'lectureVideoProgresses' => function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                }
            ])
            ->get();

        return view('student.courses.show', compact('course', 'lecturePages'));
    }
}

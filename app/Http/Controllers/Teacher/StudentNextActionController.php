<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\LecturePage;
use App\Models\StudentNextAction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentNextActionController extends Controller
{
    /**
     * Show the form for creating a new next action for a specific student.
     */
    public function create(User $student)
    {
        // Security: Ensure this teacher is assigned to the student's company
        $teacher = Auth::guard('admin')->user();
        
        if (!$teacher->isAdmin()) {
            $isAssigned = $teacher->assignedCompanies()
                ->where('companies.id', $student->company_id)
                ->exists();

            if (!$isAssigned || !$student->isStudent()) {
                abort(403, 'この生徒に対する操作権限がありません。');
            }
        }

        // Fetch published courses with their active lecture pages for selection
        $courses = Course::published()->with(['lecturePages' => function($query) {
            $query->where('is_active', true)->orderBy('sort_order');
        }])->orderBy('sort_order')->get();

        // Get the current active action if any
        $currentAction = $student->currentNextAction()->with('lecturePages')->first();

        return view('teacher.student_next_actions.create', compact('student', 'courses', 'currentAction'));
    }

    /**
     * Store a newly created next action in storage.
     */
    public function store(Request $request, User $student)
    {
        $teacher = Auth::guard('admin')->user();

        // Security check
        if (!$teacher->isAdmin()) {
            $isAssigned = $teacher->assignedCompanies()
                ->where('companies.id', $student->company_id)
                ->exists();

            if (!$isAssigned || !$student->isStudent()) {
                abort(403, 'この生徒に対する操作権限がありません。');
            }
        }

        $validated = $request->validate([
            'lecture_page_ids' => ['required', 'array', 'min:1'],
            'lecture_page_ids.*' => ['exists:lecture_pages,id'],
            'instruction_text' => ['required', 'string'],
        ], [
            'lecture_page_ids.required' => '必須視聴講義を1つ以上選択してください。',
            'lecture_page_ids.min' => '必須視聴講義を1つ以上選択してください。',
            'instruction_text.required' => '課題・指示内容を入力してください。',
        ]);

        DB::transaction(function () use ($validated, $student, $teacher) {
            // Deactivate previous actions
            $student->nextActions()->update(['is_active' => false]);

            // Create new action
            $nextAction = StudentNextAction::create([
                'student_id' => $student->id,
                'teacher_id' => $teacher->id,
                'instruction_text' => $validated['instruction_text'],
                'is_active' => true,
            ]);

            // Attach lectures
            $nextAction->lecturePages()->attach($validated['lecture_page_ids']);
        });

        return redirect()->route('teacher.assignments.students')
            ->with('status', 'next-action-saved')
            ->with('message', $student->name . 'さんの「次回までにやること」を保存しました。');
    }
}

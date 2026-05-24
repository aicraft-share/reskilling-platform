<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        // Get submissions for students in companies assigned to this teacher
        $assignedCompanyIds = Auth::user()->assignedCompanies->pluck('id');

        $query = Submission::whereHas('user', function ($query) use ($assignedCompanyIds) {
            $query->whereIn('company_id', $assignedCompanyIds);
        })->with(['user.company', 'lecturePage']);

        // Filter by status (e.g. unreviewed)
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $submissions = $query->latest()->get();

        return view('teacher.submissions.index', compact('submissions'));
    }

    public function show(Submission $submission)
    {
        // Authorization check: Teacher must be assigned to student's company
        if (!Auth::user()->assignedCompanies->contains($submission->user->company_id)) {
            abort(403);
        }

        $submission->load(['items', 'user', 'lecturePage']);
        return view('teacher.submissions.show', compact('submission'));
    }

    public function review(Request $request, Submission $submission)
    {
        // Authorization check
        if (!Auth::user()->assignedCompanies->contains($submission->user->company_id)) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:' . Submission::STATUS_PASSED . ',' . Submission::STATUS_REVISION_REQUIRED,
            'teacher_comment' => 'required|string|max:1000',
        ], [
            'teacher_comment.required' => '判定コメントを入力してください。',
        ]);

        $submission->update([
            'status' => $validated['status'],
            'teacher_comment' => $validated['teacher_comment'],
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
        ]);

        // --- Notification Logic ---
        try {
            $student = $submission->user;
            
            if ($student && $student->email && $student->notify_feedback_posted) {
                \Illuminate\Support\Facades\Mail::to($student->email)->send(new \App\Mail\FeedbackPostedMail(
                    $student->name,
                    $submission->lecturePage->title,
                    $submission->status
                ));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Feedback Notification Error: ' . $e->getMessage());
        }
        // -------------------------

        return redirect()->route('teacher.submissions.index')->with('success', 'レビューを登録しました。');
    }
}

<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Submission::where('reviewed_by', auth()->id())
            ->with(['user.company', 'lecturePage']);

        // Filter by Status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        } else {
            // Default: Both passed and revision_required are considered "reviewed"
            // So we just exclude pending/submitted.
            $query->whereIn('status', [\App\Models\Submission::STATUS_PASSED, \App\Models\Submission::STATUS_REVISION_REQUIRED]);
        }

        // Filter by Student
        if ($request->filled('student_id')) {
            $query->where('user_id', $request->student_id);
        }

        // Search by Keyword (Student Name, Company Name, Assignment Title)
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('user', function ($uq) use ($keyword) {
                    $uq->where('name', 'like', "%{$keyword}%")
                        ->orWhereHas('company', function ($cq) use ($keyword) {
                            $cq->where('name', 'like', "%{$keyword}%");
                        });
                })->orWhereHas('lecturePage', function ($lq) use ($keyword) {
                    $lq->where('title', 'like', "%{$keyword}%");
                });
            });
        }

        $submissions = $query->orderBy('reviewed_at', 'desc')->paginate(20)->withQueryString();

        // Get unique students that this teacher has reviewed for the dropdown
        $students = \App\Models\User::whereHas('submissions', function ($q) {
            $q->where('reviewed_by', auth()->id());
        })->get(['id', 'name']);

        return view('teacher.feedbacks.index', compact('submissions', 'students'));
    }

    public function show(\App\Models\Submission $feedback)
    {
        // Must belong to the current teacher
        if ($feedback->reviewed_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $feedback->load(['user.company', 'lecturePage', 'items']);

        return view('teacher.feedbacks.show', compact('feedback'));
    }
}

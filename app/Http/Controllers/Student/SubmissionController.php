<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LecturePage;
use App\Models\Submission;
use App\Models\SubmissionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function index()
    {
        $submissions = Submission::where('user_id', Auth::id())
            ->with(['lecturePage', 'items'])
            ->latest()
            ->get();

        return view('student.submissions.index', compact('submissions'));
    }

    public function store(Request $request, LecturePage $lecturePage)
    {
        $request->validate([
            'files.*' => 'required|file|max:102400', // 100MB max per file
        ]);

        // Create Submission
        $submission = Submission::create([
            'user_id' => Auth::id(),
            'lecture_page_id' => $lecturePage->id,
            'status' => Submission::STATUS_SUBMITTED,
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                // Determine type
                $type = str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'file';

                $path = $file->store('submissions', 'public');

                SubmissionItem::create([
                    'submission_id' => $submission->id,
                    'file_path' => $path,
                    'file_type' => $type,
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        // --- Notification Logic ---
        try {
            $user = Auth::user();
            $teachers = $user->company
                ? $user->company->teachers()
                    ->whereNotNull('email')
                    ->where('notify_assignment_submitted', true)
                    ->get()
                : collect();

            foreach ($teachers as $teacher) {
                \Illuminate\Support\Facades\Mail::to($teacher->email)->send(
                    new \App\Mail\AssignmentSubmittedMail(
                        $teacher->name,
                        $user->name,
                        $lecturePage->title,
                        now()->format('Y-m-d H:i')
                    )
                );
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Submission Notification Error: ' . $e->getMessage());
        }
        // -------------------------

        return redirect()->back()->with('success', '課題を提出しました。');
    }

    public function downloadItem(Request $request, SubmissionItem $item)
    {
        // Authorization check
        $submission = $item->submission;
        $user = Auth::user();

        // Teachers can download if they are assigned to the student's company
        if ($user->isTeacher() && !$user->assignedCompanies->contains($submission->user->company_id)) {
            abort(403);
        }

        // Students can only download their own submissions
        if ($user->isStudent() && $submission->user_id !== $user->id) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($item->file_path)) {
            abort(404, 'File not found');
        }

        $absolutePath = Storage::disk('public')->path($item->file_path);

        return response()->download($absolutePath, $item->original_name, [
            'Content-Type' => Storage::disk('public')->mimeType($item->file_path),
            'Content-Disposition' => 'attachment; filename="' . $item->original_name . '"',
        ]);
    }

    public function previewItem(Request $request, SubmissionItem $item)
    {
        // Authorization check
        $submission = $item->submission;
        $user = Auth::user();

        // Teachers can download/preview if they are assigned to the student's company
        if ($user->isTeacher() && !$user->assignedCompanies->contains($submission->user->company_id)) {
            abort(403);
        }

        // Students can only download/preview their own submissions
        if ($user->isStudent() && $submission->user_id !== $user->id) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($item->file_path)) {
            abort(404, 'File not found');
        }

        $absolutePath = Storage::disk('public')->path($item->file_path);
        $mimeType = Storage::disk('public')->mimeType($item->file_path);

        // only certain files should be previewed inline, others should fall back to download
        $inlinableMimeTypes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
        ];

        $disposition = in_array($mimeType, $inlinableMimeTypes) ? 'inline' : 'attachment';

        return response()->file($absolutePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => $disposition . '; filename="' . $item->original_name . '"',
        ]);
    }
}

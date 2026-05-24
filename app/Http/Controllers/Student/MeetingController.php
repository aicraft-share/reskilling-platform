<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Meeting;
use Illuminate\Support\Facades\Auth;

class MeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Get Scheduled Meetings (Participant)
        // Load 'meetingLog' so we can show recording/summary info on the same row
        $meetings = $user->participatingMeetings()
            ->with([
                'company' => fn($q) => $q->withTrashed(),
                'creator' => fn($q) => $q->withTrashed(),
                'meetingLog',
            ])
            ->get()
            ->map(function ($m) {
                $m->_type = 'meeting';
                // Add a common key for deduplication: the log ID if it exists
                $m->_dedupe_key = $m->meeting_log_id ? "log-{$m->meeting_log_id}" : "mtg-{$m->id}";
                return $m;
            });

        // 2. Get orphan MeetingLogs (Logs not linked to a Meeting record)
        $logs = \App\Models\MeetingLog::whereHas('students', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })
            ->whereDoesntHave('meeting')
            ->with([
                'company' => fn($q) => $q->withTrashed(),
                'creator' => fn($q) => $q->withTrashed(),
            ])
            ->get()
            ->map(function ($l) {
                $l->_type = 'log';
                // Add a common key for deduplication: its own ID
                $l->_dedupe_key = "log-{$l->id}";
                return $l;
            });

        // 3. Merge, Deduplicate, and Sort
        // Using unique() on the collection ensures that even if DB relationships are inconsistent,
        // we only show one row per physical meeting.
        $merged = $meetings->concat($logs)
            ->unique('_dedupe_key')
            ->sortByDesc(function ($item) {
                return $item->scheduled_at ?? $item->started_at; 
            });

        // Hide sensitive fields for all items
        $merged->each(function ($item) {
            $item->makeHidden(['transcript_text', 'zoom_start_url', 'transcript_source']);
        });

        // 4. Paginate
        $page = request()->get('page', 1);
        $perPage = 10;
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $merged->forPage($page, $perPage),
            $merged->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('student.meetings.index', ['meetings' => $paginator]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Meeting $meeting)
    {
        $user = Auth::user();

        // Security check: Student must be a participant
        if (!$meeting->participants()->where('student_id', $user->id)->exists()) {
            abort(403, 'このミーティングに参加する権限がありません。');
        }

        $meeting->makeHidden(['zoom_start_url']);

        return view('student.meetings.show', compact('meeting'));
    }

    /**
     * Display the specified MeetingLog.
     */
    public function showLog(\App\Models\MeetingLog $meetingLog)
    {
        $user = Auth::user();

        // Security check: Student must be part of the log (users pivot)
        if (!$meetingLog->students()->where('users.id', $user->id)->exists()) {
            abort(403, 'このミーティングログを閲覧する権限がありません。');
        }

        // Hide sensitive fields
        // We DO expose transcript_status because the UI needs to show "generating/failed" messages based on it?
        // Requirement said: "student には以下を絶対に返さない/見せない ... transcript_status"
        // But also "summary_status 別表示 ... failed -> ...".
        // If we hide transcript_status, we can't implement the UI logic unless we map it to a safe 'summary_status' attribute.
        // Let's hide the raw fields and add a safe accessor attribute 'summary_status_label' or similar?
        // Or just hide 'transcript_text' which is the critical one.
        // The requirement "transcript_status" hidden might be strict.
        // Let's implement a computed attribute on the model or just map it here.
        // For now, I will hide 'transcript_text', 'transcript_source', 'transcript_uploaded_at'.
        // I will keep 'transcript_status' visible for now to drive the UI logic, OR I can map it to 'summary_status' and hide 'transcript_status'.
        // Let's try to map it to be safe and strictly follow requirements.

        $summaryStatus = 'not_generated';
        if ($meetingLog->transcript_summary) {
            $summaryStatus = 'ready';
        } elseif ($meetingLog->transcript_status === 'failed') {
            $summaryStatus = 'failed';
        } elseif ($meetingLog->transcript_status === 'ready') {
            // Transcript ready but no summary yet -> potentially generating or just not clicked yet.
            // Requirement says "not_generated / generating". 
            // Since generation is manual, "not_generated" is appropriate.
            $summaryStatus = 'not_generated';
        }

        $meetingLog->setAttribute('summary_status', $summaryStatus);

        $meetingLog->makeHidden([
            'transcript_text',
            'transcript_status',
            'transcript_source',
            'transcript_uploaded_at',
            'zoom_start_url'
        ]);

        // Reuse the same view? Or create new?
        // meeting_logs usually have started_at, etc.
        // View 'student.meetings.show' expects $meeting->scheduled_at.
        // I need to adapt the view or map the object.
        // Let's create a mapped object or use a different view 'student.meeting_logs.show'?
        // Creating a new view is cleaner but more work.
        // Reusing 'student.meetings.show' requires $meeting variable to match interface.
        // I already added 'scheduled_at' accessor to MeetingLog.
        // I need 'creator' relation (MeetingLog has 'creator').
        // I need 'company' relation (MeetingLog has 'company').
        // I need 'participants' relation? View doesn't show list of other students?
        // Let's check student.meetings.show view.

        return view('student.meetings.show', ['meeting' => $meetingLog]);
    }
}

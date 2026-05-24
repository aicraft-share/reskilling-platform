<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Company;
use App\Models\Meeting;
use App\Models\MeetingLog;
use App\Models\User;
use App\Services\Zoom\ZoomClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MeetingController extends Controller
{
    /**
     * Display a listing of the resource - merged Meeting + MeetingLog list.
     */
    public function index()
    {
        $user = Auth::user();
        $assignedCompanyIds = $user->assignedCompanies()->pluck('companies.id');

        // 1. Get Scheduled Zoom Meetings (with their linked MeetingLog)
        $meetings = Meeting::whereIn('company_id', $assignedCompanyIds)
            ->with([
                'company' => fn($q) => $q->withTrashed(),
                'participants',
                'meetingLog'
            ])
            ->get()
            ->map(function ($m) {
                $m->_type = 'meeting';
                return $m;
            });

        // 2. Get orphan MeetingLogs (no linked Zoom meeting — old MTG管理 records)
        $linkedLogIds = $meetings->pluck('meeting_log_id')->filter()->values();
        $orphanLogs = MeetingLog::whereIn('company_id', $assignedCompanyIds)
            ->whereNotIn('id', $linkedLogIds)
            ->with([
                'company' => fn($q) => $q->withTrashed(),
                'students'
            ])
            ->get()
            ->map(function ($l) {
                $l->_type = 'log';
                return $l;
            });

        // 3. Merge and Sort by date desc
        $merged = $meetings->concat($orphanLogs)->sortByDesc(function ($item) {
            return $item->scheduled_at;
        });

        // 4. Paginate manually
        $page = request()->get('page', 1);
        $perPage = 15;
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $merged->forPage($page, $perPage),
            $merged->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('teacher.meetings.index', ['meetings' => $paginator]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $companies = $user->assignedCompanies()->with('students')->get();
        $defaultTime = now()->addHour()->startOfHour()->format('Y-m-d\TH:i');

        return view('teacher.meetings.create', compact('companies', 'defaultTime'));
    }

    /**
     * Store a newly created Zoom meeting and auto-create its linked MeetingLog.
     */
    public function store(Request $request, ZoomClient $zoomClient)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'title' => 'required|string|max:255',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'participants' => 'array',
            'participants.*' => 'exists:users,id',
        ]);

        $user = Auth::user();
        if (!$user->assignedCompanies()->where('companies.id', $request->company_id)->exists()) {
            abort(403, 'この企業のMTGを作成する権限がありません。');
        }

        DB::beginTransaction();
        try {
            // 1. Create Zoom Meeting
            $zoomResult = $zoomClient->createMeeting(
                $request->title,
                date('Y-m-d\TH:i:s', strtotime($request->scheduled_at)),
                (int) $request->duration_minutes
            );

            if (!$zoomResult) {
                return back()->withInput()->with('error', 'Zoomミーティングの作成に失敗しました。');
            }

            // 2. Auto-create MeetingLog for this Zoom meeting
            $meetingLog = MeetingLog::create([
                'company_id' => $request->company_id,
                'title' => $request->title,
                'started_at' => $request->scheduled_at,
                'zoom_meeting_id' => $zoomResult['id'] ?? null,
                'zoom_join_url' => $zoomResult['join_url'] ?? null,
                'zoom_start_url' => $zoomResult['start_url'] ?? null,
                'created_by' => $user->id,
                'transcript_status' => 'not_uploaded',
            ]);

            // Attach participants (students) to the MeetingLog
            if (!empty($request->participants)) {
                $validStudents = User::whereIn('id', $request->participants)
                    ->where('company_id', $request->company_id)
                    ->where('role', User::ROLE_STUDENT)
                    ->pluck('id');

                $meetingLog->students()->sync($validStudents);
            }

            // 3. Create Meeting DB record and link to the MeetingLog
            $meeting = Meeting::create([
                'company_id' => $request->company_id,
                'title' => $request->title,
                'scheduled_at' => $request->scheduled_at,
                'duration_minutes' => $request->duration_minutes,
                'zoom_meeting_id' => $zoomResult['id'] ?? null,
                'zoom_join_url' => $zoomResult['join_url'] ?? null,
                'zoom_start_url' => $zoomResult['start_url'] ?? null,
                'zoom_passcode' => $zoomResult['password'] ?? null,
                'created_by' => $user->id,
                'meeting_log_id' => $meetingLog->id,
            ]);

            // 4. Attach participants to Meeting too
            if (!empty($request->participants)) {
                $validStudents = User::whereIn('id', $request->participants)
                    ->where('company_id', $request->company_id)
                    ->where('role', User::ROLE_STUDENT)
                    ->pluck('id');

                foreach ($validStudents as $studentId) {
                    \App\Models\MeetingParticipant::create([
                        'meeting_id' => $meeting->id,
                        'student_id' => $studentId,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('teacher.meetings.index')->with('success', 'MTGを作成しました！');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'エラーが発生しました: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource - now includes MeetingLog data (URL, transcript, summary).
     */
    public function show(Meeting $meeting)
    {
        $user = Auth::user();

        if (!$user->assignedCompanies()->where('companies.id', $meeting->company_id)->exists()) {
            abort(403);
        }

        $meeting->load(['company', 'participants.student', 'meetingLog']);
        $meetingLog = $meeting->meetingLog;

        return view('teacher.meetings.show', compact('meeting', 'meetingLog'));
    }

    /**
     * Update the linked MeetingLog fields (youtube_url, memo, transcript_summary).
     */
    public function update(Request $request, Meeting $meeting)
    {
        $user = Auth::user();
        if (!$user->assignedCompanies()->where('companies.id', $meeting->company_id)->exists()) {
            abort(403);
        }

        $request->validate([
            'youtube_url' => 'nullable|url',
            'memo' => 'nullable|string',
            'transcript_summary' => 'nullable|string',
        ]);

        // If no MeetingLog exists yet (old meetings), create one on the fly
        if (!$meeting->meeting_log_id) {
            $meetingLog = MeetingLog::create([
                'company_id' => $meeting->company_id,
                'title' => $meeting->title,
                'started_at' => $meeting->scheduled_at,
                'zoom_meeting_id' => $meeting->zoom_meeting_id,
                'zoom_join_url' => $meeting->zoom_join_url,
                'zoom_start_url' => $meeting->zoom_start_url,
                'created_by' => $user->id,
                'transcript_status' => 'not_uploaded',
            ]);
            $meeting->update(['meeting_log_id' => $meetingLog->id]);
        } else {
            $meetingLog = $meeting->meetingLog;
        }

        $meetingLog->update([
            'youtube_url' => $request->youtube_url,
            'memo' => $request->memo,
            'transcript_summary' => $request->has('transcript_summary') ? $request->transcript_summary : $meetingLog->transcript_summary,
        ]);

        return redirect()->route('teacher.meetings.show', $meeting)->with('success', 'MTG情報を更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Meeting $meeting, ZoomClient $zoomClient)
    {
        $user = Auth::user();
        if (!$user->assignedCompanies()->where('companies.id', $meeting->company_id)->exists()) {
            abort(403);
        }

        if ($meeting->zoom_meeting_id) {
            $zoomClient->deleteMeeting($meeting->zoom_meeting_id);
        }

        $meeting->delete();

        return redirect()->route('teacher.meetings.index')->with('success', 'MTGを削除しました。');
    }

    /**
     * Upload a transcript (.srt/.vtt) for the linked MeetingLog.
     */
    public function uploadTranscript(Request $request, Meeting $meeting, \App\Services\SubtitleParserService $parser)
    {
        $user = Auth::user();
        if (!$user->assignedCompanies()->where('companies.id', $meeting->company_id)->exists()) {
            abort(403);
        }

        $request->validate([
            'transcript_file' => 'required|file|mimes:srt,vtt|max:2048',
        ]);

        // Ensure a MeetingLog exists
        if (!$meeting->meeting_log_id) {
            $meetingLog = MeetingLog::create([
                'company_id' => $meeting->company_id,
                'title' => $meeting->title,
                'started_at' => $meeting->scheduled_at,
                'zoom_meeting_id' => $meeting->zoom_meeting_id,
                'zoom_join_url' => $meeting->zoom_join_url,
                'zoom_start_url' => $meeting->zoom_start_url,
                'created_by' => $user->id,
                'transcript_status' => 'not_uploaded',
            ]);
            $meeting->update(['meeting_log_id' => $meetingLog->id]);
            $meeting->load('meetingLog');
        }
        $meetingLog = $meeting->meetingLog;

        $file = $request->file('transcript_file');
        $extension = $file->getClientOriginalExtension();
        $content = $file->get();

        try {
            $text = $parser->parse($content, $extension);
            $meetingLog->update([
                'transcript_text' => $text,
                'transcript_status' => 'ready',
                'transcript_source' => 'youtube_caption',
                'transcript_uploaded_at' => now(),
            ]);
            return back()->with('success', '文字起こしファイルをアップロードしました。');
        } catch (\Exception $e) {
            $meetingLog->update(['transcript_status' => 'failed']);
            return back()->with('error', 'ファイルの解析に失敗しました: ' . $e->getMessage());
        }
    }

    /**
     * Generate AI summary for the linked MeetingLog.
     */
    public function summarize(Meeting $meeting, \App\Services\MeetingSummaryService $summaryService)
    {
        $user = Auth::user();
        if (!$user->assignedCompanies()->where('companies.id', $meeting->company_id)->exists()) {
            abort(403);
        }

        $meetingLog = $meeting->meetingLog;
        if (!$meetingLog || empty($meetingLog->transcript_text)) {
            return back()->with('error', '文字起こしテキストがありません。先にファイルをアップロードしてください。');
        }

        try {
            $summary = $summaryService->summarize($meetingLog->transcript_text);
            $meetingLog->update(['transcript_summary' => $summary]);
            return back()->with('success', 'AI要約を生成しました。');
        } catch (\Exception $e) {
            return back()->with('error', 'AI要約の生成に失敗しました: ' . $e->getMessage());
        }
    }
}

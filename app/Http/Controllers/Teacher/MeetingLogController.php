<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\MeetingLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MeetingLogController extends Controller
{
    /**
     * Display the MTG Management Dashboard.
     */
    public function index(Request $request, ?\App\Models\User $student = null)
    {
        $user = Auth::user();

        // 1. Prepare Data for Create Form
        $assignedCompanies = $user->assignedCompanies()->with([
            'students' => function ($query) {
                $query->select('users.id', 'users.company_id', 'users.name');
            }
        ])->get();

        $selectedCompanyId = old('company_id');
        $selectedStudentId = old('students') ? (is_array(old('students')) ? old('students')[0] : old('students')) : null;

        if ($student && $student->exists) {
            // Validate if teacher belongs to student's company
            if (!$assignedCompanies->contains('id', $student->company_id)) {
                abort(403, 'この生徒のMTGログを作成する権限がありません。');
            }
            $selectedCompanyId = $selectedCompanyId ?? $student->company_id;
            $selectedStudentId = $selectedStudentId ?? $student->id;
        }

        // 2. Get Logs for List
        $companyIds = $assignedCompanies->pluck('id');
        $logsQuery = MeetingLog::whereIn('company_id', $companyIds);

        // If a specific student is being viewed, filter logs to that student
        if ($student && $student->exists) {
            $logsQuery->whereHas('students', function ($q) use ($student) {
                $q->where('users.id', $student->id);
            });
        }

        $logs = $logsQuery->with(['students:id,name', 'company:id,name'])
            ->orderBy('started_at', 'desc')
            ->paginate(20);

        // 3. Default Time for Form
        $defaultTime = now()->setTimezone('Asia/Tokyo')->addHour()->startOfHour()->format('Y-m-d\TH:i');

        return view('teacher.meeting_logs.index', compact('logs', 'assignedCompanies', 'selectedCompanyId', 'selectedStudentId', 'student'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Redirect to unified index
        if ($request->route('student')) {
            return redirect()->route('teacher.students.mtgs', $request->route('student'));
        }
        return redirect()->route('teacher.meeting-logs.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, \App\Services\Zoom\ZoomClient $zoomClient)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'students' => 'required|array|min:1',
            'students.*' => 'exists:users,id',
            'title' => 'required|string|max:255',
            'started_at' => 'required|date',
            'youtube_url' => 'nullable|url',
            'memo' => 'nullable|string',
        ]);

        $user = Auth::user();

        // 1. Security Check: Teacher must be assigned to the company
        if (!$user->assignedCompanies()->where('companies.id', $request->company_id)->exists()) {
            abort(403, 'この企業のMTGログを作成する権限がありません。');
        }

        // 2. Data Consistency Check: All students must belong to the selected company
        $count = User::whereIn('id', $request->students)
            ->where('company_id', $request->company_id)
            ->where('role', User::ROLE_STUDENT)
            ->count();

        if ($count !== count($request->students)) {
            return back()->withInput()->withErrors(['students' => '選択された生徒の中に、指定された企業に所属していない生徒が含まれています。']);
        }

        // Zoom Logic: Simplified via unified ZoomClient
        try {
            DB::beginTransaction();

            $meetingData = $zoomClient->createMeeting(
                $request->title,
                \Carbon\Carbon::parse($request->started_at)->toIso8601String(),
                60 // Default duration
            );

            if (!$meetingData) {
                // If createMeeting returns null, it means a real API error occurred (not mock)
                throw new \Exception('Zoomミーティングの作成に失敗しました。認証情報を確認してください。');
            }

            $log = MeetingLog::create([
                'company_id' => $request->company_id,
                'title' => $request->title,
                'started_at' => $request->started_at,
                'youtube_url' => $request->youtube_url,
                'zoom_meeting_id' => $meetingData['id'],
                'zoom_join_url' => $meetingData['join_url'],
                'zoom_start_url' => $meetingData['start_url'] ?? null,
                'zoom_status' => 'scheduled',
                'memo' => $request->memo,
                'created_by' => $user->id,
            ]);

            $log->students()->sync($request->students);

            DB::commit();

            return redirect()->route('teacher.meeting-logs.index')->with('success', 'MTGログを作成しました。');

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('MTG Creation Failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'MTGの作成に失敗しました: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MeetingLog $meetingLog)
    {
        $user = Auth::user();

        // Security Check: Teacher must be assigned to the company of the log
        if (!$user->assignedCompanies()->where('companies.id', $meetingLog->company_id)->exists()) {
            abort(403, 'このMTGログを編集する権限がありません。');
        }

        return view('teacher.meeting_logs.edit', compact('meetingLog'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MeetingLog $meetingLog)
    {
        $user = Auth::user();

        // Security Check
        if (!$user->assignedCompanies()->where('companies.id', $meetingLog->company_id)->exists()) {
            abort(403, 'このMTGログを編集する権限がありません。');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'youtube_url' => 'nullable|url',
            'memo' => 'nullable|string',
            'transcript_summary' => 'nullable|string',
        ]);

        $meetingLog->update([
            'title' => $request->title,
            'youtube_url' => $request->youtube_url,
            'memo' => $request->memo,
            'transcript_summary' => $request->has('transcript_summary') ? $request->transcript_summary : $meetingLog->transcript_summary,
        ]);

        return redirect()->route('teacher.meeting-logs.show', $meetingLog)->with('success', 'MTGログを更新しました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(MeetingLog $meetingLog)
    {
        $user = Auth::user();

        // Security Check
        if (!$user->assignedCompanies()->where('companies.id', $meetingLog->company_id)->exists()) {
            abort(403, 'このMTGログを閲覧する権限がありません。');
        }

        // Eager load for display
        $meetingLog->load(['students', 'participants']);

        return view('teacher.meeting_logs.show', compact('meetingLog'));
    }

    /**
     * Upload a transcript file for the meeting log.
     */
    public function uploadTranscript(Request $request, MeetingLog $meetingLog, \App\Services\SubtitleParserService $parser)
    {
        $user = Auth::user();

        // Security Check
        if (!$user->assignedCompanies()->where('companies.id', $meetingLog->company_id)->exists()) {
            abort(403, 'このMTGログを編集する権限がありません。');
        }

        $request->validate([
            'transcript_file' => 'required|file|mimes:srt|max:2048',
        ]);

        $file = $request->file('transcript_file');
        $extension = $file->getClientOriginalExtension();
        $content = $file->get();

        try {
            $text = $parser->parse($content, $extension);

            $meetingLog->update([
                'transcript_text' => $text,
                'transcript_status' => 'ready', // User requested 'ready'
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
     * Generate summary using AI.
     */
    public function summarize(MeetingLog $meetingLog, \App\Services\MeetingSummaryService $summaryService)
    {
        $user = Auth::user();
        if (!$user->assignedCompanies()->where('companies.id', $meetingLog->company_id)->exists()) {
            abort(403, 'このMTGログを編集する権限がありません。');
        }

        if (empty($meetingLog->transcript_text)) {
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

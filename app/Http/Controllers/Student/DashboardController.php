<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ChatThread;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. 次回までにやること
        $nextAction = $user->currentNextAction()->with(['lecturePages.course', 'teacher'])->first();

        // 2. 次回MTG (upcoming: scheduled in the future, soonest first)
        $nextMeeting = $user->participatingMeetings()
            ->where('scheduled_at', '>', now())
            ->with(['creator'])
            ->orderBy('scheduled_at', 'asc')
            ->first();

        // 3. 最新チャット (latest 3 messages from the student's chat thread)
        $chatThread = ChatThread::where('student_id', $user->id)
            ->with(['messages' => function ($q) {
                $q->with('sender')->orderBy('created_at', 'desc')->limit(3);
            }, 'instructor'])
            ->orderByDesc('last_message_at')
            ->first();

        $latestMessages = $chatThread?->messages ?? collect();

        return view('student.dashboard', compact(
            'nextAction',
            'nextMeeting',
            'chatThread',
            'latestMessages'
        ));
    }
}

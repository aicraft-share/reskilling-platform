<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatThread;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ChatController extends Controller
{
    /**
     * Display a listing of chat threads.
     */
    public function index()
    {
        $user = Auth::user();
        $query = ChatThread::with(['student', 'instructor', 'company'])
            ->withCount([
                'messages as is_unread' => function ($q) use ($user) {
                    // Basic unread counting (if needed later)
                    // $q->where('sender_id', '!=', $user->id);
                }
            ]);

        // Scoping based on role
        if ($user->isAdmin()) {
            // Admin sees all
        } elseif ($user->isCompany()) {
            $query->where('company_id', $user->company_id);
        } elseif ($user->isInstructor() || $user->isTeacher()) {
            // Auto-create threads for all assigned students if not yet created
            $assignedCompanyIds = $user->assignedCompanies->pluck('id');
            $students = \App\Models\User::whereIn('company_id', $assignedCompanyIds)
                ->where('role', 'student')
                ->get();
            foreach ($students as $student) {
                $exists = ChatThread::where('student_id', $student->id)
                    ->where('instructor_id', $user->id)
                    ->exists();
                if (!$exists) {
                    ChatThread::create([
                        'student_id' => $student->id,
                        'instructor_id' => $user->id,
                        'company_id' => $student->company_id,
                    ]);
                }
            }
            $query->where('instructor_id', $user->id);
        } elseif ($user->isStudent()) {
            $query->where('student_id', $user->id);

            // Auto-create thread for student if none exists with their assigned instructor
            $currentThreatCount = $query->count();
            if ($currentThreatCount === 0 && $user->company) {
                $firstTeacher = $user->company->teachers()->first();
                if ($firstTeacher) {
                    ChatThread::create([
                        'student_id' => $user->id,
                        'instructor_id' => $firstTeacher->id,
                        'company_id' => $user->company_id,
                    ]);
                }
            }
        } else {
            abort(403, 'Unauthorized access to chats.');
        }

        $threads = $query->orderBy('last_message_at', 'desc')->get();

        return view('chats.index', compact('threads', 'user'));
    }

    /**
     * Direct link to a student's chat (find or create)
     */
    public function direct($studentId)
    {
        $user = Auth::user();
        $student = User::findOrFail($studentId);

        // Only teachers/instructors and admins can use the direct link to a student
        if (!$user->isTeacher() && !$user->isInstructor() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        // If teacher, verify $student belongs to one of their assigned companies
        if (($user->isTeacher() || $user->isInstructor()) && !$user->isAdmin()) {
            $assignedCompanyIds = $user->assignedCompanies->pluck('id');
            if (!$assignedCompanyIds->contains($student->company_id)) {
                abort(403, 'Permission denied. Student is not in your assigned companies.');
            }
        }

        // Find or create
        $instructorId = $user->isAdmin()
            ? ($student->company?->teachers()->first()?->id ?? $user->id)
            : $user->id;

        $thread = ChatThread::firstOrCreate(
            [
                'student_id' => $student->id,
                'instructor_id' => $instructorId,
                'company_id' => $student->company_id,
            ],
            [
                'last_message_at' => now(),
            ]
        );

        return redirect()->route('chats.show', $thread);
    }

    /**
     * Display the specified chat thread.
     */
    public function show(ChatThread $chat)
    {
        $this->authorizeAccess($chat);

        $messages = $chat->messages()->with('sender')->orderBy('created_at', 'asc')->get();
        $user = Auth::user();

        // Mark messages as read if they were sent by someone else and are not already read
        $chat->messages()
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('chats.show', compact('chat', 'messages', 'user'));
    }

    /**
     * Store a newly created chat message.
     */
    public function store(Request $request, ChatThread $chat)
    {
        $user = Auth::user();

        // Only explicitly allowed roles can post (Admins, Students, Teachers/Instructors)
        if (!$user->isAdmin() && !$user->isStudent() && !$user->isTeacher() && !$user->isInstructor()) {
            abort(403, 'Only students, instructors, and admins can send messages.');
        }

        $this->authorizeAccess($chat);

        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $message = $chat->messages()->create([
            'sender_id' => $user->id,
            'message' => $validated['message'],
        ]);

        $chat->update(['last_message_at' => now()]);

        // --- Notification Logic ---
        try {
            $recipient = ($user->id === $chat->student_id) ? $chat->instructor : $chat->student;
            
            if ($recipient && $recipient->email && $recipient->notify_new_chat) {
                \Illuminate\Support\Facades\Mail::to($recipient->email)->send(new \App\Mail\NewChatMessageMail(
                    $recipient->name,
                    $user->name,
                    $chat->id,
                    $validated['message']
                ));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Chat Notification Error: ' . $e->getMessage());
        }
        // -------------------------

        return redirect()->route('chats.show', $chat);
    }

    /**
     * Helper to verify if the current user has access to this thread
     */
    private function authorizeAccess(ChatThread $thread)
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCompany()) {
            if ($thread->company_id !== $user->company_id) {
                abort(403, 'You do not have permission to view this company\'s chats.');
            }
            return true;
        }

        if ($user->isTeacher() || $user->isInstructor()) {
            if ($thread->instructor_id !== $user->id) {
                abort(403, 'You do not have permission to view other instructors\' chats.');
            }
            return true;
        }

        if ($user->isStudent()) {
            if ($thread->student_id !== $user->id) {
                abort(403, 'You do not have permission to view other students\' chats.');
            }
            return true;
        }

        abort(403, 'Unauthorized access.');
    }
}

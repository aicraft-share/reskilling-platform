<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementRecipient;
use App\Models\User;
use App\Services\AdminOperationLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Unauthorized Access');

        $announcements = Announcement::with('creator')
            ->withCount([
                'recipients as total_recipients',
                'recipients as read_count' => function ($query) {
                    $query->where('is_read', true);
                },
                'recipients as unread_count' => function ($query) {
                    $query->where('is_read', false);
                }
            ])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Unauthorized Access');

        return view('admin.announcements.create');
    }

    public function store(Request $request, AdminOperationLogger $operationLogger)
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Unauthorized Access');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'target_scope_type' => 'required|in:all,instructors,companies,students',
        ]);

        DB::beginTransaction();
        try {
            // Create the Master Announcement
            $announcement = Announcement::create([
                'title' => $validated['title'],
                'body' => $validated['body'],
                'target_scope_type' => $validated['target_scope_type'],
                'created_by_admin_id' => auth()->id(),
            ]);

            // Determine recipients based on scope
            $targetRoles = [];
            if ($validated['target_scope_type'] === Announcement::SCOPE_ALL) {
                // Send to all except admins
                $targetRoles = [User::ROLE_TEACHER, User::ROLE_COMPANY, User::ROLE_STUDENT];
            } elseif ($validated['target_scope_type'] === Announcement::SCOPE_INSTRUCTORS) {
                $targetRoles = [User::ROLE_TEACHER];
            } elseif ($validated['target_scope_type'] === Announcement::SCOPE_COMPANIES) {
                $targetRoles = [User::ROLE_COMPANY];
            } elseif ($validated['target_scope_type'] === Announcement::SCOPE_STUDENTS) {
                $targetRoles = [User::ROLE_STUDENT];
            }

            // Fetch recipient user IDs
            $userIds = User::whereIn('role', $targetRoles)
                ->whereNull('deleted_at')
                ->pluck('role', 'id');

            // Map user roles to their respective string types for the pivot
            $roleTypeMap = [
                User::ROLE_TEACHER => 'instructor',
                User::ROLE_COMPANY => 'company',
                User::ROLE_STUDENT => 'student',
            ];

            // Prepare batch insert payload
            $recipientRecords = [];
            foreach ($userIds as $userId => $role) {
                $recipientRecords[] = [
                    'announcement_id' => $announcement->id,
                    'recipient_type' => $roleTypeMap[$role],
                    'recipient_id' => $userId,
                    'is_read' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Chunk insert to handle large numbers of users avoiding memory issues
            foreach (array_chunk($recipientRecords, 1000) as $chunk) {
                AnnouncementRecipient::insert($chunk);
            }

            $operationLogger->log(
                'create',
                'announcement',
                $announcement->id,
                $announcement->title,
                [],
                [
                    'title' => $announcement->title,
                    'target_scope_type' => $announcement->target_scope_type,
                    'recipient_count' => count($recipientRecords),
                ]
            );

            DB::commit();

            return redirect()->route('admin.announcements.index')->with('success', 'お知らせを配信しました');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', '配信に失敗しました: ' . $e->getMessage());
        }
    }

    public function show(Announcement $announcement)
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Unauthorized Access');

        $announcement->load('creator');

        // Grab stats efficiently
        $stats = $announcement->recipients()
            ->selectRaw('count(*) as total, sum(case when is_read = 1 then 1 else 0 end) as read_count')
            ->first();

        $total = $stats->total;
        $readCount = $stats->read_count;
        $unreadCount = $total - $readCount;

        return view('admin.announcements.show', compact('announcement', 'total', 'readCount', 'unreadCount'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AnnouncementRecipient;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        // Must be logged in
        abort_unless(auth()->check(), 401);

        // Fetch announcements strictly for this recipient
        $announcements = AnnouncementRecipient::with('announcement.creator')
            ->where('recipient_id', auth()->id())
            // Option: check recipient_type here too if user IDs could collide across roles, 
            // but user_id is the PK of the users table so it's unique by ID.
            ->join('announcements as a', 'announcement_recipients.announcement_id', '=', 'a.id')
            ->whereNull('a.deleted_at')
            ->orderBy('a.created_at', 'desc')
            ->select('announcement_recipients.*') // Ensure we only get pivot columns + eager loaded relation
            ->paginate(15);

        return view('announcements.index', compact('announcements'));
    }

    public function show($id)
    {
        abort_unless(auth()->check(), 401);

        $recipientRecord = AnnouncementRecipient::with('announcement.creator')
            ->where('recipient_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();

        // Mark as read if not already
        if (!$recipientRecord->is_read) {
            $recipientRecord->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }

        return view('announcements.show', compact('recipientRecord'));
    }
}

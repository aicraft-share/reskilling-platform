<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Meeting;
use App\Models\MeetingLog;

$user = User::where('email', 'test@gmail.com')->first();
if (!$user) {
    echo "User not found\n";
    exit;
}

echo "User ID: {$user->id}\n";

// 1. Meetings
$meetings = $user->participatingMeetings()->get();
echo "\n--- Meetings ---\n";
foreach ($meetings as $m) {
    echo "ID: {$m->id}, Title: {$m->title}, LogID: {$m->meeting_log_id}, Scheduled: {$m->scheduled_at}\n";
}

// 2. Logs
$logs = MeetingLog::whereHas('students', function ($q) use ($user) {
    $q->where('users.id', $user->id);
})->get();
echo "\n--- MeetingLogs ---\n";
foreach ($logs as $l) {
    $hasMeeting = $l->meeting()->exists();
    echo "ID: {$l->id}, Title: {$l->title}, HasMeetingRel: " . ($hasMeeting ? 'YES' : 'NO') . ", Scheduled: {$l->started_at}\n";
}

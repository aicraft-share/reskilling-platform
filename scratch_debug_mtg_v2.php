<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Meeting;
use App\Models\MeetingLog;
use Illuminate\Support\Facades\DB;

$user = User::where('email', 'test@gmail.com')->first();
if (!$user) {
    echo "User not found\n";
    exit;
}

echo "User ID: {$user->id}\n";

// Raw Pivot Checks
$meetingPartCount = DB::table('meeting_participants')->where('student_id', $user->id)->count();
echo "Meeting Participant pivot records: $meetingPartCount\n";

$logStudentCount = DB::table('meeting_log_students')->where('student_id', $user->id)->count();
echo "Meeting Log Student pivot records: $logStudentCount\n";

// 1. Get Scheduled Meetings (Participant)
$meetings = $user->participatingMeetings()->get();
echo "\n--- Meetings (Count: " . count($meetings) . ") ---\n";
foreach ($meetings as $m) {
    echo "ID: {$m->id}, Title: {$m->title}, LogID: " . ($m->meeting_log_id ?? 'NULL') . "\n";
}

// 2. Get Meeting Logs
$logs = MeetingLog::whereHas('students', function ($q) use ($user) {
    $q->where('users.id', $user->id);
})->get();
echo "\n--- MeetingLogs (Count: " . count($logs) . ") ---\n";
foreach ($logs as $l) {
    echo "ID: {$l->id}, Title: {$l->title}\n";
}

// 3. Simulated Logic check
$meetingsMap = $user->participatingMeetings()->get()->map(function($m) {
    return $m->meeting_log_id ? "log-{$m->meeting_log_id}" : "mtg-{$m->id}";
});

$logsMap = MeetingLog::whereHas('students', function ($q) use ($user) {
    $q->where('users.id', $user->id);
})->whereDoesntHave('meeting')->get()->map(function($l) {
    return "log-{$l->id}";
});

$merged = $meetingsMap->concat($logsMap);
$unique = $merged->unique();

echo "\n--- Simulated Deduplication ---\n";
echo "Total after merge: " . $merged->count() . "\n";
echo "Total after unique: " . $unique->count() . "\n";
echo "List: " . implode(', ', $unique->toArray()) . "\n";

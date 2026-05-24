<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Meeting;
use App\Models\MeetingLog;
use Illuminate\Support\Facades\DB;

echo "--- Scan All Meetings with title 'あああああ' ---\n";
$meetings = Meeting::where('title', 'like', '%あああああ%')->get();
foreach ($meetings as $m) {
    echo "Meeting ID: {$m->id}, LogID: " . ($m->meeting_log_id ?? 'NULL') . ", Company: {$m->company_id}\n";
    $participants = DB::table('meeting_participants')->where('meeting_id', $m->id)->pluck('student_id')->toArray();
    echo "  Participants: " . implode(', ', $participants) . "\n";
}

echo "\n--- Scan All MeetingLogs with title 'あああああ' ---\n";
$logs = MeetingLog::where('title', 'like', '%あああああ%')->get();
foreach ($logs as $l) {
    echo "Log ID: {$l->id}, Company: {$l->company_id}\n";
    $students = DB::table('meeting_log_students')->where('meeting_log_id', $l->id)->pluck('student_id')->toArray();
    echo "  Students: " . implode(', ', $students) . "\n";
    $hasMeeting = Meeting::where('meeting_log_id', $l->id)->exists();
    echo "  Linked to Meeting: " . ($hasMeeting ? 'YES' : 'NO') . "\n";
}

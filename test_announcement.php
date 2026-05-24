<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Announcement;
use App\Models\AnnouncementRecipient;
use Illuminate\Support\Facades\DB;

// Admin user
$admin = User::where('role', 'admin')->first();

// Create Announcement
$announcement = Announcement::create([
    'title' => 'Test Announcement All',
    'body' => 'This is a test notification for all users.',
    'target_scope_type' => 'all',
    'created_by_admin_id' => $admin->id,
]);

echo "Created Announcement ID: {$announcement->id}\n";

$targetRoles = [User::ROLE_TEACHER, User::ROLE_COMPANY, User::ROLE_STUDENT];
$userIds = User::whereIn('role', $targetRoles)->pluck('role', 'id');

$roleTypeMap = [
    User::ROLE_TEACHER => 'instructor',
    User::ROLE_COMPANY => 'company',
    User::ROLE_STUDENT => 'student',
];

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

AnnouncementRecipient::insert($recipientRecords);

$totalSent = count($recipientRecords);
echo "Recipients expected: {$totalSent}\n";
echo "Recipients actual: " . AnnouncementRecipient::where('announcement_id', $announcement->id)->count() . "\n";

// Pick a student and mark as read
$studentRecipient = AnnouncementRecipient::where('announcement_id', $announcement->id)
    ->where('recipient_type', 'student')
    ->first();

if ($studentRecipient) {
    $studentRecipient->update(['is_read' => true, 'read_at' => now()]);
    echo "Marked student {$studentRecipient->recipient_id} as read.\n";
}

// Fetch stats just like Admin controller
$stats = Announcement::find($announcement->id)->recipients()
    ->selectRaw('count(*) as total, sum(case when is_read = 1 then 1 else 0 end) as read_count')
    ->first();

echo "Stats from DB -> Total: {$stats->total}, Read: {$stats->read_count}\n";

// Clean up
$announcement->forceDelete();
echo "Cleanup done.\n";

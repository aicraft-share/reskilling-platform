<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});

// Explicit Logout Route (GET) to preventing 419 errors
Route::get('/logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout.get');

Route::get('/dashboard', function () {
    $adminUser = Auth::guard('admin')->user();
    $webUser = Auth::guard('web')->user();

    // Prioritize Based on Role
    if ($adminUser) {
        if ($adminUser->isAdmin()) return redirect()->route('admin.dashboard');
        if ($adminUser->isTeacher()) return redirect()->route('teacher.dashboard');
    }
    
    if ($webUser) {
        if ($webUser->isCompany()) return redirect()->route('company.dashboard');
        return redirect()->route('student.dashboard');
    }

    return redirect()->route('login');
})->middleware(['auth:web,admin', 'verified'])->name('dashboard');

Route::middleware('auth:web,admin')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Chat Routes (Unified across roles, authorized internally by ChatController)
    Route::get('/chats', [\App\Http\Controllers\ChatController::class, 'index'])->name('chats.index');
    Route::get('/chats/direct/{student}', [\App\Http\Controllers\ChatController::class, 'direct'])->name('chats.direct');
    Route::get('/chats/{chat}', [\App\Http\Controllers\ChatController::class, 'show'])->name('chats.show');
    Route::post('/chats/{chat}/messages', [\App\Http\Controllers\ChatController::class, 'store'])->name('chats.messages.store');

    // Backward-compatible announcement routes (some legacy views still call announcements.index/show)
    Route::get('/announcements', [App\Http\Controllers\AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('/announcements/{id}', [App\Http\Controllers\AnnouncementController::class, 'show'])->name('announcements.show');
});

// Admin Routes
Route::middleware(['auth:admin', RoleMiddleware::class . ':' . User::ROLE_ADMIN])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/companies/export', [App\Http\Controllers\Admin\CompanyExportController::class, 'index'])->name('companies.export');
        Route::get('/companies/export/csv', [App\Http\Controllers\Admin\CompanyExportController::class, 'export'])->name('companies.export.csv');

        Route::resource('companies', App\Http\Controllers\Admin\CompanyController::class);

        // 課題管理 (Assignment Management)
        // Note: This is inside the 'admin.' name prefix group, so the route name becomes 'admin.assignments.index'
        Route::get('/assignments', [\App\Http\Controllers\Admin\AssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/{user}/{lecturePage}', [\App\Http\Controllers\Admin\AssignmentController::class, 'show'])->name('assignments.show');

        // 権限管理 (Role Management)
        Route::get('/admins', [\App\Http\Controllers\Admin\AdminUserController::class, 'index'])->name('admins.index');
        Route::post('/admins', [\App\Http\Controllers\Admin\AdminUserController::class, 'store'])->name('admins.store');
        Route::get('/admins/{admin}', [\App\Http\Controllers\Admin\AdminUserController::class, 'show'])->name('admins.show');
        Route::get('/operation-logs', [\App\Http\Controllers\Admin\AdminOperationLogController::class, 'index'])->name('operation-logs.index');
        Route::get('/operation-logs/{operationLog}', [\App\Http\Controllers\Admin\AdminOperationLogController::class, 'show'])->name('operation-logs.show');

        Route::resource('teachers', App\Http\Controllers\Admin\TeacherController::class);
        Route::resource('courses', App\Http\Controllers\Admin\CourseController::class);
        Route::resource('students', App\Http\Controllers\Admin\StudentController::class);

        // Settings & Lecture Pages (moved to settings basically)
        Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
        Route::patch('/settings/profile', [App\Http\Controllers\Admin\SettingsController::class, 'updateProfile'])->name('settings.update-profile');

        // Zoom Settings
        Route::get('/zoom-settings', [App\Http\Controllers\Admin\ZoomSettingController::class, 'edit'])->name('zoom-settings.edit');
        Route::post('/zoom-settings/test', [App\Http\Controllers\Admin\ZoomSettingController::class, 'testConnection'])->name('zoom-settings.test');

        Route::post('/zoom/sync-mtgs', [App\Http\Controllers\Admin\ZoomSyncController::class, 'sync'])->name('zoom.sync');

        Route::get('/mtgs/exports', [App\Http\Controllers\Admin\MeetingLogController::class, 'export'])->name('mtgs.export');
        Route::get('/mtgs/exports.csv', [App\Http\Controllers\Admin\MeetingLogController::class, 'downloadCsv'])->name('mtgs.export.csv');
        Route::get('/meeting-logs', [App\Http\Controllers\Admin\MeetingLogController::class, 'index'])->name('meeting-logs.index');
        // Admin MTG Log Detail/Transcript
        Route::get('/meeting-logs/{meetingLog}', [App\Http\Controllers\Admin\MeetingLogController::class, 'show'])->name('meeting-logs.show');
        Route::post('/meeting-logs/{meetingLog}/transcript', [App\Http\Controllers\Admin\MeetingLogController::class, 'uploadTranscript'])->name('meeting-logs.transcript.upload');
        Route::post('/meeting-logs/{meetingLog}/summarize', [App\Http\Controllers\Admin\MeetingLogController::class, 'summarize'])->name('meeting-logs.summarize');
        Route::post('/meeting-logs/{meetingLog}', [App\Http\Controllers\Admin\MeetingLogController::class, 'update'])->name('meeting-logs.update');

        Route::resource('meetings', App\Http\Controllers\Admin\MeetingController::class)->only(['index']);
        Route::patch('/lecture-pages/{lecturePage}/deactivate', [App\Http\Controllers\Admin\LecturePageController::class, 'deactivate'])->name('lecture-pages.deactivate');
        Route::resource('lecture-pages', App\Http\Controllers\Admin\LecturePageController::class);

        // お知らせ配信 (Announcement Delivery)
        Route::resource('announcements', App\Http\Controllers\Admin\AnnouncementController::class)->only(['index', 'create', 'store', 'show']);
    });

// Teacher Routes
Route::middleware(['auth:admin', RoleMiddleware::class . ':' . User::ROLE_TEACHER])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Teacher\DashboardController::class, 'index'])->name('dashboard');

        // Assignments (Consolidated UI)
        Route::get('/assignments/students', [App\Http\Controllers\Teacher\AssignmentController::class, 'students'])->name('assignments.students');
        Route::get('/assignments/companies', [App\Http\Controllers\Teacher\AssignmentController::class, 'companies'])->name('assignments.companies');

        // Legacy Lists -> Redirect to Assignments
        Route::get('/companies', function () {
            return redirect()->route('teacher.assignments.companies');
        })->name('companies.index');
        Route::get('/students', function () {
            return redirect()->route('teacher.assignments.students');
        })->name('students.index');

        // Keep details
        Route::get('/companies/{company}', [App\Http\Controllers\Teacher\CompanyController::class, 'show'])->name('companies.show');
        Route::get('/students/{student}/progress', [App\Http\Controllers\Teacher\StudentProgressController::class, 'show'])->name('students.progress.show');

        // Review specific routes
        Route::get('/submissions', [App\Http\Controllers\Teacher\SubmissionController::class, 'index'])->name('submissions.index');
        Route::get('/submissions/{submission}', [App\Http\Controllers\Teacher\SubmissionController::class, 'show'])->name('submissions.show');
        Route::post('/submissions/{submission}/review', [App\Http\Controllers\Teacher\SubmissionController::class, 'review'])->name('submissions.review');
        Route::get('/submissions/items/{item}/download', [App\Http\Controllers\Student\SubmissionController::class, 'downloadItem'])->name('submissions.download');
        Route::get('/submissions/items/{item}/preview', [App\Http\Controllers\Student\SubmissionController::class, 'previewItem'])->name('submissions.preview');

        // Feedbacks (History of reviewed submissions)
        Route::resource('feedbacks', App\Http\Controllers\Teacher\FeedbackController::class)->only(['index', 'show']);

        // Meetings (MTG - Unified: Zoom + log management)
        Route::resource('meetings', App\Http\Controllers\Teacher\MeetingController::class)->except(['edit', 'update']);
        Route::patch('/meetings/{meeting}', [App\Http\Controllers\Teacher\MeetingController::class, 'update'])->name('meetings.update');

        // Transcript & Summarize for Zoom Meetings (operate on the linked MeetingLog)
        Route::post('/meetings/{meeting}/transcript', [App\Http\Controllers\Teacher\MeetingController::class, 'uploadTranscript'])->name('meetings.transcript.upload');
        Route::post('/meetings/{meeting}/summarize', [App\Http\Controllers\Teacher\MeetingController::class, 'summarize'])->name('meetings.summarize');

        // Meeting Logs – all redirected to unified meetings routes
        Route::get('/meeting-logs', function () {
            return redirect()->route('teacher.meetings.index');
        })->name('meeting-logs.index');
        Route::get('/meeting-logs/{meetingLog}', function (\App\Models\MeetingLog $meetingLog) {
            // If the log is linked to a Zoom meeting, redirect to that meeting's detail
            if ($meetingLog->meeting) {
                return redirect()->route('teacher.meetings.show', $meetingLog->meeting);
            }
            // Otherwise fall back to the old log detail view
            return app(App\Http\Controllers\Teacher\MeetingLogController::class)->show($meetingLog);
        })->name('meeting-logs.show');
        Route::post('/meeting-logs', [App\Http\Controllers\Teacher\MeetingLogController::class, 'store'])->name('meeting-logs.store');
        Route::patch('/meeting-logs/{meetingLog}', [App\Http\Controllers\Teacher\MeetingLogController::class, 'update'])->name('meeting-logs.update');
        Route::post('/meeting-logs/{meetingLog}/transcript', [App\Http\Controllers\Teacher\MeetingLogController::class, 'uploadTranscript'])->name('meeting-logs.transcript.upload');
        Route::post('/meeting-logs/{meetingLog}/summarize', [App\Http\Controllers\Teacher\MeetingLogController::class, 'summarize'])->name('meeting-logs.summarize');
        Route::get('/students/{student}/meeting-logs/create', [App\Http\Controllers\Teacher\MeetingLogController::class, 'create'])->name('students.meeting-logs.create');
        Route::get('/students/{student}/meeting-logs', [App\Http\Controllers\Teacher\MeetingLogController::class, 'index'])->name('students.meeting-logs.index');
        Route::get('/students/{student}/mtgs', [App\Http\Controllers\Teacher\MeetingLogController::class, 'index'])->name('students.mtgs');
        // Settings
        Route::get('/settings', [App\Http\Controllers\Teacher\SettingController::class, 'edit'])->name('settings.edit');
        Route::patch('/settings/profile', [App\Http\Controllers\Teacher\SettingController::class, 'updateProfile'])->name('settings.update-profile');
        Route::patch('/settings/password', [App\Http\Controllers\Teacher\SettingController::class, 'updatePassword'])->name('settings.update-password');
        Route::patch('/settings/notifications', [App\Http\Controllers\Teacher\SettingController::class, 'updateNotifications'])->name('settings.update-notifications');

        // 次回までにやること (Next Steps)
        Route::get('/students/{student}/next-action', [App\Http\Controllers\Teacher\StudentNextActionController::class, 'create'])->name('students.next-action.create');
        Route::post('/students/{student}/next-action', [App\Http\Controllers\Teacher\StudentNextActionController::class, 'store'])->name('students.next-action.store');

        // お知らせ (Announcements)
        Route::get('/announcements', [App\Http\Controllers\AnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('/announcements/{id}', [App\Http\Controllers\AnnouncementController::class, 'show'])->name('announcements.show');
    });

// Student Routes
Route::middleware(['auth:web', RoleMiddleware::class . ':' . User::ROLE_STUDENT])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Student\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/courses', [App\Http\Controllers\Student\CourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/{course}', [App\Http\Controllers\Student\CourseController::class, 'show'])->name('courses.show');
        Route::get('/lectures/{lecturePage}', [App\Http\Controllers\Student\LectureController::class, 'show'])->name('lectures.show');
        Route::post('/lectures/{lecturePage}/progress', [App\Http\Controllers\Student\LectureVideoProgressController::class, 'store'])->name('lectures.progress');
        Route::post('/lectures/{lecturePage}/submit', [App\Http\Controllers\Student\SubmissionController::class, 'store'])->name('submissions.store');
        Route::get('/my-submissions', [App\Http\Controllers\Student\SubmissionController::class, 'index'])->name('submissions.index');
        Route::get('/submissions/items/{item}/download', [App\Http\Controllers\Student\SubmissionController::class, 'downloadItem'])->name('submissions.download');
        Route::get('/submissions/items/{item}/preview', [App\Http\Controllers\Student\SubmissionController::class, 'previewItem'])->name('submissions.preview');

        // Meetings
        Route::resource('meetings', App\Http\Controllers\Student\MeetingController::class)->only(['index', 'show']);
        Route::get('/meeting-logs/{meetingLog}', [App\Http\Controllers\Student\MeetingController::class, 'showLog'])->name('meeting-logs.show');

        // フィードバック（追加）
        Route::get('/feedbacks', [\App\Http\Controllers\Student\FeedbackController::class, 'index'])->name('feedbacks.index');

        // Settings
        Route::get('/settings', [App\Http\Controllers\Student\SettingController::class, 'edit'])->name('settings.edit');
        Route::patch('/settings/profile', [App\Http\Controllers\Student\SettingController::class, 'updateProfile'])->name('settings.update.profile');
        Route::patch('/settings/password', [App\Http\Controllers\Student\SettingController::class, 'updatePassword'])->name('settings.update.password');
        Route::patch('/settings/notifications', [App\Http\Controllers\Student\SettingController::class, 'updateNotifications'])->name('settings.update.notifications');

        // お知らせ (Announcements)
        Route::get('/announcements', [App\Http\Controllers\AnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('/announcements/{id}', [App\Http\Controllers\AnnouncementController::class, 'show'])->name('announcements.show');
    });

// Company Routes
Route::middleware(['auth:web', RoleMiddleware::class . ':' . User::ROLE_COMPANY])
    ->prefix('company')
    ->name('company.')
    ->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Company\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/students', [App\Http\Controllers\Company\StudentController::class, 'index'])->name('students.index');
        Route::get('/settings', [App\Http\Controllers\Company\SettingController::class, 'show'])->name('settings.show');
        Route::patch('/settings/profile', [App\Http\Controllers\Company\SettingController::class, 'updateProfile'])->name('settings.update-profile');
        Route::patch('/settings/password', [App\Http\Controllers\Company\SettingController::class, 'updatePassword'])->name('settings.update-password');

        // お知らせ (Announcements)
        Route::get('/announcements', [App\Http\Controllers\AnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('/announcements/{id}', [App\Http\Controllers\AnnouncementController::class, 'show'])->name('announcements.show');
    });

require __DIR__ . '/auth.php';

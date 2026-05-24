<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\LecturePage;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        // Require Admin Access
        abort_unless(auth()->user()->isAdmin(), 403, 'Unauthorized Access');

        // Parse Filters
        $companyId = $request->input('company_id');
        $studentName = $request->input('student_name');
        $courseId = $request->input('course_id');
        $statusFilter = $request->input('status'); // all | unsubmitted | reviewing | passed | failed

        // Query the Base Matrix (Students CROSS JOIN Courses) and LEFT JOIN Submission data
        // We use the query builder here since Eloquent doesn't natively support Cross Joins well
        // while loading relationships optimally.

        $baseQuery = DB::table('users as u')
            ->crossJoin('lecture_pages as lp')
            ->leftJoin('submissions as s', function ($join) {
                $join->on('u.id', '=', 's.user_id')
                    ->on('lp.id', '=', 's.lecture_page_id');
            })
            ->leftJoin('companies as c', 'u.company_id', '=', 'c.id')
            ->where('u.role', User::ROLE_STUDENT)
            ->whereNull('u.deleted_at')
            ->where('lp.is_active', true)
            ->where('lp.sort_order', '>', 0)
            ->whereNull('lp.deleted_at');

        // Apply Filters
        if ($companyId) {
            $baseQuery->where('u.company_id', $companyId);
        }

        if ($studentName) {
            $baseQuery->where('u.name', 'LIKE', '%' . $studentName . '%');
        }

        if ($courseId) {
            $baseQuery->where('lp.id', $courseId);
        }

        // Define Custom Status Logic internally to the query
        // - Not Submitted: s.id IS NULL
        // - Reviewing: s.id IS NOT NULL AND s.status = 'submitted'
        // - Passed: s.status = 'passed'
        // - Failed: s.status = 'revision_required'

        if ($statusFilter && $statusFilter !== 'all') {
            if ($statusFilter === 'unsubmitted') {
                $baseQuery->whereNull('s.id');
            } elseif ($statusFilter === 'reviewing') {
                $baseQuery->where('s.status', Submission::STATUS_SUBMITTED);
            } elseif ($statusFilter === 'passed') {
                $baseQuery->where('s.status', Submission::STATUS_PASSED);
            } elseif ($statusFilter === 'failed') {
                $baseQuery->where('s.status', Submission::STATUS_REVISION_REQUIRED);
            }
        }

        // Calculate KPIs (We must execute this BEFORE pagination limits are applied)
        // To accurately get KPI counts for the current filtered view, we clone the *unpaginated* query,
        // without the status filter applied (so we can count *all* statuses within the current domain of students/courses).

        $kpiQuery = DB::table('users as u')
            ->crossJoin('lecture_pages as lp')
            ->leftJoin('submissions as s', function ($join) {
                $join->on('u.id', '=', 's.user_id')
                    ->on('lp.id', '=', 's.lecture_page_id');
            })
            ->where('u.role', User::ROLE_STUDENT)
            ->whereNull('u.deleted_at')
            ->where('lp.is_active', true)
            ->where('lp.sort_order', '>', 0)
            ->whereNull('lp.deleted_at');

        if ($companyId) {
            $kpiQuery->where('u.company_id', $companyId);
        }
        if ($studentName) {
            $kpiQuery->where('u.name', 'LIKE', '%' . $studentName . '%');
        }
        if ($courseId) {
            $kpiQuery->where('lp.id', $courseId);
        }

        // Aggregate counts using conditional sum
        $kpis = $kpiQuery->selectRaw("
            SUM(CASE WHEN s.id IS NULL THEN 1 ELSE 0 END) as unsubmitted_count,
            SUM(CASE WHEN s.status = ? THEN 1 ELSE 0 END) as reviewing_count,
            SUM(CASE WHEN s.status = ? THEN 1 ELSE 0 END) as passed_count,
            SUM(CASE WHEN s.status = ? THEN 1 ELSE 0 END) as failed_count
        ", [
            Submission::STATUS_SUBMITTED,
            Submission::STATUS_PASSED,
            Submission::STATUS_REVISION_REQUIRED
        ])->first();

        // Finish preparing the main table columns
        // We select the raw IDs so we can link to the detail page
        $baseQuery->select([
            'u.id as student_id',
            'u.name as student_name',
            'u.email as student_email',
            'c.name as company_name',
            'lp.id as course_id',
            'lp.title as course_name',
            's.id as submission_id',
            's.status as submission_status',
            's.created_at as submitted_at',
            's.reviewed_at as reviewed_at',
            's.reviewed_by'
        ]);

        // Prioritize ordering: Status (Reviewing -> Failed -> Passed -> Unsubmitted) -> Course -> Company -> Student
        $baseQuery->orderByRaw("
            CASE 
                WHEN s.status = ? THEN 1
                WHEN s.status = ? THEN 2
                WHEN s.status = ? THEN 3
                ELSE 4
            END ASC
        ", [
            Submission::STATUS_SUBMITTED,
            Submission::STATUS_REVISION_REQUIRED,
            Submission::STATUS_PASSED
        ])
            ->orderBy('lp.sort_order', 'asc')
            ->orderBy('c.name', 'asc')
            ->orderBy('u.name', 'asc');

        $assignments = $baseQuery->paginate(50)->withQueryString();

        // Populate dropdown data for UI filters
        $companies = Company::orderBy('name')->get();
        $courses = LecturePage::active()->where('sort_order', '>', 0)->orderBy('sort_order')->get();

        // Optionally fetch Reviewer Names efficiently
        $reviewerIds = collect($assignments->items())->pluck('reviewed_by')->filter()->unique();
        $reviewers = User::whereIn('id', $reviewerIds)->pluck('name', 'id');

        // Attach Reviewer Names cleanly onto the paginated collection
        foreach ($assignments as $assignment) {
            $assignment->reviewer_name = $assignment->reviewed_by ? ($reviewers[$assignment->reviewed_by] ?? 'N/A') : null;
        }

        return view('admin.assignments.index', compact('assignments', 'kpis', 'companies', 'courses'));
    }

    public function show(User $user, LecturePage $lecturePage)
    {
        // Require Admin Access
        abort_unless(auth()->user()->isAdmin(), 403, 'Unauthorized Access');

        // Ensure standard constraints
        abort_unless($user->isStudent(), 404, 'User is not a student');

        $user->load('company');

        // Fetch the submission specifically
        $submission = Submission::where('user_id', $user->id)
            ->where('lecture_page_id', $lecturePage->id)
            ->with([
                'reviewer',
                'items' => function ($q) {
                    // optionally eager load submission items if attachments or links are meant to be shown later
                }
            ])
            ->first();

        return view('admin.assignments.show', compact('user', 'lecturePage', 'submission'));
    }
}

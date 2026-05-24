<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = User::where('role', User::ROLE_STUDENT)
            ->where('company_id', $user->company_id);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Sorting
        $query->orderBy('created_at', 'desc');

        // Calculate counts
        // Note: We assume all active students are assigned all active lectures for this MVP.
        $totalLectures = \App\Models\LecturePage::active()->count();

        // Eager load counts for each student
        $students = $query->withCount([
            'submissions as submission_count',
            'submissions as passed_count' => function ($query) {
                $query->where('status', \App\Models\Submission::STATUS_PASSED);
            }
        ])->paginate(20);

        return view('company.students.index', compact('students', 'totalLectures'));
    }
}

<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    /**
     * Display the students assigned to the teacher.
     */
    public function students(Request $request)
    {
        $user = Auth::user();

        // Base query for assigned companies
        $assignedCompanyIds = $user->assignedCompanies()->pluck('companies.id');
        $companies = $user->assignedCompanies;

        // Start student query
        $query = \App\Models\User::where('role', \App\Models\User::ROLE_STUDENT)
            ->whereIn('company_id', $assignedCompanyIds)
            ->with(['company', 'submissions']); // Eager load company and submissions for count

        // Apply filters
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Apply sorting
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        if ($sort === 'company_name') {
            // Sort by related company name
            $query->join('companies', 'users.company_id', '=', 'companies.id')
                ->orderBy('companies.name', $direction)
                ->select('users.*'); // ensure we only select user columns
        } else {
            $query->orderBy($sort, $direction);
        }

        $students = $query->paginate(20)->withQueryString();

        return view('teacher.assignments.students', compact('students', 'companies'));
    }

    /**
     * Display the companies assigned to the teacher.
     */
    public function companies(Request $request)
    {
        $user = Auth::user();

        $query = $user->assignedCompanies()->withCount('students');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $companies = $query->paginate(20)->withQueryString();

        return view('teacher.assignments.companies', compact('companies'));
    }
}

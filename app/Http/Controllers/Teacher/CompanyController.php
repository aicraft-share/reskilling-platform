<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    use \App\Http\Controllers\Traits\Sortable;

    public function index(Request $request)
    {
        $query = Company::whereHas('teachers', function ($q) {
            $q->where('users.id', Auth::id());
        })->withCount('students');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply dynamic sorting
        // Default: Custom status order then date desc
        if (!$request->has('sort')) {
            $query->orderByRaw("CASE status 
                WHEN 'free_trial' THEN 1 
                WHEN 'active' THEN 2 
                WHEN 'finished' THEN 3 
                ELSE 4 END ASC")
                ->orderBy('contract_start_date', 'desc');
        } else {
            $this->applySorting($query, $request, [
                'companies.name',
                'companies.status',
                'companies.contract_start_date',
                'students_count' // Allowed since we use withCount
            ], 'companies.created_at', 'desc');
        }

        $companies = $query->get();

        return view('teacher.companies.index', compact('companies'));
    }

    public function show(Company $company)
    {
        // Ensure teacher is assigned to this company
        if (!$company->teachers()->where('users.id', Auth::id())->exists()) {
            abort(403);
        }

        $company->load('students');
        return view('teacher.companies.show', compact('company'));
    }
}

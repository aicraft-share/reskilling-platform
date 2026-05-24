<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Services\AdminOperationLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Utils\AuthUtils;

class CompanyController extends Controller
{
    use \App\Http\Controllers\Traits\Sortable;

    public function index(Request $request)
    {
        $query = Company::with(['teachers', 'students'])->withCount('students');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('teacher_id')) {
            $query->whereHas('teachers', function ($q) use ($request) {
                $q->where('users.id', $request->teacher_id);
            });
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $this->applySorting(
            $query,
            $request,
            ['name', 'status', 'contract_start_date', 'training_end_date', 'contract_amount', 'payment_status'],
            'created_at',
            'desc'
        );

        $companies = $query->paginate(20)->withQueryString();
        $teachers = User::where('role', User::ROLE_TEACHER)->get();

        return view('admin.companies.index', compact('companies', 'teachers'));
    }

    public function create()
    {
        $teachers = User::where('role', User::ROLE_TEACHER)->get();
        return view('admin.companies.create', compact('teachers'));
    }

    public function store(Request $request, AdminOperationLogger $operationLogger)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'business_description' => 'nullable|string',
            'status' => 'required|in:free_trial,active,finished',
            'contract_start_date' => 'nullable|date',
            'training_end_date' => 'nullable|date|after_or_equal:contract_start_date',
            'contract_amount' => 'nullable|integer|min:0',
            'payment_status' => 'required|in:not_billed,billed,waiting_payment,paid',
            'teacher_ids' => 'nullable|array',
            'teacher_ids.*' => [
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', User::ROLE_TEACHER)),
            ],
        ]);

        $teacherIds = $request->input('teacher_ids', []);
        unset($validated['teacher_ids']);

        // Set teacher_id to the first selected teacher for backward compatibility
        $validated['teacher_id'] = !empty($teacherIds) ? $teacherIds[0] : null;

        $company = Company::create($validated);

        // Sync many-to-many teachers
        $company->teachers()->sync($teacherIds);

        // Automatically create a corporate user for this company
        $loginId = AuthUtils::generateLoginId(User::ROLE_COMPANY);
        $password = AuthUtils::generatePassword();

        User::create([
            'name' => $company->name . '（担当者）',
            'email' => $company->email,
            'login_id' => $loginId,
            'password' => Hash::make($password),
            'role' => User::ROLE_COMPANY,
            'company_id' => $company->id,
        ]);

        $operationLogger->log(
            'create',
            'company',
            $company->id,
            $company->name,
            [],
            $company->only([
                'name',
                'email',
                'status',
                'contract_start_date',
                'training_end_date',
                'contract_amount',
                'payment_status',
            ])
        );

        return redirect()->route('admin.companies.index')
            ->with('success', '企業を登録しました。')
            ->with('generated_credentials', [
                'login_id' => $loginId,
                'password' => $password,
                'name' => $company->name
            ]);
    }

    public function edit(Company $company)
    {
        $teachers = User::where('role', User::ROLE_TEACHER)->get();
        return view('admin.companies.edit', compact('company', 'teachers'));
    }

    public function update(Request $request, Company $company, AdminOperationLogger $operationLogger)
    {
        $before = $company->only([
            'name',
            'status',
            'contract_start_date',
            'training_end_date',
            'contract_amount',
            'payment_status',
            'business_description',
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'business_description' => 'nullable|string',
            'status' => 'required|in:free_trial,active,finished',
            'contract_start_date' => 'nullable|date',
            'training_end_date' => 'nullable|date|after_or_equal:contract_start_date',
            'contract_amount' => 'nullable|integer|min:0',
            'payment_status' => 'required|in:not_billed,billed,waiting_payment,paid',
            'teacher_ids' => 'nullable|array',
            'teacher_ids.*' => [
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', User::ROLE_TEACHER)),
            ],
        ]);

        $teacherIds = $request->input('teacher_ids', []);
        unset($validated['teacher_ids']);

        // Set teacher_id to the first selected teacher for backward compatibility
        $validated['teacher_id'] = !empty($teacherIds) ? $teacherIds[0] : null;

        $company->update($validated);

        // Sync many-to-many teachers
        $company->teachers()->sync($teacherIds);

        $operationLogger->log(
            'update',
            'company',
            $company->id,
            $company->name,
            $before,
            $company->fresh()->only(array_keys($before))
        );

        return redirect()->route('admin.companies.index')->with('success', '企業情報を更新しました。');
    }

    public function destroy(Company $company, AdminOperationLogger $operationLogger)
    {
        $before = $company->only([
            'name',
            'status',
            'contract_start_date',
            'training_end_date',
            'contract_amount',
            'payment_status',
            'business_description',
        ]);

        $company->delete();

        $operationLogger->log('delete', 'company', $company->id, $company->name, $before, []);
        return redirect()->route('admin.companies.index')->with('success', '企業を削除しました。');
    }
}

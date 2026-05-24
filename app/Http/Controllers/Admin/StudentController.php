<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Services\AdminOperationLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Utils\AuthUtils;

class StudentController extends Controller
{
    use \App\Http\Controllers\Traits\Sortable;

    public function index(Request $request)
    {
        // Join companies table to allow sorting by company name
        $query = User::select('users.*')
            ->where('role', User::ROLE_STUDENT)
            ->with('company')
            ->leftJoin('companies', 'users.company_id', '=', 'companies.id');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('users.email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('company_id')) {
            $query->where('users.company_id', $request->company_id);
        }

        // Handle custom sort for company name
        if ($request->sort === 'company_name') {
            $direction = $request->input('direction', 'asc');
            $query->orderBy('companies.name', $direction);
        } else {
            $this->applySorting($query, $request, [
                'name',
                'email',
                'created_at',
                'companies.status',
                'companies.contract_start_date'
            ], 'created_at', 'desc');
        }

        $students = $query->paginate(20)->withQueryString();
        $companies = Company::all();

        return view('admin.students.index', compact('students', 'companies'));
    }

    public function create()
    {
        $companies = Company::all();
        return view('admin.students.create', compact('companies'));
    }

    public function store(Request $request, AdminOperationLogger $operationLogger)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'company_id' => ['required', 'exists:companies,id'],
        ]);

        $loginId = AuthUtils::generateLoginId(User::ROLE_STUDENT);
        $password = AuthUtils::generatePassword();

        $student = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'login_id' => $loginId,
            'password' => Hash::make($password),
            'role' => User::ROLE_STUDENT,
            'company_id' => $request->company_id,
        ]);

        $operationLogger->log(
            'create',
            'student',
            $student->id,
            $student->name,
            [],
            $student->only(['name', 'email', 'company_id', 'role'])
        );

        return redirect()->route('admin.students.index')->with('success', "生徒アカウントを作成しました。ログインID: {$loginId} / パスワード: {$password}")->with('generated_credentials', ['login_id' => $loginId, 'password' => $password]);
    }

    public function edit(User $student)
    {
        $companies = Company::all();
        return view('admin.students.edit', compact('student', 'companies'));
    }

    public function update(Request $request, User $student, AdminOperationLogger $operationLogger)
    {
        $before = $student->only(['name', 'email', 'company_id']);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $student->id],
            'company_id' => ['required', 'exists:companies,id'],
        ]);

        $student->update([
            'name' => $request->name,
            'email' => $request->email,
            'company_id' => $request->company_id,
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => ['confirmed', Rules\Password::defaults()]]);
            $student->update(['password' => Hash::make($request->password)]);
            $before['password'] = '[REDACTED]';
        }

        $after = $student->fresh()->only(['name', 'email', 'company_id']);
        if ($request->filled('password')) {
            $after['password'] = '[REDACTED]';
        }
        $operationLogger->log('update', 'student', $student->id, $student->name, $before, $after);

        return redirect()->route('admin.students.index')->with('success', '生徒情報を更新しました。');
    }

    public function destroy(User $student, AdminOperationLogger $operationLogger)
    {
        $before = $student->only(['name', 'email', 'company_id', 'role']);
        $student->delete();
        $operationLogger->log('delete', 'student', $student->id, $student->name, $before, []);
        return redirect()->route('admin.students.index')->with('success', '生徒アカウントを削除しました。');
    }
}

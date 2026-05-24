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

class TeacherController extends Controller
{
    use \App\Http\Controllers\Traits\Sortable;

    public function index(Request $request)
    {
        $query = User::where('role', User::ROLE_TEACHER)->with(['assignedCompanies', 'profile']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $this->applySorting($query, $request, ['name', 'email', 'created_at'], 'created_at', 'desc');

        $teachers = $query->paginate(20)->withQueryString();

        return view('admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        $companies = Company::all();
        return view('admin.teachers.create', compact('companies'));
    }

    public function store(Request $request, AdminOperationLogger $operationLogger)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'company_ids' => ['array'],
            'company_ids.*' => ['exists:companies,id'],
            'years_of_experience' => ['nullable', 'integer'],
            'specialty_fields' => ['nullable', 'string'],
            'skills' => ['nullable', 'string'],
        ]);

        $loginId = AuthUtils::generateLoginId(User::ROLE_TEACHER);
        $password = AuthUtils::generatePassword();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'login_id' => $loginId,
            'password' => Hash::make($password),
            'role' => User::ROLE_TEACHER,
        ]);

        $user->profile()->create([
            'years_of_experience' => $request->years_of_experience,
            'specialty_fields' => $request->specialty_fields,
            'skills' => $request->skills,
        ]);

        if ($request->has('company_ids')) {
            $user->assignedCompanies()->sync($request->company_ids);
        }

        $operationLogger->log(
            'create',
            'instructor',
            $user->id,
            $user->name,
            [],
            array_merge(
                $user->only(['name', 'email', 'role']),
                [
                    'company_ids' => $request->input('company_ids', []),
                    'years_of_experience' => $request->years_of_experience,
                    'specialty_fields' => $request->specialty_fields,
                    'skills' => $request->skills,
                ]
            )
        );

        return redirect()->route('admin.teachers.index')->with('success', "講師を登録しました。ログインID: {$loginId} / パスワード: {$password}")->with('generated_credentials', ['login_id' => $loginId, 'password' => $password]);
    }

    public function edit(User $teacher)
    {
        $companies = Company::all();
        return view('admin.teachers.edit', compact('teacher', 'companies'));
    }

    public function update(Request $request, User $teacher, AdminOperationLogger $operationLogger)
    {
        $before = array_merge(
            $teacher->only(['name', 'email']),
            [
                'company_ids' => $teacher->assignedCompanies()->pluck('companies.id')->toArray(),
                'years_of_experience' => optional($teacher->profile)->years_of_experience,
                'specialty_fields' => optional($teacher->profile)->specialty_fields,
                'skills' => optional($teacher->profile)->skills,
            ]
        );

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $teacher->id],
            'company_ids' => ['array'],
            'years_of_experience' => ['nullable', 'integer'],
            'specialty_fields' => ['nullable', 'string'],
            'skills' => ['nullable', 'string'],
        ]);

        $teacher->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => ['confirmed', Rules\Password::defaults()]]);
            $teacher->update(['password' => Hash::make($request->password)]);
            $before['password'] = '[REDACTED]';
        }

        $teacher->profile()->updateOrCreate(
            ['user_id' => $teacher->id],
            [
                'years_of_experience' => $request->years_of_experience,
                'specialty_fields' => $request->specialty_fields,
                'skills' => $request->skills,
            ]
        );

        if ($request->has('company_ids')) {
            $teacher->assignedCompanies()->sync($request->company_ids);
        }

        $after = array_merge(
            $teacher->fresh()->only(['name', 'email']),
            [
                'company_ids' => $teacher->assignedCompanies()->pluck('companies.id')->toArray(),
                'years_of_experience' => optional($teacher->fresh()->profile)->years_of_experience,
                'specialty_fields' => optional($teacher->fresh()->profile)->specialty_fields,
                'skills' => optional($teacher->fresh()->profile)->skills,
            ]
        );
        if ($request->filled('password')) {
            $after['password'] = '[REDACTED]';
        }

        $operationLogger->log('update', 'instructor', $teacher->id, $teacher->name, $before, $after);

        return redirect()->route('admin.teachers.index')->with('success', '講師情報を更新しました。');
    }

    public function destroy(User $teacher, AdminOperationLogger $operationLogger)
    {
        $before = array_merge(
            $teacher->only(['name', 'email', 'role']),
            [
                'company_ids' => $teacher->assignedCompanies()->pluck('companies.id')->toArray(),
                'years_of_experience' => optional($teacher->profile)->years_of_experience,
                'specialty_fields' => optional($teacher->profile)->specialty_fields,
                'skills' => optional($teacher->profile)->skills,
            ]
        );

        $teacher->delete();
        $operationLogger->log('delete', 'instructor', $teacher->id, $teacher->name, $before, []);
        return redirect()->route('admin.teachers.index')->with('success', '講師を削除しました。');
    }
}

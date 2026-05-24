<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminOperationLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Utils\AuthUtils;

class AdminUserController extends Controller
{
    public function index()
    {
        // Require Admin Access
        abort_unless(auth()->user()->isAdmin(), 403, 'Unauthorized Access');

        // Fetch all admins ordered by newest first
        $admins = User::where('role', User::ROLE_ADMIN)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.admins.index', compact('admins'));
    }

    public function store(Request $request, AdminOperationLogger $operationLogger)
    {
        // Require Admin Access
        abort_unless(auth()->user()->isAdmin(), 403, 'Unauthorized Access');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        ]);

        $loginId = AuthUtils::generateLoginId(User::ROLE_ADMIN);
        $password = AuthUtils::generatePassword();

        $adminUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'login_id' => $loginId,
            'password' => Hash::make($password),
            'role' => User::ROLE_ADMIN,
        ]);

        $operationLogger->log(
            'create',
            'admin_user',
            $adminUser->id,
            $adminUser->name,
            [],
            $adminUser->only(['name', 'email', 'role'])
        );

        return redirect()->route('admin.admins.index')->with('success', "新しい管理者アカウントを登録しました。ログインID: {$loginId} / パスワード: {$password}")->with('generated_credentials', ['login_id' => $loginId, 'password' => $password]);
    }

    public function show(User $admin)
    {
        // Require Admin Access
        abort_unless(auth()->user()->isAdmin(), 403, 'Unauthorized Access');

        // Ensure user is actually an admin
        abort_unless($admin->isAdmin(), 404, 'User is not an admin');

        return view('admin.admins.show', compact('admin'));
    }
}

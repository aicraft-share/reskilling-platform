<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminOperationLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index', [
            'user' => auth()->user(),
        ]);
    }

    public function updateProfile(Request $request, AdminOperationLogger $operationLogger)
    {
        $user = auth()->user();
        $before = $user->only([
            'name',
            'email',
            'notify_assignment_submitted',
            'notify_new_chat',
            'notify_mtg_updated',
            'notify_feedback_posted',
            'notify_learning_updated',
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'current_password' => ['nullable', 'required_with:password', 'current_password:admin'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
            $before['password'] = '[REDACTED]';
        }

        $user->save();

        $after = $user->fresh()->only(array_keys(array_diff_key($before, ['password' => true])));
        if ($request->filled('password')) {
            $after['password'] = '[REDACTED]';
        }

        $operationLogger->log('update', 'setting', $user->id, '管理者設定', $before, $after);

        return back()->with('status', 'profile-updated');
    }
}

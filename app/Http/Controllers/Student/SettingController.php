<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    /**
     * Display the student's settings form.
     */
    public function edit()
    {
        return view('student.settings.edit', [
            'user' => \Illuminate\Support\Facades\Auth::guard('web')->user(),
        ]);
    }

    /**
     * Update the student's profile information.
     */
    public function updateProfile(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::guard('web')->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_path = $path;
        }

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'プロフィールを更新しました。']);
        }

        return back()->with('status', 'profile-updated');
    }

    /**
     * Update the student's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        \Illuminate\Support\Facades\Auth::guard('web')->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'パスワードを変更しました。']);
        }

        return back()->with('status', 'password-updated');
    }

    /**
     * Update the student's notification preferences.
     */
    public function updateNotifications(Request $request)
    {
        // Checkboxes only send data when checked.
        $request->validate([
            'notify_new_chat' => ['nullable', 'boolean'],
            'notify_feedback_posted' => ['nullable', 'boolean'],
            'notify_learning_updated' => ['nullable', 'boolean'],
        ]);

        \Illuminate\Support\Facades\Auth::guard('web')->user()->update([
            'notify_new_chat' => $request->has('notify_new_chat'),
            'notify_feedback_posted' => $request->has('notify_feedback_posted'),
            'notify_learning_updated' => $request->has('notify_learning_updated'),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => '通知設定を更新しました。']);
        }

        return back()->with('status', 'notifications-updated');
    }
}

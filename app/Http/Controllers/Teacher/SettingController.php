<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function edit()
    {
        return view('teacher.settings.edit', [
            'user' => \Illuminate\Support\Facades\Auth::guard('admin')->user(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::guard('admin')->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
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
        
        if ($request->ajax()) {
            return response()->json(['message' => 'プロフィールを更新しました。']);
        }

        return back()->with('status', 'profile-updated');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password:admin'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        \Illuminate\Support\Facades\Auth::guard('admin')->user()->update([
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
        ]);

        if ($request->ajax()) {
            return response()->json(['message' => 'パスワードを更新しました。']);
        }

        return back()->with('status', 'password-updated');
    }

    public function updateNotifications(Request $request)
    {
        // By default checkboxes don't send anything if unchecked.
        // We can handle this by using the `boolean` validation rule and fallback.
        $validated = $request->validate([
            'notify_assignment_submitted' => ['nullable', 'boolean'],
            'notify_new_chat' => ['nullable', 'boolean'],
            'notify_mtg_updated' => ['nullable', 'boolean'],
        ]);

        \Illuminate\Support\Facades\Auth::guard('admin')->user()->update([
            'notify_assignment_submitted' => $request->has('notify_assignment_submitted'),
            'notify_new_chat' => $request->has('notify_new_chat'),
            'notify_mtg_updated' => $request->has('notify_mtg_updated'),
        ]);

        if ($request->ajax()) {
            return response()->json(['message' => '通知設定を更新しました。']);
        }

        return back()->with('status', 'notifications-updated');
    }
}

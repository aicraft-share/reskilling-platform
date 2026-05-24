<?php

namespace App\Http\Controllers\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function show()
    {
        $user = \Illuminate\Support\Facades\Auth::guard('web')->user();
        $company = $user->company?->loadMissing('teachers');

        if (!$company) {
            abort(404, 'Company information not found.');
        }

        return view('company.settings.show', compact('company', 'user'));
    }

    public function updateProfile(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::guard('web')->user();
        $company = $user->company;

        if (!$company) {
            abort(404, 'Company information not found.');
        }

        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'business_description' => ['nullable', 'string', 'max:1000'],
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

        $user->email = $validated['email'];
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
            // Also sync company email if applicable
            $company->update(['email' => $validated['email']]);
        }
        $user->save();
        
        $company->update(['business_description' => $validated['business_description']]);

        if ($request->ajax()) {
            return response()->json(['message' => '企業情報を更新しました。']);
        }

        return back()->with('status', 'profile-updated');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        Auth::guard('web')->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        if ($request->ajax()) {
            return response()->json(['message' => 'パスワードを更新しました。']);
        }

        return back()->with('status', 'password-updated');
    }
}

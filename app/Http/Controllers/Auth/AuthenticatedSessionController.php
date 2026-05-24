<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the student/company login view.
     */
    public function create(): View|RedirectResponse
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login', ['isAdminLogin' => false]);
    }

    /**
     * Display the admin/teacher login view.
     */
    public function createAdmin(): View|RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login', ['isAdminLogin' => true]);
    }

    /**
     * Handle an incoming authentication request (Student/Company).
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate('web');

        $user = Auth::guard('web')->user();
        
        // Ensure this user is actually a student or company
        if ($user->isAdmin() || $user->isTeacher()) {
            Auth::guard('web')->logout();
            return back()->withErrors([
                'login_id' => 'こちらのログイン画面は「生徒・企業専用」です。管理者・講師の方は専用ログイン画面からログインしてください。',
            ]);
        }

        // Only regenerate session if no other persona is active, to preserve CSRF
        if (! Auth::guard('admin')->check()) {
            $request->session()->regenerate();
        }

        if ($user->isCompany()) {
            return redirect()->route('company.dashboard');
        }

        return redirect()->route('student.dashboard');
    }

    /**
     * Handle an incoming authentication request for admin/teacher.
     */
    public function storeAdmin(LoginRequest $request): RedirectResponse
    {
        $request->authenticate('admin');

        $user = Auth::guard('admin')->user();

        // Ensure this user is actually an admin or teacher
        if (!$user->isAdmin() && !$user->isTeacher()) {
            Auth::guard('admin')->logout();
            return back()->withErrors([
                'login_id' => 'こちらのログイン画面は「管理者・講師専用」です。生徒・企業の方は専用ログイン画面からログインしてください。',
            ]);
        }

        // Only regenerate session if no other persona is active, to preserve CSRF
        if (! Auth::guard('web')->check()) {
            $request->session()->regenerate();
        }

        return redirect()->route('dashboard');
    }

    /**
     * Destroy an authenticated session (Student/Company).
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $this->checkSessionCleanup($request);

        return redirect()->route('login');
    }

    /**
     * Destroy an authenticated session (Admin/Teacher).
     */
    public function destroyAdmin(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $this->checkSessionCleanup($request);

        return redirect()->route('admin.login');
    }

    /**
     * Clean up session only if no guards are active.
     */
    private function checkSessionCleanup(Request $request): void
    {
        // If NO guards are active, we can safely invalidate the entire session
        if (!Auth::guard('admin')->check() && !Auth::guard('web')->check()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
    }
}

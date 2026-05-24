<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $adminUser = Auth::guard('admin')->user();
        $webUser = Auth::guard('web')->user();

        // If either persona has the required role, allow access
        if ($adminUser && in_array($adminUser->role, $roles)) {
            return $next($request);
        }

        if ($webUser && in_array($webUser->role, $roles)) {
            return $next($request);
        }

        if (!$adminUser && !$webUser) {
            return redirect('login');
        }

        abort(403, 'Unauthorized action.');
    }
}

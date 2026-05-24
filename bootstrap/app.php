<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            if ($request->is('admin') || $request->is('admin/*') || $request->is('teacher') || $request->is('teacher/*')) {
                return route('admin.login');
            }
            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            // Determine the safe redirect route based on the current URL
            $route = ($request->is('admin/*') || $request->is('teacher/*') || $request->is('admin') || $request->is('teacher')) 
                ? 'admin.login' 
                : 'login';

            return redirect()->route($route)
                ->with('error', 'セッションの有効期限が切れました。ページを再読み込みしました。もう一度お試しください。');
        });
    })->create();

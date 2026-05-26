<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'No tienes permisos para acceder a este panel.',
            ]);
        }

        $role = $employee->role?->name;

        if (!in_array($role, ['admin', 'superadmin'])) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'No tienes permisos para acceder a este panel.',
            ]);
        }

        return $next($request);
    }
}
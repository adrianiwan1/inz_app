<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param string|null $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role = null)
    {
        // Sprawdzenie, czy użytkownik jest zalogowany i, jeśli jest, czy ma odpowiednią rolę
        if (!Auth::check()) {
            abort(403, 'You must be logged in to access this page.');
        }

        $user = Auth::user();

        // Sprawdzamy, czy użytkownik ma rolę 'admin' (admin ma dostęp do wszystkiego)
        if ($role === 'admin' && !$user->hasRole('admin')) {
            abort(403, 'Access denied.');
        }

        // Jeśli rola to 'employee', sprawdzamy, czy użytkownik jest pracownikiem
        if ($role === 'employee' && !$user->hasRole('employee')) {
            abort(403, 'Access denied.');
        }

        return $next($request);
    }
}

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
            return redirect('/')->with('error', 'You must be logged in to access this page.');
        }

        $user = Auth::user();

        // Sprawdzamy, czy użytkownik ma rolę 'admin' (admin ma dostęp do wszystkiego)
        if ($role === 'admin' && !$user->hasRole('admin')) {
            return redirect('/')->with('error', 'Access denied. Admins only.');
        }

        // Jeśli rola to 'employee', sprawdzamy, czy użytkownik jest pracownikiem
        if ($role === 'employee' && !$user->hasRole('employee')) {
            return redirect('/')->with('error', 'Access denied. Employees only.');
        }

        return $next($request);
    }
}

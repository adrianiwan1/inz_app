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
        if (!Auth::check()) {
            abort(403, 'You must be logged in to access this page.');
        }

        $user = Auth::user();

        // Obsługa wielu ról oddzielonych znakiem "|"
        $roles = explode('|', $role);

        if (!$user->hasAnyRole($roles)) {
            abort(403, 'Access denied.');
        }

        return $next($request);
    }
}

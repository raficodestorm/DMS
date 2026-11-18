<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EditorPermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // Admin has full access to everything
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Editor check
        if ($user->isEditor() && $user->hasPermission($permission)) {
            return $next($request);
        }

        abort(403, 'You do not have permission to access this resource.');
    }
}

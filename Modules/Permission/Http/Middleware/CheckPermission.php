<?php

namespace Modules\Permission\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        // Ensure the user is authenticated
        if (Auth::check()) {
            // Check if the authenticated user has the specified permission
            if (Auth::user()->can($permission)) {
                return $next($request);
            }
        }

        // Return unauthorized response if permission is not granted
        return response()->json(['message' => 'این عمل مجاز نمی‌باشد.'], 403);
    }
}

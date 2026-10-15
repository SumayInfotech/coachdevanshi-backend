<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
class FrontendAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('frontend.login');
        }
        $user = Auth::user();
        $roleName = Role::where('uuid', $user->role_uuid)->value('name');

        //Admin/backoffice users
        if (in_array($roleName, ['backend_user'])) {
            // Allow access to admin routes only
            // if ($request->is('admin/*')) {
            //     return $next($request);
            // }
            // If trying to access frontend routes, redirect to admin dashboard
            return redirect()->route('admin.dashboard');
        }

        // 🔹 Frontend users
        if (in_array($roleName, ['frontend_user','admin', 'super_admin'])) {
            return $next($request);
        }

        //Default: unauthorized
        abort(403, 'Unauthorized action For FrontendAuth Middleware.');

        // return $next($request);
    }
}

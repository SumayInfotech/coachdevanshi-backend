<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        //Only authentication check
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
    public function handleOld(Request $request, Closure $next)
    {
        
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();
        $roleName = Role::where('uuid', $user->role_uuid)->value('name');
        //SuperAdmin/Admin/Backend users
        if (in_array($roleName, ['super_admin', 'admin', 'backend_user'])) {
            // Allow access to admin routes only
            if ($request->is('admin/*')) {
                return $next($request);
            }
            // If trying to access frontend routes, redirect to admin dashboard
            return redirect()->route('admin.dashboard');
        }

        //Frontend users
        if (in_array($roleName, ['frontend_user'])) {
            // Allow access to frontend routes only
            // if ($request->is('frontend/*')) {
            //     return $next($request);
            // }
            // If trying to access admin routes, redirect to frontend dashboard
            return redirect()->route('frontend.new-tag');
        }

        // Default: unauthorized
        abort(403, 'Unauthorized action.From AdminAuth Middleware.');
    }
}

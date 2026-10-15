<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $roleName = Role::where('uuid', $user->role_uuid)->value('name');
            if (in_array($roleName, ['super_admin', 'admin', 'backend_user'])) {
                return redirect()->route('admin.dashboard');
            }
        }

        return $next($request);
    }
}

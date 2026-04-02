<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', ('login to access'));
        }
        if (! in_array($user->role, $roles)) {
            return redirect()->route('login')->with('error', ('Unauthorized'));
        }

        return $next($request);
    }
}

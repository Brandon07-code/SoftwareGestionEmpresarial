<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->hasMultipleRoles() && ! $user->activeRole()) {
            if (! $request->routeIs('role.select', 'role.store', 'logout')) {
                return redirect()->route('role.select');
            }
        }

        return $next($request);
    }
}

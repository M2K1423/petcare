<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401, 'Không có quyền truy cập.');
        }

        $roleSlug = $user->role?->slug;

        if (! $roleSlug || ! in_array($roleSlug, $roles, true)) {
            abort(403, 'Bạn không có quyền truy cập khu vực này.');
        }

        return $next($request);
    }
}

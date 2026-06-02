<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ActivityLog;

class LogActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Log successful authentication
        if ($request->path() === 'api/auth/login' && $response->getStatusCode() === 200) {
            $user = auth()->user();
            if ($user) {
                $user->last_login_at = now();
                $user->save();

                ActivityLog::log(
                    $user->id,
                    'login',
                    'User',
                    $user->id,
                    [],
                    ['ip' => $request->ip()],
                    "User logged in from {$request->ip()}"
                );
            }
        }

        // Log logout
        if ($request->path() === 'api/auth/logout' && $response->getStatusCode() === 200) {
            if (auth()->check()) {
                ActivityLog::log(
                    auth()->id(),
                    'logout',
                    'User',
                    auth()->id(),
                    [],
                    ['ip' => $request->ip()],
                    "User logged out from {$request->ip()}"
                );
            }
        }

        return $response;
    }
}

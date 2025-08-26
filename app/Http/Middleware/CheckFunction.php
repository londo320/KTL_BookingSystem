<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFunction
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $functionKey  The required function key
     */
    public function handle(Request $request, Closure $next, string $functionKey): Response
    {
        if (!auth()->check()) {
            abort(401, 'Authentication required.');
        }

        if (!auth()->user()->hasFunction($functionKey)) {
            abort(403, "You do not have permission to access this resource. Required function: {$functionKey}");
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InboundModuleAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if inbound module is enabled
        if (!Setting::get('inbound_module_enabled', true)) {
            // Module is disabled - return 404 to hide its existence
            abort(404);
        }

        return $next($request);
    }
}
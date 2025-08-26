<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;

class OutboundModuleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if outbound module is enabled
        if (!Setting::get('outbound_module_enabled', false)) {
            return redirect()->route('app.dashboard')
                ->with('error', 'Outbound module is currently disabled for testing. Contact your administrator to enable access.');
        }

        // Check if user has appropriate role (already handled by route middleware)
        // This middleware just checks if the module is enabled

        return $next($request);
    }
}
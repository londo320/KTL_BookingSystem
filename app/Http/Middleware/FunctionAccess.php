<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FunctionAccess
{
    /**
     * Handle an incoming request.
     * 
     * Maps route names to required functions for access control
     */
    public function handle(Request $request, Closure $next, ?string $function = null): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(401, 'Authentication required');
        }

        // Admin always has access
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // If specific function is provided, check it
        if ($function) {
            if (!$user->hasFunction($function)) {
                abort(403, "You don't have permission to access this function: {$function}");
            }
            return $next($request);
        }

        // Auto-detect function from route name
        $routeName = $request->route()->getName();
        $requiredFunctions = $this->mapRouteToFunction($routeName);
        
        if ($requiredFunctions) {
            $hasAccess = false;
            $functionsToCheck = is_array($requiredFunctions) ? $requiredFunctions : [$requiredFunctions];
            
            foreach ($functionsToCheck as $func) {
                if ($user->hasFunction($func)) {
                    $hasAccess = true;
                    break;
                }
            }
            
            if (!$hasAccess) {
                $functionList = is_array($requiredFunctions) ? implode(' OR ', $requiredFunctions) : $requiredFunctions;
                abort(403, "You don't have permission to access this function: {$functionList}");
            }
        }

        return $next($request);
    }

    /**
     * Map route names to function keys
     */
    private function mapRouteToFunction(string $routeName): string|array|null
    {
        // Remove prefix to get base route
        $baseRoute = str_replace(['app.', 'admin.', 'warehouse.', 'depot-admin.'], '', $routeName);
        
        $routeMap = [
            // Dashboard
            'dashboard' => ['dashboard.view', 'dashboard.warehouse'],
            
            // Bookings
            'bookings.index' => 'bookings.view',
            'bookings.show' => 'bookings.view', 
            'bookings.create' => 'bookings.create',
            'bookings.store' => 'bookings.create',
            'bookings.edit' => 'bookings.edit',
            'bookings.update' => 'bookings.edit',
            'bookings.destroy' => 'bookings.delete',
            'bookings.history' => ['bookings.history', 'bookings.view'],
            'bookings.rebook.show' => ['bookings.rebook', 'bookings.view'],
            'bookings.rebook.store' => ['bookings.rebook', 'bookings.edit'],
            'bookings.bulk-upload' => 'bookings.create',
            'bookings.bulk-upload.process' => 'bookings.create',
            
            // Customers
            'customers.index' => 'customers.view',
            'customers.show' => 'customers.view',
            'customers.create' => 'customers.create', 
            'customers.store' => 'customers.create',
            'customers.edit' => 'customers.edit',
            'customers.update' => 'customers.edit',
            'customers.destroy' => 'customers.delete',
            
            // Factory Bookings
            'factory-bookings.index' => 'factory-bookings.view',
            'factory-bookings.show' => 'factory-bookings.view',
            'factory-bookings.create' => 'factory-bookings.create',
            'factory-bookings.store' => 'factory-bookings.create',
            'factory-bookings.edit' => 'factory-bookings.edit',
            'factory-bookings.update' => 'factory-bookings.edit',
            'factory-bookings.destroy' => 'factory-bookings.delete',
            
            // Factory Booking Workflow
            'factory-booking-workflow.show' => ['factory-booking-workflow.show', 'factory-bookings.view'],
            'factory-booking-workflow.drop-trailer' => ['factory-booking-workflow.drop-trailer', 'factory-bookings.edit'],
            'factory-booking-workflow.move-to-location' => ['factory-booking-workflow.move-to-location', 'factory-bookings.edit'],
            'factory-booking-workflow.drop-trailer-detached' => ['factory-booking-workflow.drop-trailer-detached', 'factory-bookings.edit'],
            'factory-booking-workflow.move-to-bay' => ['factory-booking-workflow.move-to-bay', 'factory-bookings.edit'],
            'factory-booking-workflow.start-tipping' => ['factory-booking-workflow.start-tipping', 'factory-bookings.edit'],
            'factory-booking-workflow.complete-tipping' => ['factory-booking-workflow.complete-tipping', 'factory-bookings.edit'],
            'factory-booking-workflow.trailer-depart' => ['factory-booking-workflow.trailer-depart', 'factory-bookings.edit'],
            
            // Add more mappings as needed...
            'depots.index' => 'depots.view',
            'depots.create' => 'depots.create',
            'depots.edit' => 'depots.edit',
            'depots.destroy' => 'depots.delete',
            
            'carriers.index' => 'carriers.view',
            'carriers.create' => 'carriers.create', 
            'carriers.edit' => 'carriers.edit',
            'carriers.destroy' => 'carriers.delete',
            
            'users.index' => 'users.view',
            'users.create' => 'users.create',
            'users.edit' => 'users.edit',
            'users.destroy' => 'users.delete',
        ];
        
        return $routeMap[$baseRoute] ?? null;
    }
}
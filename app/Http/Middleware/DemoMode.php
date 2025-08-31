<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DemoMode
{
    /**
     * Handle an incoming request - protect Paul Carr's profile from modification
     */
    public function handle(Request $request, Closure $next)
    {
        // For GET requests, proceed normally
        if ($request->isMethod('GET')) {
            return $next($request);
        }

        // Check if this is an action targeting Paul Carr's profile
        $isPaulCarrTargeted = $this->isActionTargetingPaulCarr($request);
        $isCurrentUserPaulCarr = Auth::check() && Auth::user()->email === 'paul.carr@knowleslogistics.com';
        $user = Auth::user();
        
        // Paul Carr can always make changes to himself
        if ($isCurrentUserPaulCarr && $isPaulCarrTargeted) {
            return $next($request);
        }
        
        // If targeting Paul Carr and user is not Paul Carr, simulate
        if ($isPaulCarrTargeted && !$isCurrentUserPaulCarr) {
            // Simulate the action - don't proceed normally
        } else {
            // Check if user has permission to make actual changes
            $canMakeActualChanges = $this->userCanMakeActualChanges($user, $request);
            
            if ($canMakeActualChanges) {
                return $next($request);
            }
            // If no permission, fall through to simulation
        }

        // This is someone else trying to modify Paul Carr - simulate the action
        $routeName = $request->route()->getName();
        
        // Start a database transaction that we'll rollback
        DB::beginTransaction();
        
        try {
            // Process the request normally
            $response = $next($request);
            
            // Always rollback the transaction for demo user
            DB::rollBack();
            
            // Simulate success responses based on route patterns
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $this->getSuccessMessage($routeName, $request),
                    'demo_mode' => true
                ]);
            }
            
            // For form submissions, redirect with success message
            if (str_contains($routeName, 'store') || str_contains($routeName, 'update')) {
                return redirect()->back()->with('success', $this->getSuccessMessage($routeName, $request));
            }
            
            if (str_contains($routeName, 'destroy') || str_contains($routeName, 'delete')) {
                return redirect()->back()->with('success', 'Item deleted successfully (Demo Mode)');
            }
            
            // Default success redirect
            return redirect()->back()->with('success', 'Action completed successfully (Demo Mode)');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Even on errors, show success for demo mode
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Action completed successfully (Demo Mode)',
                    'demo_mode' => true
                ]);
            }
            
            return redirect()->back()->with('success', 'Action completed successfully (Demo Mode)');
        }
    }
    
    private function getSuccessMessage(string $routeName, Request $request): string
    {
        // Generate contextual success messages
        if (str_contains($routeName, 'booking')) {
            if (str_contains($routeName, 'store')) return 'Booking created successfully';
            if (str_contains($routeName, 'update')) return 'Booking updated successfully';
            if (str_contains($routeName, 'arrival')) return 'Vehicle arrival recorded successfully';
            if (str_contains($routeName, 'tipping')) return 'Tipping action completed successfully';
        }
        
        if (str_contains($routeName, 'movement') || str_contains($routeName, 'workflow')) {
            return 'Vehicle movement recorded successfully';
        }
        
        if (str_contains($routeName, 'depot-map')) {
            return 'Map action completed successfully';
        }
        
        if (str_contains($routeName, 'bay') || str_contains($routeName, 'location')) {
            return 'Location updated successfully';
        }
        
        return 'Action completed successfully';
    }
    
    private function isActionTargetingPaulCarr(Request $request): bool
    {
        // Check if this is a user management route targeting Paul Carr
        $route = $request->route();
        $routeName = $route->getName();
        
        // Get Paul Carr's user ID
        $paulCarrUser = \App\Models\User::where('email', 'paul.carr@knowleslogistics.com')->first();
        if (!$paulCarrUser) {
            return false;
        }
        
        // Check various ways Paul Carr might be targeted in routes
        
        // 1. Direct user ID in route parameters
        if ($route->hasParameter('user')) {
            $targetUser = $route->parameter('user');
            if ($targetUser instanceof \App\Models\User) {
                return $targetUser->id === $paulCarrUser->id;
            }
            // If it's just an ID, compare directly
            return (int)$targetUser === $paulCarrUser->id;
        }
        
        // 2. User ID in request data
        if ($request->has('user_id') && (int)$request->input('user_id') === $paulCarrUser->id) {
            return true;
        }
        
        // 3. Email in request data
        if ($request->has('email') && $request->input('email') === 'paul.carr@knowleslogistics.com') {
            return true;
        }
        
        // 4. Check if route contains Paul Carr's ID in the URL
        $url = $request->url();
        if (str_contains($url, '/users/' . $paulCarrUser->id)) {
            return true;
        }
        
        return false;
    }
    
    private function userCanMakeActualChanges($user, Request $request): bool
    {
        if (!$user) {
            return false;
        }
        
        // Paul Carr always has full access
        if ($user->email === 'paul.carr@knowleslogistics.com') {
            return true;
        }
        
        // Admin roles can make actual changes
        if ($user->hasRole(['admin', 'site-admin'])) {
            return true;
        }
        
        // Depot admin can make changes to their depot
        if ($user->hasRole('depot-admin')) {
            return true;
        }
        
        // Warehouse users need specific permissions based on the action
        if ($user->hasRole('warehouse')) {
            return $this->warehouseUserHasPermission($user, $request);
        }
        
        // Default: no permission for other roles
        return false;
    }
    
    private function warehouseUserHasPermission($user, Request $request): bool
    {
        $routeName = $request->route()->getName();
        
        // Check specific permissions based on route patterns
        if (str_contains($routeName, 'booking')) {
            return $user->hasPermissionTo('manage_bookings');
        }
        
        if (str_contains($routeName, 'tipping') || str_contains($routeName, 'workflow')) {
            return $user->hasPermissionTo('manage_tipping');
        }
        
        if (str_contains($routeName, 'movement') || str_contains($routeName, 'arrival') || str_contains($routeName, 'departure')) {
            return $user->hasPermissionTo('manage_movements');
        }
        
        if (str_contains($routeName, 'depot-map') || str_contains($routeName, 'bay') || str_contains($routeName, 'location')) {
            return $user->hasPermissionTo('manage_locations');
        }
        
        if (str_contains($routeName, 'user') || str_contains($routeName, 'admin')) {
            return $user->hasPermissionTo('manage_users');
        }
        
        // Default: warehouse users cannot make changes unless they have specific permissions
        return false;
    }
}
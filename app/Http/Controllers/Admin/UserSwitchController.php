<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserSwitchController extends Controller
{
    public function __construct()
    {
        // Apply auth middleware to all methods
        $this->middleware('auth');

        // Apply admin role only to switchTo method
        $this->middleware('role:admin')->only('switchTo');

        // Only allow initiating a switch in production for the specific authorized email
        // with preference enabled. switchBack is deliberately exempt: once a switch is
        // active, Auth::user() is the impersonated target (not paul.carr), so this check
        // would otherwise lock the admin out of their own return path in production.
        // switchBack() is safe to leave open here because it only ever returns to the
        // original_admin_id that a prior, already-authorized switchTo() call stored in
        // session - it grants no new access on its own.
        $this->middleware(function ($request, $next) {
            if (app()->isProduction()) {
                $user = Auth::user();
                // Only allow paul.carr@knowleslogistics.com in production with switch_user_enabled = true
                if (!$user || $user->email !== 'paul.carr@knowleslogistics.com' || !$user->switch_user_enabled) {
                    abort(404);
                }
            }
            return $next($request);
        })->except('switchBack');
    }

    /**
     * Switch to another user for testing
     */
    public function switchTo(Request $request, User $user)
    {
        $originalUser = Auth::user();

        // Store the original admin user ID in session
        if (! Session::has('original_admin_id')) {
            Session::put('original_admin_id', $originalUser->id);
            Session::put('switched_at', now());
        }

        // Switch to the target user
        Auth::login($user);

        // Determine redirect based on user role
        $redirectRoute = $this->getRedirectRoute($user);

        return redirect()->route($redirectRoute)
            ->with('success', "🔄 Switched to user: {$user->name} ({$user->email})");
    }

    /**
     * Switch back to original admin user
     */
    public function switchBack()
    {
        $originalAdminId = Session::get('original_admin_id');

        if (! $originalAdminId) {
            return redirect()->route('login')
                ->withErrors(['error' => 'No original admin session found.']);
        }

        $originalAdmin = User::find($originalAdminId);

        if (! $originalAdmin) {
            return redirect()->route('login')
                ->withErrors(['error' => 'Original admin user not found.']);
        }

        // Clear switching session data
        Session::forget(['original_admin_id', 'switched_at']);

        // Switch back to original admin
        Auth::login($originalAdmin);

        return redirect()->route('app.dashboard')
            ->with('success', "🔙 Switched back to admin: {$originalAdmin->name}");
    }

    /**
     * Get appropriate redirect route based on user role
     */
    private function getRedirectRoute(User $user): string
    {
        // Check roles in priority order
        if ($user->hasRole('admin')) {
            return 'app.dashboard';
        } elseif ($user->hasRole('site-admin')) {
            return 'site.dashboard'; // Correct route name
        } elseif ($user->hasRole('depot-admin')) {
            return 'app.dashboard';
        } elseif ($user->hasRole('gate-security')) {
            return 'app.arrivals.index'; // Gate check-in list, not the general dashboard
        } elseif ($user->hasRole('warehouse')) {
            return 'app.dashboard';
        } elseif ($user->hasRole('customer')) {
            return 'customer.bookings.index';
        }

        // Fallback to generic dashboard
        return 'app.dashboard';
    }
}

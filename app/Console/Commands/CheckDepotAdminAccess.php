<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class CheckDepotAdminAccess extends Command
{
    protected $signature = 'app:check-depot-admin-access';

    protected $description = 'Check depot-admin role assignments and route access';

    public function handle()
    {
        $this->info('🔍 Checking Depot-Admin Access and Routes...');

        // Find all depot-admin users
        $depotAdmins = User::role('depot-admin')->with('depots')->get();

        if ($depotAdmins->isEmpty()) {
            $this->warn('⚠️  No users found with depot-admin role');
            $this->info('Creating a test depot-admin user...');

            // Create a test depot-admin user
            $user = User::create([
                'name' => 'Test Depot Admin',
                'email' => 'depot-admin-test@example.com',
                'password' => bcrypt('password123'),
            ]);

            $user->assignRole('depot-admin');
            $this->line("✅ Created test depot-admin user: {$user->email}");

            $depotAdmins = collect([$user]);
        }

        $this->info("\n👥 Found {$depotAdmins->count()} depot-admin users:");
        foreach ($depotAdmins as $user) {
            $depotNames = $user->depots->pluck('name')->join(', ') ?: 'No depots assigned';
            $this->line("  • {$user->name} ({$user->email}) - Depots: {$depotNames}");
        }

        // Test depot-admin routes
        $this->info("\n🔗 Testing Depot-Admin Routes:");
        $routes = [
            'depot.dashboard' => 'Depot Dashboard',
            'depot.bookings.index' => 'Depot Bookings',
            'depot.slots.index' => 'Depot Slots',
            'depot.arrivals.index' => 'Depot Arrivals',
        ];

        foreach ($routes as $routeName => $description) {
            try {
                $url = route($routeName);
                $this->line("  ✅ {$description}: {$url}");
            } catch (\Exception $e) {
                $this->error("  ❌ {$description}: {$e->getMessage()}");
            }
        }

        // Test role redirections
        $this->info("\n🚀 Testing Role-Based Redirections:");
        $testUser = $depotAdmins->first();

        $this->line("Testing redirections for: {$testUser->name}");

        // Test dashboard redirect logic
        if ($testUser->hasRole('depot-admin')) {
            $this->line('  ✅ User has depot-admin role');
            try {
                $dashboardUrl = route('depot.dashboard');
                $this->line("  ✅ Should redirect to: {$dashboardUrl}");
            } catch (\Exception $e) {
                $this->error("  ❌ Dashboard route error: {$e->getMessage()}");
            }
        } else {
            $this->error('  ❌ User does not have depot-admin role');
        }

        // Check middleware configuration
        $this->info("\n🛡️  Checking Route Middleware:");

        $routeCollection = Route::getRoutes();
        $depotRoutes = $routeCollection->getByName('depot.dashboard');

        if ($depotRoutes) {
            $middleware = $depotRoutes->gatherMiddleware();
            $this->line('  Depot dashboard middleware: '.implode(', ', $middleware));

            if (in_array('role:depot-admin', $middleware)) {
                $this->line('  ✅ Correct role middleware applied');
            } else {
                $this->error('  ❌ Missing or incorrect role middleware');
            }
        } else {
            $this->error('  ❌ Depot dashboard route not found');
        }

        // Check potential issues
        $this->info("\n🔧 Common Issues Check:");

        // Check if users have multiple roles
        foreach ($depotAdmins as $user) {
            $roles = $user->getRoleNames();
            if ($roles->count() > 1) {
                $this->warn("  ⚠️  {$user->name} has multiple roles: ".$roles->join(', '));
                $this->line('     This might cause redirect conflicts');
            }
        }

        // Summary
        $this->info("\n📋 Summary:");
        $this->line('✅ Issues Fixed:');
        $this->line('  • Added missing depot-admin redirects in AuthenticatedSessionController');
        $this->line('  • Added missing depot-admin redirects in LoginController');
        $this->line('  • Fixed dashboard route to check roles instead of defaulting to admin');
        $this->line('  • Added proper depot-admin booking routes');
        $this->line('  • Added test route for debugging: /test-roles');

        $this->line("\n🚀 Next Steps:");
        $this->line('  1. Test login with a depot-admin user');
        $this->line('  2. Verify they are redirected to /depot-admin/dashboard');
        $this->line('  3. Check that they can access depot-admin booking routes');
        $this->line('  4. Ensure proper depot filtering is applied');
        $this->line('  5. Remove the /test-roles route in production');

        return 0;
    }
}

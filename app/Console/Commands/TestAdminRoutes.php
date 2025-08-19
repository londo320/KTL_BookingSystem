<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class TestAdminRoutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-admin-routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test admin routes and functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Admin Routes and Controllers...');

        // Key admin routes to test
        $routes = [
            'admin.dashboard' => 'Admin Dashboard',
            'admin.bookings.index' => 'Bookings List',
            'admin.depots.index' => 'Depots Management',
            'admin.users.index' => 'Users Management',
            'admin.slots.index' => 'Slots Management',
            'admin.customers.index' => 'Customers Management',
            'admin.products.index' => 'Products Management',
            'admin.settings.index' => 'Settings',
            'admin.slotReleaseRules.index' => 'Slot Release Rules',
        ];

        $this->info('Checking route existence...');

        foreach ($routes as $routeName => $description) {
            try {
                $url = route($routeName);
                $this->line("✅ {$description}: {$url}");
            } catch (\Exception $e) {
                $this->error("❌ {$description}: Route not found - {$e->getMessage()}");
            }
        }

        // Check if views exist
        $this->info("\nChecking key admin views...");
        $views = [
            'admin.dashboard' => 'resources/views/admin/dashboard.blade.php',
            'admin.bookings.index' => 'resources/views/admin/bookings/index.blade.php',
            'admin.depots.index' => 'resources/views/admin/depots/index.blade.php',
        ];

        foreach ($views as $viewName => $path) {
            $fullPath = base_path($path);
            if (file_exists($fullPath)) {
                $this->line("✅ View {$viewName}: exists");
            } else {
                $this->error("❌ View {$viewName}: missing at {$path}");
            }
        }

        // Check database connectivity and data
        $this->info("\nChecking database connectivity...");
        try {
            $userCount = User::count();
            $this->line("✅ Database connected - {$userCount} users found");

            $adminUsers = User::role('admin')->count();
            $this->line("✅ Admin users: {$adminUsers}");

        } catch (\Exception $e) {
            $this->error("❌ Database issue: {$e->getMessage()}");
        }

        $this->info("\n🏁 Admin routes test completed!");

        return 0;
    }
}

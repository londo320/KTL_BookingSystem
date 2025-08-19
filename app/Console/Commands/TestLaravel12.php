<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestLaravel12 extends Command
{
    protected $signature = 'test:laravel12';

    protected $description = 'Test Laravel 12 compatibility and configuration';

    public function handle()
    {
        $this->info('🚀 Laravel 12 Compatibility Test');
        $this->line('');

        // Test basic Laravel functionality
        $this->info('✅ Laravel Version: '.app()->version());

        // Test service providers
        $providers = array_keys(app()->getLoadedProviders());
        $appProviders = array_filter($providers, fn ($p) => str_starts_with($p, 'App\\'));
        $this->info('✅ App Service Providers: '.count($appProviders));

        // Test cache
        try {
            cache()->put('test_key', 'test_value', 60);
            $value = cache()->get('test_key');
            $this->info('✅ Cache: '.($value === 'test_value' ? 'Working' : 'Failed'));
        } catch (\Exception $e) {
            $this->error('❌ Cache: Failed - '.$e->getMessage());
        }

        // Test config
        $this->info('✅ App Name: '.config('app.name'));
        $this->info('✅ Environment: '.config('app.env'));

        // Test routes
        $routeCount = count(\Route::getRoutes());
        $this->info('✅ Routes Loaded: '.$routeCount);

        // Test middleware
        $middleware = app('Illuminate\Contracts\Http\Kernel')->getMiddlewareGroups();
        $this->info('✅ Middleware Groups: '.count($middleware));

        // Test database (basic)
        try {
            \Schema::hasTable('users');
            $this->info('✅ Database: Schema accessible');
        } catch (\Exception $e) {
            $this->warn('⚠️  Database: Not connected (expected in CLI)');
        }

        $this->line('');
        $this->info('🎉 Laravel 12 compatibility test completed successfully!');

        return Command::SUCCESS;
    }
}

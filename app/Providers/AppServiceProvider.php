<?php

namespace App\Providers;

use App\Models\Booking;
use App\Observers\BookingObserver;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Share a default null $slot in every view
        View::share('slot', null);

        // Register model observers
        Booking::observe(BookingObserver::class);

        // Register custom blade directives for function-based permissions
        $this->registerFunctionBladeDirectives();
    }

    /**
     * Register blade directives for function-based permissions
     */
    private function registerFunctionBladeDirectives(): void
    {
        // @canFunction('function.key') directive
        Blade::if('canFunction', function ($functionKey) {
            return auth()->check() && auth()->user()->hasFunction($functionKey);
        });

        // @cannotFunction('function.key') directive  
        Blade::if('cannotFunction', function ($functionKey) {
            return !auth()->check() || !auth()->user()->hasFunction($functionKey);
        });

        // @hasAnyFunction(['func1', 'func2']) directive
        Blade::if('hasAnyFunction', function ($functionKeys) {
            if (!auth()->check()) return false;
            foreach ($functionKeys as $key) {
                if (auth()->user()->hasFunction($key)) return true;
            }
            return false;
        });

        // @hasAllFunctions(['func1', 'func2']) directive
        Blade::if('hasAllFunctions', function ($functionKeys) {
            if (!auth()->check()) return false;
            foreach ($functionKeys as $key) {
                if (!auth()->user()->hasFunction($key)) return false;
            }
            return true;
        });
    }
}

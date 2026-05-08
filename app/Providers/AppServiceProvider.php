<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Opcodes\LogViewer\Facades\LogViewer;

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
        RateLimiter::for('downloads', function (Request $request): Limit {
            return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
        });

        if (app()->environment('production')) {
            LogViewer::auth(function ($request) {
                $user = $request->user();
                return $user && $user->hasRole('admin');
            });
        }
    }
}

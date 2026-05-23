<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

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
        // If running behind a proxy (ngrok, load balancer), trust proxy headers.
        // You can set TRUSTED_PROXIES in your .env (comma-separated) or use '*' for all.
        $trusted = env('TRUSTED_PROXIES', null);
        if ($trusted !== null) {
            $proxies = array_map('trim', explode(',', $trusted));
            Request::setTrustedProxies($proxies, 31);
        } else {
            // For local development with ngrok it's convenient to trust all proxies.
            if (app()->environment('local')) {
                Request::setTrustedProxies(['0.0.0.0/0'], 31);
            }
        }

        // If APP_URL is HTTPS, force URL generation to use HTTPS (fix mixed-content with ngrok)
        $appUrl = env('APP_URL', config('app.url'));
        if ($appUrl && str_starts_with($appUrl, 'https://')) {
            URL::forceScheme('https');
        }
    }
}

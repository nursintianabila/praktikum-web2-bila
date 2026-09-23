<?php

namespace App\Providers;

use App\Models\Ticket;
use App\Models\User;
use App\Policies\TicketPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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
        // Pendaftaran Policy untuk Model Ticket
        Gate::policy(Ticket::class, TicketPolicy::class);

        // Gate khusus Admin untuk melihat ringkasan laporan
        Gate::define('view-ticket-summary', function (User $user) {
            return (bool) $user->is_admin;
        });

        // Rate Limiter khusus endpoint Login (Maksimal 5x percobaan per menit per IP)
        RateLimiter::for('api-login', function (Request $request) {
            return Limit::perMinute(5)->by('login-ip:' . $request->ip());
        });

        // Rate Limiter umum untuk API v1 (Maksimal 60 request per menit per User ID)
        RateLimiter::for('api-v1', function (Request $request) {
            return Limit::perMinute(60)->by('api-user:' . $request->user()->id);
        });
    }
}
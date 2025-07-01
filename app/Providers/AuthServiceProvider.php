<?php

namespace App\Providers;

use App\Models\Gedung;
use App\Models\Jurusan;
use App\Models\User;
use App\Policies\GedungPolicy;
use App\Policies\JurusanPolicy;
use App\Policies\UserPolicy;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Jurusan::class, JurusanPolicy::class);
        Gate::policy(Gedung::class, GedungPolicy::class);
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Kode ini akan dieksekusi sebelum pengecekan permission lainnya
        // Jika user memiliki role 'Superadmin', maka return true (diizinkan)
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Superadmin') ? true : null;
        });
    }
}
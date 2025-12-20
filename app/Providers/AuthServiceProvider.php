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

        Gate::define('View:ManageFinance', function ($user) {
            return $user->hasAnyRole([
                'Pimpinan Lembaga Pengusul',
                'Kepala SPPG',
                'PJ Pelaksana',
                'Staf Kornas',
                'Direktur Kornas',
                'Staf Akuntan Kornas' // Added new role
            ]);
        });

        Gate::define('View:ProductionVerificationSetting', function ($user) {
            return $user->hasAnyRole([
                'Kepala SPPG',
                'PJ Pelaksana',
                'Superadmin',
                'Direktur Kornas',
                'Staf Kornas'
            ]);
        });
    }
}
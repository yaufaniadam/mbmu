<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CanAccessAdminPanel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->hasAnyRole([
            'Superadmin', 
            'Direktur Kornas', 
            'Staf Kornas', 
            'Staf Akuntan Kornas', 
            'Kepala SPPG'
        ])) {
            return $next($request);
        }

        abort(403, 'Unauthorized access to admin panel.');
    }
}

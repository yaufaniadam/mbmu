<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CanAccessProductionPanel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if (Auth::check() && $user->hasAnyRole([
            'Superadmin',
            'Ahli Gizi', 
            'Staf Gizi', 
            'Staf Pengantaran', 
            'Staf Akuntan'
        ])) {
            return $next($request);
        }

        \Illuminate\Support\Facades\Log::warning('Unauthorized access attempt to production panel', [
            'user_id' => $user?->id,
            'email' => $user?->email,
            'roles' => $user?->getRoleNames(),
        ]);

        abort(403, 'Unauthorized access to production panel.');
    }
}

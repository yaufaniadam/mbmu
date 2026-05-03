<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyWebhookSecret
{
    /**
     * Verify that the incoming webhook request contains a valid secret token.
     * The token can be sent as a query parameter or in the Authorization header.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('whatsapp.webhook_secret');

        // If no secret is configured, reject all webhook requests for safety
        if (empty($secret)) {
            abort(403, 'Webhook secret not configured.');
        }

        $providedToken = $request->query('token')
            ?? $request->header('X-Webhook-Secret')
            ?? $request->input('secret');

        if (!hash_equals($secret, (string) $providedToken)) {
            abort(403, 'Invalid webhook token.');
        }

        return $next($request);
    }
}

<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google reCAPTCHA v3
    |--------------------------------------------------------------------------
    */
    'site_key'   => env('RECAPTCHA_SITE_KEY', ''),
    'secret_key' => env('RECAPTCHA_SECRET_KEY', ''),

    /**
     * Minimum score to accept (0.0 = definitely bot, 1.0 = definitely human)
     * Recommended: 0.5
     */
    'min_score' => 0.5,
];

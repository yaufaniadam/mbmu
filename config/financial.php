<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Billing Period
    |--------------------------------------------------------------------------
    |
    | This value determines how many active production days are required
    | before an operational invoice is automatically generated for an SPPG.
    | Default is 10 days.
    |
    */
    'billing_period' => env('INVOICE_BILLING_PERIOD', 10),
];

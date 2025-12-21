<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:generate-bills')->everyTenSeconds();

// Auto-generate daily distribution plans for all SPPGs
Schedule::command('distribution:generate-daily')->dailyAt('00:01');

// Auto-generate SPPG Invoices every 10 active days
Schedule::command('invoice:generate')->dailyAt('00:05');

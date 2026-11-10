<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Prepaid subscriptions: take the hourly plan charges and pause the ones that ran out of balance.
// (Also happens on admin/website visits; this keeps idle restaurants up to date.)
Artisan::command('billing:settle', function () {
    $n = 0;
    \App\Models\Company::query()->each(function ($company) use (&$n) { \App\Support\Billing::settle($company); $n++; });
    $this->info("Settled {$n} companies.");
})->purpose('Charge running subscriptions from company balances');

\Illuminate\Support\Facades\Schedule::command('billing:settle')->hourly()->withoutOverlapping();

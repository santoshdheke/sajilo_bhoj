<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// billing:settle and its hourly schedule live in the Restaurant module (RestaurantServiceProvider).

// Daily database backup (see App\Console\Commands\BackupDatabase).
Schedule::command('db:backup')->dailyAt('02:00')->withoutOverlapping();

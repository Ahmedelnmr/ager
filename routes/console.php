<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\GenerateContractEndingNotificationsJob;
use App\Jobs\MarkOverdueSchedulesJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily scheduler at 08:00
Schedule::job(new MarkOverdueSchedulesJob)->dailyAt('08:00');
Schedule::job(new GenerateContractEndingNotificationsJob)->dailyAt('08:05');

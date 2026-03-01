<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\GenerateContractEndingNotificationsJob;
use App\Jobs\MarkOverdueSchedulesJob;
use App\Jobs\SendDailyRentRemindersJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily scheduler
Schedule::job(new MarkOverdueSchedulesJob)->dailyAt('08:00');
Schedule::job(new GenerateContractEndingNotificationsJob)->dailyAt('08:05');
Schedule::job(new SendDailyRentRemindersJob)->dailyAt('08:10'); // Rent due today + overdue reminders

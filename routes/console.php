<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('credits:reset-daily')
    // ->dailyAt('00:00')
    // ->timezone('America/New_York') // Set your timezone
    ->daily()
    ->before(function () {
        logger('Starting daily credit reset');
    })
    ->after(function () {
        logger('Completed daily credit reset');
    });

    Schedule::command('reminders:send-feedback')
    // ->dailyAt('00:00')
    // ->timezone('America/New_York') // Set your timezone
    ->hourly()
    ->before(function () {
        logger('Sending Feedback Reminders');
    })
    ->after(function () {
        logger('Sent Feedback Reminders');
    });
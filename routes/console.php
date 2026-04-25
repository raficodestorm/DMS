<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('offers:expire')->daily();

// need to add this on server , cPanel > Cron Jobs > then add this
//  * * * * * php /your-project-path/artisan schedule:run >> /dev/null 2>&1 
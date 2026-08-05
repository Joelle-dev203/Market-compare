<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // <--- ADD THIS LINE

// Your existing Artisan commands...
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Paste your schedule command here:
Schedule::command('scrape:prices')->dailyAt('02:00');
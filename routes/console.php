<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule::command('import:candidate-one')->everyMinute();
Schedule::command('telescope:prune --hours=6')->hourly();
Schedule::command('cleanup:temp-files')->hourly();
Schedule::command('gallup:clean-history --days=7')->daily();

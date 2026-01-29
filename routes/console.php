<?php

declare(strict_types=1);

use App\Jobs\GerarFaturasJob;
use App\Jobs\GerarVencimentoFaturasJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new GerarFaturasJob)->dailyAt('06:00')->withoutOverlapping();
Schedule::job(new GerarVencimentoFaturasJob)->dailyAt('06:00')->withoutOverlapping();

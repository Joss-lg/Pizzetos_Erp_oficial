<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Cierra mesas abiertas de días anteriores todos los días a las 3:00 AM
Schedule::command('mesas:cerrar-nocturno')->dailyAt('03:00');

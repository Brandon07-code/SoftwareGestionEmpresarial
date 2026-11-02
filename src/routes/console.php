<?php

use App\Console\Commands\CheckLowStockCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Console Schedule (Turnix ERP - Clase 14)
|--------------------------------------------------------------------------
| Configuración de tareas programadas (Scheduler) para ejecución automática.
*/
Schedule::command(CheckLowStockCommand::class)
    ->dailyAt('08:00')
    ->description('Verificación diaria automática de productos con bajo inventario');

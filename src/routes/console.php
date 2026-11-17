<?php

use App\Console\Commands\CheckLowStockCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
| Tareas programadas del ERP (Clase 14).
| En el servidor se configura UNA sola tarea del sistema operativo (cron) que cada minuto ejecuta:
| php artisan schedule:run
| y Laravel decide qué tarea toca. En desarrollo: ./vendor/bin/sail php artisan schedule:work
*/
Schedule::command('erp:resumen-diario')
    ->dailyAt(config('erp.daily_summary_at', '19:00'))
    ->timezone('America/Bogota')
    ->withoutOverlapping();

Schedule::command(CheckLowStockCommand::class)
    ->dailyAt('08:00')
    ->description('Verificación diaria automática de productos con bajo inventario');

// Mantenimiento: borra registros de auditoría con más de 365 días (config/activitylog.php)
Schedule::command('activitylog:clean')->monthly();

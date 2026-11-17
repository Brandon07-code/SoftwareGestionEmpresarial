<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\User;
use App\Notifications\DailySummaryNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;

class SendDailySummary extends Command
{
    protected $signature = 'erp:resumen-diario {--fecha= : Fecha a resumir (Y-m-d). Por defecto, hoy}';

    protected $description = 'Envía por correo el resumen de ventas, compras y stock bajo a quienes tienen el permiso ver-reportes';

    public function handle(): int
    {
        $date = $this->option('fecha') ?: now()->toDateString();

        $summary = [
            'date' => $date,
            'sales_count' => Sale::whereDate('sale_date', $date)->count(),
            'sales_total' => (float) Sale::whereDate('sale_date', $date)->sum('total'),
            'purchases_total' => (float) Purchase::whereDate('purchase_date', $date)->sum('total'),
            'pending_purchases' => Purchase::whereIn('status', ['pendiente', 'parcial'])->count(),
            'low_stock' => Product::where('active', true)
                ->where('stock', '<=', config('erp.low_stock_threshold', 5))
                ->orderBy('stock')->pluck('name')->all(),
        ];

        $recipientsQuery = User::permission('ver-reportes');
        if (Schema::hasColumn('users', 'active')) {
            $recipientsQuery->where('active', true);
        }
        $recipients = $recipientsQuery->get();
        Notification::send($recipients, new DailySummaryNotification($summary));

        $this->info("Resumen del {$date} enviado a {$recipients->count()} usuario(s).");

        return self::SUCCESS;
    }
}

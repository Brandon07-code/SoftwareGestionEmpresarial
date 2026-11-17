<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DailySummaryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array  $summary  ['date', 'sales_count', 'sales_total', 'purchases_total', 'pending_purchases', 'low_stock' => [...]]
     */
    public function __construct(public array $summary) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $s = $this->summary;

        $mail = (new MailMessage)
            ->subject("Resumen del día {$s['date']}")
            ->greeting("Buenas noches, {$notifiable->name}")
            ->line("Ventas del día: {$s['sales_count']} por $".number_format((float) $s['sales_total'], 2).'.')
            ->line('Compras registradas hoy: $'.number_format((float) $s['purchases_total'], 2).'.')
            ->line("Órdenes de compra por recibir: {$s['pending_purchases']}.");

        if (! empty($s['low_stock'])) {
            $mail->line('Productos con stock bajo: '.implode(', ', $s['low_stock']).'.');
        }

        return $mail->action('Abrir el Dashboard', route('dashboard'));
    }
}

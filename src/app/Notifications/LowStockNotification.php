<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Alerta de stock bajo por correo y en la campana del ERP.
 * ShouldQueue: el correo se envía en segundo plano (cola), así la venta no espera al servidor de correo.
 */
class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Product $product,
        public ?int $currentStock = null
    ) {
        $this->currentStock = $currentStock ?? (int) $product->stock;
    }

    // Canales: correo + tabla notifications (campana)
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Stock bajo: {$this->product->name}")
            ->greeting("Hola, {$notifiable->name}")
            ->line("El producto «{$this->product->name}» quedó con {$this->product->stock} unidades.")
            ->line('El mínimo configurado es '.config('erp.low_stock_threshold', 5).' unidades.')
            ->action('Crear orden de compra', route('purchases.create'))
            ->line('Este mensaje fue generado automáticamente por el ERP.');
    }

    // Lo que se guarda en la columna data de la tabla notifications
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Stock bajo',
            'message' => "«{$this->product->name}» quedó con {$this->product->stock} unidades.",
            'url' => route('products.kardex', $this->product),
            // Campos para compatibilidad con vistas previas
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'stock' => $this->product->stock,
            'action_url' => route('products.kardex', $this->product),
        ];
    }
}

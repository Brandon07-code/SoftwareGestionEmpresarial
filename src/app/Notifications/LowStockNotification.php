<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Product $product,
        public int $currentStock
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $categoryName = $this->product->category?->name ?? 'Sin categoría';

        return (new MailMessage)
            ->subject("⚠️ Alerta de Inventario Crítico: {$this->product->name}")
            ->greeting("Estimado(a) {$notifiable->name},")
            ->line('El sistema Turnix ERP ha detectado que las existencias de un producto han alcanzado un umbral crítico.')
            ->line("• Producto: {$this->product->name}")
            ->line("• Stock Actual: {$this->currentStock} unidades")
            ->line("• Categoría: {$categoryName}")
            ->action('Ver Inventario en Turnix ERP', url('/products?search='.urlencode($this->product->name)))
            ->line('Se recomienda generar una orden de compra a proveedores para evitar quiebres de inventario.')
            ->salutation('Atentamente, Sistema de Alertas Turnix ERP');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'category' => $this->product->category?->name ?? 'General',
            'stock' => $this->currentStock,
            'type' => 'stock_critico',
            'message' => "El producto «{$this->product->name}» ha alcanzado stock crítico ({$this->currentStock} unidades restantes).",
            'action_url' => url('/products?search='.urlencode($this->product->name)),
        ];
    }
}

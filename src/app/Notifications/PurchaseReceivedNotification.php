<?php

namespace App\Notifications;

use App\Models\Purchase;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PurchaseReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Purchase $purchase, public bool $complete) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $purchase = $this->purchase->load(['provider', 'user', 'details.product']);

        // El mismo PDF de la Clase 10, generado en memoria y adjunto al correo
        $pdf = Pdf::loadView('pdf.purchase', ['purchase' => $purchase])->setPaper('letter')->output();

        return (new MailMessage)
            ->subject("Mercancía recibida: {$purchase->purchase_number}")
            ->greeting("Hola, {$notifiable->name}")
            ->line($this->complete
                ? "La orden {$purchase->purchase_number} de {$purchase->provider?->name} fue recibida COMPLETA."
                : "Se registró una recepción PARCIAL de la orden {$purchase->purchase_number}.")
            ->action('Ver la orden', route('purchases.show', $purchase))
            ->attachData($pdf, "{$purchase->purchase_number}.pdf", ['mime' => 'application/pdf']);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->complete ? 'Compra recibida' : 'Recepción parcial',
            'message' => "Orden {$this->purchase->purchase_number}: ".($this->complete ? 'recibida completa.' : 'llegó una parte.'),
            'url' => route('purchases.show', $this->purchase),
            'action_url' => route('purchases.show', $this->purchase),
        ];
    }
}

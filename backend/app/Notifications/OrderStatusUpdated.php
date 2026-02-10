<?php

namespace App\Notifications;

use App\Models\TravelOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public TravelOrder $order,
        public string $previousStatus
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $statusMessages = [
            'approved' => 'aprovado',
            'rejected' => 'rejeitado',
            'cancelled' => 'cancelado',
        ];

        $statusText = $statusMessages[$this->order->status] ?? $this->order->status;

        return (new MailMessage)
            ->subject("Pedido de viagem {$statusText}")
            ->line("O status do seu pedido de viagem para {$this->order->destination} foi atualizado.")
            ->line("Status anterior: {$this->previousStatus}")
            ->line("Novo status: {$this->order->status}")
            ->line("Destino: {$this->order->destination}")
            ->line("Data de partida: {$this->order->departure_date->format('d/m/Y')}")
            ->line("Data de retorno: {$this->order->return_date->format('d/m/Y')}")
            ->line('Obrigado por utilizar nosso sistema!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->order->status,
            'previous_status' => $this->previousStatus,
            'destination' => $this->order->destination,
            'departure_date' => $this->order->departure_date->format('Y-m-d'),
            'return_date' => $this->order->return_date->format('Y-m-d'),
            'message' => "O status do seu pedido para {$this->order->destination} foi atualizado de {$this->previousStatus} para {$this->order->status}.",
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @return BroadcastMessage
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'order_id' => $this->order->id,
            'status' => $this->order->status,
            'previous_status' => $this->previousStatus,
            'destination' => $this->order->destination,
            'departure_date' => $this->order->departure_date->format('Y-m-d'),
            'return_date' => $this->order->return_date->format('Y-m-d'),
            'message' => "O status do seu pedido para {$this->order->destination} foi atualizado de {$this->previousStatus} para {$this->order->status}.",
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}

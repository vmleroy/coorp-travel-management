<?php

namespace App\Notifications;

use App\Models\TravelOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTravelOrderForAdmin extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public TravelOrder $order
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
        return (new MailMessage)
            ->subject('Nova solicitação de viagem')
            ->line("Uma nova solicitação de viagem foi criada por {$this->order->user->name}.")
            ->line("Destino: {$this->order->destination}")
            ->line("Data de partida: {$this->order->departure_date->format('d/m/Y')}")
            ->line("Data de retorno: {$this->order->return_date->format('d/m/Y')}")
            ->line("Status: {$this->order->status}")
            ->action('Ver solicitação', url("/admin/travel-orders/{$this->order->id}"))
            ->line('Por favor, revise e aprove/rejeite esta solicitação.');
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
            'destination' => $this->order->destination,
            'status' => $this->order->status,
            'departure_date' => $this->order->departure_date->format('Y-m-d'),
            'return_date' => $this->order->return_date->format('Y-m-d'),
            'user' => [
                'id' => $this->order->user->id,
                'name' => $this->order->user->name,
                'email' => $this->order->user->email,
            ],
            'message' => "{$this->order->user->name} criou uma nova solicitação de viagem para {$this->order->destination}.",
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
            'destination' => $this->order->destination,
            'status' => $this->order->status,
            'departure_date' => $this->order->departure_date->format('Y-m-d'),
            'return_date' => $this->order->return_date->format('Y-m-d'),
            'user' => [
                'id' => $this->order->user->id,
                'name' => $this->order->user->name,
                'email' => $this->order->user->email,
            ],
            'message' => "{$this->order->user->name} criou uma nova solicitação de viagem para {$this->order->destination}.",
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}

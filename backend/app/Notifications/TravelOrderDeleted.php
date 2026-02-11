<?php

namespace App\Notifications;

use App\Models\TravelOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TravelOrderDeleted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public TravelOrder $travelOrder,
        public string $deletedBy
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Solicitação de Viagem Excluída')
            ->line('Sua solicitação de viagem foi excluída.')
            ->line('Destino: ' . $this->travelOrder->destination)
            ->line('Data de partida: ' . $this->travelOrder->departure_date->format('d/m/Y'))
            ->line('Excluída por: ' . $this->deletedBy)
            ->line('Se você não solicitou esta exclusão, entre em contato com o administrador.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'travel_order_deleted',
            'travel_order_id' => $this->travelOrder->id,
            'destination' => $this->travelOrder->destination,
            'departure_date' => $this->travelOrder->departure_date,
            'return_date' => $this->travelOrder->return_date,
            'deleted_by' => $this->deletedBy,
            'message' => "Sua solicitação de viagem para {$this->travelOrder->destination} foi excluída."
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'travel_order_deleted',
            'travel_order_id' => $this->travelOrder->id,
            'destination' => $this->travelOrder->destination,
            'deleted_by' => $this->deletedBy,
            'message' => "Sua solicitação de viagem para {$this->travelOrder->destination} foi excluída."
        ]);
    }
}

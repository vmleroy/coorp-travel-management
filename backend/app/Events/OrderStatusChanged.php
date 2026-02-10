<?php

namespace App\Events;

use App\Models\TravelOrder;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public TravelOrder $order,
        public string $previousStatus
    ) {
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('notifications.' . $this->order->user_id),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'destination' => $this->order->destination,
            'status' => $this->order->status,
            'previous_status' => $this->previousStatus,
            'departure_date' => $this->order->departure_date->format('Y-m-d'),
            'return_date' => $this->order->return_date->format('Y-m-d'),
            'user' => [
                'id' => $this->order->user->id,
                'name' => $this->order->user->name,
            ],
            'message' => "O status do seu pedido para {$this->order->destination} foi atualizado de {$this->previousStatus} para {$this->order->status}.",
            'timestamp' => now()->toIso8601String(),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'order.status.changed';
    }
}

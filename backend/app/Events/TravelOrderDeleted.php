<?php

namespace App\Events;

use App\Models\TravelOrder;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TravelOrderDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public TravelOrder $travelOrder,
        public string $deletedBy
    ) {}

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('admin-notifications'),
            new Channel('user.' . $this->travelOrder->user_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'travel-order.deleted';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'travel_order_id' => $this->travelOrder->id,
            'destination' => $this->travelOrder->destination,
            'user_id' => $this->travelOrder->user_id,
            'deleted_by' => $this->deletedBy,
            'deleted_at' => now()->toISOString(),
        ];
    }
}

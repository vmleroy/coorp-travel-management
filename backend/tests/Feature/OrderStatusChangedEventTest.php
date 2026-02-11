<?php

use App\Events\OrderStatusChanged;
use App\Models\User;
use App\Models\TravelOrder;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('event can be created with order and previous status', function () {
    $user = User::factory()->create();
    $order = TravelOrder::factory()->for($user)->create([
        'status' => 'approved',
    ]);

    $event = new OrderStatusChanged($order, 'pending');

    expect($event->order)->toBe($order)
        ->and($event->previousStatus)->toBe('pending');
});

test('event broadcasts on correct private channel', function () {
    $user = User::factory()->create();
    $order = TravelOrder::factory()->for($user)->create([
        'user_id' => $user->id,
    ]);

    $event = new OrderStatusChanged($order, 'pending');
    $channels = $event->broadcastOn();

    expect($channels)->toHaveCount(1)
        ->and($channels[0]->name)->toBe("private-notifications.{$user->id}");
});

test('event broadcast data contains correct information', function () {
    $user = User::factory()->create();
    $order = TravelOrder::factory()->for($user)->create([
        'destination' => 'Berlin',
        'status' => 'approved',
        'departure_date' => '2026-03-15',
        'return_date' => '2026-03-20',
    ]);

    $event = new OrderStatusChanged($order, 'pending');
    $broadcastData = $event->broadcastWith();

    expect($broadcastData)->toHaveKeys(['order_id', 'destination', 'status', 'previous_status', 'departure_date', 'return_date', 'user', 'message', 'timestamp'])
        ->and($broadcastData['order_id'])->toBe($order->id)
        ->and($broadcastData['destination'])->toBe('Berlin')
        ->and($broadcastData['status'])->toBe('approved')
        ->and($broadcastData['previous_status'])->toBe('pending')
        ->and($broadcastData['user']['id'])->toBe($user->id)
        ->and($broadcastData['user']['name'])->toBe($user->name)
        ->and($broadcastData['message'])->toContain('Berlin')
        ->and($broadcastData['message'])->toContain('pending')
        ->and($broadcastData['message'])->toContain('approved');
});

test('event broadcasts with custom name', function () {
    $user = User::factory()->create();
    $order = TravelOrder::factory()->for($user)->create();

    $event = new OrderStatusChanged($order, 'pending');

    expect($event->broadcastAs())->toBe('order.status.changed');
});

test('event is dispatched when order status is updated', function () {
    Event::fake([OrderStatusChanged::class]);

    $user = User::factory()->create();
    $order = TravelOrder::factory()->for($user)->create([
        'status' => 'pending',
    ]);

    $service = app(\App\Services\TravelOrderService::class);
    $service->updateStatus($order->id, 'approved');

    Event::assertDispatched(OrderStatusChanged::class, function ($event) use ($order) {
        return $event->order->id === $order->id
            && $event->previousStatus === 'pending';
    });
});

test('event is dispatched when order is cancelled', function () {
    Event::fake([OrderStatusChanged::class]);

    $user = User::factory()->create();
    $order = TravelOrder::factory()->for($user)->create([
        'status' => 'pending',
    ]);

    $service = app(\App\Services\TravelOrderService::class);
    $service->cancel($order->id);

    Event::assertDispatched(OrderStatusChanged::class, function ($event) use ($order) {
        return $event->order->id === $order->id
            && $event->previousStatus === 'pending';
    });
});

test('event is not dispatched when status does not change', function () {
    Event::fake([OrderStatusChanged::class]);

    $user = User::factory()->create();
    $order = TravelOrder::factory()->for($user)->create([
        'status' => 'approved',
    ]);

    $service = app(\App\Services\TravelOrderService::class);
    $service->updateStatus($order->id, 'approved');

    Event::assertNotDispatched(OrderStatusChanged::class);
});

test('event is not dispatched when cancel fails', function () {
    Event::fake([OrderStatusChanged::class]);

    $user = User::factory()->create();
    $order = TravelOrder::factory()->for($user)->create([
        'status' => 'approved', // Already approved, cannot cancel
    ]);

    $service = app(\App\Services\TravelOrderService::class);
    
    try {
        $service->cancel($order->id);
    } catch (\Exception $e) {
        // Expected exception
    }

    Event::assertNotDispatched(OrderStatusChanged::class);
});

<?php

use App\Models\User;
use App\Models\TravelOrder;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Notification::fake();
    $this->admin = User::factory()->admin()->create();
    $this->user = User::factory()->create();
});

test('user can get all their notifications', function () {
    $order = TravelOrder::factory()->for($this->user)->create();

    // Create some notifications in database
    $this->user->notifications()->create([
        'id' => \Illuminate\Support\Str::uuid(),
        'type' => 'App\Notifications\OrderStatusUpdated',
        'data' => [
            'order_id' => $order->id,
            'status' => 'approved',
            'message' => 'Test notification',
        ],
        'read_at' => null,
    ]);

    $response = $this->actingAs($this->user)
        ->getJson('/api/notifications');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'type', 'data', 'read_at', 'created_at'],
            ],
        ]);
});

test('user can get only unread notifications', function () {
    $order = TravelOrder::factory()->for($this->user)->create();

    // Create one read notification
    $this->user->notifications()->create([
        'id' => \Illuminate\Support\Str::uuid(),
        'type' => 'App\Notifications\OrderStatusUpdated',
        'data' => ['order_id' => $order->id, 'status' => 'approved', 'message' => 'Read notification'],
        'read_at' => now(),
    ]);

    // Create one unread notification
    $this->user->notifications()->create([
        'id' => \Illuminate\Support\Str::uuid(),
        'type' => 'App\Notifications\OrderStatusUpdated',
        'data' => ['order_id' => $order->id, 'status' => 'rejected', 'message' => 'Unread notification'],
        'read_at' => null,
    ]);

    $response = $this->actingAs($this->user)
        ->getJson('/api/notifications/unread');

    $response->assertStatus(200);

    $unreadNotifications = $response->json('data');
    expect($unreadNotifications)->toHaveCount(1)
        ->and($unreadNotifications[0]['read_at'])->toBeNull();
});

test('user can mark a specific notification as read', function () {
    $order = TravelOrder::factory()->for($this->user)->create();

    $notification = $this->user->notifications()->create([
        'id' => \Illuminate\Support\Str::uuid(),
        'type' => 'App\Notifications\OrderStatusUpdated',
        'data' => ['order_id' => $order->id, 'status' => 'approved', 'message' => 'Test'],
        'read_at' => null,
    ]);

    $response = $this->actingAs($this->user)
        ->putJson("/api/notifications/{$notification->id}/read");

    $response->assertStatus(200)
        ->assertJson(['message' => 'Notification marked as read']);

    expect($notification->fresh()->read_at)->not->toBeNull();
});

test('user cannot mark another users notification as read', function () {
    $otherUser = User::factory()->create();
    $order = TravelOrder::factory()->for($otherUser)->create();

    $notification = $otherUser->notifications()->create([
        'id' => \Illuminate\Support\Str::uuid(),
        'type' => 'App\Notifications\OrderStatusUpdated',
        'data' => ['order_id' => $order->id, 'status' => 'approved', 'message' => 'Test'],
        'read_at' => null,
    ]);

    $response = $this->actingAs($this->user)
        ->putJson("/api/notifications/{$notification->id}/read");

    $response->assertStatus(404);
});

test('user can mark all notifications as read', function () {
    $order = TravelOrder::factory()->for($this->user)->create();

    // Create multiple unread notifications
    for ($i = 0; $i < 3; $i++) {
        $this->user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\Notifications\OrderStatusUpdated',
            'data' => ['order_id' => $order->id, 'status' => 'approved', 'message' => "Test $i"],
            'read_at' => null,
        ]);
    }

    expect($this->user->unreadNotifications)->toHaveCount(3);

    $response = $this->actingAs($this->user)
        ->putJson('/api/notifications/read-all');

    $response->assertStatus(200)
        ->assertJson(['message' => 'All notifications marked as read']);

    expect($this->user->fresh()->unreadNotifications)->toHaveCount(0);
});

test('user can delete a specific notification', function () {
    $order = TravelOrder::factory()->for($this->user)->create();

    $notification = $this->user->notifications()->create([
        'id' => \Illuminate\Support\Str::uuid(),
        'type' => 'App\Notifications\OrderStatusUpdated',
        'data' => ['order_id' => $order->id, 'status' => 'approved', 'message' => 'Test'],
        'read_at' => null,
    ]);

    $response = $this->actingAs($this->user)
        ->deleteJson("/api/notifications/{$notification->id}");

    $response->assertStatus(200)
        ->assertJson(['message' => 'Notification deleted']);

    expect($this->user->notifications()->find($notification->id))->toBeNull();
});

test('user cannot delete another users notification', function () {
    $otherUser = User::factory()->create();
    $order = TravelOrder::factory()->for($otherUser)->create();

    $notification = $otherUser->notifications()->create([
        'id' => \Illuminate\Support\Str::uuid(),
        'type' => 'App\Notifications\OrderStatusUpdated',
        'data' => ['order_id' => $order->id, 'status' => 'approved', 'message' => 'Test'],
        'read_at' => null,
    ]);

    $response = $this->actingAs($this->user)
        ->deleteJson("/api/notifications/{$notification->id}");

    $response->assertStatus(404);
    expect($otherUser->notifications()->find($notification->id))->not->toBeNull();
});

test('notifications endpoint requires authentication', function () {
    $response = $this->getJson('/api/notifications');
    $response->assertStatus(401);
});

test('notifications are paginated', function () {
    $order = TravelOrder::factory()->for($this->user)->create();

    // Create 20 notifications
    for ($i = 0; $i < 20; $i++) {
        $this->user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\Notifications\OrderStatusUpdated',
            'data' => ['order_id' => $order->id, 'status' => 'approved', 'message' => "Test $i"],
            'read_at' => null,
        ]);
    }

    $response = $this->actingAs($this->user)
        ->getJson('/api/notifications');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data',
            'current_page',
            'per_page',
            'total',
        ]);

    expect($response->json('data'))->toHaveCount(15) // Default pagination
        ->and($response->json('total'))->toBe(20);
});

test('can specify custom per_page for notifications', function () {
    $order = TravelOrder::factory()->for($this->user)->create();

    // Create 10 notifications
    for ($i = 0; $i < 10; $i++) {
        $this->user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\Notifications\OrderStatusUpdated',
            'data' => ['order_id' => $order->id, 'status' => 'approved', 'message' => "Test $i"],
            'read_at' => null,
        ]);
    }

    $response = $this->actingAs($this->user)
        ->getJson('/api/notifications?per_page=5');

    $response->assertStatus(200);
    expect($response->json('data'))->toHaveCount(5);
});

test('notification can be created with order and previous status', function () {
    $user = User::factory()->create();
    $order = TravelOrder::factory()->for($user)->create([
        'status' => 'approved',
    ]);

    $notification = new OrderStatusUpdated($order, 'pending');

    expect($notification->order)->toBe($order)
        ->and($notification->previousStatus)->toBe('pending');
});

test('notification uses database and mail channels', function () {
    $user = User::factory()->create();
    $order = TravelOrder::factory()->for($user)->create();
    $notification = new OrderStatusUpdated($order, 'pending');

    $channels = $notification->via($user);

    expect($channels)->toBe(['database', 'mail', 'broadcast']);
});

test('notification toArray returns correct data', function () {
    $user = User::factory()->create();
    $order = TravelOrder::factory()->for($user)->create([
        'destination' => 'Paris',
        'status' => 'approved',
        'departure_date' => '2026-03-01',
        'return_date' => '2026-03-10',
    ]);

    $notification = new OrderStatusUpdated($order, 'pending');
    $array = $notification->toArray($user);

    expect($array)->toHaveKeys(['order_id', 'status', 'previous_status', 'destination', 'departure_date', 'return_date', 'message'])
        ->and($array['order_id'])->toBe($order->id)
        ->and($array['status'])->toBe('approved')
        ->and($array['previous_status'])->toBe('pending')
        ->and($array['destination'])->toBe('Paris')
        ->and($array['message'])->toContain('Paris')
        ->and($array['message'])->toContain('pending')
        ->and($array['message'])->toContain('approved');
});

test('notification toMail returns mail message', function () {
    $user = User::factory()->create();
    $order = TravelOrder::factory()->for($user)->create([
        'destination' => 'Tokyo',
        'status' => 'approved',
        'departure_date' => '2026-03-01',
        'return_date' => '2026-03-10',
    ]);

    $notification = new OrderStatusUpdated($order, 'pending');
    $mailMessage = $notification->toMail($user);

    expect($mailMessage->subject)->toBe('Pedido de viagem aprovado')
        ->and($mailMessage->introLines)->toContain('O status do seu pedido de viagem para Tokyo foi atualizado.')
        ->and($mailMessage->introLines)->toContain('Status anterior: pending')
        ->and($mailMessage->introLines)->toContain('Novo status: approved');
});

test('notification mail subject changes based on status', function () {
    $user = User::factory()->create();

    $approvedOrder = TravelOrder::factory()->for($user)->create(['status' => 'approved']);
    $rejectedOrder = TravelOrder::factory()->for($user)->create(['status' => 'rejected']);
    $cancelledOrder = TravelOrder::factory()->for($user)->create(['status' => 'cancelled']);

    $approvedNotification = new OrderStatusUpdated($approvedOrder, 'pending');
    $rejectedNotification = new OrderStatusUpdated($rejectedOrder, 'pending');
    $cancelledNotification = new OrderStatusUpdated($cancelledOrder, 'pending');

    expect($approvedNotification->toMail($user)->subject)->toBe('Pedido de viagem aprovado')
        ->and($rejectedNotification->toMail($user)->subject)->toBe('Pedido de viagem rejeitado')
        ->and($cancelledNotification->toMail($user)->subject)->toBe('Pedido de viagem cancelado');
});

test('notification toBroadcast returns broadcast message', function () {
    $user = User::factory()->create();
    $order = TravelOrder::factory()->for($user)->create([
        'destination' => 'London',
        'status' => 'approved',
        'departure_date' => '2026-03-01',
        'return_date' => '2026-03-10',
    ]);

    $notification = new OrderStatusUpdated($order, 'pending');
    $broadcastMessage = $notification->toBroadcast($user);

    expect($broadcastMessage->data)->toHaveKeys(['order_id', 'status', 'previous_status', 'destination', 'departure_date', 'return_date', 'message', 'timestamp'])
        ->and($broadcastMessage->data['order_id'])->toBe($order->id)
        ->and($broadcastMessage->data['status'])->toBe('approved')
        ->and($broadcastMessage->data['previous_status'])->toBe('pending')
        ->and($broadcastMessage->data['destination'])->toBe('London')
        ->and($broadcastMessage->data['message'])->toContain('London');
});

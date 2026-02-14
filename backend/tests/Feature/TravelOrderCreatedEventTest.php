<?php

use App\Events\TravelOrderCreated;
use App\Models\User;
use App\Models\TravelOrder;
use App\Notifications\NewTravelOrderForAdmin;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('event can be created with travel order', function () {
    $user = User::factory()->create();
    $order = TravelOrder::factory()->for($user)->create();

    $event = new TravelOrderCreated($order);

    expect($event->order)->toBe($order);
});

test('event broadcasts on admin notifications channel', function () {
    $user = User::factory()->create();
    $order = TravelOrder::factory()->for($user)->create();

    $event = new TravelOrderCreated($order);
    $channels = $event->broadcastOn();

        expect($channels)->toHaveCount(1)
            ->and($channels[0]->name)->toBe('admin-notifications');
});

test('event broadcast data contains order and user information', function () {
    $user = User::factory()->create(['name' => 'John Doe']);
    $order = TravelOrder::factory()->for($user)->create([
        'destination' => 'New York',
        'departure_date' => '2026-04-01',
        'return_date' => '2026-04-10',
    ]);

    $event = new TravelOrderCreated($order);
    $broadcastData = $event->broadcastWith();

    expect($broadcastData)->toHaveKeys(['order_id', 'destination', 'status', 'departure_date', 'return_date', 'user', 'message', 'timestamp'])
        ->and($broadcastData['order_id'])->toBe($order->id)
        ->and($broadcastData['destination'])->toBe('New York')
        ->and($broadcastData['user']['name'])->toBe('John Doe')
        ->and($broadcastData['message'])->toContain('John Doe')
        ->and($broadcastData['message'])->toContain('New York');
});

test('event broadcasts with custom name', function () {
    $user = User::factory()->create();
    $order = TravelOrder::factory()->for($user)->create();

    $event = new TravelOrderCreated($order);

    expect($event->broadcastAs())->toBe('travel.order.created');
});

test('event is dispatched when new order is created', function () {
    Event::fake([TravelOrderCreated::class]);

    $user = User::factory()->create();

    $service = app(\App\Services\TravelOrderService::class);
    $order = $service->create([
        'user_id' => $user->id,
        'destination' => 'Paris',
        'departure_date' => '2026-05-01',
        'return_date' => '2026-05-10',
    ]);

    Event::assertDispatched(TravelOrderCreated::class, function ($event) use ($order) {
        return $event->order->id === $order->id;
    });
});

test('admins are notified when new order is created', function () {
    Notification::fake();

    $admin1 = User::factory()->admin()->create();
    $admin2 = User::factory()->admin()->create();
    $regularUser = User::factory()->create();

    $service = app(\App\Services\TravelOrderService::class);
    $order = $service->create([
        'user_id' => $regularUser->id,
        'destination' => 'Tokyo',
        'departure_date' => '2026-06-01',
        'return_date' => '2026-06-10',
    ]);

    Notification::assertSentTo(
        [$admin1, $admin2],
        NewTravelOrderForAdmin::class,
        function ($notification) use ($order) {
            return $notification->order->id === $order->id;
        }
    );

    // Regular user should NOT receive admin notification
    Notification::assertNotSentTo($regularUser, NewTravelOrderForAdmin::class);
});

test('notification contains correct order information', function () {
    $user = User::factory()->create(['name' => 'Jane Smith']);
    $order = TravelOrder::factory()->for($user)->create([
        'destination' => 'London',
        'departure_date' => '2026-07-15',
        'return_date' => '2026-07-25',
    ]);

    $admin = User::factory()->admin()->create();
    $notification = new NewTravelOrderForAdmin($order);

    $array = $notification->toArray($admin);

    expect($array)->toHaveKeys(['order_id', 'destination', 'status', 'departure_date', 'return_date', 'user', 'message'])
        ->and($array['order_id'])->toBe($order->id)
        ->and($array['destination'])->toBe('London')
        ->and($array['user']['name'])->toBe('Jane Smith')
        ->and($array['message'])->toContain('Jane Smith')
        ->and($array['message'])->toContain('London');
});

test('notification uses database mail and broadcast channels', function () {
    $user = User::factory()->create();
    $order = TravelOrder::factory()->for($user)->create();
    $admin = User::factory()->admin()->create();

    $notification = new NewTravelOrderForAdmin($order);
    $channels = $notification->via($admin);

    expect($channels)->toBe(['database', 'mail', 'broadcast']);
});

test('notification email has correct subject and content', function () {
    $user = User::factory()->create(['name' => 'Mike Johnson']);
    $order = TravelOrder::factory()->for($user)->create([
        'destination' => 'Berlin',
        'departure_date' => '2026-08-01',
        'return_date' => '2026-08-10',
    ]);

    $admin = User::factory()->admin()->create();
    $notification = new NewTravelOrderForAdmin($order);
    $mailMessage = $notification->toMail($admin);

    expect($mailMessage->subject)->toBe('Nova solicitação de viagem')
        ->and($mailMessage->introLines)->toContain("Uma nova solicitação de viagem foi criada por Mike Johnson.")
        ->and($mailMessage->introLines)->toContain("Destino: Berlin");
});

test('notification toBroadcast returns correct data', function () {
    $user = User::factory()->create(['name' => 'Sarah Connor']);
    $order = TravelOrder::factory()->for($user)->create([
        'destination' => 'Rome',
        'departure_date' => '2026-09-01',
        'return_date' => '2026-09-10',
    ]);

    $admin = User::factory()->admin()->create();
    $notification = new NewTravelOrderForAdmin($order);
    $broadcastMessage = $notification->toBroadcast($admin);

    expect($broadcastMessage->data)->toHaveKeys(['order_id', 'destination', 'status', 'departure_date', 'return_date', 'user', 'message', 'timestamp'])
        ->and($broadcastMessage->data['order_id'])->toBe($order->id)
        ->and($broadcastMessage->data['destination'])->toBe('Rome')
        ->and($broadcastMessage->data['user']['name'])->toBe('Sarah Connor')
        ->and($broadcastMessage->data['message'])->toContain('Sarah Connor')
        ->and($broadcastMessage->data['message'])->toContain('Rome');
});

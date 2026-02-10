<?php

namespace Tests\Feature;

use App\Models\TravelOrder;
use App\Models\User;
use App\Services\TravelOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TravelOrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TravelOrderService $travelOrderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelOrderService = new TravelOrderService();
    }

    /**
     * Test creating a travel order
     */
    public function test_can_create_travel_order(): void
    {
        $user = User::factory()->create();

        $data = [
            'user_id' => $user->id,
            'destination' => 'Paris, France',
            'departure_date' => '2026-03-15',
            'return_date' => '2026-03-22',
        ];

        $travelOrder = $this->travelOrderService->create($data);

        $this->assertInstanceOf(TravelOrder::class, $travelOrder);
        $this->assertEquals($user->id, $travelOrder->user_id);
        $this->assertEquals('Paris, France', $travelOrder->destination);
        $this->assertEquals('pending', $travelOrder->status);
        $this->assertDatabaseHas('travel_order', [
            'id' => $travelOrder->id,
            'destination' => 'Paris, France',
        ]);
    }

    /**
     * Test creating travel order with default pending status
     */
    public function test_travel_order_defaults_to_pending_status(): void
    {
        $user = User::factory()->create();

        $travelOrder = $this->travelOrderService->create([
            'user_id' => $user->id,
            'destination' => 'London, UK',
            'departure_date' => '2026-04-10',
            'return_date' => '2026-04-17',
        ]);

        $this->assertEquals('pending', $travelOrder->status);
    }

    /**
     * Test updating a travel order
     */
    public function test_can_update_travel_order(): void
    {
        $user = User::factory()->create();
        $travelOrder = TravelOrder::factory()->create([
            'user_id' => $user->id,
            'destination' => 'New York',
            'status' => 'pending', // Only pending orders can be updated
        ]);

        $updatedData = [
            'destination' => 'Los Angeles',
        ];

        $updated = $this->travelOrderService->update($travelOrder->id, $updatedData);

        $this->assertEquals('Los Angeles', $updated->destination);
        $this->assertDatabaseHas('travel_order', [
            'id' => $travelOrder->id,
            'destination' => 'Los Angeles',
        ]);
    }

    /**
     * Test deleting a travel order
     */
    public function test_can_delete_travel_order(): void
    {
        $user = User::factory()->create();
        $travelOrder = TravelOrder::factory()->create([
            'user_id' => $user->id,
        ]);

        $deleted = $this->travelOrderService->delete($travelOrder->id);

        // Verify soft delete - record should not be found in regular query
        $this->assertNull(TravelOrder::find($travelOrder->id));
        $this->assertSoftDeleted('travel_order', ['id' => $travelOrder->id]);
    }

    /**
     * Test getting a single travel order
     */
    public function test_can_get_travel_order_by_id(): void
    {
        $user = User::factory()->create();
        $travelOrder = TravelOrder::factory()->create([
            'user_id' => $user->id,
            'destination' => 'Tokyo, Japan',
        ]);

        $retrieved = $this->travelOrderService->get($travelOrder->id);

        $this->assertInstanceOf(TravelOrder::class, $retrieved);
        $this->assertEquals($travelOrder->id, $retrieved->id);
        $this->assertEquals('Tokyo, Japan', $retrieved->destination);
    }

    /**
     * Test getting all travel orders
     */
    public function test_can_get_all_travel_orders(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        TravelOrder::factory(3)->create(['user_id' => $user1->id]);
        TravelOrder::factory(2)->create(['user_id' => $user2->id]);

        $allOrders = $this->travelOrderService->getAll();

        $this->assertCount(5, $allOrders);
    }

    /**
     * Test getting travel orders by user ID
     */
    public function test_can_get_travel_orders_by_user_id(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        TravelOrder::factory(3)->create(['user_id' => $user1->id]);
        TravelOrder::factory(2)->create(['user_id' => $user2->id]);

        $user1Orders = $this->travelOrderService->getByUserId($user1->id);

        $this->assertCount(3, $user1Orders);
        $this->assertTrue($user1Orders->every(fn($order) => $order->user_id === $user1->id));
    }

    /**
     * Test getting non-existent travel order throws exception
     */
    public function test_get_non_existent_travel_order_throws_exception(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->travelOrderService->get(999);
    }

    /**
     * Test updating non-existent travel order throws exception
     */
    public function test_update_non_existent_travel_order_throws_exception(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->travelOrderService->update(999, ['destination' => 'Updated']);
    }

    /**
     * Test deleting non-existent travel order throws exception
     */
    public function test_delete_non_existent_travel_order_throws_exception(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->travelOrderService->delete(999);
    }

    /**
     * Test travel order status transitions
     */
    public function test_travel_order_status_transitions(): void
    {
        $user = User::factory()->create();
        $travelOrder = TravelOrder::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        // Transition: pending -> approved (using updateStatus)
        $approved = $this->travelOrderService->updateStatus($travelOrder->id, 'approved');
        $this->assertEquals('approved', $approved->status);

        // Transition: approved -> rejected
        $rejected = $this->travelOrderService->updateStatus($approved->id, 'rejected');
        $this->assertEquals('rejected', $rejected->status);

        // Transition: rejected -> cancelled
        $cancelled = $this->travelOrderService->updateStatus($rejected->id, 'cancelled');
        $this->assertEquals('cancelled', $cancelled->status);
    }

    /**
     * Test creating multiple travel orders for same user
     */
    public function test_user_can_have_multiple_travel_orders(): void
    {
        $user = User::factory()->create();

        $order1 = $this->travelOrderService->create([
            'user_id' => $user->id,
            'destination' => 'Paris',
            'departure_date' => '2026-03-15',
            'return_date' => '2026-03-22',
        ]);

        $order2 = $this->travelOrderService->create([
            'user_id' => $user->id,
            'destination' => 'London',
            'departure_date' => '2026-04-10',
            'return_date' => '2026-04-17',
        ]);

        $userOrders = $this->travelOrderService->getByUserId($user->id);

        $this->assertCount(2, $userOrders);
        $this->assertTrue($userOrders->contains($order1));
        $this->assertTrue($userOrders->contains($order2));
    }

    /**
     * Test empty result when user has no travel orders
     */
    public function test_get_travel_orders_for_user_with_no_orders(): void
    {
        $user = User::factory()->create();

        $userOrders = $this->travelOrderService->getByUserId($user->id);

        $this->assertCount(0, $userOrders);
        $this->assertTrue($userOrders->isEmpty());
    }

     /**
     * Test paginated travel orders
     */
    public function test_can_paginate_travel_orders(): void
    {
        $user = User::factory()->create();
        // Create 25 travel orders for the user
        TravelOrder::factory(25)->create(['user_id' => $user->id]);

        // Page 1, 10 per page
        $filters = [
            'per_page' => 10,
            'page' => 1,
        ];
        $paginated = $this->travelOrderService->getAll($filters);
        $this->assertEquals(10, $paginated->count());
        $this->assertEquals(25, $paginated->total());
        $this->assertEquals(1, $paginated->currentPage());

        // Page 3, 10 per page (should have 5 items)
        $filters['page'] = 3;
        $paginated = $this->travelOrderService->getAll($filters);
        $this->assertEquals(5, $paginated->count());
        $this->assertEquals(3, $paginated->currentPage());
    }

    /**
     * Test travel order soft delete
     */
    public function test_travel_order_uses_soft_deletes(): void
    {
        $user = User::factory()->create();
        $travelOrder = TravelOrder::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->travelOrderService->delete($travelOrder->id);

        // Check soft delete
        $this->assertSoftDeleted('travel_order', ['id' => $travelOrder->id]);
    }

    /**
     * Test travel order cascade delete with user
     */
    public function test_travel_orders_cascade_delete_with_user(): void
    {
        $user = User::factory()->create();
        TravelOrder::factory(3)->create(['user_id' => $user->id]);

        $this->assertCount(3, $this->travelOrderService->getByUserId($user->id));

        $user->delete();

        $this->assertCount(0, $this->travelOrderService->getByUserId($user->id));
    }

    /**
     * Test travel order includes user relationship
     */
    public function test_travel_order_includes_user_relationship(): void
    {
        $user = User::factory()->create(['name' => 'John Doe']);
        $travelOrder = TravelOrder::factory()->create([
            'user_id' => $user->id,
            'destination' => 'Tokyo',
        ]);

        $retrieved = $this->travelOrderService->get($travelOrder->id);

        $this->assertNotNull($retrieved->user);
        $this->assertEquals('John Doe', $retrieved->user->name);
        $this->assertEquals($user->id, $retrieved->user->id);
    }

    /**
     * Test travel order list includes user names
     */
    public function test_travel_order_list_includes_user_names(): void
    {
        $user1 = User::factory()->create(['name' => 'Alice']);
        $user2 = User::factory()->create(['name' => 'Bob']);

        TravelOrder::factory()->create(['user_id' => $user1->id]);
        TravelOrder::factory()->create(['user_id' => $user2->id]);

        $orders = $this->travelOrderService->getAll();

        $this->assertCount(2, $orders);
        $this->assertNotNull($orders[0]->user);
        $this->assertNotNull($orders[1]->user);

        $names = $orders->pluck('user.name')->toArray();
        $this->assertContains('Alice', $names);
        $this->assertContains('Bob', $names);
    }

    /**
     * Test filtering travel orders by destination
     */
    public function test_can_filter_travel_orders_by_destination(): void
    {
        $user = User::factory()->create();
        TravelOrder::factory()->create(['user_id' => $user->id, 'destination' => 'Paris, France']);
        TravelOrder::factory()->create(['user_id' => $user->id, 'destination' => 'Tokyo, Japan']);
        TravelOrder::factory()->create(['user_id' => $user->id, 'destination' => 'New York, USA']);

        $filters = ['destination' => 'Paris'];
        $orders = $this->travelOrderService->getAll($filters);

        $this->assertCount(1, $orders);
        $this->assertStringContainsString('Paris', $orders[0]->destination);
    }

    /**
     * Test filtering travel orders by departure date range
     */
    public function test_can_filter_travel_orders_by_departure_date(): void
    {
        $user = User::factory()->create();
        TravelOrder::factory()->create([
            'user_id' => $user->id,
            'departure_date' => '2026-03-01',
        ]);
        TravelOrder::factory()->create([
            'user_id' => $user->id,
            'departure_date' => '2026-06-15',
        ]);
        TravelOrder::factory()->create([
            'user_id' => $user->id,
            'departure_date' => '2026-09-20',
        ]);

        $filters = [
            'departure_date_from' => '2026-05-01',
            'departure_date_to' => '2026-08-31',
        ];
        $orders = $this->travelOrderService->getAll($filters);

        $this->assertCount(1, $orders);
        $this->assertEquals('2026-06-15', $orders[0]->departure_date->format('Y-m-d'));
    }

    /**
     * Test filtering travel orders by return date range
     */
    public function test_can_filter_travel_orders_by_return_date(): void
    {
        $user = User::factory()->create();
        TravelOrder::factory()->create([
            'user_id' => $user->id,
            'return_date' => '2026-03-10',
        ]);
        TravelOrder::factory()->create([
            'user_id' => $user->id,
            'return_date' => '2026-06-20',
        ]);

        $filters = ['return_date_from' => '2026-06-01'];
        $orders = $this->travelOrderService->getAll($filters);

        $this->assertCount(1, $orders);
        $this->assertEquals('2026-06-20', $orders[0]->return_date->format('Y-m-d'));
    }

    /**
     * Test filtering travel orders by creation date
     */
    public function test_can_filter_travel_orders_by_creation_date(): void
    {
        $user = User::factory()->create();

        // Create order and manually set created_at
        $order1 = TravelOrder::factory()->create(['user_id' => $user->id]);
        $order1->created_at = '2026-01-01';
        $order1->save();

        $order2 = TravelOrder::factory()->create(['user_id' => $user->id]);
        $order2->created_at = '2026-02-15';
        $order2->save();

        $filters = ['created_from' => '2026-02-01'];
        $orders = $this->travelOrderService->getAll($filters);

        $this->assertCount(1, $orders);
    }

    /**
     * Test cancel only works for pending orders
     */
    public function test_cancel_only_works_for_pending_orders(): void
    {
        $user = User::factory()->create();

        // Test pending order can be cancelled
        $pendingOrder = TravelOrder::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $cancelled = $this->travelOrderService->cancel($pendingOrder->id);
        $this->assertEquals('cancelled', $cancelled->status);

        // Test approved order cannot be cancelled
        $approvedOrder = TravelOrder::factory()->create([
            'user_id' => $user->id,
            'status' => 'approved',
        ]);

        $notCancelled = $this->travelOrderService->cancel($approvedOrder->id);
        $this->assertEquals('approved', $notCancelled->status);

        // Test rejected order cannot be cancelled
        $rejectedOrder = TravelOrder::factory()->create([
            'user_id' => $user->id,
            'status' => 'rejected',
        ]);

        $notCancelled2 = $this->travelOrderService->cancel($rejectedOrder->id);
        $this->assertEquals('rejected', $notCancelled2->status);
    }

    /**
     * Test combining multiple filters
     */
    public function test_can_combine_multiple_filters(): void
    {
        $user = User::factory()->create();

        TravelOrder::factory()->create([
            'user_id' => $user->id,
            'destination' => 'Paris',
            'status' => 'pending',
            'departure_date' => '2026-06-01',
        ]);

        TravelOrder::factory()->create([
            'user_id' => $user->id,
            'destination' => 'Paris',
            'status' => 'approved',
            'departure_date' => '2026-06-15',
        ]);

        TravelOrder::factory()->create([
            'user_id' => $user->id,
            'destination' => 'Tokyo',
            'status' => 'pending',
            'departure_date' => '2026-06-10',
        ]);

        $filters = [
            'destination' => 'Paris',
            'status' => 'pending',
            'departure_date_from' => '2026-06-01',
            'departure_date_to' => '2026-06-10',
        ];

        $orders = $this->travelOrderService->getAll($filters);

        $this->assertCount(1, $orders);
        $this->assertEquals('Paris', $orders[0]->destination);
        $this->assertEquals('pending', $orders[0]->status);
    }
}


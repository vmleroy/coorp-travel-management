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
        ]);

        $updatedData = [
            'destination' => 'Los Angeles',
            'status' => 'approved',
        ];

        $updated = $this->travelOrderService->update($travelOrder->id, $updatedData);

        $this->assertEquals('Los Angeles', $updated->destination);
        $this->assertEquals('approved', $updated->status);
        $this->assertDatabaseHas('travel_order', [
            'id' => $travelOrder->id,
            'destination' => 'Los Angeles',
            'status' => 'approved',
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

        $this->assertEquals('cancelled', $deleted->status);
        // Verify soft delete - record should not be found in regular query
        $this->assertNull(TravelOrder::find($travelOrder->id));
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

        // Transition: pending -> approved
        $approved = $this->travelOrderService->update($travelOrder->id, ['status' => 'approved']);
        $this->assertEquals('approved', $approved->status);

        // Transition: approved -> rejected
        $rejected = $this->travelOrderService->update($approved->id, ['status' => 'rejected']);
        $this->assertEquals('rejected', $rejected->status);

        // Transition: rejected -> cancelled
        $cancelled = $this->travelOrderService->update($rejected->id, ['status' => 'cancelled']);
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
}

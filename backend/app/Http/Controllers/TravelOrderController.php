<?php

namespace App\Http\Controllers;

use App\Models\TravelOrder;
use App\Services\TravelOrderService;
use Illuminate\Http\Request;

class TravelOrderController extends Controller
{
    protected TravelOrderService $travelOrderService;

    public function __construct(TravelOrderService $travelOrderService)
    {
        $this->travelOrderService = $travelOrderService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $requestedData = $request->validate([
            'destination' => 'required|string|max:255',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:departure_date',
        ]);

        $result = $this->travelOrderService->create($requestedData);

        return response()->json([
            'travel_order' => $result,
            'message' => 'Travel order created successfully',
        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $id = $request->route('id');
        $result = $this->travelOrderService->get($id);

        return response()->json([
            'travel_orders' => $result,
            'message' => 'Travel orders retrieved successfully',
        ], 200);
    }

    /**
     * Display a listing of all resources.
     */
    public function showAll()
    {
        $result = $this->travelOrderService->getAll();

        return response()->json([
            'travel_orders' => $result,
            'message' => 'Travel orders retrieved successfully',
        ], 200);
    }

    /**
     * Display the specified resource for the given user.
     */
    public function showAllByUser(Request $request)
    {
        $userId = $request->route('user_id');
        $result = $this->travelOrderService->getByUserId($userId);

        return response()->json([
            'travel_orders' => $result,
            'message' => 'Travel orders retrieved successfully',
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $id = $request->route('id');
        $requestedData = $request->validate([
            'destination' => 'sometimes|required|string|max:255',
            'departure_date' => 'sometimes|required|date',
            'return_date' => 'sometimes|required|date|after_or_equal:departure_date',
        ]);

        $result = $this->travelOrderService->update($id, $requestedData);

        return response()->json([
            'travel_order' => $result,
            'message' => 'Travel order updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = $request->route('id');
        $result = $this->travelOrderService->delete($id);

        return response()->json([
            'travel_order' => $result,
            'message' => 'Travel order cancelled successfully',
        ], 200);
    }

    public function updateStatus(Request $request)
    {
        $id = $request->route('id');
        $requestedData = $request->validate([
            'status' => 'required|string|in:approved,rejected',
        ]);

        $result = $this->travelOrderService->updateStatus($id, $requestedData['status']);

        return response()->json([
            'travel_order' => $result,
            'message' => 'Travel order status updated successfully',
        ], 200);
    }

    /**
     * Cancel the specified resource.
     */
    public function cancel(Request $request)
    {
        $id = $request->route('id');
        $result = $this->travelOrderService->cancel($id);

        $message = $result->status === 'cancelled' ? 'Travel order cancelled successfully' : 'Travel order could not be cancelled';

        return response()->json([
            'travel_order' => $result,
            'message' => $message,
        ], 200);
    }
}

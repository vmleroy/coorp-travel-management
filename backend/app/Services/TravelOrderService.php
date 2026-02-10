<?php

namespace App\Services;

use App\Models\TravelOrder;

class TravelOrderService
{
    public function create(array $data)
    {
        $travelOrder = TravelOrder::create([
            'user_id' => $data['user_id'],
            'destination' => $data['destination'],
            'departure_date' => $data['departure_date'],
            'return_date' => $data['return_date'],
            'status' => 'pending',
        ]);

        return $travelOrder;
    }

    public function update(int $id, array $data)
    {
        $travelOrder = TravelOrder::findOrFail($id);
        $travelOrder->update($data);

        return $travelOrder;
    }

    public function delete(int $id)
    {
        $travelOrder = TravelOrder::findOrFail($id);
        $travelOrder->update(['status' => 'cancelled']);
        $travelOrder->delete();

        return $travelOrder;
    }

    public function get(int $id)
    {
        $travelOrder = TravelOrder::findOrFail($id);

        return $travelOrder;
    }

    public function getAll()
    {
        $travelOrders = TravelOrder::all();

        return $travelOrders;
    }

    public function getByUserId(int $userId)
    {
        $travelOrder = TravelOrder::where('user_id', $userId)->get();

        return $travelOrder;
    }
}

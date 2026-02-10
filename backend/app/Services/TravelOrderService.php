<?php

namespace App\Services;

use App\Models\TravelOrder;
use Illuminate\Database\Eloquent\Builder;

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
        $travelOrder->delete();

        return $travelOrder;
    }

    public function cancel(int $id)
    {
        $travelOrder = TravelOrder::findOrFail($id);

        if ($travelOrder->status === 'cancelled' || $travelOrder->status !== 'approved') {
            return $travelOrder;
        }

        $travelOrder->status = 'cancelled';
        $travelOrder->save();

        return $travelOrder;
    }

    public function get(int $id)
    {
        $travelOrder = TravelOrder::findOrFail($id);

        return $travelOrder;
    }

    public function getAll(array $filters = [])
    {
        $query = TravelOrder::query();
        $query = $this->applyFilters($query, $filters);
        return $this->applyPagination($query, $filters);
    }

    public function getByUserId(int $userId, array $filters = [])
    {
        $query = TravelOrder::query()->where('user_id', $userId);
        $query = $this->applyFilters($query, $filters);
        return $this->applyPagination($query, $filters);
    }

    private function applyFilters(Builder $query, array $filters)
    {
        // Filter by status
        if (!empty($filters['status'])) {
            $status = $filters['status'];
            if (is_array($status)) {
                $query->whereIn('status', $status);
            }

            if (!is_array($status)) {
                $query->where('status', $status);
            }
        }

        if (!empty($filters['return_date_from'])) {
            $query->whereDate('return_date', '>=', $filters['return_date_from']);
        }

        // Sort
        if (!empty($filters['sort_by'])) {
            $sortBy = $filters['sort_by'];
            $sortOrder = $filters['sort_order'] ?? 'asc';
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    private function applyPagination(Builder $query, array $filters)
    {
        if (!empty($filters['per_page'])) {
            $perPage = $filters['per_page'];
            $page = $filters['page'] ?? 1;
            return $query->paginate($perPage, ['*'], 'page', $page);
        }

        return $query->get();
    }
}

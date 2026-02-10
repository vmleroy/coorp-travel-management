<?php

namespace App\Services;

use App\Events\OrderStatusChanged;
use App\Events\TravelOrderCreated;
use App\Models\TravelOrder;
use App\Models\User;
use App\Notifications\NewTravelOrderForAdmin;
use App\Notifications\OrderStatusUpdated;
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

        // Dispatch broadcast event for admins
        TravelOrderCreated::dispatch($travelOrder);

        // Notify all admin users
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewTravelOrderForAdmin($travelOrder));
        }

        return $travelOrder;
    }

    public function update(int $id, array $data)
    {
        $travelOrder = TravelOrder::findOrFail($id);

        if ($travelOrder->status !== 'pending') {
            throw new \Exception('Não é possível editar uma solicitação que não está pendente. Status atual: ' . $travelOrder->status);
        }

        $travelOrder->update($data);

        return $travelOrder;
    }

    public function updateStatus(int $id, string $status)
    {
        $travelOrder = TravelOrder::findOrFail($id);
        $previousStatus = $travelOrder->status;

        // Only send notification if status actually changed and is approved or rejected
        $shouldNotify = $previousStatus !== $status &&
                        in_array($status, ['approved', 'rejected']);

        $travelOrder->status = $status;
        $travelOrder->save();

        if ($shouldNotify) {
            // Dispatch broadcast event
            OrderStatusChanged::dispatch($travelOrder, $previousStatus);

            // Send notification (database, mail, broadcast)
            $travelOrder->user->notify(new OrderStatusUpdated($travelOrder, $previousStatus));
        }

        return $travelOrder;
    }

    public function cancel(int $id)
    {
        $travelOrder = TravelOrder::findOrFail($id);
        $previousStatus = $travelOrder->status;

        // Only allow cancellation if status is pending (not yet approved)
        if ($travelOrder->status !== 'pending') {
            throw new \Exception('Não é possível cancelar uma solicitação que não está pendente. Status atual: ' . $travelOrder->status);
        }

        $travelOrder->status = 'cancelled';
        $travelOrder->save();

        // Dispatch broadcast event
        OrderStatusChanged::dispatch($travelOrder, $previousStatus);

        // Send notification about cancellation (database, mail, broadcast)
        $travelOrder->user->notify(new OrderStatusUpdated($travelOrder, $previousStatus));

        return $travelOrder;
    }

    public function delete(int $id)
    {
        $travelOrder = TravelOrder::findOrFail($id);
        $travelOrder->delete();

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

        // Filter by destination
        if (!empty($filters['destination'])) {
            $query->where('destination', 'like', '%' . $filters['destination'] . '%');
        }

        // Filter by departure date range
        if (!empty($filters['departure_date_from'])) {
            $query->whereDate('departure_date', '>=', $filters['departure_date_from']);
        }

        if (!empty($filters['departure_date_to'])) {
            $query->whereDate('departure_date', '<=', $filters['departure_date_to']);
        }

        // Filter by return date range
        if (!empty($filters['return_date_from'])) {
            $query->whereDate('return_date', '>=', $filters['return_date_from']);
        }

        if (!empty($filters['return_date_to'])) {
            $query->whereDate('return_date', '<=', $filters['return_date_to']);
        }

        // Filter by created_at date range
        if (!empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }

        if (!empty($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
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

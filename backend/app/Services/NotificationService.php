<?php

namespace App\Services;

use App\Models\Notification;
use Log;

class NotificationService
{

    public function getUserNotifications($userId, $limit = 10)
    {
        $res = Notification::query()
            ->select(['*'])
            ->where(function ($q) use ($userId) {
                $q->where('notifiable_id', $userId)
                    ->whereNull('read_at'); // Notificações não lidas
            })
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
        return $res;
    }

    public function markAsRead($id)
    {
        return Notification::query()
            ->where('id', $id)
            ->whereNull('read_at')
            ->first()
            ->update(['read_at' => now()]);
    }

    public function markAllAsRead($userId)
    {
        return Notification::query()
            ->where(function ($q) use ($userId) {
                $q->where('notifiable_id', $userId)
                    ->whereNull('read_at'); // Notificações não lidas
            })
            ->update(['read_at' => now()]);
    }

    public function updateMessage($notificationId, $message)
    {
        return Notification::query()
            ->where(function ($q) use ($notificationId) {
                $q->where('id', $notificationId)
                    ->whereNull('read_at'); // Notificações não lidas
            })->first()
            ->update(['message' => $message]);
    }
}

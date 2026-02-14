<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificationService;
use App\Models\User;

class NotificationController extends Controller
{
    protected $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $this->service->getUserNotifications($user->id, 10);
        return response()->json([
            'data' => $notifications,
        ]);
    }

    public function markAsRead(Request $request)
    {
        $id = urldecode($request->route('id'));
        $this->service->markAsRead($id);
        return response()->json(['message' => 'Notificações marcadas como lidas']);
    }

    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        $this->service->markAllAsRead($user->id);
        return response()->json(['message' => 'Todas as notificações marcadas como lidas']);
    }

    public function updateMessage(Request $request)
    {
        $id = urldecode($request->route('id'));
        $message = $request->input('message');
        if (!$message) {
            return response()->json(['message' => 'Descrição é obrigatória'], 422);
        }

        $notification = $this->service->updateMessage($id, $message);
        if (!$notification) {
            return response()->json(['message' => 'Notificação não encontrada'], 404);
        }

        return response()->json([
            'message' => 'Descrição atualizada com sucesso',
            'data' => $notification,
        ]);
    }
}

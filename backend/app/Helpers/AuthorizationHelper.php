<?php

namespace App\Helpers;

use App\Models\TravelOrder;
use App\Models\User;

class AuthorizationHelper
{
    public static function isAdmin(User $user): bool
    {
        return $user->role->value === 'admin';
    }

    public static function isOwner(User $user, TravelOrder $travelOrder): bool
    {
        return $travelOrder->user_id === $user->id;
    }

    public static function isAdminOrOwner(User $user, TravelOrder $travelOrder): bool
    {
        return self::isAdmin($user) || self::isOwner($user, $travelOrder);
    }

    public static function canAccessTravelOrder(User $user, TravelOrder $travelOrder): void
    {
        if (!self::isAdminOrOwner($user, $travelOrder)) {
            throw new \Exception('Você não tem permissão para acessar esta solicitação.');
        }
    }
}

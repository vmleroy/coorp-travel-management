<?php

namespace App\Http\Middleware;

use App\Helpers\AuthorizationHelper;
use App\Models\TravelOrder;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTravelOrderOwnership
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Autenticação necessária. Por favor, faça login para acessar este recurso.',
                'error' => 'unauthenticated'
            ], 401);
        }

        $travelOrderId = $request->route('id');

        if (!$travelOrderId) {
            return response()->json([
                'success' => false,
                'message' => 'Identificador da solicitação não fornecido.',
                'error' => 'missing_id'
            ], 400);
        }

        $travelOrder = TravelOrder::find($travelOrderId);

        if (!$travelOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Solicita\u00e7\u00e3o de viagem n\u00e3o encontrada.',
                'error' => 'not_found'
            ], 404);
        }

        if (!AuthorizationHelper::isAdminOrOwner($request->user(), $travelOrder)) {
            return response()->json([
                'success' => false,
                'message' => 'Voc\u00ea n\u00e3o tem permiss\u00e3o para acessar esta solicita\u00e7\u00e3o.',
                'error' => 'forbidden'
            ], 403);
        }

        $request->merge(['travel_order' => $travelOrder]);

        return $next($request);
    }
}

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
        ], [
            'destination.required' => 'O destino é obrigatório.',
            'destination.max' => 'O destino não pode ter mais de 255 caracteres.',
            'departure_date.required' => 'A data de partida é obrigatória.',
            'departure_date.date' => 'A data de partida deve ser uma data válida.',
            'return_date.required' => 'A data de retorno é obrigatória.',
            'return_date.date' => 'A data de retorno deve ser uma data válida.',
            'return_date.after_or_equal' => 'A data de retorno deve ser igual ou posterior à data de partida.',
        ]);

        $requestedData['user_id'] = $request->user()->id;

        $result = $this->travelOrderService->create($requestedData);

        return response()->json([
            'success' => true,
            'message' => 'Solicitação de viagem criada com sucesso!',
            'data' => [
                'travel_order' => $result,
            ]
        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $travelOrder = $request->get('travel_order');

        return response()->json([
            'success' => true,
            'message' => 'Solicitação de viagem recuperada com sucesso.',
            'data' => [
                'travel_order' => $travelOrder,
            ]
        ], 200);
    }

    /**
     * Display a listing of all resources.
     */
    public function showAll()
    {
        $result = $this->travelOrderService->getAll();

        return response()->json([
            'success' => true,
            'message' => 'Solicitações de viagem recuperadas com sucesso.',
            'data' => [
                'travel_orders' => $result,
            ]
        ], 200);
    }

    /**
     * Display the specified resource for the given user.
     */
    public function showAllByUser(Request $request)
    {
        $userId = $request->user()->id;
        $result = $this->travelOrderService->getByUserId($userId);

        return response()->json([
            'success' => true,
            'message' => 'Solicitações de viagem do usuário recuperadas com sucesso.',
            'data' => [
                'travel_orders' => $result,
            ]
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $travelOrder = $request->get('travel_order');
        $requestedData = $request->validate([
            'destination' => 'sometimes|required|string|max:255',
            'departure_date' => 'sometimes|required|date',
            'return_date' => 'sometimes|required|date|after_or_equal:departure_date',
        ], [
            'destination.required' => 'O destino é obrigatório.',
            'destination.max' => 'O destino não pode ter mais de 255 caracteres.',
            'departure_date.required' => 'A data de partida é obrigatória.',
            'departure_date.date' => 'A data de partida deve ser uma data válida.',
            'return_date.required' => 'A data de retorno é obrigatória.',
            'return_date.date' => 'A data de retorno deve ser uma data válida.',
            'return_date.after_or_equal' => 'A data de retorno deve ser igual ou posterior à data de partida.',
        ]);

        $result = $this->travelOrderService->update($travelOrder, $requestedData);

        return response()->json([
            'success' => true,
            'message' => 'Solicitação de viagem atualizada com sucesso!',
            'data' => [
                'travel_order' => $result,
            ]
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = $request->route('id');
        $result = $this->travelOrderService->delete($id, $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Solicitação de viagem excluída com sucesso!',
            'data' => [
                'travel_order' => $result,
            ]
        ], 200);
    }

    public function updateStatus(Request $request)
    {
        $id = $request->route('id');
        $requestedData = $request->validate([
            'status' => 'required|string|in:approved,rejected',
        ], [
            'status.required' => 'O status é obrigatório.',
            'status.in' => 'O status deve ser "approved" (aprovado) ou "rejected" (rejeitado).',
        ]);

        $result = $this->travelOrderService->updateStatus($id, $requestedData['status']);

        $statusMessage = $requestedData['status'] === 'approved' ? 'aprovada' : 'rejeitada';

        return response()->json([
            'success' => true,
            'message' => "Solicitação de viagem {$statusMessage} com sucesso!",
            'data' => [
                'travel_order' => $result,
            ]
        ], 200);
    }

    /**
     * Cancel the specified resource.
     */
    public function cancel(Request $request)
    {
        $id = $request->route('id');
        $result = $this->travelOrderService->cancel($id);

        return response()->json([
            'success' => true,
            'message' => 'Solicitação de viagem cancelada com sucesso!',
            'data' => [
                'travel_order' => $result,
            ]
        ], 200);
    }
}

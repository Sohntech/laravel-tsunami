<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionHistoryRequest;
use App\Services\TransactionHistoryService;
use Illuminate\Support\Facades\Log;

class TransactionHistoryController extends Controller
{
    protected $historyService;

    public function __construct(TransactionHistoryService $historyService)
    {
        $this->historyService = $historyService;
    }

    public function index(TransactionHistoryRequest $request)
    {
        try {
            $result = $this->historyService->getHistory(
                $request->user()->id,
                $request->validated()
            );

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Erreur historique : ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la récupération de l\'historique',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
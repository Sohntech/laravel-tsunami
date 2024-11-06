<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferRequest;
use App\Services\TransferService;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    protected $transferService;

    public function __construct(TransferService $transferService)
    {
        $this->transferService = $transferService;
    }

    public function transfer(TransferRequest $request)
    {
        try {
            $result = $this->transferService->transfer(
                $request->user(),
                $request->telephone,
                $request->montant
            );

            if (!$result['status']) {
                return response()->json($result, 400);
            }

            return response()->json($result, 200);

        } catch (\Exception $e) {
            Log::error('Erreur transfert : ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors du transfert',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
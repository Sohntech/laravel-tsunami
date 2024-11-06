<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScheduleTransferRequest;
use App\Services\ScheduledTransferService;
use Illuminate\Support\Facades\Log;

class ScheduledTransferController extends Controller
{
    protected $scheduledTransferService;

    public function __construct(ScheduledTransferService $scheduledTransferService)
    {
        $this->scheduledTransferService = $scheduledTransferService;
    }

    public function schedule(ScheduleTransferRequest $request)
    {
        try {
            $result = $this->scheduledTransferService->scheduleTransfer(
                $request->user(),
                $request->telephone,
                $request->montant,
                $request->frequence,
                $request->date_debut,
                $request->date_fin,
                $request->heure_execution
            );

            return response()->json($result, $result['status'] ? 200 : 400);

        } catch (\Exception $e) {
            Log::error('Erreur planification : ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la planification du transfert',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
<?php

namespace App\Services;

use App\Repositories\Interfaces\ScheduledTransferRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ScheduledTransferService
{
    protected $scheduledTransferRepository;
    protected $userRepository;
    protected $transferService;

    public function __construct(
        ScheduledTransferRepositoryInterface $scheduledTransferRepository,
        UserRepositoryInterface $userRepository,
        TransferService $transferService
    ) {
        $this->scheduledTransferRepository = $scheduledTransferRepository;
        $this->userRepository = $userRepository;
        $this->transferService = $transferService;
    }

    public function scheduleTransfer($expediteur, $telephone, $montant, $frequence, $dateDebut, $dateFin, $heureExecution)
    {
        try {
            // Trouver le destinataire
            $destinataire = $this->userRepository->findByPhone($telephone);

            if ($expediteur->id === $destinataire->id) {
                return [
                    'status' => false,
                    'message' => 'Vous ne pouvez pas planifier un transfert vers vous-même'
                ];
            }

            // Calculer la prochaine exécution
            $nextExecution = Carbon::parse($dateDebut . ' ' . $heureExecution);
            
            if ($nextExecution->isPast()) {
                $nextExecution = $nextExecution->addDay();
            }

            // Créer le transfert planifié
            $scheduledTransfer = $this->scheduledTransferRepository->create([
                'exp' => $expediteur->id,
                'destinataire' => $destinataire->id,
                'montant' => $montant,
                'frequence' => $frequence,
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin,
                'heure_execution' => $heureExecution,
                'next_execution' => $nextExecution
            ]);

            return [
                'status' => true,
                'message' => 'Transfert planifié avec succès',
                'scheduled_transfer' => [
                    'id' => $scheduledTransfer->id,
                    'montant' => $montant,
                    'frequence' => $frequence,
                    'destinataire' => [
                        'nom' => $destinataire->nom,
                        'prenom' => $destinataire->prenom,
                        'telephone' => $destinataire->telephone
                    ],
                    'date_debut' => $dateDebut,
                    'date_fin' => $dateFin,
                    'heure_execution' => $heureExecution,
                    'next_execution' => $nextExecution
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Erreur planification transfert : ' . $e->getMessage());
            throw $e;
        }
    }

    public function executeScheduledTransfers()
    {
        try {
            $now = Carbon::now();
            $activeTransfers = $this->scheduledTransferRepository->findActive();

            foreach ($activeTransfers as $transfer) {
                if ($transfer->next_execution && $now->gte($transfer->next_execution)) {
                    // Exécuter le transfert
                    $result = $this->transferService->transfer(
                        $transfer->expediteur,
                        $transfer->beneficiaire->telephone,
                        $transfer->montant
                    );

                    if ($result['status']) {
                        // Calculer la prochaine exécution
                        $nextExecution = $this->calculateNextExecution($transfer);
                        
                        // Mettre à jour le transfert planifié
                        $this->scheduledTransferRepository->update($transfer->id, [
                            'last_execution' => now(),
                            'next_execution' => $nextExecution
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Erreur exécution transferts planifiés : ' . $e->getMessage());
            throw $e;
        }
    }

    protected function calculateNextExecution($transfer)
    {
        $lastExecution = $transfer->next_execution ?? Carbon::parse($transfer->date_debut . ' ' . $transfer->heure_execution);
        
        switch ($transfer->frequence) {
            case 'daily':
                $next = $lastExecution->copy()->addDay();
                break;
            case 'weekly':
                $next = $lastExecution->copy()->addWeek();
                break;
            case 'monthly':
                $next = $lastExecution->copy()->addMonth();
                break;
        }

        if ($transfer->date_fin && $next->startOfDay()->gt(Carbon::parse($transfer->date_fin)->endOfDay())) {
            return null;
        }

        return $next;
    }
}
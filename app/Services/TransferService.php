<?php

namespace App\Services;

use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TransferService
{
    protected $transactionRepository;
    protected $userRepository;

    const TYPE_TRANSFERT_MULTIPLE = 2; // Replace with the appropriate value
    public function __construct(
        TransactionRepositoryInterface $transactionRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->userRepository = $userRepository;
    }

    public function transfer($expediteur, $telephone, $montant)
    {
        try {
            DB::beginTransaction();

            // Vérifier le solde de l'expéditeur
            if ($expediteur->solde < $montant) {
                return [
                    'status' => false,
                    'message' => 'Solde insuffisant'
                ];
            }

            // Trouver le destinataire
            $destinataire = $this->userRepository->findByPhone($telephone);

            if ($expediteur->id === $destinataire->id) {
                return [
                    'status' => false,
                    'message' => 'Vous ne pouvez pas effectuer un transfert vers vous-même'
                ];
            }

            // Créer la transaction
            $transaction = $this->transactionRepository->create([
                'montant' => $montant,
                'exp' => $expediteur->id,
                'destinataire' => $destinataire->id,
                'type_id' => 1, // Transfert simple
                'agent' => null // Explicitement null pour un transfert simple
            ]);

            // Mettre à jour les soldes
            $expediteur->solde -= $montant;
            $destinataire->solde += $montant;

            $expediteur->save();
            $destinataire->save();

            DB::commit();

            return [
                'status' => true,
                'message' => 'Transfert effectué avec succès',
                'transaction' => [
                    'id' => $transaction->id,
                    'montant' => $montant,
                    'destinataire' => [
                        'nom' => $destinataire->nom,
                        'prenom' => $destinataire->prenom,
                        'telephone' => $destinataire->telephone
                    ],
                    'date' => $transaction->created_at,
                ],
                'nouveau_solde' => $expediteur->solde
            ];

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur transfert : ' . $e->getMessage());
            throw $e;
        }
    }
    public function cancelTransfer($userId, $transactionId, $reason)
    {
        try {
            DB::beginTransaction();

            // Récupérer la transaction
            $transaction = $this->transactionRepository->findById($transactionId);

            if (!$transaction) {
                return [
                    'status' => false,
                    'message' => 'Transaction introuvable'
                ];
            }

            // Vérifier que l'utilisateur est l'expéditeur
            if ($transaction->exp !== $userId) {
                return [
                    'status' => false,
                    'message' => 'Vous n\'êtes pas autorisé à annuler cette transaction'
                ];
            }

            // Vérifier que la transaction n'est pas déjà annulée
            if ($transaction->status === 'cancelled') {
                return [
                    'status' => false,
                    'message' => 'Cette transaction est déjà annulée'
                ];
            }

            // Vérifier le délai de 30 minutes
            $timeDiff = Carbon::now()->diffInMinutes($transaction->created_at);
            if ($timeDiff > 30) {
                return [
                    'status' => false,
                    'message' => 'Le délai d\'annulation de 30 minutes est dépassé'
                ];
            }

            // Récupérer l'expéditeur et le destinataire
            $expediteur = $this->userRepository->findById($transaction->exp);
            $destinataire = $this->userRepository->findById($transaction->destinataire);

            // Rembourser le montant
            $expediteur->solde += $transaction->montant;
            $destinataire->solde -= $transaction->montant;

            // Mettre à jour la transaction
            $this->transactionRepository->update($transactionId, [
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancel_reason' => $reason,
                'cancelled_by' => $userId
            ]);

            // Sauvegarder les changements
            $expediteur->save();
            $destinataire->save();

            DB::commit();

            return [
                'status' => true,
                'message' => 'Transaction annulée avec succès',
                'data' => [
                    'transaction_id' => $transactionId,
                    'montant_remboursé' => $transaction->montant,
                    'nouveau_solde' => $expediteur->solde
                ]
            ];

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur annulation transfert : ' . $e->getMessage());
            throw $e;
        }
    }
    public function multipleTransfer($expediteur, array $telephones, $montant)
    {
        try {
            DB::beginTransaction();

            $successfulTransfers = [];
            $failedTransfers = [];
            $remainingSolde = $expediteur->solde;
            $initialSolde = $expediteur->solde;
            $totalNeeded = $montant * count($telephones);

            // Vérifier chaque destinataire
            foreach ($telephones as $telephone) {
                $destinataire = $this->userRepository->findByPhone($telephone);
                
                // Vérifier que ce n'est pas l'expéditeur lui-même
                if ($destinataire->id === $expediteur->id) {
                    $failedTransfers[] = [
                        'telephone' => $telephone,
                        'nom' => $destinataire->nom,
                        'prenom' => $destinataire->prenom,
                        'reason' => 'Impossible de transférer à soi-même'
                    ];
                    continue;
                }

                // Vérifier si le solde est suffisant pour ce transfert
                if ($remainingSolde < $montant) {
                    $failedTransfers[] = [
                        'telephone' => $telephone,
                        'nom' => $destinataire->nom,
                        'prenom' => $destinataire->prenom,
                        'reason' => 'Solde insuffisant'
                    ];
                    continue;
                }

                try {
                    // Créer la transaction
                    $transaction = $this->transactionRepository->create([
                        'montant' => $montant,
                        'exp' => $expediteur->id,
                        'destinataire' => $destinataire->id,
                        'type_id' => self::TYPE_TRANSFERT_MULTIPLE,
                        'agent' => null
                    ]);

                    // Mettre à jour les soldes
                    $remainingSolde -= $montant;
                    $destinataire->solde += $montant;
                    $destinataire->save();

                    $successfulTransfers[] = [
                        'transaction_id' => $transaction->id,
                        'destinataire' => [
                            'nom' => $destinataire->nom,
                            'prenom' => $destinataire->prenom,
                            'telephone' => $destinataire->telephone
                        ],
                        'montant' => $montant
                    ];

                } catch (\Exception $e) {
                    Log::error('Erreur lors du transfert vers ' . $destinataire->telephone . ': ' . $e->getMessage());
                    $failedTransfers[] = [
                        'telephone' => $telephone,
                        'nom' => $destinataire->nom,
                        'prenom' => $destinataire->prenom,
                        'reason' => 'Erreur technique lors du transfert'
                    ];
                }
            }

            // Mettre à jour le solde final de l'expéditeur
            $expediteur->solde = $remainingSolde;
            $expediteur->save();

            DB::commit();

            // Préparer le message approprié
            $message = count($successfulTransfers) > 0 ? 
                'Transferts effectués avec succès.' : 
                'Aucun transfert n\'a pu être effectué.';

            if (count($failedTransfers) > 0) {
                $remainingAmount = $totalNeeded - ($initialSolde - $remainingSolde);
                $message .= ' Certains transferts n\'ont pas pu être effectués par manque de solde. ';
                if ($remainingAmount > 0) {
                    $message .= sprintf(
                        'Il vous manque %s FCFA pour effectuer les transferts restants. Nous vous invitons à recharger votre compte et réessayer pour les destinataires manqués.',
                        number_format($remainingAmount, 0, ',', ' ')
                    );
                }
            }

            return [
                'status' => count($successfulTransfers) > 0,
                'message' => $message,
                'data' => [
                    'successful_transfers' => $successfulTransfers,
                    'failed_transfers' => $failedTransfers,
                    'total_transferred' => count($successfulTransfers) * $montant,
                    'remaining_solde' => $remainingSolde,
                    'transfers_completed' => count($successfulTransfers),
                    'transfers_failed' => count($failedTransfers)
                ]
            ];

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur transferts multiples : ' . $e->getMessage());
            throw $e;
        }
    }
}
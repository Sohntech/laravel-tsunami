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
}
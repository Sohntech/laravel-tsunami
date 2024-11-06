<?php

namespace App\Services;

use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
}
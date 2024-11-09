<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
class TransactionRepository implements TransactionRepositoryInterface
{
    protected $model;

    public function __construct(Transaction $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function findByUser(int $userId)
    {
        return $this->model->where('exp', $userId)
                          ->orWhere('destinataire', $userId)
                          ->orderBy('created_at', 'desc')
                          ->get();
    }

    public function findById(int $id)
    {
        return $this->model->find($id);
    }

    public function update(int $id, array $data)
    {
        $transaction = $this->model->findOrFail($id);
        $transaction->update($data);
        return $transaction;
    }
   

    public function getHistory($userId, array $filters = [], $perPage = 15)
    {
        $query = $this->model
            ->where(function ($query) use ($userId) {
                $query->where('exp', $userId)
                      ->orWhere('destinataire', $userId);
            })
            ->with([
                'expediteur' => function ($query) {
                    $query->select('id', 'nom', 'prenom', 'telephone');
                },
                'beneficiaire' => function ($query) {
                    $query->select('id', 'nom', 'prenom', 'telephone');
                },
                'type' => function ($query) {
                    $query->select('id', 'libelle');
                }
            ]);

        // Appliquer les filtres
        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        if (!empty($filters['type'])) {
            $query->where('type_id', $filters['type']);
        }

        if (!empty($filters['montant_min'])) {
            $query->where('montant', '>=', $filters['montant_min']);
        }

        if (!empty($filters['montant_max'])) {
            $query->where('montant', '<=', $filters['montant_max']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getStats($userId, array $filters = [])
    {
        $query = $this->model->where(function ($query) use ($userId) {
            $query->where('exp', $userId)
                  ->orWhere('destinataire', $userId);
        });

        // Appliquer les mêmes filtres que pour l'historique
        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        $stats = [
            'total_envoyé' => $query->clone()->where('exp', $userId)->sum('montant'),
            'total_reçu' => $query->clone()->where('destinataire', $userId)->sum('montant'),
            'nombre_envois' => $query->clone()->where('exp', $userId)->count(),
            'nombre_receptions' => $query->clone()->where('destinataire', $userId)->count(),
        ];

        return $stats;
    }
    public function getMerchantTransactions(int $merchantId, array $filters = [])
    {
        $query = $this->model
            ->where('destinataire', $merchantId)
            ->where('type_id', 4) // PAIEMENT_MARCHAND
            ->where('status', 'completed');

        if (isset($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        return $query->get();
    }
}
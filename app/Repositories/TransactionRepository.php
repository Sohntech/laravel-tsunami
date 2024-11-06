<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Repositories\Interfaces\TransactionRepositoryInterface;

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
}
<?php

namespace App\Repositories;

use App\Models\ScheduledTransfer;
use App\Repositories\Interfaces\ScheduledTransferRepositoryInterface;

class ScheduledTransferRepository implements ScheduledTransferRepositoryInterface
{
    protected $model;

    public function __construct(ScheduledTransfer $model)
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
                          ->with(['beneficiaire:id,nom,prenom,telephone'])
                          ->orderBy('created_at', 'desc')
                          ->get();
    }

    public function cancel(int $id)
    {
        return $this->model->where('id', $id)->update(['is_active' => false]);
    }

    public function findActive()
    {
        return $this->model->where('is_active', true)
                          ->where(function ($query) {
                              $query->whereNull('date_fin')
                                    ->orWhere('date_fin', '>=', now()->toDateString());
                          })
                          ->get();
    }

    public function update(int $id, array $data)
    {
        $scheduledTransfer = $this->model->findOrFail($id);
        $scheduledTransfer->update($data);
        return $scheduledTransfer;
    }
}
<?php

namespace App\Repositories\Interfaces;

interface ScheduledTransferRepositoryInterface
{
    public function create(array $data);
    public function findByUser(int $userId);
    public function cancel(int $id);
    public function findActive();
    public function update(int $id, array $data);
}
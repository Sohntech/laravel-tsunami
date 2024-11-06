<?php

namespace App\Repositories\Interfaces;

interface TransactionRepositoryInterface
{
    public function create(array $data);
    public function findByUser(int $userId);
    public function findById(int $id);
    public function update(int $id, array $data);  // Ajout de la méthode update
}
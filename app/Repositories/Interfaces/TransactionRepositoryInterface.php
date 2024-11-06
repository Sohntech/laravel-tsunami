<?php

namespace App\Repositories\Interfaces;

interface TransactionRepositoryInterface
{
    public function create(array $data);
    public function findByUser(int $userId);
    public function findById(int $id);
    public function update(int $id, array $data);  // Ajout de la méthode update
    public function getHistory($userId, array $filters = [], $perPage = 15);
    public function getStats($userId, array $filters = []);  // Ajout de la méthode getStats
}
<?php

namespace App\Repositories\Interfaces;

interface UserRepositoryInterface
{
    public function create(array $userData);
    public function update(int $id, array $data);
    public function findByPhone(string $phone);
    public function findByEmail(string $email);
    public function findById(int $id);
}
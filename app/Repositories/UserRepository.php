<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function create(array $userData)
    {
        return $this->model->create($userData);
    }

    public function update(int $id, array $data)
    {
        $user = $this->model->findOrFail($id);
        $user->update($data);
        return $user;
    }

    public function findByPhone(string $phone)
    {
        return $this->model->where('telephone', $phone)->first();
    }

    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }
}
<?php

namespace App\Repositories;

use App\Models\Favori;
use App\Repositories\Interfaces\FavoriRepositoryInterface;

class FavoriRepository implements FavoriRepositoryInterface
{
    protected $model;

    public function __construct(Favori $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function findByUser(int $userId)
    {
        return $this->model->where('user_id', $userId)
                          ->with('favori:id,nom,prenom,telephone')
                          ->get();
    }

    public function delete(int $userId, int $favoriId)
    {
        return $this->model->where('user_id', $userId)
                          ->where('favori_id', $favoriId)
                          ->delete();
    }

    public function exists(int $userId, int $favoriId)
    {
        return $this->model->where('user_id', $userId)
                          ->where('favori_id', $favoriId)
                          ->exists();
    }
}
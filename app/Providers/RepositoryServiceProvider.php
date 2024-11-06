<?php

namespace App\Providers;

use App\Repositories\FavoriRepository;
use App\Repositories\Interfaces\FavoriRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\Services\Interfaces\AuthServiceInterface;
use App\Services\AuthService;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Repositories\TransactionRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );
        $this->app->bind(
            AuthServiceInterface::class,
            AuthService::class
        );
        // Nouveau binding pour TransactionRepository
        $this->app->bind(
            TransactionRepositoryInterface::class,
            TransactionRepository::class
        );
        $this->app->bind(
            FavoriRepositoryInterface::class,
            FavoriRepository::class
        );
    }
}
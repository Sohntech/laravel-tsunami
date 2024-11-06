<?php

namespace App\Services\Interfaces;

interface AuthServiceInterface
{
    public function login(string $telephone, string $code);
    public function logout($user);
    public function getCurrentUser($user);
}
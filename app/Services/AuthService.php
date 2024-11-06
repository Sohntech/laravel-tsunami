<?php

namespace App\Services;

use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
class AuthService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(string $telephone, string $code)
    {
        try {
            $user = $this->userRepository->findByPhone($telephone);

            if (!$user) {
                return [
                    'status' => false,
                    'message' => 'Numéro de téléphone non trouvé',
                    'code' => 401
                ];
            }

            // Utiliser Hash::check pour comparer le code fourni avec le hash stocké
            if (!Hash::check($code, $user->code)) {
                return [
                    'status' => false,
                    'message' => 'Code invalide',
                    'code' => 401
                ];
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            $userResponse = $user->toArray();
            $userResponse['code'] = '******';

            return [
                'status' => true,
                'message' => 'Connexion réussie',
                'user' => $userResponse,
                'token' => $token,
                'code' => 200
            ];

        } catch (\Exception $e) {
            Log::error('Erreur login service: ' . $e->getMessage());
            throw $e;
        }
    }
    public function logout($user)
    {
        try {
            $user->currentAccessToken()->delete();
            return [
                'status' => true,
                'message' => 'Déconnexion réussie'
            ];
        } catch (\Exception $e) {
            Log::error('Erreur logout service: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getCurrentUser($user)
    {
        try {
            $userResponse = $user->toArray();
            $userResponse['code'] = '******';

            return [
                'status' => true,
                'user' => $userResponse
            ];
        } catch (\Exception $e) {
            Log::error('Erreur get current user service: ' . $e->getMessage());
            throw $e;
        }
    }
}
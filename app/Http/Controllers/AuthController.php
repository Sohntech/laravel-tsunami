<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request)
    {
        try {
            $result = $this->authService->login(
                $request->telephone,
                $request->code
            );

            return response()->json([
                'status' => $result['status'],
                'message' => $result['message'],
                'user' => $result['user'] ?? null,
                'access_token' => $result['token'] ?? null,
                'token_type' => 'Bearer'
            ], $result['code']);

        } catch (\Exception $e) {
            Log::error('Erreur de connexion: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la connexion',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $result = $this->authService->logout($request->user());
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Erreur de déconnexion: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la déconnexion',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function me(Request $request)
    {
        try {
            $result = $this->authService->getCurrentUser($request->user());
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Erreur profil: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la récupération du profil',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
<?php

namespace App\Services;

use App\Repositories\Interfaces\FavoriRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Log;

class FavoriService
{
    protected $favoriRepository;
    protected $userRepository;

    public function __construct(
        FavoriRepositoryInterface $favoriRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->favoriRepository = $favoriRepository;
        $this->userRepository = $userRepository;
    }

    public function addFavori($userId, $telephone, $alias = null)
    {
        try {
            // Trouver l'utilisateur à ajouter en favori
            $favoriUser = $this->userRepository->findByPhone($telephone);

            if ($userId === $favoriUser->id) {
                return [
                    'status' => false,
                    'message' => 'Vous ne pouvez pas vous ajouter vous-même en favori'
                ];
            }

            // Vérifier si déjà en favori
            if ($this->favoriRepository->exists($userId, $favoriUser->id)) {
                return [
                    'status' => false,
                    'message' => 'Ce numéro est déjà dans vos favoris'
                ];
            }

            // Ajouter aux favoris
            $favori = $this->favoriRepository->create([
                'user_id' => $userId,
                'favori_id' => $favoriUser->id,
                'alias' => $alias
            ]);

            return [
                'status' => true,
                'message' => 'Contact ajouté aux favoris avec succès',
                'favori' => [
                    'id' => $favori->id,
                    'nom' => $favoriUser->nom,
                    'prenom' => $favoriUser->prenom,
                    'telephone' => $favoriUser->telephone,
                    'alias' => $alias
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Erreur ajout favori : ' . $e->getMessage());
            throw $e;
        }
    }

    public function getFavoris($userId)
    {
        try {
            $favoris = $this->favoriRepository->findByUser($userId);
            
            return [
                'status' => true,
                'favoris' => $favoris
            ];

        } catch (\Exception $e) {
            Log::error('Erreur récupération favoris : ' . $e->getMessage());
            throw $e;
        }
    }
}
<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddFavoriRequest;
use App\Services\FavoriService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
class FavoriController extends Controller
{
    protected $favoriService;

    public function __construct(FavoriService $favoriService)
    {
        $this->favoriService = $favoriService;
    }

    public function add(AddFavoriRequest $request)
    {
        try {
            $result = $this->favoriService->addFavori(
                $request->user()->id,
                $request->telephone,
                $request->alias
            );

            return response()->json($result, $result['status'] ? 200 : 400);

        } catch (\Exception $e) {
            Log::error('Erreur ajout favori : ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de l\'ajout aux favoris',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function list(Request $request)
    {
        try {
            $result = $this->favoriService->getFavoris($request->user()->id);
            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Erreur liste favoris : ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la récupération des favoris',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
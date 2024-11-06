<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CardPdfGenerator
{
    public function generateCard(User $user, string $qrUrl): string
    {
        try {
            Log::info('Début de la génération de la carte PDF pour l\'utilisateur : ' . $user->id);
            
            // Configuration optimisée
            $pdf = PDF::loadView('pdf.card', [
                'user' => $user,
                'qrUrl' => $qrUrl
            ])->setOptions([
                'dpi' => 300,
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isFontSubsettingEnabled' => true,
                'isPhpEnabled' => true,
                'defaultPaperSize' => 'a4',
                'defaultMediaType' => 'screen',
                'debugPng' => false,
                'debugKeepTemp' => false,
                'debugCss' => false,
                'debugLayout' => false,
                'margin_top' => 0,
                'margin_right' => 0,
                'margin_bottom' => 0,
                'margin_left' => 0
            ]);

            // Générer un nom de fichier unique
            $filename = 'wave_card_' . $user->id . '_' . time() . '.pdf';
            $filepath = storage_path('app/public/cards/' . $filename);

            // Sauvegarder avec compression optimale
            $pdf->save($filepath);

            Log::info('Carte PDF générée avec succès : ' . $filepath);

            return $filepath;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération de la carte PDF : ' . $e->getMessage());
            throw $e;
        }
    }
}
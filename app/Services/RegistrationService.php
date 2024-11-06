<?php

namespace App\Services;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Events\UserRegistered;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RegistrationService
{
    protected $userRepository;
    protected $smsService;
    protected $cardPdfGenerator;

    public function __construct(
        UserRepositoryInterface $userRepository,
        SendSms $smsService,
        CardPdfGenerator $cardPdfGenerator
    ) {
        $this->userRepository = $userRepository;
        $this->smsService = $smsService;
        $this->cardPdfGenerator = $cardPdfGenerator;
    }

    public function registerUser(array $validatedData)
    {
        try {
            // Générer le code
            $originalCode = strtoupper(Str::random(6));
            $hashedCode = Hash::make($originalCode);

            // Préparer les données utilisateur
            $userData = $this->prepareUserData($validatedData, $hashedCode);

            // Créer l'utilisateur
            $user = $this->userRepository->create($userData);

            // Générer et sauvegarder le QR Code
            $qrUrl = $this->generateQRCode($user, $originalCode);
            
            // Mettre à jour la carte de l'utilisateur
            $this->userRepository->update($user->id, ['carte' => $qrUrl]);

            // Générer le PDF
            $cardPdfPath = $this->cardPdfGenerator->generateCard($user, $qrUrl);

            // Envoyer les notifications
            $this->sendNotifications($user, $qrUrl, $cardPdfPath, $originalCode);

            // Nettoyer les fichiers temporaires
            if ($cardPdfPath && file_exists($cardPdfPath)) {
                unlink($cardPdfPath);
            }

            return [
                'user' => $user,
                'qrUrl' => $qrUrl
            ];

        } catch (\Exception $e) {
            Log::error('Erreur registration service: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function prepareUserData(array $validatedData, string $hashedCode): array
    {
        return [
            'nom' => $validatedData['nom'],
            'prenom' => $validatedData['prenom'],
            'telephone' => $validatedData['telephone'],
            'email' => $validatedData['email'],
            'role_id' => $validatedData['roleId'],
            'solde' => 0,
            'promo' => 0,
            'etatcarte' => true,
            'code' => $hashedCode
        ];
    }

    protected function generateQRCode($user, $code)
    {
        try {
            $directory = storage_path('app/public/qrcodes');
            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            $qrCodePath = storage_path("app/public/qrcodes/qr_{$user->id}.svg");
            QrCode::format('svg')
                  ->size(200)
                  ->backgroundColor(255, 255, 255)
                  ->color(0, 0, 0)
                  ->margin(1)
                  ->generate($code, $qrCodePath);

            $result = Cloudinary::upload($qrCodePath, [
                'folder' => 'qrcodes/',
                'public_id' => "qr_{$user->id}",
                'resource_type' => 'raw'
            ]);

            if (file_exists($qrCodePath)) {
                unlink($qrCodePath);
            }

            return $result->getSecurePath();
        } catch (\Exception $e) {
            Log::error('Erreur QR Code : ' . $e->getMessage());
            throw $e;
        }
    }

    protected function sendNotifications($user, $qrUrl, $cardPdfPath, $code)
    {
        try {
            // Envoyer SMS
            $this->smsService->send(
                '+221' . $user->telephone,
                "Bienvenue sur Wave ! Votre code de vérification est : {$code}"
            );
            
            // Préparer et déclencher l'événement pour l'envoi d'email
            $mailData = [
                'user' => $user,
                'qrUrl' => $qrUrl,
                'cardPdfPath' => $cardPdfPath,
                'code' => $code
            ];
            
            event(new UserRegistered($mailData));
            
            Log::info('Notifications envoyées avec succès pour l\'utilisateur : ' . $user->id);
            
        } catch (\Exception $e) {
            Log::error('Erreur notifications : ' . $e->getMessage());
            throw $e;
        }
    }
}
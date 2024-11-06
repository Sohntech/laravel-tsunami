<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class WelcomeUser extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $qrUrl;
    protected $cardPdfPath;

    public function __construct($user, $qrUrl, $cardPdfPath)
    {
        $this->user = $user;
        $this->qrUrl = $qrUrl;
        $this->cardPdfPath = $cardPdfPath;
    }

    public function build()
    {
        try {
            Log::info('Construction de l\'email pour : ' . $this->user->email);
            Log::info('Chemin du PDF : ' . $this->cardPdfPath);

            $email = $this->view('emails.welcome')
                         ->subject('Bienvenue sur Wave');

            if ($this->cardPdfPath && file_exists($this->cardPdfPath)) {
                Log::info('Ajout de la pièce jointe PDF');
                $email->attach($this->cardPdfPath, [
                    'as' => 'votre_carte_wave.pdf',
                    'mime' => 'application/pdf'
                ]);
            } else {
                Log::error('Fichier PDF non trouvé : ' . $this->cardPdfPath);
            }

            return $email;
        } catch (\Exception $e) {
            Log::error('Erreur dans la construction de l\'email : ' . $e->getMessage());
            throw $e;
        }
    }
}
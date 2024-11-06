<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Mail\WelcomeUser;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendWelcomeNotification
{
    public function handle(UserRegistered $event)
    {
        try {
            Mail::to($event->mailData['user']->email)
                ->send(new WelcomeUser(
                    $event->mailData['user'],
                    $event->mailData['qrUrl'],
                    $event->mailData['cardPdfPath'],
                    $event->mailData['code']
                ));

            Log::info('Email de bienvenue envoyé avec succès à : ' . $event->mailData['user']->email);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'email de bienvenue : ' . $e->getMessage());
        }
    }
}
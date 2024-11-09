<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class SendSms
{
    protected $client;

    public function __construct()
    {
        // Utilisation des informations d'environnement pour initialiser le client Twilio
        $this->client = new Client(
            getenv('TWILIO_ACCOUNT_SID'),
            getenv('TWILIO_AUTH_TOKEN')
        );
    }

    public function send($to, $message)
    {
        try {
            $this->client->messages->create(
                $to,
                [
                    'from' => getenv('TWILIO_PHONE_NUMBER'),
                    'body' => $message
                ]
            );
            return true;
        } catch (\Exception $e) {
            Log::error('SMS sending failed: ' . $e->getMessage());
            return false;
        }
    }
}

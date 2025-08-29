<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class InfobipService
{
    protected $apiKey;
    protected $baseUrl;
    protected $from;

   public function __construct()
    {
        $this->apiKey = config('services.infobip.api_key') ?? '92a3800cbe103bade12be7c66950ae7c-274e2f79-0f64-41f4-8c5a-e381034b269b';
        $this->baseUrl = config('services.infobip.base_url') ?? 'https://38m35m.api.infobip.com';
        $this->from   = config('services.infobip.from') ?? 'AFT_IMPORT';

        logger()->info('Infobip Config', [
            'apiKey' => $this->apiKey,
            'baseUrl' => $this->baseUrl,
            'from' => $this->from,
        ]);
    }



    /**
     * Envoie un SMS à un destinataire.
     *
     * @param string $to Le numéro du destinataire au format international (ex: 2250XXXXXXXX)
     * @param string $message Le contenu du message
     * @return array|null
     */
    public function sendSms(string $to, string $message)
    {
        // Validation basique des prérequis
        if (!$this->apiKey || !$this->baseUrl) {
            Log::error('Infobip credentials are not configured.');
            return null;
        }

        // Assurez-vous que le numéro est au bon format (retire le '+' s'il est présent)
        $to = ltrim($to, '+');

         $response = Http::withHeaders([
                'Authorization' => 'App ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl . '/sms/2/text/advanced', [
                'messages' => [
                    [
                        'from' => $this->from,
                        'destinations' => [
                            ['to' => $to],
                        ],
                        'text' => $message,
                    ],
                ],
            ]);


        // Gestion des erreurs de la réponse
        if ($response->failed()) {
            Log::error('Infobip SMS sending failed.', [
                'status' => $response->status(),
                'response' => $response->body(),
                'to' => $to,
            ]);
        }

        return $response->json();
    }

}
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InfobipSmsService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $sender;

    public function __construct()
    {
        $this->baseUrl = config('infobip.base_url');
        $this->apiKey = config('infobip.api_key');
        $this->sender = config('infobip.sms_sender');

        if (empty($this->baseUrl) || empty($this->apiKey) || empty($this->sender)) {
            Log::error('Infobip configuration error: Missing base URL, API key or sender.');
            throw new \RuntimeException('Infobip service cannot be initialized due to missing configuration.');
        }
    }

    public function sendSms(string $to, string $message): bool
    {
        try {
            if (!preg_match('/^\+[1-9]\d{1,14}$/', $to)) {
                Log::warning("Infobip SMS: Format de numéro invalide pour '{$to}'.");
                return false;
            }

            $url = rtrim($this->baseUrl, '/') . '/sms/2/text/advanced';

            $payload = [
                'messages' => [
                    [
                        'from' => $this->sender,
                        'destinations' => [
                            ['to' => $to]
                        ],
                        'text' => $message,
                    ]
                ]
            ];

            // dd($payload, $url);
            $response = Http::withHeaders([
                'Authorization' => 'App ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($url, $payload);
            

            // dd($response->body());
            if ($response->successful()) {
                Log::info("✅ SMS envoyé à {$to} via Infobip. Message: {$message}");
                return true;
            } else {
                Log::error("❌ Échec de l'envoi du SMS à {$to}. Response: " . $response->body());
                return false;
            }
        } catch (\Throwable $e) {
            Log::error("❌ Erreur inattendue lors de l'envoi de SMS à {$to}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class infobipService
{
    protected $apiKey;
    protected $baseUrl;
    protected $from;

    public function __construct()
    {
        $this->apiKey = config('services.infobip.api_key');
        $this->baseUrl = config('services.infobip.base_url');
        $this->from = config('services.infobip.from');
    }

    public function sendSms($to, $message)
    {
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

        return $response->json();
    }
}

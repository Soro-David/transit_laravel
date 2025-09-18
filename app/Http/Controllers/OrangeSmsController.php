<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrangeSmsController extends Controller
{
    public function sendTestSms(Request $request)
    {
        $to = '+2250160003513';
        $from = '+2250769502967';
        $message = $request->input('message', 'Bonjour, test SMS Orange');

        $clientId = env('ORANGE_CLIENT_ID');
        $clientSecret = env('ORANGE_CLIENT_SECRET');
        $scope = env('ORANGE_SCOPE', 'SMS');

        $auth = base64_encode("$clientId:$clientSecret");

        // dd($auth);
        // Obtenir le token
        $ch = curl_init("https://api.orange.com/oauth/v3/token");
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Basic $auth",
                "Content-Type: application/x-www-form-urlencoded"
            ],
            CURLOPT_POSTFIELDS => "grant_type=client_credentials&scope=$scope",
            CURLOPT_RETURNTRANSFER => true,
        ]);
        $response = curl_exec($ch);

        // dd($response);
        if (curl_errno($ch)) {
            return response()->json(['error' => 'Erreur cURL lors de la récupération du token', 'message' => curl_error($ch)]);
        }
        curl_close($ch);

        $tokenData = json_decode($response, true);
        if (!isset($tokenData['access_token'])) {
            return response()->json([
                'error' => 'Impossible d’obtenir le token',
                'response' => $response
            ]);
        }

        $token = $tokenData['access_token'];

        // Préparer le SMS
        $smsData = [
            "outboundSMSMessageRequest" => [
                "address" => "tel:$to",
                "senderAddress" => "tel:$from",
                "outboundSMSTextMessage" => ["message" => $message]
            ]
        ];

        $url = "https://api.orange.com/smsmessaging/v1/outbound/tel:" . urlencode($from) . "/requests";
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $token",
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode($smsData),
            CURLOPT_RETURNTRANSFER => true,
        ]);
        $smsResponse = curl_exec($ch);

        if (curl_errno($ch)) {
            return response()->json(['error' => 'Erreur cURL lors de l’envoi du SMS', 'message' => curl_error($ch)]);
        }

        curl_close($ch);

        return response()->json([
            'token' => $token,
            'sms_response' => json_decode($smsResponse, true)
        ]);
    }
}

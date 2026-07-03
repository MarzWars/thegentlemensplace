<?php
// app/Services/LiveKitService.php
namespace App\Services;

class LiveKitService
{
    private string $apiKey;
    private string $apiSecret;

    public function __construct()
    {
        $this->apiKey    = LIVEKIT_API_KEY;
        $this->apiSecret = LIVEKIT_API_SECRET;
    }

    /**
     * Generate LiveKit WebRTC Access Token (JWT)
     */
    public function generateToken(string $roomName, string $identity, string $name = ''): string
    {
        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT'
        ];

        $now = time();
        $payload = [
            'iss' => $this->apiKey,
            'sub' => $identity,
            'nbf' => $now - 10,
            'exp' => $now + 3600, // 1 hour expiration
            'video' => [
                'roomJoin'     => true,
                'room'         => $roomName,
                'canPublish'   => true,
                'canSubscribe' => true,
                'canPublishData'=> true,
            ]
        ];

        if ($name !== '') {
            $payload['name'] = $name;
        }

        $base64UrlHeader  = $this->base64UrlEncode(json_encode($header));
        $base64UrlPayload = $this->base64UrlEncode(json_encode($payload));
        $signature        = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->apiSecret, true);
        $base64UrlSignature = $this->base64UrlEncode($signature);

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    private function base64UrlEncode(string $text): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($text));
    }
}

<?php
// app/Services/PusherService.php
namespace App\Services;

class PusherService
{
    private string $instanceId;
    private string $secretKey;

    public function __construct()
    {
        $this->instanceId = PUSHER_BEAMS_INSTANCE_ID;
        $this->secretKey  = PUSHER_BEAMS_SECRET_KEY;
    }

    /**
     * Send Pusher Beams push notification to performer's device interest
     */
    public function sendCallNotification(int $performerId, string $callerName, string $callUuid): bool
    {
        $url = "https://{$this->instanceId}.pushnotifications.pusher.com/pubapi/v1/instances/{$this->instanceId}/publishes";

        $payload = [
            'interests' => ['performer_' . $performerId],
            'web' => [
                'notification' => [
                    'title'     => 'Incoming Voice Call',
                    'body'      => "{$callerName} is calling you now. Click to open your dashboard.",
                    'deep_link' => BASE_URL . BASE_PATH . '/performer-dash',
                ],
                'data' => [
                    'type'        => 'incoming_call',
                    'call_uuid'   => $callUuid,
                    'caller_name' => $callerName,
                ]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->secretKey
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return true;
        }

        error_log('[PusherService] Beams publish failed: ' . $response);
        return false;
    }
}

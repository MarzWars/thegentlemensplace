<?php
// app/Services/TelephonyService.php
namespace App\Services;

class TelephonyService
{
    private \Twilio\Rest\Client $client;
    private string $fromNumber;
    private string $webhookBaseUrl;

    public function __construct()
    {
        $this->client         = new \Twilio\Rest\Client(TWILIO_ACCOUNT_SID, TWILIO_AUTH_TOKEN);
        $this->fromNumber     = TWILIO_PHONE_NUMBER;
        $this->webhookBaseUrl = BASE_URL;
    }

    /**
     * Initiate an outbound bridged call.
     * 1. Calls the user first (or user dials a number)
     * 2. When user answers → conference bridge → calls performer
     */
    public function initiateCall(string $callUuid, string $userPhone, string $performerPhone): array
    {
        // TwiML for the initial call to the user
        // When they answer, Twilio calls the performer and bridges
        $twimlUrl = $this->webhookBaseUrl . '/webhook/twiml-bridge?'
                  . http_build_query(['call_uuid' => $callUuid, 'performer_phone' => urlencode($performerPhone)]);

        $call = $this->client->calls->create(
            $userPhone,     // Call user first
            $this->fromNumber,
            [
                'url'          => $twimlUrl,
                'method'       => 'GET',
                'statusCallback' => $this->webhookBaseUrl . '/webhook/call-status',
                'statusCallbackMethod' => 'POST',
                'statusCallbackEvent'  => ['initiated', 'ringing', 'answered', 'completed'],
                'timeout'      => 30,  // seconds to wait for answer
            ]
        );

        return [
            'call_sid' => $call->sid,
            'status'   => $call->status,
        ];
    }

    /**
     * TwiML response for bridging call to performer.
     * This endpoint is called by Twilio when user answers.
     */
    public function generateBridgeTwiML(string $performerPhone, string $callUuid): string
    {
        $statusUrl = $this->webhookBaseUrl . '/webhook/call-billing?call_uuid=' . urlencode($callUuid);

        return '<?xml version="1.0" encoding="UTF-8"?><Response>
            <Say voice="alice">Please hold while we connect you. Your credits will begin deducting once connected.</Say>
            <Dial timeout="30" callerId="' . htmlspecialchars($this->fromNumber) . '"
                  action="' . htmlspecialchars($statusUrl) . '"
                  method="POST">
                <Number statusCallbackEvent="initiated ringing answered completed"
                        statusCallback="' . htmlspecialchars($statusUrl) . '"
                        statusCallbackMethod="POST">
                    ' . htmlspecialchars($performerPhone) . '
                </Number>
            </Dial>
            <Say voice="alice">The call has ended. Thank you for using TheGentlemensPlace.</Say>
        </Response>';
    }

    /**
     * Hang up an active call (called when user runs out of credits).
     */
    public function hangupCall(string $callSid): void
    {
        $this->client->calls($callSid)->update(['status' => 'completed']);
    }

    /**
     * Play IVR warning to user (low credits).
     */
    public function playLowCreditWarning(string $callSid, float $creditsRemaining): void
    {
        $twimlUrl = $this->webhookBaseUrl . '/webhook/twiml-warning?credits=' . $creditsRemaining;
        $this->client->calls($callSid)->update(['url' => $twimlUrl, 'method' => 'GET']);
    }
}
<?php
// app/Controllers/BillingController.php
namespace App\Controllers;

use App\Services\{CreditService, TelephonyService};
use App\Models\{Call, Performer};
use App\Config\Database;

class BillingController
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Twilio calls this on every call status update.
     * Also used for per-minute billing tick.
     */
    public function callStatus(): void
    {
        // Validate Twilio signature (IMPORTANT — prevents fake webhook calls)
        $this->validateTwilioSignature() or $this->denyAndExit();

        $callSid    = $_POST['CallSid']    ?? '';
        $callStatus = $_POST['CallStatus'] ?? '';
        $callUuid   = $_GET['call_uuid']   ?? '';

        $callModel = new Call();
        $call = $callModel->findByUuid($callUuid);

        if (!$call) { http_response_code(200); exit; }

        switch ($callStatus) {
            case 'ringing':
                $callModel->updateStatus($call['id'], 'ringing');
                break;

            case 'in-progress':
                $callModel->markAnswered($call['id']);
                break;

            case 'completed':
            case 'no-answer':
            case 'busy':
            case 'failed':
            case 'canceled':
                $this->finalizeCall($call, $callStatus, $_POST);
                break;
        }

        http_response_code(200);
        echo '<?xml version="1.0" encoding="UTF-8"?><Response></Response>';
    }

    /**
     * Per-minute billing tick — called by Twilio at each billing interval.
     * Set up in TwiML with <Dial action="..."> pointing here every 60 seconds.
     */
    public function callBilling(): void
    {
        $this->validateTwilioSignature() or $this->denyAndExit();

        $callUuid       = $_GET['call_uuid'] ?? '';
        $dialCallStatus = $_POST['DialCallStatus'] ?? '';
        $callDuration   = (int)($_POST['DialCallDuration'] ?? 0);

        $callModel = new Call();
        $call = $callModel->findByUuid($callUuid);

        if (!$call || $call['status'] !== 'in_progress') {
            http_response_code(200);
            echo '<?xml version="1.0" encoding="UTF-8"?><Response></Response>';
            return;
        }

        $ratePerMinute = (float)$call['rate_per_minute'];
        $userId        = (int)$call['user_id'];

        $creditService = new CreditService();
        $newBalance    = $creditService->deductCallCredits($userId, $call['id'], $ratePerMinute);

        $telephony = new TelephonyService();

        if ($newBalance === false || $newBalance <= 0) {
            // No credits — play warning and disconnect
            error_log("[Billing] User {$userId} out of credits. Ending call {$callUuid}.");
            http_response_code(200);
            echo '<?xml version="1.0" encoding="UTF-8"?><Response>
                <Say voice="alice">You have run out of credits. Please add more credits to continue calling. Goodbye.</Say>
                <Hangup/>
            </Response>';
            return;
        }

        $lowCreditThreshold = (int)$this->getSetting('low_credit_warning');
        if ($newBalance <= $lowCreditThreshold) {
            // Play low-credit warning but continue
            http_response_code(200);
            echo '<?xml version="1.0" encoding="UTF-8"?><Response>
                <Say voice="alice">Warning: You have ' . round($newBalance) . ' credits remaining. Please add more credits soon.</Say>
            </Response>';
            return;
        }

        // Continue call — return empty TwiML
        http_response_code(200);
        echo '<?xml version="1.0" encoding="UTF-8"?><Response></Response>';
    }

    private function finalizeCall(array $call, string $status, array $postData): void
    {
        $durationSecs    = (int)($postData['CallDuration'] ?? 0);
        $callModel       = new Call();
        $performerModel  = new Performer();
        $creditService   = new CreditService();

        // Calculate actual credits used based on duration
        $minutesBilled   = ceil($durationSecs / 60);
        $totalCredits    = $minutesBilled * (float)$call['rate_per_minute'];

        // Calculate performer earnings
        $performer       = $performerModel->find($call['performer_id']);
        $commissionRate  = (float)$performer['commission_rate'] / 100;
        $performerEarns  = round($totalCredits * $commissionRate, 4);
        $platformEarns   = round($totalCredits - $performerEarns, 4);

        // Update call record
        $callModel->finalize($call['id'], [
            'status'              => $this->mapStatus($status),
            'duration_seconds'    => $durationSecs,
            'credits_used'        => $totalCredits,
            'performer_earnings'  => $performerEarns,
            'platform_earnings'   => $platformEarns,
            'ended_at'            => date('Y-m-d H:i:s'),
            'termination_reason'  => $status,
        ]);

        // Update performer earnings
        $performerModel->addEarnings($call['performer_id'], $performerEarns);
        $performerModel->incrementCallStats($call['performer_id'], $durationSecs);
    }

    private function validateTwilioSignature(): bool
    {
        // Twilio signs every request with X-Twilio-Signature header
        $signature  = $_SERVER['HTTP_X_TWILIO_SIGNATURE'] ?? '';
        $url        = BASE_URL . $_SERVER['REQUEST_URI'];
        $authToken  = TWILIO_AUTH_TOKEN;

        // Build the string to sign
        $params = $_POST;
        ksort($params);
        $str = $url;
        foreach ($params as $key => $val) {
            $str .= $key . $val;
        }

        $expected = base64_encode(hash_hmac('sha1', $str, $authToken, true));
        return hash_equals($expected, $signature);
    }

    private function getSetting(string $key): string
    {
        $stmt = $this->db->prepare("SELECT `value` FROM settings WHERE `key` = ?");
        $stmt->execute([$key]);
        return $stmt->fetchColumn() ?: '';
    }

    private function mapStatus(string $twilioStatus): string
    {
        return match($twilioStatus) {
            'completed'  => 'completed',
            'no-answer'  => 'no_answer',
            'busy'       => 'busy',
            'failed'     => 'failed',
            'canceled'   => 'cancelled',
            default      => 'completed',
        };
    }

    private function denyAndExit(): never
    {
        http_response_code(403);
        exit('Forbidden');
    }
}
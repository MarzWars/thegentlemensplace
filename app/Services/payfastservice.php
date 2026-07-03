<?php
// app/Services/PayFastService.php
namespace App\Services;

class PayFastService
{
    private string $merchantId;
    private string $merchantKey;
    private string $passphrase;
    private bool   $sandbox;
    private string $baseUrl;

    public function __construct()
    {
        $this->merchantId  = PAYFAST_MERCHANT_ID;
        $this->merchantKey = PAYFAST_MERCHANT_KEY;
        $this->passphrase  = PAYFAST_PASSPHRASE;
        $this->sandbox     = PAYFAST_SANDBOX;
        $this->baseUrl     = $this->sandbox
            ? 'https://sandbox.payfast.co.za/eng/process'
            : 'https://www.payfast.co.za/eng/process';
    }

    /**
     * Generate a PayFast-compatible MD5 signature.
     *
     * Rules (official PayFast PHP integration guide):
     *  - Sort parameters alphabetically (ksort) — required by PayFast
     *  - Skip empty string values
     *  - Skip the 'signature' key itself
     *  - Use urlencode() on each trimmed value
     *  - Append passphrase at the end if one is set
     *  - MD5 hash the resulting string
     */
    public function generateSignature(array $data): string
    {
        // PayFast requires alphabetical sort for consistent hashing
        ksort($data);

        $pfOutput = '';
        foreach ($data as $key => $value) {
            if ($key === 'signature') continue;
            if (trim((string)$value) === '') continue; // skip blank fields

            $pfOutput .= $key . '=' . urlencode(trim((string)$value)) . '&';
        }

        // Remove trailing '&'
        $pfOutput = rtrim($pfOutput, '&');

        // Append passphrase if configured
        if (!empty($this->passphrase)) {
            $pfOutput .= '&passphrase=' . urlencode($this->passphrase);
        }

        return md5($pfOutput);
    }

    public function buildPaymentData(array $transaction, string $returnUrl, string $cancelUrl, string $notifyUrl): array
    {
        $data = [
            'merchant_id'      => $this->merchantId,
            'merchant_key'     => $this->merchantKey,
            'return_url'       => $returnUrl,
            'cancel_url'       => $cancelUrl,
            'notify_url'       => $notifyUrl,
            'm_payment_id'     => $transaction['uuid'],
            'amount'           => number_format($transaction['amount_zar'], 2, '.', ''),
            'item_name'        => $transaction['item_name'],
            'item_description' => 'Credits for TheGentlemensPlace.eu',
            'email_address'    => $transaction['user_email'],
            'name_first'       => $transaction['user_name'],
        ];

        $data['signature'] = $this->generateSignature($data);
        return $data;
    }

    public function buildFormHtml(array $paymentData): string
    {
        $inputs = '';
        foreach ($paymentData as $key => $value) {
            $inputs .= '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">' . "\n";
        }

        return '
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Redirecting to Secure Payment...</title>
  <style>
    :root {
      --black: #0a0805;
      --charcoal: #111008;
      --gold: #c9a84c;
      --gold-lt: #e0c06a;
      --cream: #f0e8d0;
      --cream-dim: #c4b896;
      --ff-serif: \'Playfair Display\', Georgia, serif;
      --ff-sans: \'Montserrat\', sans-serif;
    }
    body {
      background: var(--black);
      color: var(--cream-dim);
      font-family: var(--ff-sans);
      margin: 0;
      padding: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      text-align: center;
    }
    .redirect-card {
      max-width: 480px;
      width: 90%;
      background: var(--charcoal);
      border: 1px solid rgba(201, 168, 76, 0.15);
      padding: 3.5rem 2.5rem;
      border-radius: 4px;
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6);
    }
    .logo-mark {
      width: 48px;
      height: 48px;
      border: 1px solid var(--gold);
      margin: 0 auto 2rem;
      display: grid;
      place-items: center;
      font-family: var(--ff-serif);
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--gold);
    }
    h1 {
      font-family: var(--ff-serif);
      font-size: 1.45rem;
      font-weight: 600;
      color: var(--cream);
      margin: 0 0 1rem 0;
    }
    p {
      font-size: 0.82rem;
      line-height: 1.6;
      color: var(--cream-dim);
      opacity: 0.8;
      margin: 0 0 2rem 0;
    }
    .spinner {
      width: 40px;
      height: 40px;
      border: 2px solid rgba(201, 168, 76, 0.1);
      border-top-color: var(--gold);
      border-radius: 50%;
      margin: 0 auto 2.5rem;
      animation: spin 1s linear infinite;
    }
    @keyframes spin {
      to { transform: rotate(360deg); }
    }
    .btn-payfast {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: var(--gold);
      color: var(--black);
      border: 1px solid var(--gold);
      font-family: var(--ff-sans);
      font-size: 0.65rem;
      font-weight: 600;
      letter-spacing: 0.22em;
      text-transform: uppercase;
      text-decoration: none;
      padding: 0.9rem 2.25rem;
      cursor: pointer;
      transition: background 0.3s, border-color 0.3s;
      width: 100%;
    }
    .btn-payfast:hover {
      background: var(--gold-lt);
      border-color: var(--gold-lt);
    }
  </style>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
</head>
<body>
  <div class="redirect-card">
    <div class="logo-mark">GP</div>
    <h1>Connecting to Secure Gateway</h1>
    <p>Please wait a moment while we redirect you to PayFast to complete your payment securely.</p>
    <div class="spinner"></div>
    <form id="payfast-form" action="' . $this->baseUrl . '" method="POST">
      ' . $inputs . '
      <button type="submit" class="btn-payfast">Proceed to PayFast</button>
    </form>
  </div>
  <script>
    setTimeout(function() {
      document.getElementById("payfast-form").submit();
    }, 600);
  </script>
</body>
</html>';
    }

    public function validateITN(array $data): bool
    {
        // 1. Verify IP is from PayFast.
        //    PayFast uses rotating AWS IPs — we match by subnet range rather than
        //    a static list since new IPs are added regularly.
        //    Known subnets observed in production:
        //      102.216.36.0/24  — active (seen: .1 .2 .10 .12 .13 .14 .15)
        //      13.245.74.0/24   — active (seen: .88)
        //      3.163.x.x/16     — documented AWS migration IPs
        //      197.97.145.x     — legacy
        //      41.74.179.x      — legacy
        $validSubnets = [
            '102.216.36.',   // Active PayFast AWS subnet (confirmed in logs)
            '13.245.74.',    // Active PayFast AWS subnet (confirmed in logs)
            '13.244.',       // PayFast AWS af-south-1 range
            '13.245.',       // PayFast AWS af-south-1 range
            '13.246.',       // PayFast AWS af-south-1 range
            '3.163.',        // PayFast documented AWS IPs
            '197.97.145.',   // Legacy
            '41.74.179.',    // Legacy
        ];

        // Resolve real IP — supports Cloudflare and reverse proxies
        $remoteIp = $_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $remoteIp = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $remoteIp = trim($ips[0]);
        }

        error_log('[PayFast ITN] Resolved IP: ' . $remoteIp);

        $ipAllowed = false;
        foreach ($validSubnets as $subnet) {
            if (str_starts_with($remoteIp, $subnet)) {
                $ipAllowed = true;
                break;
            }
        }

        if (!$ipAllowed) {
            if (!$this->sandbox) {
                error_log('[PayFast ITN] FAILED: IP ' . $remoteIp . ' not in valid PayFast subnet');
                return false;
            }
            error_log('[PayFast ITN] Sandbox mode — IP check skipped for: ' . $remoteIp);
        }

        // 2. Verify signature
        $receivedSig = $data['signature'] ?? '';
        
        // Reconstruct parameter string in received order, including empty fields
        $pfParamString = '';
        foreach ($data as $key => $value) {
            if ($key === 'signature') continue;
            $pfParamString .= $key . '=' . urlencode(stripslashes((string)$value)) . '&';
        }
        $pfParamString = rtrim($pfParamString, '&');

        // Append passphrase if configured
        if (!empty($this->passphrase)) {
            $pfParamString .= '&passphrase=' . urlencode($this->passphrase);
        }

        $expectedSig = md5($pfParamString);

        error_log('[PayFast ITN] Received signature: ' . $receivedSig);
        error_log('[PayFast ITN] Expected signature: ' . $expectedSig);

        if (!hash_equals($expectedSig, $receivedSig)) {
            error_log('[PayFast ITN] FAILED: Signature mismatch');
            return false;
        }

        // 3. Verify payment status
        if (($data['payment_status'] ?? '') !== 'COMPLETE') {
            error_log('[PayFast ITN] FAILED: Payment status is "' . ($data['payment_status'] ?? 'missing') . '"');
            return false;
        }

        // 4. Verify with PayFast server (server-to-server ping)
        // We pass the full original $data (which includes the signature) to the validation request.
        $valid = $this->verifyWithServer($data);
        if (!$valid) {
            error_log('[PayFast ITN] FAILED: Server-to-server verification returned invalid');
        }
        return $valid;
    }

    private function verifyWithServer(array $data): bool
    {
        $validateUrl = $this->sandbox
            ? 'https://sandbox.payfast.co.za/eng/query/validate'
            : 'https://www.payfast.co.za/eng/query/validate';

        $ch = curl_init($validateUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER     => ['version: 1.0.0', 'merchant-id: ' . $this->merchantId],
        ]);
        $response  = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        error_log('[PayFast ITN] Server validation response: "' . $response . '"');
        if ($curlError) {
            error_log('[PayFast ITN] cURL error: ' . $curlError);
        }

        return strtolower(trim($response)) === 'valid';
    }
}
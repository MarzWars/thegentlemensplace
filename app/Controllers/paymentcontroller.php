<?php
// app/Controllers/PaymentController.php
namespace App\Controllers;

use App\Core\{Controller, CSRF, Lang};
use App\Services\CreditService;
use App\Config\Database;

class PaymentController extends Controller
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ── Sandbox simulation (POC only) ─────────────────────
    public function simulate(): void
    {
        if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
            $this->abort(403);
        }

        $uuid = preg_replace('/[^a-f0-9\-]/', '', $_POST['uuid'] ?? '');
        if (!$uuid) {
            $this->flashError('Invalid transaction reference.');
            $this->redirect(BASE_PATH . '/credits');
        }

        $stmt = $this->db->prepare("SELECT * FROM transactions WHERE uuid = ? LIMIT 1");
        $stmt->execute([$uuid]);
        $tx = $stmt->fetch();

        if (!$tx) {
            $this->flashError('Transaction not found.');
            $this->redirect(BASE_PATH . '/credits');
        }

        if ($tx['status'] === 'completed') {
            $this->flashSuccess('Credits already applied to your account.');
            $this->redirect(BASE_PATH . '/credits');
        }

        if ((int)$tx['user_id'] !== (int)$_SESSION['user_id']) {
            $this->abort(403);
        }

        $creditService = new CreditService();
        $totalCredits  = (float)$tx['credits_purchased'] + (float)$tx['bonus_credits'];
        $success = $creditService->awardPurchaseCredits(
            (int)$tx['user_id'],
            (int)$tx['id'],
            $totalCredits,
            'SANDBOX-' . strtoupper(substr($uuid, 0, 8))
        );

        if ($success) {
            $this->db->prepare("
                UPDATE transactions
                SET status = 'completed',
                    payfast_payment_status = 'COMPLETE',
                    itn_received_at = NOW(),
                    updated_at = NOW()
                WHERE id = ?
            ")->execute([$tx['id']]);

            $balStmt = $this->db->prepare("SELECT credit_balance FROM users WHERE id = ?");
            $balStmt->execute([$tx['user_id']]);
            $_SESSION['credits'] = (float)$balStmt->fetchColumn();

            $this->redirect(BASE_PATH . '/payment/success?ref=' . urlencode($tx['merchant_reference']));
        } else {
            $this->flashError('Could not apply credits. Please contact support.');
            $this->redirect(BASE_PATH . '/credits');
        }
    }

    // ── Real Checkout Redirect ────────────────────────────
    public function checkout(): void
    {
        if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
            $this->abort(403);
        }

        $uuid = preg_replace('/[^a-f0-9\-]/', '', $_POST['uuid'] ?? '');
        if (!$uuid) {
            $this->flashError('Invalid transaction reference.');
            $this->redirect(BASE_PATH . '/credits');
        }

        $stmt = $this->db->prepare("SELECT * FROM transactions WHERE uuid = ? LIMIT 1");
        $stmt->execute([$uuid]);
        $tx = $stmt->fetch();

        if (!$tx || $tx['status'] === 'completed') {
            $this->flashError('Transaction not found or already completed.');
            $this->redirect(BASE_PATH . '/credits');
        }

        // Fetch user email/name details
        $userStmt = $this->db->prepare("SELECT email, username FROM users WHERE id = ?");
        $userStmt->execute([$tx['user_id']]);
        $user = $userStmt->fetch();

        $tx['user_email'] = $user['email'];
        $tx['user_name']  = $user['username'] ?: 'Gentleman Customer';

        // Build callback URLs using the pre-computed BASE_URL (handles Cloudflare HTTPS correctly)
        $returnUrl = BASE_URL . BASE_PATH . '/payment/success?ref=' . urlencode($tx['merchant_reference']);
        $cancelUrl = BASE_URL . BASE_PATH . '/payment/cancel';
        $notifyUrl = BASE_URL . BASE_PATH . '/payment/notify';

        error_log('[PayFast Checkout] notify_url=' . $notifyUrl);

        // Build payment and signature using PayFastService
        $pfService = new \App\Services\PayFastService();
        $paymentData = $pfService->buildPaymentData($tx, $returnUrl, $cancelUrl, $notifyUrl);

        // Echoes an auto-submitting hidden HTML form pointing directly to PayFast gateway
        echo $pfService->buildFormHtml($paymentData);
        exit;
    }

    // ── PayFast ITN webhook (production) ──────────────────
    public function notify(): void
    {
        $raw = file_get_contents('php://input');
        error_log('[PayFast ITN] Raw received: ' . $raw);

        if (empty($raw)) {
            error_log('[PayFast ITN] ERROR: Empty body received — PayFast could not reach notify URL');
            http_response_code(200); exit;
        }

        parse_str($raw, $data);

        // In sandbox mode skip IP/signature validation, but still award credits
        if (!PAYFAST_SANDBOX) {
            $pfService = new \App\Services\PayFastService();
            if (!$pfService->validateITN($data)) {
                error_log('[PayFast ITN] validateITN returned false — aborting');
                http_response_code(400);
                exit;
            }
        } else {
            error_log('[PayFast ITN] Sandbox mode — skipping IP/signature validation');
            // Still enforce COMPLETE status
            if (($data['payment_status'] ?? '') !== 'COMPLETE') {
                error_log('[PayFast ITN] Sandbox: payment_status is not COMPLETE, ignoring');
                http_response_code(200); exit;
            }
        }

        $merchantRef = $data['m_payment_id'] ?? '';
        error_log('[PayFast ITN] Looking up transaction uuid: ' . $merchantRef);

        $stmt = $this->db->prepare("SELECT * FROM transactions WHERE uuid = ? LIMIT 1");
        $stmt->execute([$merchantRef]);
        $tx = $stmt->fetch();

        if (!$tx) {
            error_log('[PayFast ITN] ERROR: Transaction not found for uuid: ' . $merchantRef);
            http_response_code(200); exit;
        }

        if ($tx['status'] === 'completed') {
            error_log('[PayFast ITN] Transaction already completed, skipping duplicate ITN');
            http_response_code(200); exit;
        }

        // Verify amount matches what we expect
        $expectedAmount = number_format((float)$tx['amount_zar'], 2, '.', '');
        $receivedAmount = $data['amount_gross'] ?? '';
        if ($expectedAmount !== $receivedAmount) {
            error_log('[PayFast ITN] ERROR: Amount mismatch. Expected: ' . $expectedAmount . ', Got: ' . $receivedAmount);
            http_response_code(400); exit;
        }

        $pfPaymentId  = $data['pf_payment_id'] ?? 'SANDBOX-' . strtoupper(substr($merchantRef, 0, 8));
        $totalCredits = (float)$tx['credits_purchased'] + (float)$tx['bonus_credits'];

        error_log('[PayFast ITN] Awarding ' . $totalCredits . ' credits to user_id=' . $tx['user_id']);

        $creditService = new CreditService();
        $success = $creditService->awardPurchaseCredits(
            (int)$tx['user_id'],
            (int)$tx['id'],
            $totalCredits,
            $pfPaymentId
        );

        if ($success) {
            $this->db->prepare("
                UPDATE transactions
                SET status = 'completed',
                    payfast_pf_payment_id = ?,
                    payfast_payment_status = 'COMPLETE',
                    itn_received_at = NOW(),
                    updated_at = NOW()
                WHERE id = ?
            ")->execute([$pfPaymentId, $tx['id']]);
            error_log('[PayFast ITN] SUCCESS: Credits awarded and transaction marked completed');
        } else {
            error_log('[PayFast ITN] ERROR: awardPurchaseCredits returned false for tx id=' . $tx['id']);
        }

        http_response_code(200); exit;
    }

    // ── Return pages ──────────────────────────────────────
    public function success(): void
    {
        $ref = htmlspecialchars($_GET['ref'] ?? '');
        $this->view('credits/success', [
            'title'   => 'Payment Successful — ' . Lang::t('meta.site_name'),
            'layout'  => 'home',
            'ref'     => $ref,
            'balance' => (float)($_SESSION['credits'] ?? 0),
        ]);
    }

    public function cancel(): void
    {
        $this->view('credits/cancelled', [
            'title'  => 'Payment Cancelled — ' . Lang::t('meta.site_name'),
            'layout' => 'home',
        ]);
    }
}

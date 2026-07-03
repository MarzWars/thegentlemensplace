<?php
// app/Controllers/CreditController.php
namespace App\Controllers;

use App\Core\{Controller, CSRF, RateLimit, Lang};
use App\Config\Database;
use App\Models\User;

class CreditController extends Controller
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ── GET /credits ──────────────────────────────────────
    public function packages(): void
    {
        $userId   = (int)$_SESSION['user_id'];
        $balance  = (float)($_SESSION['credits'] ?? 0);
        $currency = CURRENCY;
        $packages = $this->getPackages();

        // Attach display prices to each package
        foreach ($packages as &$pkg) {
            $pkg['display_price']    = \App\Services\CurrencyService::packagePrice($pkg, $currency);
            $pkg['display_currency'] = $currency;
            $pkg['display_symbol']   = \App\Services\CurrencyService::symbol($currency);
            $pkg['price_zar_actual'] = \App\Services\CurrencyService::packagePriceZAR($pkg, $currency);
        }
        unset($pkg);

        $recentTx = [];
        try {
            $txStmt = $this->db->prepare("
                SELECT t.*, cp.name AS package_name
                FROM transactions t
                LEFT JOIN credit_packages cp ON t.package_id = cp.id
                WHERE t.user_id = ?
                ORDER BY t.created_at DESC
                LIMIT 5
            ");
            $txStmt->execute([$userId]);
            $recentTx = $txStmt->fetchAll();
        } catch (\Exception $e) {
            // Table may not exist yet in dev
        }

        $this->view('credits/packages', [
            'title'        => Lang::t('meta.credits_title'),
            'metaDesc'     => Lang::t('meta.credits_desc'),
            'metaKeywords' => Lang::t('meta.credits_keywords'),
            'layout'       => 'home',
            'packages'     => $packages,
            'balance'      => $balance,
            'recentTx'     => $recentTx,
            'currency'     => $currency,
        ]);
    }

    // ── POST /credits/purchase ────────────────────────────
    public function initiatePurchase(): void
    {
        if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
            $this->abort(403, 'Invalid request.');
        }

        $userId    = (int)$_SESSION['user_id'];
        $packageId = (int)($_POST['package_id'] ?? 0);

        // Rate limit — max 10 purchase attempts per hour
        if (!RateLimit::check('purchase', (string)$userId, 10, 3600)) {
            $this->flashError('Too many purchase attempts. Please wait a moment.');
            $this->redirect(BASE_PATH . '/credits');
        }

        // Load package
        $packages = $this->getPackages();
        $package  = null;
        foreach ($packages as $p) {
            if ((int)$p['id'] === $packageId) { $package = $p; break; }
        }

        if (!$package) {
            $this->flashError('Invalid package selected.');
            $this->redirect(BASE_PATH . '/credits');
        }

        // Load user
        $userModel = new User();
        $user      = $userModel->findById($userId);

        // Work out the ZAR amount to charge (PayFast always charges ZAR)
        $currency      = CURRENCY;
        $displayPrice  = \App\Services\CurrencyService::packagePrice($package, $currency);
        $zarAmount     = \App\Services\CurrencyService::packagePriceZAR($package, $currency);
        $displaySymbol = \App\Services\CurrencyService::symbol($currency);

        // Create a pending transaction record
        $uuid      = User::generateUuid();
        $reference = 'TGP-' . strtoupper(substr($uuid, 0, 8));
        $itemName  = $package['name'] . ' Credit Package';

        try {
            $this->db->prepare("
                INSERT INTO transactions
                    (uuid, user_id, package_id, amount_zar, credits_purchased, bonus_credits,
                     merchant_reference, item_name, status, ip_address, user_agent)
                VALUES
                    (?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?)
            ")->execute([
                $uuid,
                $userId,
                $package['id'],
                $zarAmount,
                $package['credits'],
                $package['bonus_credits'],
                $reference,
                $itemName,
                $_SERVER['REMOTE_ADDR'],
                substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
            ]);
        } catch (\Exception $e) {
            error_log('[CreditController] Insert failed: ' . $e->getMessage());
            $this->flashError('Could not create transaction. Please ensure the database is set up.');
            $this->redirect(BASE_PATH . '/credits');
        }

        $this->view('credits/confirm', [
            'title'         => 'Confirm Purchase — ' . Lang::t('meta.site_name'),
            'layout'        => 'home',
            'package'       => $package,
            'uuid'          => $uuid,
            'reference'     => $reference,
            'user'          => $user,
            'currency'      => $currency,
            'displayPrice'  => $displayPrice,
            'displaySymbol' => $displaySymbol,
            'zarAmount'     => $zarAmount,
        ]);
    }

    // ── GET /credits/history ──────────────────────────────
    public function history(): void
    {
        $userId = (int)$_SESSION['user_id'];

        $stmt = $this->db->prepare("
            SELECT t.*, cp.name AS package_name
            FROM transactions t
            LEFT JOIN credit_packages cp ON t.package_id = cp.id
            WHERE t.user_id = ?
            ORDER BY t.created_at DESC
        ");
        $stmt->execute([$userId]);
        $transactions = $stmt->fetchAll();

        $ledgerStmt = $this->db->prepare("
            SELECT * FROM credit_ledger
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT 50
        ");
        $ledgerStmt->execute([$userId]);
        $ledger = $ledgerStmt->fetchAll();

        $this->view('credits/history', [
            'title'        => 'Credit History — ' . Lang::t('meta.site_name'),
            'layout'       => 'home',
            'transactions' => $transactions,
            'ledger'       => $ledger,
            'balance'      => (float)($_SESSION['credits'] ?? 0),
        ]);
    }

    // ── Helpers ───────────────────────────────────────────

    private function getPackages(): array
    {
        try {
            $stmt = $this->db->query("
                SELECT * FROM credit_packages
                WHERE is_active = 1
                ORDER BY sort_order ASC, price_zar ASC
            ");
            $rows = $stmt->fetchAll();
            if (!empty($rows)) return $rows;
        } catch (\Exception $e) {
            // DB not ready
        }

        // Demo packages — used until real ones are added via admin
        return [
            [
                'id'           => 1,
                'name'         => 'Starter',
                'credits'      => '15.0000',
                'price_zar'    => '150.00',
                'price_eur'    => '8.00',
                'price_gbp'    => '6.50',
                'price_usd'    => '9.00',
                'bonus_credits'=> '0.0000',
                'is_featured'  => 0,
                'is_active'    => 1,
                'sort_order'   => 1,
                '_demo'        => true,
            ],
            [
                'id'           => 2,
                'name'         => 'Gentleman',
                'credits'      => '40.0000',
                'price_zar'    => '350.00',
                'price_eur'    => '18.00',
                'price_gbp'    => '15.00',
                'price_usd'    => '20.00',
                'bonus_credits'=> '5.0000',
                'is_featured'  => 1,
                'is_active'    => 1,
                'sort_order'   => 2,
                '_demo'        => true,
            ],
            [
                'id'           => 3,
                'name'         => 'Elite',
                'credits'      => '90.0000',
                'price_zar'    => '700.00',
                'price_eur'    => '36.00',
                'price_gbp'    => '30.00',
                'price_usd'    => '40.00',
                'bonus_credits'=> '15.0000',
                'is_featured'  => 0,
                'is_active'    => 1,
                'sort_order'   => 3,
                '_demo'        => true,
            ],
        ];
    }
}

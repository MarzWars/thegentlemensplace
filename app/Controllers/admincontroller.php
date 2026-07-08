<?php
// app/Controllers/AdminController.php
namespace App\Controllers;

use App\Core\{Controller, CSRF, RateLimit, Lang};
use App\Config\Database;
use App\Services\CurrencyService;

class AdminController extends Controller
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ── Auth ──────────────────────────────────────────────

    public function showLogin(): void
    {
        if (!empty($_SESSION['admin_id'])) {
            $this->redirect(BASE_PATH . '/admin');
        }
        $this->view('admin/login', ['title' => 'Admin Login', 'layout' => 'admin_auth']);
    }

    public function login(): void
    {
        if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
            $this->abort(403);
        }

        $ip = $_SERVER['REMOTE_ADDR'];
        if (!RateLimit::check('admin_login', $ip, 5, 900)) {
            $this->view('admin/login', [
                'title'  => 'Admin Login',
                'layout' => 'admin_auth',
                'error'  => 'Too many attempts. Wait 15 minutes.',
            ]);
            return;
        }

        $email    = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        $stmt = $this->db->prepare(
            "SELECT * FROM admins WHERE email = ? AND is_active = 1 LIMIT 1"
        );
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if (!$admin || !password_verify($password, $admin['password_hash'])) {
            $this->view('admin/login', [
                'title'  => 'Admin Login',
                'layout' => 'admin_auth',
                'error'  => 'Invalid credentials.',
            ]);
            return;
        }

        session_regenerate_id(true);
        $_SESSION['admin_id']   = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['admin_role'] = $admin['role'];

        $this->db->prepare(
            "UPDATE admins SET last_login_at = NOW(), last_login_ip = ? WHERE id = ?"
        )->execute([$ip, $admin['id']]);

        $this->redirect(BASE_PATH . '/admin');
    }

    public function logout(): void
    {
        unset($_SESSION['admin_id'], $_SESSION['admin_name'], $_SESSION['admin_role']);
        $this->redirect(BASE_PATH . '/admin/login');
    }

    // ── Dashboard ─────────────────────────────────────────

    public function dashboard(): void
    {
        $currency = defined('CURRENCY') ? CURRENCY : 'EUR';

        $stats = [
            'total_users'       => $this->count('users'),
            'active_users'      => $this->count('users', "status = 'active'"),
            'active_performers' => $this->count('performers', "status = 'active'"),
            'online_performers' => $this->count('performers', "online_status = 1"),
            'pending_approval'  => $this->count('performers', "status = 'pending_approval'"),
            'calls_today'       => $this->count('calls', "DATE(created_at) = CURDATE()"),
            'calls_month'       => $this->count('calls', "MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())"),
            'revenue_today_zar' => $this->sum('transactions', 'amount_zar', "status='completed' AND DATE(created_at)=CURDATE()"),
            'revenue_month_zar' => $this->sum('transactions', 'amount_zar', "status='completed' AND MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW())"),
            'credits_in_system' => $this->sum('users', 'credit_balance'),
            'pending_payouts'   => $this->sum('performers', 'earnings_balance'),
        ];

        // Format revenue in display currency
        $stats['revenue_today_fmt'] = CurrencyService::format(
            CurrencyService::fromZAR($stats['revenue_today_zar'], $currency), $currency
        );
        $stats['revenue_month_fmt'] = CurrencyService::format(
            CurrencyService::fromZAR($stats['revenue_month_zar'], $currency), $currency
        );

        // Recent calls
        $recentCalls = $this->db->query("
            SELECT c.id, c.status, c.duration_seconds, c.credits_used, c.created_at,
                   u.username, p.display_name AS performer_name
            FROM calls c
            JOIN users u ON c.user_id = u.id
            JOIN performers p ON c.performer_id = p.id
            ORDER BY c.created_at DESC LIMIT 8
        ")->fetchAll();

        // Pending performer approvals
        $pendingPerformers = $this->db->query("
            SELECT id, display_name, slug, age, category, languages, rate_per_minute, created_at
            FROM performers WHERE status = 'pending_approval'
            ORDER BY created_at ASC LIMIT 10
        ")->fetchAll();

        // Recent transactions
        $recentTx = $this->db->query("
            SELECT t.id, t.amount_zar, t.status, t.created_at,
                   u.username, cp.name AS package_name
            FROM transactions t
            JOIN users u ON t.user_id = u.id
            LEFT JOIN credit_packages cp ON t.package_id = cp.id
            ORDER BY t.created_at DESC LIMIT 8
        ")->fetchAll();

        $this->view('admin/dashboard', [
            'title'             => 'Admin Dashboard',
            'layout'            => 'admin',
            'stats'             => $stats,
            'recentCalls'       => $recentCalls,
            'pendingPerformers' => $pendingPerformers,
            'recentTx'          => $recentTx,
            'currency'          => $currency,
            'proxyMode'         => $this->getSetting('admin_proxy_mode', '0') === '1',
        ]);
    }

    // ── Users ─────────────────────────────────────────────

    public function users(): void
    {
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $search = $_GET['q'] ?? '';
        $offset = ($page - 1) * 25;

        $where  = '1=1';
        $params = [];
        if ($search) {
            $where    = "(username LIKE ? OR email LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $stmt = $this->db->prepare(
            "SELECT id, uuid, username, email, credit_balance, status, email_verified, age_verified,
                    last_login_at, created_at
             FROM users WHERE {$where}
             ORDER BY created_at DESC LIMIT 25 OFFSET {$offset}"
        );
        $stmt->execute($params);
        $users = $stmt->fetchAll();

        $total = (int)$this->db->prepare("SELECT COUNT(*) FROM users WHERE {$where}")
                               ->execute($params) ? $this->db->query("SELECT FOUND_ROWS()")->fetchColumn() : 0;

        $this->view('admin/users', [
            'title'  => 'Users — Admin',
            'layout' => 'admin',
            'users'  => $users,
            'search' => $search,
            'page'   => $page,
        ]);
    }

    // ── Performers ────────────────────────────────────────

    public function performers(): void
    {
        $filter = $_GET['filter'] ?? 'all';
        $where  = match($filter) {
            'pending'  => "status = 'pending_approval'",
            'active'   => "status = 'active'",
            'suspended'=> "status = 'suspended'",
            default    => '1=1',
        };

        $performers = $this->db->query("
            SELECT id, display_name, slug, age, category, rate_per_minute,
                   status, online_status, rating_avg, total_calls, earnings_balance, created_at,
                   bio, phone_number, languages, profile_photo, cover_photo, short_video, voice_sample,
                   video_enabled, video_min_credits, video_min_minutes, video_rate_per_minute
            FROM performers WHERE {$where}
            ORDER BY created_at DESC LIMIT 100
        ")->fetchAll();

        $this->view('admin/performers', [
            'title'      => 'Performers — Admin',
            'layout'     => 'admin',
            'performers' => $performers,
            'filter'     => $filter,
        ]);
    }

    public function approvePerformer(string $id): void
    {
        if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
            $this->abort(403);
        }
        $id = (int)$id;
        $this->db->prepare("
            UPDATE performers
            SET status = 'active', approved_by = ?, approved_at = NOW()
            WHERE id = ? AND status = 'pending_approval'
        ")->execute([$_SESSION['admin_id'], $id]);

        $this->flashSuccess('Performer approved.');
        $this->redirect(BASE_PATH . '/admin/performers?filter=pending');
    }

    public function suspendPerformer(string $id): void
    {
        if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
            $this->abort(403);
        }
        $id = (int)$id;
        $this->db->prepare(
            "UPDATE performers SET status = 'suspended' WHERE id = ?"
        )->execute([$id]);

        $this->flashSuccess('Performer suspended.');
        $this->redirect(BASE_PATH . '/admin/performers');
    }

    // ── Transactions ──────────────────────────────────────

    public function transactions(): void
    {
        $currency = defined('CURRENCY') ? CURRENCY : 'EUR';

        $stmt = $this->db->query("
            SELECT t.*, u.username, cp.name AS package_name
            FROM transactions t
            JOIN users u ON t.user_id = u.id
            LEFT JOIN credit_packages cp ON t.package_id = cp.id
            ORDER BY t.created_at DESC LIMIT 100
        ");
        $transactions = $stmt->fetchAll();

        $this->view('admin/transactions', [
            'title'        => 'Transactions — Admin',
            'layout'       => 'admin',
            'transactions' => $transactions,
            'currency'     => $currency,
        ]);
    }

    // ── Calls ─────────────────────────────────────────────

    public function calls(): void
    {
        $stmt = $this->db->query("
            SELECT c.*, u.username, p.display_name AS performer_name
            FROM calls c
            JOIN users u ON c.user_id = u.id
            JOIN performers p ON c.performer_id = p.id
            ORDER BY c.created_at DESC LIMIT 100
        ");
        $calls = $stmt->fetchAll();

        $this->view('admin/calls', [
            'title'  => 'Calls — Admin',
            'layout' => 'admin',
            'calls'  => $calls,
        ]);
    }

    // ── Payouts ───────────────────────────────────────────

    public function payouts(): void
    {
        $stmt = $this->db->query("
            SELECT pp.*, p.display_name AS performer_name
            FROM performer_payouts pp
            JOIN performers p ON pp.performer_id = p.id
            ORDER BY pp.created_at DESC LIMIT 50
        ");
        $payouts = $stmt->fetchAll();

        // Performers with pending balance
        $pendingBalances = $this->db->query("
            SELECT id, display_name, earnings_balance
            FROM performers
            WHERE earnings_balance > 0
            ORDER BY earnings_balance DESC
        ")->fetchAll();

        $this->view('admin/payouts', [
            'title'           => 'Payouts — Admin',
            'layout'          => 'admin',
            'payouts'         => $payouts,
            'pendingBalances' => $pendingBalances,
        ]);
    }

    public function processPayout(): void
    {
        if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
            $this->abort(403);
        }

        $performerId = (int)($_POST['performer_id'] ?? 0);
        $amount      = (float)($_POST['amount'] ?? 0);
        $method      = $_POST['method'] ?? 'eft';
        $reference   = htmlspecialchars(trim($_POST['reference'] ?? ''));

        if ($performerId <= 0 || $amount <= 0) {
            $this->flashError('Invalid payout data.');
            $this->redirect(BASE_PATH . '/admin/payouts');
        }

        $this->db->beginTransaction();
        try {
            $this->db->prepare("
                INSERT INTO performer_payouts
                    (performer_id, amount, method, reference, status, period_start, period_end, processed_by, processed_at)
                VALUES
                    (?, ?, ?, ?, 'paid', CURDATE(), CURDATE(), ?, NOW())
            ")->execute([$performerId, $amount, $method, $reference, $_SESSION['admin_id']]);

            $this->db->prepare(
                "UPDATE performers SET earnings_balance = earnings_balance - ? WHERE id = ?"
            )->execute([$amount, $performerId]);

            $this->db->commit();
            $this->flashSuccess('Payout of R ' . number_format($amount, 2) . ' processed.');
        } catch (\Exception $e) {
            $this->db->rollBack();
            $this->flashError('Payout failed: ' . $e->getMessage());
        }

        $this->redirect(BASE_PATH . '/admin/payouts');
    }

    // ── Settings ──────────────────────────────────────────

    private function ensureCreditSettingsExist(): void
    {
        $keys = [
            'package_price_starter' => ['value' => '150.00', 'type' => 'string', 'desc' => 'Starter package price in ZAR'],
            'package_price_gentleman' => ['value' => '350.00', 'type' => 'string', 'desc' => 'Gentleman package price in ZAR'],
            'package_price_elite' => ['value' => '700.00', 'type' => 'string', 'desc' => 'Elite package price in ZAR'],
            'package_price_master' => ['value' => '1000.00', 'type' => 'string', 'desc' => 'Master package price in ZAR'],
            'package_price_vip' => ['value' => '2000.00', 'type' => 'string', 'desc' => 'VIP package price in ZAR'],
            'package_price_ultimate' => ['value' => '5000.00', 'type' => 'string', 'desc' => 'Ultimate package price in ZAR'],
            'package_credits_starter' => ['value' => '15.0000', 'type' => 'string', 'desc' => 'Starter package base credits'],
            'package_credits_gentleman' => ['value' => '40.0000', 'type' => 'string', 'desc' => 'Gentleman package base credits'],
            'package_credits_elite' => ['value' => '90.0000', 'type' => 'string', 'desc' => 'Elite package base credits'],
            'package_credits_master' => ['value' => '130.0000', 'type' => 'string', 'desc' => 'Master package base credits'],
            'package_credits_vip' => ['value' => '280.0000', 'type' => 'string', 'desc' => 'VIP package base credits'],
            'package_credits_ultimate' => ['value' => '750.0000', 'type' => 'string', 'desc' => 'Ultimate package base credits'],
            'package_bonus_starter' => ['value' => '0.0000', 'type' => 'string', 'desc' => 'Starter package bonus credits'],
            'package_bonus_gentleman' => ['value' => '5.0000', 'type' => 'string', 'desc' => 'Gentleman package bonus credits'],
            'package_bonus_elite' => ['value' => '15.0000', 'type' => 'string', 'desc' => 'Elite package bonus credits'],
            'package_bonus_master' => ['value' => '25.0000', 'type' => 'string', 'desc' => 'Master package bonus credits'],
            'package_bonus_vip' => ['value' => '50.0000', 'type' => 'string', 'desc' => 'VIP package bonus credits'],
            'package_bonus_ultimate' => ['value' => '150.0000', 'type' => 'string', 'desc' => 'Ultimate package bonus credits'],
        ];

        foreach ($keys as $key => $meta) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM settings WHERE `key` = ?");
            $stmt->execute([$key]);
            if ((int)$stmt->fetchColumn() === 0) {
                $this->db->prepare("
                    INSERT INTO settings (`key`, `value`, `type`, `description`)
                    VALUES (?, ?, ?, ?)
                ")->execute([$key, $meta['value'], $meta['type'], $meta['desc']]);

                // Also update credit_packages table to match
                if (preg_match('/^package_(price|credits|bonus)_(.*)$/', $key, $m)) {
                    $fieldMap = [
                        'price' => 'price_zar',
                        'credits' => 'credits',
                        'bonus' => 'bonus_credits'
                    ];
                    $dbField = $fieldMap[$m[1]];
                    $pkgName = $m[2];
                    $this->db->prepare("UPDATE credit_packages SET {$dbField} = ? WHERE LOWER(name) = ?")->execute([(float)$meta['value'], strtolower($pkgName)]);
                }
            }
        }
    }

    public function settings(): void
    {
        $this->ensureCreditSettingsExist();

        $stmt = $this->db->query("SELECT * FROM settings ORDER BY `key` ASC");
        $settingRows = $stmt->fetchAll();

        $lastSync = $this->db->query("
            SELECT MAX(updated_at) FROM settings
            WHERE `key` IN ('eur_to_zar', 'gbp_to_zar', 'usd_to_zar')
        ")->fetchColumn();

        $this->view('admin/settings', [
            'title'       => 'Settings — Admin',
            'layout'      => 'admin',
            'settingRows' => $settingRows,
            'lastSync'    => $lastSync ?: null,
        ]);
    }

    public function syncRates(): void
    {
        if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
            $this->abort(403);
        }

        $success = \App\Services\CurrencyService::fetchAndUpdateLiveRates();
        if ($success) {
            $this->flashSuccess('Exchange rates synchronized successfully.');
        } else {
            $this->flashError('Failed to sync exchange rates. Check error logs.');
        }

        $this->redirect(BASE_PATH . '/admin/settings');
    }

    public function saveSetting(): void
    {
        if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
            $this->abort(403);
        }

        $key   = preg_replace('/[^a-z0-9_]/', '', $_POST['key'] ?? '');
        $value = $_POST['value'] ?? '';

        if ($key) {
            $this->db->beginTransaction();
            try {
                if ($key === 'admin_proxy_mode') {
                    $this->updatePerformersForProxyMode($value);
                }
                if (preg_match('/^package_(price|credits|bonus)_(.*)$/', $key, $m)) {
                    $fieldMap = [
                        'price' => 'price_zar',
                        'credits' => 'credits',
                        'bonus' => 'bonus_credits'
                    ];
                    $dbField = $fieldMap[$m[1]];
                    $pkgName = $m[2];
                    $this->db->prepare("UPDATE credit_packages SET {$dbField} = ? WHERE LOWER(name) = ?")->execute([(float)$value, strtolower($pkgName)]);
                }
                $this->db->prepare(
                    "UPDATE settings SET `value` = ?, `updated_at` = NOW() WHERE `key` = ?"
                )->execute([$value, $key]);
                $this->db->commit();
            } catch (\Exception $e) {
                $this->db->rollBack();
                $this->flashError('Failed to save setting: ' . $e->getMessage());
                $this->redirect(BASE_PATH . '/admin/settings');
                return;
            }
        }

        $this->flashSuccess('Setting updated.');
        $this->redirect(BASE_PATH . '/admin/settings');
    }

    // ── User Status Management ──────────────────────────────────────────

    public function approveUser(string $id): void
    {
        if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
            $this->abort(403);
        }
        $id = (int)$id;
        $this->db->prepare("
            UPDATE users
            SET status = 'active', age_verified = 1, email_verified = 1, age_verified_at = NOW()
            WHERE id = ?
        ")->execute([$id]);

        $this->flashSuccess('User approved and verified.');
        $this->redirect(BASE_PATH . '/admin/users');
    }

    public function suspendUser(string $id): void
    {
        if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
            $this->abort(403);
        }
        $id = (int)$id;
        $this->db->prepare("
            UPDATE users SET status = 'suspended' WHERE id = ?
        ")->execute([$id]);

        $this->flashSuccess('User suspended.');
        $this->redirect(BASE_PATH . '/admin/users');
    }

    public function banUser(string $id): void
    {
        if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
            $this->abort(403);
        }
        $id = (int)$id;
        $this->db->prepare("
            UPDATE users SET status = 'banned' WHERE id = ?
        ")->execute([$id]);

        $this->flashSuccess('User banned.');
        $this->redirect(BASE_PATH . '/admin/users');
    }

    public function activateUser(string $id): void
    {
        if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
            $this->abort(403);
        }
        $id = (int)$id;
        $this->db->prepare("
            UPDATE users SET status = 'active' WHERE id = ?
        ")->execute([$id]);

        $this->flashSuccess('User account activated.');
        $this->redirect(BASE_PATH . '/admin/users');
    }

    public function promoteToAdmin(string $id): void
    {
        if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
            $this->abort(403);
        }
        // Only superadmins can promote users
        if (($_SESSION['admin_role'] ?? '') !== 'superadmin') {
            $this->flashError('Only superadmins can promote users to admin.');
            $this->redirect(BASE_PATH . '/admin/users');
        }

        $id = (int)$id;
        $role = $_POST['role'] ?? 'admin';
        if (!in_array($role, ['superadmin', 'admin', 'moderator', 'finance'])) {
            $role = 'admin';
        }

        // Fetch user info
        $stmt = $this->db->prepare("SELECT username, email, password_hash FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        if (!$user) {
            $this->flashError('User not found.');
            $this->redirect(BASE_PATH . '/admin/users');
        }

        // Check if admin with this email already exists
        $stmt = $this->db->prepare("SELECT id FROM admins WHERE email = ?");
        $stmt->execute([$user['email']]);
        $exists = $stmt->fetch();

        if ($exists) {
            $this->flashError('This user is already an administrator.');
            $this->redirect(BASE_PATH . '/admin/users');
        }

        // Insert into admins table
        $this->db->prepare("
            INSERT INTO admins (email, password_hash, name, role, is_active)
            VALUES (?, ?, ?, ?, 1)
        ")->execute([$user['email'], $user['password_hash'], $user['username'], $role]);

        $this->flashSuccess('User promoted to administrator successfully.');
        $this->redirect(BASE_PATH . '/admin/admins');
    }

    // ── Admin Accounts Management ──────────────────────────────────────────

    public function adminsList(): void
    {
        $stmt = $this->db->query("SELECT * FROM admins ORDER BY created_at DESC");
        $admins = $stmt->fetchAll();

        $this->view('admin/admins', [
            'title'  => 'Admin Accounts — Admin',
            'layout' => 'admin',
            'admins' => $admins,
        ]);
    }

    public function updateAdminRole(string $id): void
    {
        if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
            $this->abort(403);
        }
        if (($_SESSION['admin_role'] ?? '') !== 'superadmin') {
            $this->flashError('Only superadmins can change admin roles.');
            $this->redirect(BASE_PATH . '/admin/admins');
        }

        $id = (int)$id;
        $role = $_POST['role'] ?? 'admin';
        if (!in_array($role, ['superadmin', 'admin', 'moderator', 'finance'])) {
            $this->abort(400);
        }

        // Prevent self-demotion from superadmin if they are the only one
        if ($id === (int)$_SESSION['admin_id'] && $role !== 'superadmin') {
            $count = (int)$this->db->query("SELECT COUNT(*) FROM admins WHERE role = 'superadmin' AND is_active = 1")->fetchColumn();
            if ($count <= 1) {
                $this->flashError('Cannot demote the only active superadmin.');
                $this->redirect(BASE_PATH . '/admin/admins');
            }
        }

        $this->db->prepare("UPDATE admins SET role = ? WHERE id = ?")->execute([$role, $id]);

        $this->flashSuccess('Admin role updated.');
        $this->redirect(BASE_PATH . '/admin/admins');
    }

    public function toggleAdminActive(string $id): void
    {
        if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
            $this->abort(403);
        }
        if (($_SESSION['admin_role'] ?? '') !== 'superadmin') {
            $this->flashError('Only superadmins can toggle admin status.');
            $this->redirect(BASE_PATH . '/admin/admins');
        }

        $id = (int)$id;

        // Prevent deactivating oneself
        if ($id === (int)$_SESSION['admin_id']) {
            $this->flashError('You cannot deactivate your own administrative account.');
            $this->redirect(BASE_PATH . '/admin/admins');
        }

        $stmt = $this->db->prepare("SELECT is_active FROM admins WHERE id = ?");
        $stmt->execute([$id]);
        $admin = $stmt->fetch();

        if ($admin) {
            $newStatus = $admin['is_active'] ? 0 : 1;
            $this->db->prepare("UPDATE admins SET is_active = ? WHERE id = ?")->execute([$newStatus, $id]);
            $this->flashSuccess($newStatus ? 'Admin account activated.' : 'Admin account deactivated.');
        } else {
            $this->flashError('Admin account not found.');
        }

        $this->redirect(BASE_PATH . '/admin/admins');
    }

    public function addPerformer(): void
    {
        if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
            $this->abort(403);
        }

        $username      = trim($_POST['username'] ?? '');
        $email         = trim($_POST['email'] ?? '');
        $password      = trim($_POST['password'] ?? '');
        $displayName   = trim($_POST['display_name'] ?? '');
        $age           = (int)($_POST['age'] ?? 0);
        $phoneNumber   = trim($_POST['phone_number'] ?? '');
        $ratePerMinute = (float)($_POST['rate_per_minute'] ?? 1.00);
        $languages     = trim($_POST['languages'] ?? 'English');
        $category      = $_POST['category'] ?? 'chat';

        $videoEnabled       = isset($_POST['video_enabled']) ? 1 : 0;
        $videoMinCredits    = (float)($_POST['video_min_credits'] ?? 15.00);
        $videoMinMinutes    = (int)($_POST['video_min_minutes'] ?? 10);
        $videoRatePerMinute = (float)($_POST['video_rate_per_minute'] ?? 5.00);

        if (!$username || !$email || !$password || !$displayName || $age <= 0 || !$phoneNumber) {
            $this->flashError('All fields are required.');
            $this->redirect(BASE_PATH . '/admin/performers');
            return;
        }

        if (strlen($password) < 10) {
            $this->flashError('Password must be at least 10 characters.');
            $this->redirect(BASE_PATH . '/admin/performers');
            return;
        }

        $this->db->beginTransaction();
        try {
            // Check if username/email already exists
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ((int)$stmt->fetchColumn() > 0) {
                throw new \Exception('Username or Email already exists.');
            }

            // Create User account
            $userUuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', 
                mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), 
                mt_rand(16384, 20479), mt_rand(32768, 49151), 
                mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)
            );
            $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            
            $stmt = $this->db->prepare("
                INSERT INTO users (uuid, username, email, password_hash, status, email_verified, age_verified)
                VALUES (?, ?, ?, ?, 'active', 1, 1)
            ");
            $stmt->execute([$userUuid, $username, $email, $hash]);
            $userId = $this->db->lastInsertId();

            // Create Performer Profile
            $slugBase = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $displayName), '-'));
            $slug = $slugBase;
            $i = 1;
            while (true) {
                $check = $this->db->prepare("SELECT COUNT(*) FROM performers WHERE slug = ?");
                $check->execute([$slug]);
                if ((int)$check->fetchColumn() === 0) break;
                $slug = $slugBase . '-' . $i++;
            }

            $performerUuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', 
                mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), 
                mt_rand(16384, 20479), mt_rand(32768, 49151), 
                mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)
            );

            $stmt = $this->db->prepare("
                INSERT INTO performers (uuid, user_id, display_name, slug, age, phone_number, phone_verified, rate_per_minute, video_enabled, video_min_credits, video_min_minutes, video_rate_per_minute, status, online_status, category, languages)
                VALUES (?, ?, ?, ?, ?, ?, 1, ?, ?, ?, ?, ?, 'active', 0, ?, ?)
            ");
            $stmt->execute([
                $performerUuid,
                $userId,
                $displayName,
                $slug,
                $age,
                $phoneNumber,
                $ratePerMinute,
                $videoEnabled,
                $videoMinCredits,
                $videoMinMinutes,
                $videoRatePerMinute,
                $category,
                $languages
            ]);
            $performerId = $this->db->lastInsertId();

            // Process photo if present
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
                $paths = \App\Services\FileUpload::savePerformerPhoto($_FILES['photo'], $performerId);
                $update = $this->db->prepare("UPDATE performers SET profile_photo = ? WHERE id = ?");
                $update->execute([$paths['path'], $performerId]);
            }

            $this->db->commit();
            $this->flashSuccess('Performer and linked user account created successfully.');
        } catch (\Exception $e) {
            $this->db->rollBack();
            $this->flashError($e->getMessage());
        }

        $this->redirect(BASE_PATH . '/admin/performers');
    }

    public function editPerformer(string $id): void
    {
        if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
            $this->abort(403);
        }

        $id = (int)$id;
        $displayName   = trim($_POST['display_name'] ?? '');
        $age           = (int)($_POST['age'] ?? 0);
        $phoneNumber   = trim($_POST['phone_number'] ?? '');
        $ratePerMinute = (float)($_POST['rate_per_minute'] ?? 1.00);
        $languages     = trim($_POST['languages'] ?? 'English');
        $category      = $_POST['category'] ?? 'chat';
        $bio           = trim($_POST['bio'] ?? '');

        $videoEnabled       = isset($_POST['video_enabled']) ? 1 : 0;
        $videoMinCredits    = (float)($_POST['video_min_credits'] ?? 15.00);
        $videoMinMinutes    = (int)($_POST['video_min_minutes'] ?? 10);
        $videoRatePerMinute = (float)($_POST['video_rate_per_minute'] ?? 5.00);

        if (!$displayName || $age <= 0 || !$phoneNumber) {
            $this->flashError('Display Name, Age, and Phone Number are required.');
            $this->redirect(BASE_PATH . '/admin/performers');
        }

        $this->db->beginTransaction();
        try {
            // Update text fields
            $stmt = $this->db->prepare("
                UPDATE performers
                SET display_name = ?, age = ?, phone_number = ?, rate_per_minute = ?, languages = ?, category = ?, bio = ?, 
                    video_enabled = ?, video_min_credits = ?, video_min_minutes = ?, video_rate_per_minute = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([
                $displayName, 
                $age, 
                $phoneNumber, 
                $ratePerMinute, 
                $languages, 
                $category, 
                $bio, 
                $videoEnabled,
                $videoMinCredits,
                $videoMinMinutes,
                $videoRatePerMinute,
                $id
            ]);

            // Handle Profile Photo if present
            if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] !== UPLOAD_ERR_NO_FILE) {
                $paths = \App\Services\FileUpload::savePerformerPhoto($_FILES['profile_photo'], $id);
                $update = $this->db->prepare("UPDATE performers SET profile_photo = ? WHERE id = ?");
                $update->execute([$paths['path'], $id]);
            }

            // Handle Cover Photo if present
            if (isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] !== UPLOAD_ERR_NO_FILE) {
                $paths = \App\Services\FileUpload::savePerformerPhoto($_FILES['cover_photo'], $id);
                $update = $this->db->prepare("UPDATE performers SET cover_photo = ? WHERE id = ?");
                $update->execute([$paths['path'], $id]);
            }

            // Handle Short Video if present
            if (isset($_FILES['short_video']) && $_FILES['short_video']['error'] !== UPLOAD_ERR_NO_FILE) {
                // Get old video path to delete it
                $oldVidStmt = $this->db->prepare("SELECT short_video FROM performers WHERE id = ?");
                $oldVidStmt->execute([$id]);
                $oldVideoPath = $oldVidStmt->fetchColumn();

                $videoPath = \App\Services\FileUpload::savePerformerVideo($_FILES['short_video'], $id);
                $update = $this->db->prepare("UPDATE performers SET short_video = ? WHERE id = ?");
                $update->execute([$videoPath, $id]);

                if ($oldVideoPath && file_exists(PUBLIC_PATH . '/' . $oldVideoPath)) {
                    @unlink(PUBLIC_PATH . '/' . $oldVideoPath);
                }
            }

            // Handle Voice Sample if present
            if (isset($_FILES['voice_sample']) && $_FILES['voice_sample']['error'] !== UPLOAD_ERR_NO_FILE) {
                // Get old voice path to delete it
                $oldVoiceStmt = $this->db->prepare("SELECT voice_sample FROM performers WHERE id = ?");
                $oldVoiceStmt->execute([$id]);
                $oldVoicePath = $oldVoiceStmt->fetchColumn();

                $voicePath = \App\Services\FileUpload::savePerformerVoice($_FILES['voice_sample'], $id);
                $update = $this->db->prepare("UPDATE performers SET voice_sample = ? WHERE id = ?");
                $update->execute([$voicePath, $id]);

                if ($oldVoicePath && file_exists(PUBLIC_PATH . '/' . $oldVoicePath)) {
                    @unlink(PUBLIC_PATH . '/' . $oldVoicePath);
                }
            }

            $this->db->commit();
            $this->flashSuccess('Performer profile updated successfully.');
        } catch (\Exception $e) {
            $this->db->rollBack();
            $this->flashError($e->getMessage());
        }

        $this->redirect(BASE_PATH . '/admin/performers');
    }

    public function deletePerformerMedia(string $id): void
    {
        if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
            $this->abort(403);
        }

        $id = (int)$id;
        $mediaType = $_POST['media_type'] ?? '';

        if (!in_array($mediaType, ['profile_photo', 'cover_photo', 'short_video', 'voice_sample'])) {
            $this->flashError('Invalid media type.');
            $this->redirect(BASE_PATH . '/admin/performers');
            return;
        }

        $stmt = $this->db->prepare("SELECT profile_photo, cover_photo, short_video, voice_sample FROM performers WHERE id = ?");
        $stmt->execute([$id]);
        $performer = $stmt->fetch();

        if (!$performer) {
            $this->flashError('Performer not found.');
            $this->redirect(BASE_PATH . '/admin/performers');
            return;
        }

        $oldPath = $performer[$mediaType];
        if ($oldPath) {
            // Update database
            $up = $this->db->prepare("UPDATE performers SET {$mediaType} = NULL, updated_at = NOW() WHERE id = ?");
            $up->execute([$id]);

            // Delete file
            if (file_exists(PUBLIC_PATH . '/' . $oldPath)) {
                @unlink(PUBLIC_PATH . '/' . $oldPath);
            }

            // Delete thumbnail for profile photo
            if ($mediaType === 'profile_photo') {
                $thumbPath = str_replace('uploads/performers/' . $id . '/', 'uploads/performers/' . $id . '/thumb_', $oldPath);
                if (file_exists(PUBLIC_PATH . '/' . $thumbPath)) {
                    @unlink(PUBLIC_PATH . '/' . $thumbPath);
                }
            }

            $this->flashSuccess(ucwords(str_replace('_', ' ', $mediaType)) . ' deleted successfully.');
        } else {
            $this->flashError('No file exists to delete.');
        }

        $this->redirect(BASE_PATH . '/admin/performers');
    }

    // ── Helpers ───────────────────────────────────────────

    private function count(string $table, string $where = '1=1'): int
    {
        return (int)$this->db->query(
            "SELECT COUNT(*) FROM `{$table}` WHERE {$where}"
        )->fetchColumn();
    }

    private function sum(string $table, string $col, string $where = '1=1'): float
    {
        return (float)$this->db->query(
            "SELECT COALESCE(SUM(`{$col}`),0) FROM `{$table}` WHERE {$where}"
        )->fetchColumn();
    }

    // ── Admin Proxy Mode ──────────────────────────────────

    public function adminCallPage(): void
    {
        if (empty($_SESSION['admin_id'])) {
            $this->redirect(BASE_PATH . '/admin/login');
            return;
        }

        $proxyMode = $this->getSetting('admin_proxy_mode', '0') === '1';

        $this->view('admin/admin-call', [
            'title' => 'Incoming Calls Portal',
            'layout' => 'admin',
            'proxyMode' => $proxyMode,
        ]);
    }

    public function pendingCalls(): void
    {
        header('Content-Type: application/json');
        if (empty($_SESSION['admin_id'])) {
            echo json_encode([]);
            exit;
        }

        $stmt = $this->db->query("
            SELECT c.uuid, c.created_at, u.username, p.display_name AS performer_name
            FROM calls c
            JOIN users u ON c.user_id = u.id
            JOIN performers p ON c.performer_id = p.id
            WHERE c.status = 'ringing'
            ORDER BY c.created_at DESC
        ");
        $calls = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($calls as &$call) {
            $call['wait_seconds'] = time() - strtotime($call['created_at']);
        }

        echo json_encode($calls);
        exit;
    }

    public function answerCall(string $uuid): void
    {
        header('Content-Type: application/json');
        if (empty($_SESSION['admin_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $stmt = $this->db->prepare("SELECT * FROM calls WHERE uuid = ? LIMIT 1");
        $stmt->execute([$uuid]);
        $call = $stmt->fetch();

        if (!$call) {
            echo json_encode(['success' => false, 'message' => 'Call not found']);
            exit;
        }

        $this->db->prepare("
            UPDATE calls
            SET status = 'in_progress', answered_at = NOW()
            WHERE id = ?
        ")->execute([$call['id']]);

        echo json_encode([
            'success' => true,
            'room_url' => BASE_PATH . '/admin/call/room/' . $uuid
        ]);
        exit;
    }

    public function callRoom(string $uuid): void
    {
        if (empty($_SESSION['admin_id'])) {
            $this->redirect(BASE_PATH . '/admin/login');
            return;
        }

        $stmt = $this->db->prepare("SELECT * FROM calls WHERE uuid = ? LIMIT 1");
        $stmt->execute([$uuid]);
        $call = $stmt->fetch();

        if (!$call) {
            $this->abort(404, 'Call session not found.');
        }

        $liveKit = new \App\Services\LiveKitService();
        $identity = 'admin_' . $_SESSION['admin_id'];
        $displayName = 'Performer';
        $token = $liveKit->generateToken($uuid, $identity, $displayName);

        $stmt = $this->db->prepare("SELECT * FROM performers WHERE id = ? LIMIT 1");
        $stmt->execute([$call['performer_id']]);
        $performer = $stmt->fetch();

        $this->view('calls/room', [
            'title'        => $call['type'] === 'video' ? 'Video Call Room (Proxy Mode)' : 'Voice Call Room (Proxy Mode)',
            'call'         => $call,
            'performer'    => $performer,
            'livekitToken' => $token,
            'livekitUrl'   => LIVEKIT_URL,
            'isPerformer'  => true,
            'isAdmin'      => true,
        ]);
    }

    public function toggleProxyMode(): void
    {
        header('Content-Type: application/json');
        if (empty($_SESSION['admin_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $current = $this->getSetting('admin_proxy_mode', '0');
        $newVal = $current === '1' ? '0' : '1';

        $this->db->beginTransaction();
        try {
            $this->updatePerformersForProxyMode($newVal);
            $this->setSetting('admin_proxy_mode', $newVal, 'boolean', 'Admin answers all incoming performer calls');
            $this->db->commit();

            echo json_encode(['success' => true, 'proxy_mode' => (int)$newVal]);
        } catch (\Exception $e) {
            $this->db->rollBack();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    private function updatePerformersForProxyMode(string $newVal): void
    {
        if ($newVal === '1') {
            $stmt = $this->db->query("SELECT id FROM performers WHERE status = 'active' AND online_status = 0");
            $ids = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            if (!empty($ids)) {
                $idsStr = implode(',', $ids);
                $this->db->query("UPDATE performers SET online_status = 1 WHERE id IN ($idsStr)");
                $this->setSetting('admin_proxy_forced_online', $idsStr);
            } else {
                $this->setSetting('admin_proxy_forced_online', '');
            }
        } else {
            $idsStr = $this->getSetting('admin_proxy_forced_online', '');
            if (!empty($idsStr)) {
                $ids = array_filter(array_map('intval', explode(',', $idsStr)));
                if (!empty($ids)) {
                    $placeholders = implode(',', $ids);
                    $this->db->query("UPDATE performers SET online_status = 0 WHERE id IN ($placeholders)");
                }
            }
            $this->setSetting('admin_proxy_forced_online', '');
        }
    }

    private function getSetting(string $key, string $default = ''): string
    {
        $stmt = $this->db->prepare("SELECT `value` FROM settings WHERE `key` = ? LIMIT 1");
        $stmt->execute([$key]);
        $val = $stmt->fetchColumn();
        return $val !== false ? $val : $default;
    }

    private function setSetting(string $key, string $value, string $type = 'string', string $desc = ''): void
    {
        $this->db->prepare("
            INSERT INTO settings (`key`, `value`, `type`, `description`)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = NOW()
        ")->execute([$key, $value, $type, $desc]);
    }
}

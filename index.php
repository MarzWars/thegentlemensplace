<?php
declare(strict_types=1);

// ── Bootstrap ────────────────────────────────────────────
define('APP_ROOT',   __DIR__ . '/app');
define('PUBLIC_PATH', __DIR__);
define('BASE_URL',   ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    || (!empty($_SERVER['HTTP_CF_VISITOR']) && strpos($_SERVER['HTTP_CF_VISITOR'], '"scheme":"https"') !== false))
    ? 'https://' . $_SERVER['HTTP_HOST']
    : 'http://'  . $_SERVER['HTTP_HOST']);

// BASE_PATH: sub-directory prefix (e.g. '/public_html' on localhost, '' on production)
define('BASE_PATH', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));

// ── Core requires ─────────────────────────────────────────
require_once APP_ROOT . '/Config/config.php';
require_once APP_ROOT . '/Config/database.php';
require_once APP_ROOT . '/Core/csrf.php';
require_once APP_ROOT . '/Core/ratelimit.php';
require_once APP_ROOT . '/Core/validator.php';
require_once APP_ROOT . '/Core/middleware.php';
require_once APP_ROOT . '/Core/lang.php';
require_once APP_ROOT . '/Core/router.php';
require_once APP_ROOT . '/Core/controller.php';
require_once APP_ROOT . '/Core/view.php';

// ── Model requires ────────────────────────────────────────
require_once APP_ROOT . '/Models/user.php';
require_once APP_ROOT . '/Models/performer.php';
require_once APP_ROOT . '/Models/call.php';
require_once APP_ROOT . '/Models/calllink.php';
require_once APP_ROOT . '/Models/stream.php';

// ── Service requires ──────────────────────────────────────
require_once APP_ROOT . '/Services/mailer.php';
require_once APP_ROOT . '/Services/creditservice.php';
require_once APP_ROOT . '/Services/currencyservice.php';
require_once APP_ROOT . '/Services/payfastservice.php';
require_once APP_ROOT . '/Services/telephonyservice.php';
require_once APP_ROOT . '/Services/fileupload.php';
require_once APP_ROOT . '/Services/livekitservice.php';
require_once APP_ROOT . '/Services/pusherservice.php';

// ── Controller requires ───────────────────────────────────
require_once APP_ROOT . '/Controllers/authcontroller.php';
require_once APP_ROOT . '/Controllers/homecontroller.php';
require_once APP_ROOT . '/Controllers/profilecontroller.php';
require_once APP_ROOT . '/Controllers/performercontroller.php';
require_once APP_ROOT . '/Controllers/creditcontroller.php';
require_once APP_ROOT . '/Controllers/paymentcontroller.php';
require_once APP_ROOT . '/Controllers/admincontroller.php';
require_once APP_ROOT . '/Controllers/callcontroller.php';
require_once APP_ROOT . '/Controllers/billingcontroller.php';
require_once APP_ROOT . '/Controllers/performerdashcontroller.php';
require_once APP_ROOT . '/Controllers/streamcontroller.php';
require_once APP_ROOT . '/Controllers/sitemapcontroller.php';

// ── Session ───────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// ── Maintenance Mode Check ────────────────────────────────
try {
    $db = \App\Config\Database::getInstance();
    $stmt = $db->query("SELECT `value` FROM settings WHERE `key` = 'maintenance_mode' LIMIT 1");
    $maintenance = $stmt->fetchColumn() === '1';
    define('MAINTENANCE_MODE', $maintenance);
} catch (\Exception $e) {
    define('MAINTENANCE_MODE', false);
}
if (!defined('MAINTENANCE_MODE')) {
    define('MAINTENANCE_MODE', false);
}

// ── Locale resolution ─────────────────────────────────────
// Raw URI relative to BASE_PATH (strips the sub-directory prefix)
$rawUri = $_GET['url'] ?? '/';

// Explicit Language Override Handler
if (!empty($_GET['set_lang'])) {
    $setLang = $_GET['set_lang'];
    if (array_key_exists($setLang, \App\Core\Lang::$supported)) {
        \App\Core\Lang::setPreference($setLang);
    }
    
    // Redirect to clean URL
    $queryParams = $_GET;
    unset($queryParams['set_lang']);
    unset($queryParams['url']);
    
    $cleanUri = '/' . ltrim($_GET['url'] ?? '', '/');
    $queryString = http_build_query($queryParams);
    $redirectUrl = BASE_URL . BASE_PATH . $cleanUri . ($queryString !== '' ? '?' . $queryString : '');
    
    header("Location: " . $redirectUrl, true, 302);
    exit;
}

// Resolve locale from the first URI segment (e.g. 'fr', 'de', '')
$resolved = \App\Core\Lang::resolveFromUri($rawUri);
\App\Core\Lang::init($resolved['locale']);

// Store locale in session so controllers/views can read it
$_SESSION['locale'] = \App\Core\Lang::locale();

// ── Language Auto-Detection and Redirection ───────────────
$routeUriClean = trim($resolved['remainder'] ?? '', '/');
$isAdminRoute = ($routeUriClean === 'admin' || str_starts_with($routeUriClean, 'admin/'));
$isWebhook = ($routeUriClean === 'payment/notify' || str_starts_with($routeUriClean, 'webhook/'));
$isSitemap = ($routeUriClean === 'sitemap.xml');

if (!$isAdminRoute && !$isWebhook && !$isSitemap) {
    // Check if the URL has a language prefix
    $parts = explode('/', ltrim($rawUri, '/'), 2);
    $firstSegment = $parts[0] ?? '';
    $hasPrefix = array_key_exists($firstSegment, \App\Core\Lang::$supported);

    if (!$hasPrefix) {
        $detectedLang = \App\Core\Lang::detectLanguage();
        \App\Core\Lang::setPreference($detectedLang);

        if ($detectedLang !== 'en') {
            $redirectPath = '/' . $detectedLang . '/' . ltrim($rawUri, '/');
            header("Location: " . BASE_URL . BASE_PATH . $redirectPath, true, 302);
            exit;
        }
    }
}

if (MAINTENANCE_MODE) {
    $routeUriClean = trim($resolved['remainder'] ?? '', '/');
    $isAdminRoute = ($routeUriClean === 'admin' || str_starts_with($routeUriClean, 'admin/'));
    $isAdminLoggedIn = !empty($_SESSION['admin_id']);

    // Allow critical webhooks to bypass maintenance
    $isWebhook = ($routeUriClean === 'payment/notify' || str_starts_with($routeUriClean, 'webhook/'));

    // Allow login, logout, and account pages for logged-in clients/performers
    $isAuthRoute = ($routeUriClean === 'login' || $routeUriClean === 'logout');
    $isAccountRoute = ($routeUriClean === 'account' || str_starts_with($routeUriClean, 'account/'));
    $isUserLoggedIn = !empty($_SESSION['user_id']) || !empty($_SESSION['performer_id']);

    $allowed = $isAdminLoggedIn || $isAdminRoute || $isWebhook || $isSitemap || $isAuthRoute || ($isAccountRoute && $isUserLoggedIn);

    if (!$allowed) {
        http_response_code(503);
        header('Retry-After: 3600');
        require APP_ROOT . '/Views/errors/503.php';
        exit;
    }
}

// ── Currency detection ────────────────────────────────────
// Allow ?currency=EUR|GBP|USD|ZAR to override
if (!empty($_GET['currency']) && isset(\App\Services\CurrencyService::CURRENCIES[$_GET['currency']])) {
    \App\Services\CurrencyService::setCurrency($_GET['currency']);
}
// Make current currency available globally
define('CURRENCY', \App\Services\CurrencyService::detectCurrency());

// The remainder is the actual route to dispatch
$routeUri = $resolved['remainder'];

// ── Route ─────────────────────────────────────────────────
$router = new \App\Core\Router();
$router->dispatch($routeUri);

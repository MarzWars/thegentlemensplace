<?php
// app/Config/config.php
// ── Environment ───────────────────────────────────────────
// Change APP_ENV to 'production' on the live server.
// On localhost: errors are shown, sandbox is on.
// On production: errors are hidden, sandbox must be off.

define('APP_ENV', 'production'); // 'development' | 'production'

// ── Error reporting ───────────────────────────────────────
if (APP_ENV === 'development') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    error_reporting(E_ALL);
}

// ── Database ──────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_NAME', 'production');
define('DB_USER', 'production');
define('DB_PASS', 'Horn100500!');          // Set a strong password on production

// ── App ───────────────────────────────────────────────────
define('APP_KEY', 'base64:fHhMNG0zMkpDdnM4V1lxaEZBNlE5dmFjRUtVd1p1eTI5TkhPYnkwOTIzWT0=');


// ── PayFast ───────────────────────────────────────────────
// Sandbox credentials (safe to use during testing)
define('PAYFAST_MERCHANT_ID',  '34662261');
define('PAYFAST_MERCHANT_KEY', 'itrf3cffp0kis');
define('PAYFAST_PASSPHRASE',   '');           // Leave empty if not set in PayFast dashboard
define('PAYFAST_SANDBOX',      false);         // Set to FALSE on production with real credentials

// ── Telephony ─────────────────────────────────────────────
define('TELEPHONY_PROVIDER',  'twilio');
define('TWILIO_ACCOUNT_SID',  'ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
define('TWILIO_AUTH_TOKEN',   'your_auth_token');
define('TWILIO_PHONE_NUMBER', '+15551234567');

// ── LiveKit ───────────────────────────────────────────────
define('LIVEKIT_URL',         'wss://chatline-5hw2qdlp.livekit.cloud');
define('LIVEKIT_API_KEY',     'APIQobgomaMNG5A');
define('LIVEKIT_API_SECRET',  '5KHJRTmotQZv1uUs29VIcJcpp62NJjAAXYYev9LNlzY');

// ── Pusher Beams ──────────────────────────────────────────
define('PUSHER_BEAMS_INSTANCE_ID',  'f69936f5-f652-4ac6-8f02-4d2a048ca15f');
define('PUSHER_BEAMS_SECRET_KEY',   'AD48170293466522E631ED7E68C77126DE4591C63FEE3A751BB810397D07DB8A');

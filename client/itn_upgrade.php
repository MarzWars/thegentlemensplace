<?php
require_once __DIR__ . '/config.php';

// ITN is server-to-server, so no session or login checks

$db = getDb();
if (!$db) {
    error_log("[ITN Upgrade] Could not connect to DB.");
    http_response_code(500);
    exit;
}

if (!class_exists('App\Services\PayFastService')) {
    error_log("[ITN Upgrade] PayFastService not found.");
    http_response_code(500);
    exit;
}

$pf = new \App\Services\PayFastService();

// Verify the ITN request
$isValid = $pf->verifyItn($_POST, $_SERVER['REMOTE_ADDR']);

if (!$isValid) {
    error_log("[ITN Upgrade] Validation failed for: " . json_encode($_POST));
    http_response_code(400);
    exit;
}

// Valid request, check payment status
if (isset($_POST['payment_status']) && $_POST['payment_status'] === 'COMPLETE') {
    $userId = (int)($_POST['custom_str1'] ?? 0);
    $amount = (float)($_POST['amount_gross'] ?? 0);

    if ($userId > 0) {
        $stmt = $db->prepare("UPDATE performers SET tier = 'premium' WHERE user_id = ?");
        $stmt->execute([$userId]);
        
        error_log("[ITN Upgrade] Upgraded user_id {$userId} to premium tier. Amount paid: {$amount}");
    } else {
        error_log("[ITN Upgrade] No valid user_id provided in custom_str1.");
    }
} else {
    error_log("[ITN Upgrade] Payment not COMPLETE. Status: " . ($_POST['payment_status'] ?? 'unknown'));
}

// Always respond with 200 OK so PayFast knows we received it
http_response_code(200);
echo "OK";

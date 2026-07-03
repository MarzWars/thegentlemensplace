<?php
/**
 * ╔══════════════════════════════════════════════════════╗
 * ║  THE GENTLEMAN'S PLACE — Admin Setup Script          ║
 * ║                                                      ║
 * ║  Run this ONCE to create the first superadmin.       ║
 * ║  DELETE THIS FILE immediately after use.             ║
 * ║                                                      ║
 * ║  URL: http://localhost/public_html/admin-setup.php   ║
 * ╚══════════════════════════════════════════════════════╝
 */

// ── Safety check — refuse to run on production ───────────
$host = $_SERVER['HTTP_HOST'] ?? '';
if (!in_array($host, ['localhost', '127.0.0.1']) && strpos($host, '.local') === false) {
    http_response_code(403);
    die('This setup script can only run on localhost. Delete it before deploying.');
}

define('APP_ROOT',   __DIR__ . '/app');
define('PUBLIC_PATH', __DIR__);
define('BASE_URL',   'http://localhost');
define('BASE_PATH',  '/public_html');
define('CURRENCY',   'EUR');

require_once APP_ROOT . '/Config/config.php';
require_once APP_ROOT . '/Config/database.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm']  ?? '');
    $secret   = trim($_POST['secret']   ?? '');

    // Simple secret to prevent accidental use
    if ($secret !== '@GameHack100!') {
        $error = 'Wrong setup secret.';
    } elseif (!$name || !$email || !$password) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } elseif (strlen($password) < 10) {
        $error = 'Password must be at least 10 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $db = \App\Config\Database::getInstance();

            // Check if any admin already exists
            $count = (int)$db->query("SELECT COUNT(*) FROM admins")->fetchColumn();
            if ($count > 0) {
                $error = 'An admin already exists. Delete this file immediately.';
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                $db->prepare("
                    INSERT INTO admins (email, password_hash, name, role, is_active)
                    VALUES (?, ?, ?, 'superadmin', 1)
                ")->execute([$email, $hash, $name]);

                $success = "Superadmin created successfully. DELETE THIS FILE NOW.";
            }
        } catch (\Exception $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Setup — The Gentleman's Place</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { background: #0a0805; color: #c4b896; font-family: Georgia, serif;
           display: flex; align-items: center; justify-content: center;
           min-height: 100vh; padding: 2rem; }
    .card { background: #111008; border: 1px solid rgba(201,168,76,.2);
            padding: 2.5rem; width: 100%; max-width: 460px; }
    h1 { font-size: 1.4rem; color: #f0e8d0; margin-bottom: .4rem; }
    .sub { font-size: .75rem; color: rgba(196,184,150,.4); letter-spacing: .1em;
           margin-bottom: 2rem; }
    .warn { background: rgba(224,144,64,.12); border: 1px solid rgba(224,144,64,.3);
            color: #e09040; padding: .85rem 1rem; font-size: .78rem;
            margin-bottom: 1.5rem; line-height: 1.5; }
    .error   { background: rgba(107,26,42,.4); border: 1px solid rgba(160,36,60,.4);
               color: #f0b0b8; padding: .85rem 1rem; font-size: .78rem; margin-bottom: 1.5rem; }
    .success { background: rgba(45,106,79,.3); border: 1px solid rgba(82,183,136,.4);
               color: #a8e6c8; padding: .85rem 1rem; font-size: .78rem; margin-bottom: 1.5rem; }
    .group { margin-bottom: 1.1rem; }
    label { display: block; font-size: .65rem; letter-spacing: .18em;
            text-transform: uppercase; color: #c4b896; margin-bottom: .4rem; }
    input { width: 100%; background: rgba(201,168,76,.04); border: 1px solid rgba(201,168,76,.15);
            color: #f0e8d0; font-family: sans-serif; font-size: .85rem;
            padding: .75rem 1rem; outline: none; }
    input:focus { border-color: rgba(201,168,76,.45); }
    button { width: 100%; background: #c9a84c; color: #0a0805; border: none;
             font-family: sans-serif; font-size: .7rem; font-weight: 700;
             letter-spacing: .2em; text-transform: uppercase;
             padding: .9rem; cursor: pointer; margin-top: .5rem; }
    button:hover { background: #e0c06a; }
    .note { font-size: .65rem; color: rgba(196,184,150,.3); margin-top: 1.5rem;
            line-height: 1.6; text-align: center; }
    a { color: #9a7a30; }
  </style>
</head>
<body>
<div class="card">
  <h1>Admin Setup</h1>
  <p class="sub">One-time superadmin creation</p>

  <div class="warn">
    ⚠ This script creates the first admin account. Run it once, then
    <strong>delete this file immediately</strong>. Never deploy it to production.
  </div>

  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="success">
      <?= htmlspecialchars($success) ?><br><br>
      <strong>Next steps:</strong><br>
      1. Delete <code>admin-setup.php</code> now<br>
      2. Go to <a href="<?= BASE_PATH ?>/admin/login">Admin Login</a>
    </div>
  <?php else: ?>
  <form method="POST">
    <div class="group">
      <label>Your Name</label>
      <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
             placeholder="Site Administrator" required />
    </div>
    <div class="group">
      <label>Email Address</label>
      <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
             placeholder="admin@thegentlemensplace.eu" required />
    </div>
    <div class="group">
      <label>Password (min 10 chars)</label>
      <input type="password" name="password" placeholder="Strong password" required />
    </div>
    <div class="group">
      <label>Confirm Password</label>
      <input type="password" name="confirm" placeholder="Repeat password" required />
    </div>
    <div class="group">
      <label>Setup Secret</label>
      <input type="password" name="secret" placeholder="Enter secret key provided" required />
    </div>
    <button type="submit">Create Superadmin Account</button>
  </form>
  <?php endif; ?>

  <p class="note">
    After creating the admin, go to
    <a href="<?= BASE_PATH ?>/admin/login"><?= BASE_PATH ?>/admin/login</a>
    and sign in with the credentials above.
  </p>
</div>
</body>
</html>

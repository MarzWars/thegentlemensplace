<?php
require_once __DIR__ . '/config.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    
    $db = getDb();
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        // Find performer profile
        $pStmt = $db->prepare("SELECT id FROM performers WHERE user_id = ? LIMIT 1");
        $pStmt->execute([$user['id']]);
        $performer = $pStmt->fetch();

        if ($performer) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_uuid'] = $user['uuid'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['performer_id'] = (int)$performer['id'];
            
            header('Location: dashboard.php');
            exit;
        } else {
            $error = "This account is not a registered performer account.";
        }
    } else {
        $error = "Invalid email or password.";
    }
}

$pageTitle = 'Sign In - Independent Performers';
require_once 'header.php';
?>
<div style="max-width: 400px; margin: 6rem auto; padding: 2rem; background: #111; border-radius: 8px; border: 1px solid rgba(201,168,76,0.2);">
  <h1 style="color: #c9a84c; text-align: center; margin-bottom: 2rem; font-family: 'Cinzel', serif;">Performer Sign In</h1>
  <?php if (!empty($error)): ?>
    <div style="color: #ff4d4d; margin-bottom: 1rem; text-align: center;"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="POST" action="login.php">
    <div style="margin-bottom: 1rem;">
      <label style="display: block; color: #f0e8d0; margin-bottom: 0.5rem;">Email</label>
      <input type="email" name="email" required style="width: 100%; padding: 0.75rem; background: #1a1a1a; border: 1px solid #333; color: #fff; border-radius: 4px; box-sizing: border-box;">
    </div>
    <div style="margin-bottom: 2rem;">
      <label style="display: block; color: #f0e8d0; margin-bottom: 0.5rem;">Password</label>
      <input type="password" name="password" required style="width: 100%; padding: 0.75rem; background: #1a1a1a; border: 1px solid #333; color: #fff; border-radius: 4px; box-sizing: border-box;">
    </div>
    <button type="submit" class="btn-primary">Sign In</button>
  </form>
  <div style="margin-top: 1.5rem; text-align: center;">
    <a href="register.php" style="color: #c9a84c; text-decoration: none;">Don't have an account? Register</a>
  </div>
</div>
<?php require_once 'footer.php'; ?>

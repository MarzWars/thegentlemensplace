<?php
require_once __DIR__ . '/config.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$tier = $_GET['tier'] ?? 'free';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $username = htmlspecialchars(trim($_POST['username'] ?? ''), ENT_QUOTES);
    $password = $_POST['password'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $phone_number = htmlspecialchars(trim($_POST['phone_number'] ?? ''), ENT_QUOTES);
    $selectedTier = $_POST['tier'] === 'premium' ? 'premium' : 'free';

    if (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    } else {
        $db = getDb();
        
        // Check if email or username exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ? OR username = ? LIMIT 1");
        $stmt->execute([$email, $username]);
        if ($stmt->fetch()) {
            $error = "Email or Username already taken.";
        } else {
            // Generate UUID (reusing logic from User model equivalent)
            $data = random_bytes(16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40); 
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80); 
            $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));

            $db->beginTransaction();
            try {
                // Insert User
                $uStmt = $db->prepare("INSERT INTO users (uuid, username, email, password_hash, date_of_birth, status, age_verified, age_verified_at, age_verify_method) VALUES (?, ?, ?, ?, ?, 'active', 1, NOW(), 'dob_declaration')");
                $uStmt->execute([
                    $uuid,
                    $username,
                    $email,
                    password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
                    $dob
                ]);
                $userId = $db->lastInsertId();

                $age = (new DateTime($dob))->diff(new DateTime())->y;
                
                // Insert Performer
                $pData = random_bytes(16);
                $pData[6] = chr(ord($pData[6]) & 0x0f | 0x40); 
                $pData[8] = chr(ord($pData[8]) & 0x3f | 0x80); 
                $performerUuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($pData), 4));
                
                $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $username));
                $pStmt = $db->prepare("INSERT INTO performers (uuid, user_id, display_name, slug, age, phone_number, status, tier) VALUES (?, ?, ?, ?, ?, ?, 'pending_approval', ?)");
                $pStmt->execute([$performerUuid, $userId, $username, $slug, $age, $phone_number, $selectedTier]);
                $performerId = $db->lastInsertId();

                $db->commit();

                // Log them in automatically
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_uuid'] = $uuid;
                $_SESSION['username'] = $username;
                $_SESSION['performer_id'] = $performerId;
                
                header('Location: dashboard.php');
                exit;

            } catch (\Exception $e) {
                $db->rollBack();
                $error = "Registration failed: " . $e->getMessage();
            }
        }
    }
}

$pageTitle = 'Register - Independent Performers';
require_once 'header.php';
?>
<div style="max-width: 500px; margin: 4rem auto; padding: 2rem; background: #111; border-radius: 8px; border: 1px solid rgba(201,168,76,0.2);">
  <h1 style="color: #c9a84c; text-align: center; margin-bottom: 1rem; font-family: 'Cinzel', serif;">Performer Registration</h1>
  <?php if (!empty($error)): ?>
    <div style="color: #ff4d4d; margin-bottom: 1rem; text-align: center;"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  
  <form method="POST" action="register.php">
    <div style="margin-bottom: 1.5rem;">
      <label style="display: block; color: #f0e8d0; margin-bottom: 0.75rem;">Select Tier</label>
      <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
        <label style="color: #fff; display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
          <input type="radio" name="tier" value="free" <?= $tier === 'free' ? 'checked' : '' ?>> Free Tier (20% fee)
        </label>
        <label style="color: #fff; display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
          <input type="radio" name="tier" value="premium" <?= $tier === 'premium' ? 'checked' : '' ?>> Premium Tier ($25/mo, 10% fee)
        </label>
      </div>
    </div>
    <div style="margin-bottom: 1rem;">
      <label style="display: block; color: #f0e8d0; margin-bottom: 0.5rem;">Username</label>
      <input type="text" name="username" required style="width: 100%; padding: 0.75rem; background: #1a1a1a; border: 1px solid #333; color: #fff; border-radius: 4px; box-sizing: border-box;">
    </div>
    <div style="margin-bottom: 1rem;">
      <label style="display: block; color: #f0e8d0; margin-bottom: 0.5rem;">Email</label>
      <input type="email" name="email" required style="width: 100%; padding: 0.75rem; background: #1a1a1a; border: 1px solid #333; color: #fff; border-radius: 4px; box-sizing: border-box;">
    </div>
    <div style="margin-bottom: 1rem;">
      <label style="display: block; color: #f0e8d0; margin-bottom: 0.5rem;">Password</label>
      <input type="password" name="password" required minlength="8" style="width: 100%; padding: 0.75rem; background: #1a1a1a; border: 1px solid #333; color: #fff; border-radius: 4px; box-sizing: border-box;">
    </div>
    <div style="margin-bottom: 1rem;">
      <label style="display: block; color: #f0e8d0; margin-bottom: 0.5rem;">Date of Birth</label>
      <input type="date" name="dob" required style="width: 100%; padding: 0.75rem; background: #1a1a1a; border: 1px solid #333; color: #fff; border-radius: 4px; box-sizing: border-box;">
    </div>
    <div style="margin-bottom: 2rem;">
      <label style="display: block; color: #f0e8d0; margin-bottom: 0.5rem;">Phone Number</label>
      <input type="text" name="phone_number" required style="width: 100%; padding: 0.75rem; background: #1a1a1a; border: 1px solid #333; color: #fff; border-radius: 4px; box-sizing: border-box;">
    </div>
    <button type="submit" class="btn-primary">Complete Registration</button>
  </form>
  <div style="margin-top: 1.5rem; text-align: center;">
    <a href="login.php" style="color: #c9a84c; text-decoration: none;">Already have an account? Sign In</a>
  </div>
</div>
<?php require_once 'footer.php'; ?>

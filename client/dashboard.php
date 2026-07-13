<?php
require_once __DIR__ . '/config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$db = getDb();

$stmt = $db->prepare("SELECT * FROM performers WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$performer = $stmt->fetch();

$msg = '';
if (isset($_GET['msg']) && $_GET['msg'] === 'updated') {
    $msg = "Profile updated successfully!";
} elseif (isset($_GET['msg']) && $_GET['msg'] === 'upgraded') {
    $msg = "Premium Upgrade successful! Your profile will reflect Premium status momentarily.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isLocal = strpos(__DIR__, 'tgplace') !== false;
    $uploadBaseDir = $isLocal ? __DIR__ . '/../uploads/performers/' : __DIR__ . '/../thegentlemensplace.eu/uploads/performers/';
    
    $performerDir = $uploadBaseDir . $performer['id'] . '/';
    if (!is_dir($performerDir)) {
        mkdir($performerDir, 0755, true);
    }

    $profilePhotoPath = $performer['profile_photo'];
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
        $filename = bin2hex(random_bytes(8)) . '.' . $ext;
        if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $performerDir . $filename)) {
            $profilePhotoPath = 'uploads/performers/' . $performer['id'] . '/' . $filename;
        }
    }

    $coverPhotoPath = $performer['cover_photo'];
    if (isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['cover_photo']['name'], PATHINFO_EXTENSION);
        $filename = 'cover_' . bin2hex(random_bytes(8)) . '.' . $ext;
        if (move_uploaded_file($_FILES['cover_photo']['tmp_name'], $performerDir . $filename)) {
            $coverPhotoPath = 'uploads/performers/' . $performer['id'] . '/' . $filename;
        }
    }

    $videoEnabled = isset($_POST['video_enabled']) ? 1 : 0;

    $stmt = $db->prepare("UPDATE performers SET bio = ?, rate_per_minute = ?, bank_details = ?, profile_photo = ?, cover_photo = ?, video_enabled = ? WHERE user_id = ?");
    $stmt->execute([
        $_POST['bio'] ?? '',
        $_POST['rate_per_minute'] ?? 0,
        $_POST['bank_details'] ?? '',
        $profilePhotoPath,
        $coverPhotoPath,
        $videoEnabled,
        $_SESSION['user_id']
    ]);
    
    header('Location: dashboard.php?msg=updated');
    exit;
}

$pageTitle = 'Dashboard - Independent Performers';
require_once 'header.php';
?>

<div style="max-width: 800px; margin: 4rem auto; padding: 2rem; background: #111; border-radius: 8px; border: 1px solid rgba(201,168,76,0.2);">
  <h1 style="color: #c9a84c; margin-bottom: 2rem; font-family: 'Cinzel', serif;">My Dashboard</h1>
  
  <?php if (!empty($msg)): ?>
    <div style="background: #4ade80; color: #000; padding: 1rem; margin-bottom: 2rem; border-radius: 4px; font-weight: bold;"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <div style="background: #1a1a1a; padding: 1rem; border-radius: 4px; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
      <h3 style="color: #fff; margin: 0; margin-bottom: 0.5rem;">Status: <span style="color: <?= $performer['status'] === 'active' ? '#4ade80' : '#fbbf24' ?>;"><?= ucfirst($performer['status']) ?></span></h3>
      <p style="margin: 0; color: #aaa;">Current Tier: <strong style="color: #c9a84c; text-transform: uppercase;"><?= htmlspecialchars($performer['tier']) ?></strong></p>
    </div>
    <div style="display: flex; gap: 1rem;">
      <?php if ($performer['tier'] === 'free'): ?>
        <?php
          if (class_exists('App\Services\CurrencyService') && class_exists('App\Services\PayFastService')) {
              $upgradeEur = 25.00;
              $upgradeZar = \App\Services\CurrencyService::toZAR($upgradeEur, 'EUR');
              $pf = new \App\Services\PayFastService();
              $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
              $domain = $_SERVER['HTTP_HOST'];
              
              $pfData = [
                  'merchant_id'  => PAYFAST_MERCHANT_ID,
                  'merchant_key' => PAYFAST_MERCHANT_KEY,
                  'return_url'   => $protocol . $domain . '/dashboard.php?msg=upgraded',
                  'cancel_url'   => $protocol . $domain . '/dashboard.php',
                  'notify_url'   => $protocol . $domain . '/itn_upgrade.php',
                  'm_payment_id' => uniqid('upg_'),
                  'amount'       => number_format($upgradeZar, 2, '.', ''),
                  'item_name'    => 'Premium Tier Upgrade - 25 EUR',
                  'custom_str1'  => (string)$_SESSION['user_id'],
              ];
              $pfData['signature'] = $pf->generateSignature($pfData);
              $pfAction = PAYFAST_SANDBOX ? 'https://sandbox.payfast.co.za/eng/process' : 'https://www.payfast.co.za/eng/process';
          }
        ?>
        <?php if (isset($pfData)): ?>
        <form method="POST" action="<?= $pfAction ?>" style="margin: 0; padding: 0;">
            <?php foreach ($pfData as $key => $val): ?>
              <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($val) ?>">
            <?php endforeach; ?>
            <button type="submit" class="btn-primary" style="padding: 0.5rem 1rem; background: transparent; color: #c9a84c; border: 1px solid #c9a84c; cursor: pointer;">Upgrade to Premium (€25)</button>
        </form>
        <?php endif; ?>
      <?php endif; ?>
      <a href="rooms.php" class="btn-primary" style="padding: 0.5rem 1rem;">Go to Rooms</a>
    </div>
  </div>

  <form method="POST" action="dashboard.php" enctype="multipart/form-data">
    <div style="display: flex; gap: 2rem; flex-wrap: wrap; margin-bottom: 1.5rem;">
      <div style="flex: 1; min-width: 300px;">
        <label style="display: block; color: #f0e8d0; margin-bottom: 0.5rem;">Profile Photo</label>
        <?php if (!empty($performer['profile_photo'])): ?>
          <img src="/<?= htmlspecialchars($performer['profile_photo']) ?>" style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px; margin-bottom: 0.5rem; display: block;">
        <?php endif; ?>
        <input type="file" name="profile_photo" accept="image/*" style="width: 100%; color: #fff;">
      </div>
      <div style="flex: 1; min-width: 300px;">
        <label style="display: block; color: #f0e8d0; margin-bottom: 0.5rem;">Cover Photo</label>
        <?php if (!empty($performer['cover_photo'])): ?>
          <img src="/<?= htmlspecialchars($performer['cover_photo']) ?>" style="width: 200px; height: 100px; object-fit: cover; border-radius: 4px; margin-bottom: 0.5rem; display: block;">
        <?php endif; ?>
        <input type="file" name="cover_photo" accept="image/*" style="width: 100%; color: #fff;">
      </div>
    </div>

    <div style="margin-bottom: 1.5rem;">
      <label style="display: block; color: #f0e8d0; margin-bottom: 0.5rem;">Bio</label>
      <textarea name="bio" rows="4" style="width: 100%; padding: 0.75rem; background: #1a1a1a; border: 1px solid #333; color: #fff; border-radius: 4px; box-sizing: border-box;"><?= htmlspecialchars($performer['bio'] ?? '') ?></textarea>
    </div>

    <div style="display: flex; gap: 2rem; flex-wrap: wrap; margin-bottom: 1.5rem;">
      <div style="flex: 1; min-width: 200px;">
        <label style="display: block; color: #f0e8d0; margin-bottom: 0.5rem;">Rate per Minute ($)</label>
        <input type="number" name="rate_per_minute" step="0.01" value="<?= htmlspecialchars($performer['rate_per_minute'] ?? '') ?>" style="width: 100%; padding: 0.75rem; background: #1a1a1a; border: 1px solid #333; color: #fff; border-radius: 4px; box-sizing: border-box;">
      </div>
      <div style="flex: 1; min-width: 200px; display: flex; align-items: center;">
        <label style="color: #f0e8d0; display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-top: 1.5rem;">
          <input type="checkbox" name="video_enabled" value="1" <?= empty($performer['video_enabled']) ? '' : 'checked' ?>>
          Enable Video Calls
        </label>
      </div>
    </div>

    <div style="margin-bottom: 2rem;">
      <label style="display: block; color: #f0e8d0; margin-bottom: 0.5rem;">Bank Details (for payouts)</label>
      <textarea name="bank_details" rows="3" placeholder="Account Name, Number, Routing/Swift" style="width: 100%; padding: 0.75rem; background: #1a1a1a; border: 1px solid #333; color: #fff; border-radius: 4px; box-sizing: border-box;"><?= htmlspecialchars($performer['bank_details'] ?? '') ?></textarea>
    </div>

    <button type="submit" class="btn-primary">Save Settings</button>
  </form>
</div>
<?php require_once 'footer.php'; ?>

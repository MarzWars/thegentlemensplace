<?php
require_once __DIR__ . '/config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$db = getDb();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'toggle_online') {
        $db->prepare("UPDATE performers SET online_status = NOT online_status, last_seen_at = NOW() WHERE user_id = ?")->execute([$_SESSION['user_id']]);
        header('Location: rooms.php');
        exit;
    }
    if (isset($_POST['action']) && $_POST['action'] === 'toggle_video') {
        $db->prepare("UPDATE performers SET video_enabled = NOT video_enabled WHERE user_id = ?")->execute([$_SESSION['user_id']]);
        header('Location: rooms.php');
        exit;
    }
}

$stmt = $db->prepare("SELECT * FROM performers WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$performer = $stmt->fetch();

$isOnline = (bool)$performer['online_status'];
$isVideoEnabled = (bool)$performer['video_enabled'];

$pageTitle = 'Rooms - Independent Performers';
require_once 'header.php';
?>
<div style="max-width: 800px; margin: 4rem auto; padding: 2rem; background: #111; border-radius: 8px; border: 1px solid rgba(201,168,76,0.2); text-align: center;">
  <h1 style="color: #c9a84c; margin-bottom: 1rem; font-family: 'Cinzel', serif;">Performer Rooms</h1>
  
  <p style="color: #aaa; margin-bottom: 2rem;">
    You are currently <strong style="color: <?= $isOnline ? '#4ade80' : '#ff4d4d' ?>;"><?= $isOnline ? 'ONLINE' : 'OFFLINE' ?></strong>.
  </p>
  
  <div style="display: flex; justify-content: center; gap: 1rem; margin-bottom: 4rem;">
    <form method="POST" action="rooms.php">
      <input type="hidden" name="action" value="toggle_online">
      <button type="submit" class="btn-primary" style="font-size: 1.25rem; <?= $isOnline ? 'background: #ff4d4d; border-color: #ff4d4d; color: #fff;' : '' ?>">
        <?= $isOnline ? 'Go Offline' : 'Go Online' ?>
      </button>
    </form>

    <form method="POST" action="rooms.php">
      <input type="hidden" name="action" value="toggle_video">
      <button type="submit" class="btn-ghost" style="font-size: 1.25rem; <?= $isVideoEnabled ? 'color: #4ade80; border-color: #4ade80;' : '' ?>">
        <?= $isVideoEnabled ? 'Video Enabled ✓' : 'Video Disabled ✗' ?>
      </button>
    </form>
  </div>

  <div style="margin-top: 4rem; padding-top: 2rem; border-top: 1px solid #333;">
    <h3 style="color: #fff; margin-bottom: 1rem;">Recent Call History</h3>
    <p style="color: #666;">No recent calls.</p>
  </div>
</div>
<?php require_once 'footer.php'; ?>

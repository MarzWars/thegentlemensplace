<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($title ?? 'Admin') ?> — The Gentleman's Place</title>
  <meta name="robots" content="noindex, nofollow" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="stylesheet" href="<?= BASE_PATH ?>/Assets/css/main.css" />
  <link rel="stylesheet" href="<?= BASE_PATH ?>/Assets/css/admin.css" />
</head>
<body class="admin-auth-body">
  <?= $content ?>
  <script src="<?= BASE_PATH ?>/Assets/js/main.js"></script>
</body>
</html>

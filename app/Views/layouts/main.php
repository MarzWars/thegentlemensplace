<?php
use App\Core\Lang;
$locale = Lang::locale();
$pageRobots = $metaRobots ?? 'noindex, nofollow';
?>
<!DOCTYPE html>
<html lang="<?= $locale ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="RATING" content="RTA-5042-1996-1400-1577-RTA" />
  <meta name="theme-color" content="#0a0805" />
  <title><?= htmlspecialchars($title ?? "The Gentleman's Place") ?></title>
  <meta name="description" content="<?= htmlspecialchars($metaDesc ?? Lang::t('meta.home_desc')) ?>" />
  <meta name="robots" content="<?= htmlspecialchars($pageRobots) ?>" />

  <?php 
    $currentPath = '/' . ltrim($_GET['url'] ?? '', '/');
    $cleanPath   = preg_replace('#^/(' . implode('|', array_keys(Lang::$supported)) . ')(/|$)#', '/', $currentPath);
    $cleanPath   = '/' . ltrim($cleanPath, '/');
    $canonicalUrl = BASE_URL . BASE_PATH . Lang::prefix() . ($cleanPath === '/' ? '' : $cleanPath);
  ?>
  <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>" />

  <!-- ── Google Analytics 4 (consent-aware) ── -->
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('consent', 'default', {
      'analytics_storage': 'denied',
      'ad_storage':        'denied',
      'wait_for_update':   500
    });
    gtag('js', new Date());
    gtag('config', 'G-TRV7G9DE1N', {'send_page_view': true});
  </script>
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-TRV7G9DE1N"></script>

  <!-- ── Resource hints ── -->
  <link rel="dns-prefetch" href="//fonts.googleapis.com" />
  <link rel="dns-prefetch" href="//fonts.gstatic.com" />
  <link rel="dns-prefetch" href="//www.googletagmanager.com" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="preload" href="<?= BASE_PATH ?>/Assets/css/main.css" as="style" />

  <!-- ── Favicon ── -->
  <link rel="icon" type="image/svg+xml" href="<?= BASE_PATH ?>/rta_logo.svg" />

  <link rel="stylesheet" href="<?= BASE_PATH ?>/Assets/css/main.css" />
</head>
<body class="auth-body">

<?php if (defined('MAINTENANCE_MODE') && MAINTENANCE_MODE): ?>
  <?php if (!empty($_SESSION['admin_id'])): ?>
    <div style="background: #6b1a2a; color: #f0e8d0; text-align: center; padding: 0.6rem 1rem; font-size: 0.8rem; font-weight: 600; letter-spacing: 0.05em; border-bottom: 1px solid rgba(201,168,76,.2); position: relative; z-index: 10001; font-family: sans-serif;">
      ⚠️ SITE IS CURRENTLY IN MAINTENANCE MODE (Visible only to logged-in administrators)
    </div>
  <?php elseif (!empty($_SESSION['user_id']) || !empty($_SESSION['performer_id'])): ?>
    <div style="background: #6b1a2a; color: #f0e8d0; text-align: center; padding: 0.6rem 1rem; font-size: 0.8rem; font-weight: 600; letter-spacing: 0.05em; border-bottom: 1px solid rgba(201,168,76,.2); position: relative; z-index: 10001; font-family: sans-serif;">
      ⚠️ THE SITE IS CURRENTLY UNDERGOING SCHEDULED MAINTENANCE.
    </div>
  <?php endif; ?>
<?php endif; ?>

<div id="cursor"></div>
<div id="cursor-ring"></div>

<!-- AGE GATE -->
<?php if (!isset($_COOKIE['age_confirmed'])): ?>
<div id="age-gate" class="age-gate-overlay" role="dialog" aria-modal="true" aria-labelledby="age-gate-title">
  <div class="age-gate-modal">
    <div class="age-gate-monogram">GC</div>
    <h2 class="age-gate-title" id="age-gate-title">Age Verification Required</h2>
    <p class="age-gate-body">This website contains adult content intended exclusively for individuals aged <strong>18 years or older</strong>.</p>
    <ul class="age-gate-list">
      <li>You are at least 18 years of age</li>
      <li>Adult content is legal in your jurisdiction</li>
      <li>You accept our <a href="<?= BASE_PATH ?>/terms">Terms of Service</a> and <a href="<?= BASE_PATH ?>/privacy">Privacy Policy</a></li>
    </ul>
    <div class="age-gate-actions">
      <button id="age-enter-btn" class="btn-primary">I Am 18+ — Enter</button>
      <a href="https://google.com" class="age-gate-exit">Exit</a>
    </div>
    <p class="age-gate-legal">Registered with the Film &amp; Publication Board of South Africa.</p>
  </div>
</div>
<script>
document.getElementById('age-enter-btn').addEventListener('click', function () {
  var d = new Date();
  d.setTime(d.getTime() + 30 * 24 * 60 * 60 * 1000);
  document.cookie = 'age_confirmed=1;expires=' + d.toUTCString() + ';path=/;SameSite=Lax';
  document.getElementById('age-gate').style.display = 'none';
});
</script>
<?php endif; ?>

<!-- NAVBAR -->
<header id="navbar">
  <div class="nav-inner">
    <a href="<?= BASE_PATH ?>/" class="nav-logo" aria-label="The Gentleman's Place — Home">
      <img src="<?= BASE_PATH ?>/Assets/img/logo.png" alt="The Gentleman's Place" style="height: 90px; width: auto; margin-top: 5px;" />
    </a>
    <nav class="nav-links" aria-label="Main navigation">
      <a href="<?= BASE_PATH ?>/performers">Performers</a>
      <a href="<?= BASE_PATH ?>/#how-it-works">How It Works</a>
      <a href="<?= BASE_PATH ?>/#pricing">Credits</a>
    </nav>
    <div class="nav-actions">
      <?php if (!empty($_SESSION['user_id'])): ?>
        <span class="nav-credit-badge">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          <?= number_format((float)($_SESSION['credits'] ?? 0), 0) ?> credits
        </span>
        <a href="<?= BASE_PATH ?>/account" class="nav-link-btn">My Account</a>
        <a href="<?= BASE_PATH ?>/logout" class="btn-ghost-sm">Sign Out</a>
      <?php else: ?>
        <a href="<?= BASE_PATH ?>/login" class="nav-link-btn">Sign In</a>
        <a href="<?= BASE_PATH ?>/register" class="btn-primary-sm">Join Now</a>
      <?php endif; ?>
    </div>
    <button class="nav-hamburger" id="hamburger" aria-label="Toggle menu" aria-expanded="false" aria-controls="nav-drawer">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

<!-- MOBILE DRAWER -->
<div id="nav-drawer" role="dialog" aria-label="Mobile navigation" aria-hidden="true">
  <button class="drawer-close" id="drawer-close" aria-label="Close menu">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
  </button>
  <div class="drawer-logo">
    <img src="<?= BASE_PATH ?>/Assets/img/logo.png" alt="The Gentleman's Place" style="height: 75px; width: auto;" />
  </div>
  <nav class="drawer-links">
    <a href="<?= BASE_PATH ?>/performers">Performers</a>
    <a href="<?= BASE_PATH ?>/#how-it-works">How It Works</a>
    <a href="<?= BASE_PATH ?>/#pricing">Credits</a>
    <?php if (!empty($_SESSION['user_id'])): ?>
      <a href="<?= BASE_PATH ?>/account">My Account</a>
      <a href="<?= BASE_PATH ?>/logout">Sign Out</a>
    <?php else: ?>
      <a href="<?= BASE_PATH ?>/login">Sign In</a>
      <a href="<?= BASE_PATH ?>/register" class="drawer-cta">Join Now</a>
    <?php endif; ?>
  </nav>
</div>
<div id="drawer-overlay"></div>

<!-- FLASH MESSAGES -->
<?php if (!empty($_SESSION['flash_error'])): ?>
  <div class="flash flash-error" role="alert"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
  <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>
<?php if (!empty($_SESSION['flash_success'])): ?>
  <div class="flash flash-success" role="alert"><?= htmlspecialchars($_SESSION['flash_success']) ?></div>
  <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<?= $content ?>

<?php require APP_ROOT . '/Views/partials/cookie-banner.php'; ?>
<script src="<?= BASE_PATH ?>/Assets/js/main.js"></script>
</body>
</html>

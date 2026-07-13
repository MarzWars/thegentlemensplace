<?php
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($pageTitle ?? 'Independent Performers - The Gentleman\'s Place') ?></title>
    <link rel="stylesheet" href="/Assets/css/main.css">
<body>

<div id="cursor"></div>
<div id="cursor-ring"></div>

<!-- PRELOADER -->
<div id="preloader" role="status" aria-label="Loading">
  <div class="preloader-crest">
    <div class="preloader-line">Est. MMXXV</div>
    <div class="preloader-monogram">GC</div>
    <div class="preloader-line">The Gentleman's Place</div>
  </div>
  <div class="preloader-bar-wrap"><div class="preloader-bar"></div></div>
  <div class="preloader-pct">0%</div>
</div>

<header id="navbar">
  <div class="nav-inner">
    <a href="/" class="nav-logo" aria-label="The Gentleman's Place">
      <span class="nav-logo-mark" aria-hidden="true">GC</span>
      <span class="nav-logo-text">The Gentleman's Place</span>
    </a>

    <nav class="nav-links" aria-label="Main navigation">
        <a href="/#how-it-works">How It Works</a>
        <a href="/#tiers">Tiers</a>
    </nav>

    <div class="nav-actions">
        <?php if (isLoggedIn()): ?>
            <a href="dashboard.php" class="nav-link-btn">My Dashboard</a>
            <a href="logout.php" class="btn-ghost-sm">Sign Out</a>
        <?php else: ?>
            <a href="login.php" class="nav-link-btn">Sign In</a>
            <a href="register.php" class="btn-primary-sm">Register</a>
        <?php endif; ?>
    </div>

    <button class="nav-hamburger" id="hamburger" aria-label="Toggle menu" aria-expanded="false" aria-controls="nav-drawer">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

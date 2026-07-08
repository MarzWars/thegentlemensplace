<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($title ?? 'Admin') ?> — TGP Admin</title>
  <meta name="robots" content="noindex, nofollow" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="stylesheet" href="<?= BASE_PATH ?>/Assets/css/main.css" />
  <link rel="stylesheet" href="<?= BASE_PATH ?>/Assets/css/admin.css" />
</head>
<body class="admin-body">

<?php if (defined('MAINTENANCE_MODE') && MAINTENANCE_MODE && !empty($_SESSION['admin_id'])): ?>
  <div style="background: #6b1a2a; color: #f0e8d0; text-align: center; padding: 0.6rem 1rem; font-size: 0.8rem; font-weight: 600; letter-spacing: 0.05em; border-bottom: 1px solid rgba(201,168,76,.2); position: relative; z-index: 10001; font-family: sans-serif;">
    ⚠️ SITE IS CURRENTLY IN MAINTENANCE MODE
  </div>
<?php endif; ?>

<div class="admin-layout">

  <!-- ── Sidebar ── -->
  <aside class="admin-sidebar" id="admin-sidebar">
    <div class="admin-sidebar-header">
      <img src="<?= BASE_PATH ?>/Assets/img/logo.png" alt="Logo" style="height: 80px; width: auto;" />
      <div class="admin-logo-text">
        <span class="admin-logo-title">Admin Panel</span>
        <span class="admin-logo-role"><?= htmlspecialchars($_SESSION['admin_role'] ?? 'admin') ?></span>
      </div>
    </div>

    <nav class="admin-nav">
      <?php
      // Determine active nav item from request URI
      $adminUri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
      // Strip BASE_PATH prefix
      $baseTrim = trim(BASE_PATH, '/');
      if ($baseTrim && str_starts_with($adminUri, $baseTrim)) {
          $adminUri = ltrim(substr($adminUri, strlen($baseTrim)), '/');
      }
      $isActive = fn(string $path) => $adminUri === trim($path, '/') || str_starts_with($adminUri, trim($path, '/') . '/');
      ?>
      <div class="admin-nav-group">
        <span class="admin-nav-label">Overview</span>
        <a href="<?= BASE_PATH ?>/admin" class="admin-nav-item <?= $adminUri === 'admin' ? 'active' : '' ?>">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
          Dashboard
        </a>
      </div>

      <div class="admin-nav-group">
        <span class="admin-nav-label">People</span>
        <a href="<?= BASE_PATH ?>/admin/users" class="admin-nav-item <?= $isActive('admin/users') ? 'active' : '' ?>">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          Users
        </a>
        <a href="<?= BASE_PATH ?>/admin/performers" class="admin-nav-item <?= $isActive('admin/performers') ? 'active' : '' ?>">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          Performers
        </a>
        <a href="<?= BASE_PATH ?>/admin/performers?filter=pending" class="admin-nav-item admin-nav-pending">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          Pending Approval
        </a>
      </div>

      <div class="admin-nav-group">
        <span class="admin-nav-label">Activity</span>
        <a href="<?= BASE_PATH ?>/admin/call" class="admin-nav-item <?= $isActive('admin/call') ? 'active' : '' ?>">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.8a16 16 0 0 0 6.29 6.29l.95-.95a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          Live Calls Portal
        </a>
        <a href="<?= BASE_PATH ?>/admin/calls" class="admin-nav-item <?= $isActive('admin/calls') ? 'active' : '' ?>">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          Calls History
        </a>
        <a href="<?= BASE_PATH ?>/admin/transactions" class="admin-nav-item <?= $isActive('admin/transactions') ? 'active' : '' ?>">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
          Transactions
        </a>
        <a href="<?= BASE_PATH ?>/admin/payouts" class="admin-nav-item <?= $isActive('admin/payouts') ? 'active' : '' ?>">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
          Payouts
        </a>
      </div>

      <div class="admin-nav-group">
        <span class="admin-nav-label">System</span>
        <a href="<?= BASE_PATH ?>/admin/settings" class="admin-nav-item <?= $isActive('admin/settings') ? 'active' : '' ?>">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
          Settings
        </a>
        <a href="<?= BASE_PATH ?>/admin/admins" class="admin-nav-item <?= $isActive('admin/admins') ? 'active' : '' ?>">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><circle cx="12" cy="11" r="3"/></svg>
          Admins
        </a>
        <a href="<?= BASE_PATH ?>/admin/logout" class="admin-nav-item admin-nav-logout">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          Sign Out
        </a>
      </div>
    </nav>
  </aside>

  <!-- ── Main ── -->
  <div class="admin-main">
    <header class="admin-topbar">
      <button class="admin-sidebar-toggle" id="admin-sidebar-toggle" aria-label="Toggle sidebar">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>
      <div class="admin-topbar-title"><?= htmlspecialchars($title ?? 'Dashboard') ?></div>
      <div class="admin-topbar-user">
        <span class="admin-topbar-name"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></span>
        <a href="<?= BASE_PATH ?>/admin/logout" class="admin-topbar-logout">Sign Out</a>
      </div>
    </header>

    <!-- Flash messages -->
    <?php if (!empty($_SESSION['flash_error'])): ?>
      <div class="admin-flash admin-flash-error"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
      <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_success'])): ?>
      <div class="admin-flash admin-flash-success"><?= htmlspecialchars($_SESSION['flash_success']) ?></div>
      <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <div class="admin-content">
      <?= $content ?>
    </div>
  </div>

</div>

<script src="<?= BASE_PATH ?>/Assets/js/main.js"></script>
<script>
document.getElementById('admin-sidebar-toggle')?.addEventListener('click', () => {
  document.getElementById('admin-sidebar')?.classList.toggle('collapsed');
});
</script>
</body>
</html>

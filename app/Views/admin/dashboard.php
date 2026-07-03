<!-- Switch Styling -->
<style>
.switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 26px;
}
.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(196,184,150,.15);
  transition: .3s;
  border: 1px solid rgba(201,168,76,.2);
}
.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: var(--admin-text-dim);
  transition: .3s;
}
input:checked + .slider {
  background-color: rgba(82, 183, 136, 0.2);
  border-color: var(--admin-success);
}
input:checked + .slider:before {
  transform: translateX(24px);
  background-color: var(--admin-success);
}
.slider.round {
  border-radius: 34px;
}
.slider.round:before {
  border-radius: 50%;
}
</style>

<div class="admin-page-header">
  <h1 class="admin-page-title">Admin Dashboard</h1>
</div>

<!-- Proxy Mode Toggle Banner -->
<div class="admin-panel" style="border: 1px solid var(--admin-border); margin-bottom: 2rem; border-radius: 4px; background: rgba(20, 18, 8, 0.6); backdrop-filter: blur(10px);">
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
    <div>
      <h3 style="color: var(--admin-gold); margin: 0 0 0.25rem 0; font-size: 1.05rem; font-family: var(--ff-serif); letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.5rem;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/><path d="M2 12h20"/></svg>
        Proxy Mode Control
      </h3>
      <p style="color: var(--admin-text); margin: 0; font-size: 0.78rem; opacity: 0.8; max-width: 680px;">
        When enabled, all active performers' status is set to <strong>Online</strong>. Incoming performer calls are intercepted so they can be answered directly from the admin Live Calls Portal.
      </p>
    </div>
    <div style="display: flex; align-items: center; gap: 1rem;">
      <span id="proxy-status-label" style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.1em; color: <?= $proxyMode ? 'var(--admin-success)' : 'var(--admin-text-dim)' ?>;">
        <?= $proxyMode ? 'PROXY ACTIVE' : 'PROXY INACTIVE' ?>
      </span>
      <label class="switch">
        <input type="checkbox" id="proxy-mode-checkbox" onchange="toggleProxyMode(this)" <?= $proxyMode ? 'checked' : '' ?>>
        <span class="slider round"></span>
      </label>
    </div>
  </div>
</div>

<!-- Stats Grid -->
<div class="admin-stats-grid">
  <div class="admin-stat-card">
    <div class="admin-stat-icon admin-stat-blue">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
    </div>
    <div class="admin-stat-body">
      <span class="admin-stat-value"><?= number_format($stats['total_users']) ?></span>
      <span class="admin-stat-label">Total Users</span>
    </div>
    <a href="<?= BASE_PATH ?>/admin/users" class="admin-stat-action">View</a>
  </div>

  <div class="admin-stat-card">
    <div class="admin-stat-icon admin-stat-blue">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
    </div>
    <div class="admin-stat-body">
      <span class="admin-stat-value"><?= number_format($stats['active_performers']) ?></span>
      <span class="admin-stat-label">Active Models</span>
    </div>
    <a href="<?= BASE_PATH ?>/admin/performers" class="admin-stat-action">View</a>
  </div>

  <div class="admin-stat-card">
    <div class="admin-stat-icon admin-stat-green">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    </div>
    <div class="admin-stat-body">
      <span class="admin-stat-value"><?= number_format($stats['online_performers']) ?></span>
      <span class="admin-stat-label">Online Models</span>
    </div>
  </div>

  <div class="admin-stat-card <?= $stats['pending_approval'] > 0 ? 'admin-stat-card-warn' : '' ?>">
    <div class="admin-stat-icon admin-stat-warn">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    </div>
    <div class="admin-stat-body">
      <span class="admin-stat-value"><?= number_format($stats['pending_approval']) ?></span>
      <span class="admin-stat-label">Pending Approval</span>
    </div>
    <?php if ($stats['pending_approval'] > 0): ?>
      <a href="<?= BASE_PATH ?>/admin/performers?filter=pending" class="admin-stat-action">Review</a>
    <?php endif; ?>
  </div>

  <div class="admin-stat-card">
    <div class="admin-stat-icon admin-stat-gold">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
    </div>
    <div class="admin-stat-body">
      <span class="admin-stat-value"><?= $stats['revenue_today_fmt'] ?></span>
      <span class="admin-stat-label">Today's Revenue</span>
    </div>
  </div>

  <div class="admin-stat-card">
    <div class="admin-stat-icon admin-stat-gold">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
    </div>
    <div class="admin-stat-body">
      <span class="admin-stat-value"><?= $stats['revenue_month_fmt'] ?></span>
      <span class="admin-stat-label">Month Revenue</span>
    </div>
  </div>
</div>

<!-- Pending Performer Approvals Section -->
<?php if (!empty($pendingPerformers)): ?>
<div class="admin-panel" style="border: 1px solid var(--admin-border); margin-bottom: 2rem; border-radius: 4px;">
  <div class="admin-panel-header">
    <h2 class="admin-panel-title">Models Awaiting Approval</h2>
    <a href="<?= BASE_PATH ?>/admin/performers?filter=pending" class="admin-panel-link">View All Pending</a>
  </div>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Name</th><th>Age</th><th>Category</th><th>Rate</th><th>Languages</th><th>Submitted At</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pendingPerformers as $p): ?>
        <tr>
          <td>
            <a href="<?= BASE_PATH ?>/performer/<?= htmlspecialchars($p['slug']) ?>" target="_blank" style="color:var(--admin-gold); font-weight:600;">
              <?= htmlspecialchars($p['display_name']) ?>
            </a>
          </td>
          <td><?= (int)$p['age'] ?></td>
          <td style="font-size:.72rem;"><?= htmlspecialchars($p['category']) ?></td>
          <td><?= number_format((float)$p['rate_per_minute'], 0) ?> cr/min</td>
          <td><?= htmlspecialchars($p['languages']) ?></td>
          <td><?= date('Y-m-d H:i', strtotime($p['created_at'])) ?></td>
          <td>
            <form method="POST" action="<?= BASE_PATH ?>/admin/performer/approve/<?= (int)$p['id'] ?>" style="display:inline;">
              <?= \App\Core\CSRF::field() ?>
              <button type="submit" class="admin-btn admin-btn-sm admin-btn-success">Approve</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<!-- Dashboard Grid (Recent activity) -->
<div class="admin-dashboard-grid">
  <!-- Left Side: Recent Calls -->
  <div class="admin-panel">
    <div class="admin-panel-header">
      <h2 class="admin-panel-title">Recent Calls</h2>
      <a href="<?= BASE_PATH ?>/admin/calls" class="admin-panel-link">All Calls</a>
    </div>
    <?php if (empty($recentCalls)): ?>
      <p class="admin-empty">No calls logged yet.</p>
    <?php else: ?>
      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Client</th><th>Model</th><th>Duration</th><th>Cost</th><th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentCalls as $c): ?>
            <tr>
              <td><?= htmlspecialchars($c['username']) ?></td>
              <td><?= htmlspecialchars($c['performer_name']) ?></td>
              <td><?= $c['duration_seconds'] > 0 ? gmdate('i:s', (int)$c['duration_seconds']) : '—' ?></td>
              <td><?= number_format((float)$c['credits_used'], 1) ?> cr</td>
              <td>
                <span class="admin-badge <?= $c['status'] === 'completed' ? 'admin-badge-success' : 'admin-badge-warn' ?>">
                  <?= htmlspecialchars($c['status']) ?>
                </span>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

  <!-- Right Side: Recent Transactions -->
  <div class="admin-panel">
    <div class="admin-panel-header">
      <h2 class="admin-panel-title">Recent Purchases</h2>
      <a href="<?= BASE_PATH ?>/admin/transactions" class="admin-panel-link">All Payments</a>
    </div>
    <?php if (empty($recentTx)): ?>
      <p class="admin-empty">No purchases logged yet.</p>
    <?php else: ?>
      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Client</th><th>Package</th><th>Amount</th><th>Status</th><th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentTx as $t): ?>
            <tr>
              <td><?= htmlspecialchars($t['username']) ?></td>
              <td><?= htmlspecialchars($t['package_name'] ?? 'Custom') ?></td>
              <td>R <?= number_format((float)$t['amount_zar'], 2) ?></td>
              <td>
                <span class="admin-badge <?= $t['status'] === 'completed' ? 'admin-badge-success' : 'admin-badge-warn' ?>">
                  <?= htmlspecialchars($t['status']) ?>
                </span>
              </td>
              <td><?= date('m-d H:i', strtotime($t['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
function toggleProxyMode(checkbox) {
  checkbox.disabled = true;
  const statusLabel = document.getElementById('proxy-status-label');
  
  fetch('<?= BASE_PATH ?>/admin/proxy-mode/toggle', {
    method: 'POST'
  })
  .then(res => res.json())
  .then(data => {
    checkbox.disabled = false;
    if (data.success) {
      if (data.proxy_mode === 1) {
        statusLabel.textContent = 'PROXY ACTIVE';
        statusLabel.style.color = 'var(--admin-success)';
      } else {
        statusLabel.textContent = 'PROXY INACTIVE';
        statusLabel.style.color = 'var(--admin-text-dim)';
      }
      // Reload page to reflect changed performer online statuses in stats
      window.location.reload();
    } else {
      alert('Error toggling proxy mode: ' + (data.message || 'Unknown error'));
      checkbox.checked = !checkbox.checked;
    }
  })
  .catch(err => {
    checkbox.disabled = false;
    alert('Failed to toggle proxy mode.');
    checkbox.checked = !checkbox.checked;
  });
}
</script>

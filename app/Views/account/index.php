<?php
use App\Services\CurrencyService;
$username    = htmlspecialchars($user['username']);
$email       = htmlspecialchars($user['email']);
$phone       = htmlspecialchars($user['phone'] ?? '');
$balance     = number_format((float)$user['credit_balance'], 2);
$memberSince = date('F Y', strtotime($user['created_at']));
$lastLogin   = $user['last_login_at'] ? date('d M Y, H:i', strtotime($user['last_login_at'])) : 'First visit';
$totalCalls  = count($calls);
$activeTab   = $_GET['tab'] ?? 'overview';

// Total spent — stored in ZAR, display in user's currency
$acctCurrency  = defined('CURRENCY') ? CURRENCY : 'EUR';
$totalSpentZAR = array_sum(array_column($transactions, 'amount_zar'));
$totalSpentFmt = CurrencyService::format(
    CurrencyService::fromZAR($totalSpentZAR, $acctCurrency),
    $acctCurrency
);
$zarSymbol = CurrencyService::symbol('ZAR');
?>

<div class="account-page">

  <!-- ── Sidebar ── -->
  <aside class="account-sidebar">
    <div class="account-avatar" aria-hidden="true">
      <?= strtoupper(substr($user['username'], 0, 2)) ?>
    </div>
    <div class="account-sidebar-name"><?= $username ?></div>
    <div class="account-sidebar-since">Member since <?= $memberSince ?></div>

    <div class="account-balance-card">
      <span class="balance-label">Credit Balance</span>
      <span class="balance-value"><?= $balance ?></span>
      <a href="<?= BASE_PATH ?>/credits" class="btn-primary btn-block" style="margin-top:1rem; font-size:.6rem;">
        Top Up Credits
      </a>
    </div>

    <nav class="account-nav" aria-label="Account sections">
      <a href="?tab=overview"  class="account-nav-item <?= $activeTab === 'overview'  ? 'active' : '' ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        Overview
      </a>
      <a href="?tab=history" class="account-nav-item <?= $activeTab === 'history' ? 'active' : '' ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        History
      </a>
      <a href="?tab=profile"  class="account-nav-item <?= $activeTab === 'profile'  ? 'active' : '' ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        Profile
      </a>
      <a href="?tab=security" class="account-nav-item <?= $activeTab === 'security' ? 'active' : '' ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        Security
      </a>
      <?php if (!empty($performer)): ?>
      <a href="<?= BASE_PATH ?>/performer-dash" class="account-nav-item" style="color:var(--gold);">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18M3 9h18"/></svg>
        Performer Dashboard
      </a>
      <?php endif; ?>
      <a href="<?= BASE_PATH ?>/performers" class="account-nav-item">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Browse Performers
      </a>
      <a href="<?= BASE_PATH ?>/logout" class="account-nav-item account-nav-logout">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Sign Out
      </a>
    </nav>
  </aside>

  <!-- ── Main content ── -->
  <main class="account-main">

    <?php if (!empty($_SESSION['flash_error'])): ?>
      <div class="form-alert form-alert-error" role="alert" style="margin-bottom:1.5rem;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <?= htmlspecialchars($_SESSION['flash_error']) ?>
      </div>
      <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_success'])): ?>
      <div class="form-alert form-alert-success" role="alert" style="margin-bottom:1.5rem;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
        <?= htmlspecialchars($_SESSION['flash_success']) ?>
      </div>
      <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <?php if ($activeTab === 'overview'): ?>
    <!-- ════════════════ OVERVIEW ════════════════ -->
    <div class="account-section">
      <h1 class="account-section-title">Welcome back, <?= $username ?></h1>
      <p class="account-section-sub">Here's a snapshot of your account.</p>

      <div class="account-stats-grid">
        <div class="account-stat">
          <span class="account-stat-value"><?= $balance ?></span>
          <span class="account-stat-label">Credits Available</span>
        </div>
        <div class="account-stat">
          <span class="account-stat-value"><?= $totalCalls ?></span>
          <span class="account-stat-label">Total Calls</span>
        </div>
        <div class="account-stat">
          <span class="account-stat-value"><?= htmlspecialchars($totalSpentFmt) ?></span>
          <span class="account-stat-label">Total Spent</span>
        </div>
        <div class="account-stat">
          <span class="account-stat-value"><?= $lastLogin ?></span>
          <span class="account-stat-label">Last Login</span>
        </div>
      </div>

      <!-- Status badges -->
      <div class="account-badges">
        <span class="account-badge <?= $user['email_verified'] ? 'badge-ok' : 'badge-warn' ?>">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
          Email <?= $user['email_verified'] ? 'Verified' : 'Not Verified' ?>
        </span>
        <span class="account-badge badge-ok">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          Age Verified
        </span>
        <span class="account-badge <?= $user['status'] === 'active' ? 'badge-ok' : 'badge-warn' ?>">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/></svg>
          Account <?= ucfirst($user['status']) ?>
        </span>
      </div>

      <!-- Quick actions -->
      <div class="account-quick-actions">
        <a href="<?= BASE_PATH ?>/performers" class="account-action-card">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          <span class="action-card-title">Browse Performers</span>
          <span class="action-card-sub">Find your perfect match</span>
        </a>
        <a href="<?= BASE_PATH ?>/credits" class="account-action-card">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
          <span class="action-card-title">Buy Credits</span>
          <span class="action-card-sub">Top up your balance</span>
        </a>
        <a href="?tab=history" class="account-action-card">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          <span class="action-card-title">Call History</span>
          <span class="action-card-sub">View past connections</span>
        </a>
        <?php if (!empty($performer)): ?>
        <a href="<?= BASE_PATH ?>/performer-dash" class="account-action-card" style="border-color:rgba(201,168,76,0.3); background:rgba(201,168,76,0.02);">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" style="color:var(--gold);"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18M3 9h18"/></svg>
          <span class="action-card-title" style="color:var(--gold);">Performer Dashboard</span>
          <span class="action-card-sub">Manage calls & settings</span>
        </a>
        <?php endif; ?>
      </div>
    </div>

    <?php elseif ($activeTab === 'history'): ?>
    <!-- ════════════════ HISTORY ════════════════ -->
    <div class="account-section">
      <h1 class="account-section-title">History</h1>
      <p class="account-section-sub">Your calls and credit purchases.</p>

      <h2 class="account-sub-heading">Recent Calls</h2>
      <?php if (empty($calls)): ?>
        <div class="account-empty">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.8a16 16 0 0 0 6.29 6.29l.95-.95a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          <p>No calls yet. <a href="<?= BASE_PATH ?>/performers">Browse performers</a> to get started.</p>
        </div>
      <?php else: ?>
        <div class="account-table-wrap">
          <table class="account-table">
            <thead>
              <tr>
                <th>Performer</th>
                <th>Date</th>
                <th>Duration</th>
                <th>Credits</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($calls as $call): ?>
              <tr>
                <td>
                  <a href="<?= BASE_PATH ?>/performer/<?= htmlspecialchars($call['performer_slug']) ?>">
                    <?= htmlspecialchars($call['performer_name']) ?>
                  </a>
                </td>
                <td><?= date('d M Y', strtotime($call['created_at'])) ?></td>
                <td><?= $call['duration_seconds'] ? gmdate('i:s', $call['duration_seconds']) : '—' ?></td>
                <td><?= number_format((float)$call['credits_used'], 2) ?></td>
                <td><span class="status-pill status-<?= htmlspecialchars($call['status']) ?>"><?= ucfirst(str_replace('_', ' ', $call['status'])) ?></span></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <h2 class="account-sub-heading" style="margin-top:2.5rem;">Credit Purchases</h2>
      <?php if (empty($transactions)): ?>
        <div class="account-empty">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
          <p>No purchases yet. <a href="<?= BASE_PATH ?>/credits">Buy credits</a> to start calling.</p>
        </div>
      <?php else: ?>
        <div class="account-table-wrap">
          <table class="account-table">
            <thead>
              <tr>
                <th>Package</th>
                <th>Date</th>
                <th>Charged (ZAR)</th>
                <th>You Paid</th>
                <th>Credits</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($transactions as $tx): ?>
              <tr>
                <td><?= htmlspecialchars($tx['package_name'] ?? $tx['item_name']) ?></td>
                <td><?= date('d M Y', strtotime($tx['created_at'])) ?></td>
                <td><?= $zarSymbol ?> <?= number_format((float)$tx['amount_zar'], 2) ?></td>
                <td style="color:var(--gold-dim);">
                  <?= CurrencyService::format(
                        CurrencyService::fromZAR((float)$tx['amount_zar'], $acctCurrency),
                        $acctCurrency
                      ) ?>
                </td>
                <td><?= number_format((float)$tx['credits_purchased'] + (float)$tx['bonus_credits'], 2) ?></td>
                <td><span class="status-pill status-<?= htmlspecialchars($tx['status']) ?>"><?= ucfirst($tx['status']) ?></span></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

    <?php elseif ($activeTab === 'profile'): ?>
    <!-- ════════════════ PROFILE ════════════════ -->
    <div class="account-section" id="profile">
      <h1 class="account-section-title">Profile Details</h1>
      <p class="account-section-sub">Update your contact information.</p>

      <form class="auth-form account-form" method="POST" action="<?= BASE_PATH ?>/account/update" novalidate>
        <?= \App\Core\CSRF::field() ?>
        <input type="hidden" name="action" value="update_profile" />

        <div class="form-group">
          <label class="form-label">Username</label>
          <div class="input-wrap">
            <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <input type="text" class="form-input" value="<?= $username ?>" disabled aria-label="Username (cannot be changed)" />
          </div>
          <p style="font-size:.65rem; color:rgba(196,184,150,.35); margin-top:.3rem;">Username cannot be changed.</p>
        </div>

        <div class="form-group">
          <label class="form-label">Email Address</label>
          <div class="input-wrap">
            <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            <input type="email" class="form-input" value="<?= $email ?>" disabled aria-label="Email (cannot be changed)" />
          </div>
          <p style="font-size:.65rem; color:rgba(196,184,150,.35); margin-top:.3rem;">Contact support to change your email.</p>
        </div>

        <div class="form-group">
          <label for="phone" class="form-label">Phone Number <span class="form-label-note">(optional — used for call connection)</span></label>
          <div class="input-wrap">
            <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.8a16 16 0 0 0 6.29 6.29l.95-.95a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            <input
              type="tel" id="phone" name="phone" class="form-input"
              placeholder="+27 82 000 0000"
              value="<?= $phone ?>"
              autocomplete="tel"
            />
          </div>
        </div>

        <button type="submit" class="btn-primary btn-submit" style="align-self:flex-start; min-width:160px;">
          <span class="btn-text">Save Changes</span>
          <span class="btn-spinner" aria-hidden="true"></span>
        </button>
      </form>

      <?php if (!empty($performer)): ?>
        <div class="account-divider" style="margin: 2.5rem 0; height: 1px; background: rgba(201,168,76,.1);"></div>
        <h2 class="account-sub-heading">Public Performer Profile</h2>
        <p class="account-section-sub">Customize how you appear to other members on the main directory.</p>

        <form class="auth-form account-form" method="POST" action="<?= BASE_PATH ?>/account/update" enctype="multipart/form-data" novalidate>
          <?= \App\Core\CSRF::field() ?>
          <input type="hidden" name="action" value="update_performer_profile" />

          <div class="form-row">
            <div class="form-group">
              <label for="perf-display-name" class="form-label">Display Name</label>
              <div class="input-wrap">
                <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2a10 10 0 1 0 10 10H12V2z"/><path d="M12 2a10 10 0 0 1 10 10h-10V2z"/></svg>
                <input type="text" id="perf-display-name" name="display_name" class="form-input" value="<?= htmlspecialchars($performer['display_name']) ?>" required style="padding-left:2.6rem;" />
              </div>
            </div>
            <div class="form-group">
              <label for="perf-age" class="form-label">Age</label>
              <div class="input-wrap">
                <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <input type="number" id="perf-age" name="age" class="form-input" value="<?= (int)$performer['age'] ?>" min="18" required style="padding-left:2.6rem;" />
              </div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="perf-rate" class="form-label">Rate per Minute (Credits)</label>
              <div class="input-wrap">
                <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                <input type="number" id="perf-rate" name="rate_per_minute" class="form-input" value="<?= number_format((float)$performer['rate_per_minute'], 0) ?>" min="1" required style="padding-left:2.6rem;" />
              </div>
            </div>
            <div class="form-group">
              <label for="perf-languages" class="form-label">Languages</label>
              <div class="input-wrap">
                <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <input type="text" id="perf-languages" name="languages" class="form-input" value="<?= htmlspecialchars($performer['languages'] ?? 'English') ?>" required style="padding-left:2.6rem;" />
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="perf-category" class="form-label">Primary Category</label>
            <select name="category" id="perf-category" class="form-input" style="padding-left:1rem;">
              <?php foreach (['chat' => 'Chat', 'roleplay' => 'Roleplay', 'fantasy' => 'Fantasy', 'couples' => 'Couples', 'mature' => 'Mature', 'fetish' => 'Fetish'] as $k => $v): ?>
                <option value="<?= $k ?>" <?= $performer['category'] === $k ? 'selected' : '' ?>><?= $v ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="perf-bio" class="form-label">Bio</label>
            <textarea id="perf-bio" name="bio" class="form-input" style="min-height:120px; padding:1rem; font-family:sans-serif;" placeholder="Write a short summary about yourself..."><?= htmlspecialchars($performer['bio'] ?? '') ?></textarea>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="profile-photo" class="form-label">Profile Photo</label>
              <?php if (!empty($performer['profile_photo'])): ?>
                <div style="margin-bottom:.5rem; display:flex; align-items:flex-end; gap:.5rem;">
                  <img src="<?= BASE_PATH ?>/<?= htmlspecialchars($performer['profile_photo']) ?>" alt="<?= htmlspecialchars($performer['display_name']) ?> Profile Photo" style="width:80px; height:100px; object-fit:cover; border:1px solid rgba(201,168,76,.2);" />
                  <button type="submit" form="delete-profile-photo-form" class="btn-ghost btn-sm" style="color:var(--error-lt); border-color:rgba(160,36,60,.3); background:rgba(107,26,42,.15); font-size:.6rem; padding:.25rem .5rem; cursor:pointer;" onclick="return confirm('Delete profile photo?')">Remove</button>
                </div>
              <?php endif; ?>
              <input type="file" id="profile-photo" name="profile_photo" class="form-input" accept="image/jpeg,image/png,image/webp" style="padding-left:1rem;" />
            </div>
            <div class="form-group">
              <label for="cover-photo" class="form-label">Cover Photo</label>
              <?php if (!empty($performer['cover_photo'])): ?>
                <div style="margin-bottom:.5rem; display:flex; align-items:flex-end; gap:.5rem;">
                  <img src="<?= BASE_PATH ?>/<?= htmlspecialchars($performer['cover_photo']) ?>" alt="<?= htmlspecialchars($performer['display_name']) ?> Cover Photo" style="width:160px; height:100px; object-fit:cover; border:1px solid rgba(201,168,76,.2);" />
                  <button type="submit" form="delete-cover-photo-form" class="btn-ghost btn-sm" style="color:var(--error-lt); border-color:rgba(160,36,60,.3); background:rgba(107,26,42,.15); font-size:.6rem; padding:.25rem .5rem; cursor:pointer;" onclick="return confirm('Delete cover photo?')">Remove</button>
                </div>
              <?php endif; ?>
              <input type="file" id="cover-photo" name="cover_photo" class="form-input" accept="image/jpeg,image/png,image/webp" style="padding-left:1rem;" />
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="short-video" class="form-label">Short Video (10s MP4, Max 20MB)</label>
              <?php if (!empty($performer['short_video'])): ?>
                <div style="margin-bottom:.5rem; display:flex; align-items:flex-end; gap:.5rem;">
                  <video src="<?= BASE_PATH ?>/<?= htmlspecialchars($performer['short_video']) ?>" controls style="max-height:80px; border:1px solid rgba(201,168,76,.2); border-radius:4px; display:block;"></video>
                  <button type="submit" form="delete-short-video-form" class="btn-ghost btn-sm" style="color:var(--error-lt); border-color:rgba(160,36,60,.3); background:rgba(107,26,42,.15); font-size:.6rem; padding:.25rem .5rem; cursor:pointer;" onclick="return confirm('Delete intro video?')">Remove</button>
                </div>
              <?php endif; ?>
              <input type="file" id="short-video" name="short_video" class="form-input" accept="video/mp4" style="padding-left:1rem;" />
            </div>
            <div class="form-group">
              <label for="voice-sample" class="form-label">Voice Sample (MP3/MP4, Max 10MB)</label>
              <?php if (!empty($performer['voice_sample'])): ?>
                <div style="margin-bottom:.5rem; display:flex; align-items:flex-end; gap:.5rem;">
                  <audio src="<?= BASE_PATH ?>/<?= htmlspecialchars($performer['voice_sample']) ?>" controls style="max-width:200px; display:block;"></audio>
                  <button type="submit" form="delete-voice-sample-form" class="btn-ghost btn-sm" style="color:var(--error-lt); border-color:rgba(160,36,60,.3); background:rgba(107,26,42,.15); font-size:.6rem; padding:.25rem .5rem; cursor:pointer;" onclick="return confirm('Delete voice sample?')">Remove</button>
                </div>
              <?php endif; ?>
              <input type="file" id="voice-sample" name="voice_sample" class="form-input" accept="audio/mpeg,audio/mp3,audio/mp4,video/mp4" style="padding-left:1rem;" />
            </div>
          </div>

          <button type="submit" class="btn-primary btn-submit" style="align-self:flex-start; min-width:200px; margin-top:1rem;">
            <span class="btn-text">Update Performer Profile</span>
            <span class="btn-spinner" aria-hidden="true"></span>
          </button>
        </form>

        <form id="delete-profile-photo-form" method="POST" action="<?= BASE_PATH ?>/account/update" style="display:none;">
          <?= \App\Core\CSRF::field() ?>
          <input type="hidden" name="action" value="delete_performer_media" />
          <input type="hidden" name="media_type" value="profile_photo" />
        </form>
        <form id="delete-cover-photo-form" method="POST" action="<?= BASE_PATH ?>/account/update" style="display:none;">
          <?= \App\Core\CSRF::field() ?>
          <input type="hidden" name="action" value="delete_performer_media" />
          <input type="hidden" name="media_type" value="cover_photo" />
        </form>
        <form id="delete-short-video-form" method="POST" action="<?= BASE_PATH ?>/account/update" style="display:none;">
          <?= \App\Core\CSRF::field() ?>
          <input type="hidden" name="action" value="delete_performer_media" />
          <input type="hidden" name="media_type" value="short_video" />
        </form>
        <form id="delete-voice-sample-form" method="POST" action="<?= BASE_PATH ?>/account/update" style="display:none;">
          <?= \App\Core\CSRF::field() ?>
          <input type="hidden" name="action" value="delete_performer_media" />
          <input type="hidden" name="media_type" value="voice_sample" />
        </form>
      <?php endif; ?>
    </div>

    <?php elseif ($activeTab === 'security'): ?>
    <!-- ════════════════ SECURITY ════════════════ -->
    <div class="account-section" id="security">
      <h1 class="account-section-title">Security</h1>
      <p class="account-section-sub">Keep your account safe.</p>

      <div class="security-info-row">
        <div class="security-info-item">
          <span class="security-info-label">Last Login</span>
          <span class="security-info-value"><?= $lastLogin ?></span>
        </div>
        <div class="security-info-item">
          <span class="security-info-label">Last Login IP</span>
          <span class="security-info-value"><?= htmlspecialchars($user['last_login_ip'] ?? '—') ?></span>
        </div>
        <div class="security-info-item">
          <span class="security-info-label">Account Status</span>
          <span class="security-info-value"><?= ucfirst($user['status']) ?></span>
        </div>
      </div>

      <div class="account-divider"></div>

      <h2 class="account-sub-heading">Change Password</h2>
      <form class="auth-form account-form" method="POST" action="<?= BASE_PATH ?>/account/update" novalidate id="register-form">
        <?= \App\Core\CSRF::field() ?>
        <input type="hidden" name="action" value="change_password" />

        <div class="form-group">
          <label for="current_password" class="form-label">Current Password</label>
          <div class="input-wrap">
            <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <input type="password" id="current_password" name="current_password" class="form-input" placeholder="Your current password" autocomplete="current-password" required />
            <button type="button" class="input-toggle-pw" aria-label="Toggle password visibility" tabindex="-1">
              <svg class="eye-show" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg class="eye-hide" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
            </button>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="new_password" class="form-label">New Password</label>
            <div class="input-wrap">
              <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              <input type="password" id="new_password" name="new_password" class="form-input" placeholder="Min. 8 characters" autocomplete="new-password" minlength="8" required />
            </div>
            <div class="pw-strength">
              <div class="pw-strength-bar"><div class="pw-strength-fill" id="pw-strength-fill"></div></div>
              <span class="pw-strength-label" id="pw-strength-label"></span>
            </div>
          </div>
          <div class="form-group">
            <label for="confirm_password" class="form-label">Confirm New Password</label>
            <div class="input-wrap">
              <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              <input type="password" id="confirm_password" name="confirm_password" class="form-input" placeholder="Repeat new password" autocomplete="new-password" required />
            </div>
          </div>
        </div>

        <button type="submit" class="btn-primary btn-submit" style="align-self:flex-start; min-width:180px;">
          <span class="btn-text">Update Password</span>
          <span class="btn-spinner" aria-hidden="true"></span>
        </button>
      </form>
    </div>
    <?php endif; ?>

  </main>
</div>

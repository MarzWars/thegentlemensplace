<div class="admin-page-header">
  <h1 class="admin-page-title">Users</h1>
  <form method="GET" action="<?= BASE_PATH ?>/admin/users" class="admin-search-form">
    <div class="input-wrap" style="max-width:320px;">
      <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" name="q" class="form-input" placeholder="Search username or email…"
             value="<?= htmlspecialchars($search) ?>" />
    </div>
    <button type="submit" class="admin-btn admin-btn-primary">Search</button>
    <?php if ($search): ?>
      <a href="<?= BASE_PATH ?>/admin/users" class="admin-btn">Clear</a>
    <?php endif; ?>
  </form>
</div>

<?php if (empty($users)): ?>
  <p class="admin-empty">No users found.</p>
<?php else: ?>
<div class="admin-table-wrap">
  <table class="admin-table">
    <thead>
      <tr>
        <th>Username</th><th>Email</th><th>Credits</th>
        <th>Email Verified</th><th>Age Verified</th><th>Status</th><th>Last Login</th><th>Joined</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $u): ?>
      <tr>
        <td style="font-weight:500; color:var(--cream-dim);"><?= htmlspecialchars($u['username']) ?></td>
        <td style="font-size:.78rem; color:rgba(196,184,150,.55);"><?= htmlspecialchars($u['email']) ?></td>
        <td><?= number_format((float)$u['credit_balance'], 2) ?></td>
        <td>
          <?php if ($u['email_verified']): ?>
            <span class="admin-badge admin-badge-success">Verified</span>
          <?php else: ?>
            <span class="admin-badge admin-badge-warn">Pending</span>
          <?php endif; ?>
        </td>
        <td>
          <?php if ($u['age_verified']): ?>
            <span class="admin-badge admin-badge-success">Verified</span>
          <?php else: ?>
            <span class="admin-badge admin-badge-warn">Pending</span>
          <?php endif; ?>
        </td>
        <td><span class="status-pill status-<?= htmlspecialchars($u['status']) ?>"><?= ucfirst($u['status']) ?></span></td>
        <td style="font-size:.72rem; color:rgba(196,184,150,.4);">
          <?= $u['last_login_at'] ? date('d M Y', strtotime($u['last_login_at'])) : '—' ?>
        </td>
        <td style="font-size:.72rem; color:rgba(196,184,150,.4);">
          <?= date('d M Y', strtotime($u['created_at'])) ?>
        </td>
        <td class="admin-actions-cell">
          <?php if ($u['status'] === 'pending'): ?>
            <form method="POST" action="<?= BASE_PATH ?>/admin/users/approve/<?= (int)$u['id'] ?>" style="display:inline;">
              <?= \App\Core\CSRF::field() ?>
              <button type="submit" class="admin-btn admin-btn-sm admin-btn-success">Approve & Verify</button>
            </form>
          <?php endif; ?>

          <?php if ($u['status'] === 'active'): ?>
            <form method="POST" action="<?= BASE_PATH ?>/admin/users/suspend/<?= (int)$u['id'] ?>" style="display:inline;">
              <?= \App\Core\CSRF::field() ?>
              <button type="submit" class="admin-btn admin-btn-sm admin-btn-danger" onclick="return confirm('Suspend user <?= htmlspecialchars(addslashes($u['username'])) ?>?')">Suspend</button>
            </form>
            <form method="POST" action="<?= BASE_PATH ?>/admin/users/ban/<?= (int)$u['id'] ?>" style="display:inline;">
              <?= \App\Core\CSRF::field() ?>
              <button type="submit" class="admin-btn admin-btn-sm admin-btn-danger" onclick="return confirm('Permanently BAN user <?= htmlspecialchars(addslashes($u['username'])) ?>?')">Ban</button>
            </form>
          <?php endif; ?>

          <?php if ($u['status'] === 'suspended' || $u['status'] === 'banned'): ?>
            <form method="POST" action="<?= BASE_PATH ?>/admin/users/activate/<?= (int)$u['id'] ?>" style="display:inline;">
              <?= \App\Core\CSRF::field() ?>
              <button type="submit" class="admin-btn admin-btn-sm admin-btn-success">Reactivate</button>
            </form>
          <?php endif; ?>

          <?php if (($_SESSION['admin_role'] ?? '') === 'superadmin'): ?>
            <button class="admin-btn admin-btn-sm admin-btn-primary" onclick="openPromoteModal(<?= (int)$u['id'] ?>, '<?= htmlspecialchars(addslashes($u['username'])) ?>')">Make Admin</button>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<!-- Promote Admin Modal -->
<div id="promote-modal" class="admin-modal" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="promote-modal-title">
  <div class="admin-modal-card">
    <h2 class="admin-modal-title" id="promote-modal-title">Promote User to Admin</h2>
    <form method="POST" id="promote-form" action="" class="auth-form">
      <?= \App\Core\CSRF::field() ?>
      <div class="form-group">
        <label class="form-label">Username</label>
        <input type="text" id="promote-username" class="form-input" disabled />
      </div>
      <div class="form-group">
        <label for="promote-role" class="form-label">Administrator Role</label>
        <select name="role" id="promote-role" class="form-input" style="padding-left:1rem;">
          <option value="admin">Administrator (Standard)</option>
          <option value="superadmin">Superadministrator (Full Access)</option>
          <option value="moderator">Moderator</option>
          <option value="finance">Finance Manager</option>
        </select>
      </div>
      <div style="display:flex; gap:.75rem; margin-top:1.5rem;">
        <button type="submit" class="btn-primary" style="flex:1;">Promote to Admin</button>
        <button type="button" class="btn-ghost" onclick="closePromoteModal()" style="flex:1;">Cancel</button>
      </div>
    </form>
  </div>
</div>
<div id="promote-overlay" onclick="closePromoteModal()" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.7); z-index:1998;"></div>

<script>
function openPromoteModal(id, username) {
  document.getElementById('promote-form').action = '<?= BASE_PATH ?>/admin/users/make-admin/' + id;
  document.getElementById('promote-username').value = username;
  document.getElementById('promote-modal').style.display = 'flex';
  document.getElementById('promote-overlay').style.display = 'block';
  document.body.style.overflow = 'hidden';
}
function closePromoteModal() {
  document.getElementById('promote-modal').style.display = 'none';
  document.getElementById('promote-overlay').style.display = 'none';
  document.body.style.overflow = '';
}
</script>

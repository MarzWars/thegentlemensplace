<div class="admin-page-header">
  <h1 class="admin-page-title">Administrative Accounts</h1>
  <div style="font-size:.7rem; color:rgba(196,184,150,.35); letter-spacing:.1em;">
    Manage platform access & administrator privileges
  </div>
</div>

<?php if (empty($admins)): ?>
  <p class="admin-empty">No administrator accounts found.</p>
<?php else: ?>
<div class="admin-table-wrap">
  <table class="admin-table">
    <thead>
      <tr>
        <th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Last Login</th><th>Created</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($admins as $admin): ?>
      <tr>
        <td style="font-weight:600; color:var(--cream-dim);"><?= htmlspecialchars($admin['name']) ?></td>
        <td style="font-size:.78rem; color:rgba(196,184,150,.55);"><?= htmlspecialchars($admin['email']) ?></td>
        <td>
          <span class="status-pill status-active" style="text-transform:uppercase; font-size:.65rem; font-weight:600; letter-spacing:.05em;">
            <?= htmlspecialchars($admin['role']) ?>
          </span>
        </td>
        <td>
          <?php if ($admin['is_active']): ?>
            <span class="admin-badge admin-badge-success">Active</span>
          <?php else: ?>
            <span class="admin-badge admin-badge-warn">Inactive</span>
          <?php endif; ?>
        </td>
        <td style="font-size:.72rem; color:rgba(196,184,150,.4);">
          <?= $admin['last_login_at'] ? date('d M Y, H:i', strtotime($admin['last_login_at'])) : '—' ?>
        </td>
        <td style="font-size:.72rem; color:rgba(196,184,150,.4);">
          <?= date('d M Y', strtotime($admin['created_at'])) ?>
        </td>
        <td class="admin-actions-cell">
          <?php if (($_SESSION['admin_role'] ?? '') === 'superadmin'): ?>
            <!-- Toggle Active State -->
            <?php if ($admin['id'] !== (int)$_SESSION['admin_id']): ?>
              <form method="POST" action="<?= BASE_PATH ?>/admin/admins/toggle-active/<?= (int)$admin['id'] ?>" style="display:inline;">
                <?= \App\Core\CSRF::field() ?>
                <button type="submit" class="admin-btn admin-btn-sm <?= $admin['is_active'] ? 'admin-btn-danger' : 'admin-btn-success' ?>">
                  <?= $admin['is_active'] ? 'Deactivate' : 'Activate' ?>
                </button>
              </form>
            <?php endif; ?>

            <!-- Change Role Trigger -->
            <button class="admin-btn admin-btn-sm admin-btn-primary" 
                    onclick="openRoleModal(<?= (int)$admin['id'] ?>, '<?= htmlspecialchars(addslashes($admin['name'])) ?>', '<?= htmlspecialchars($admin['role']) ?>')">
              Change Role
            </button>
          <?php else: ?>
            <span style="font-size:.65rem; color:rgba(196,184,150,.25); font-style:italic;">No privileges</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<!-- Change Role Modal -->
<div id="role-modal" class="admin-modal" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="role-modal-title">
  <div class="admin-modal-card">
    <h2 class="admin-modal-title" id="role-modal-title">Change Administrative Role</h2>
    <form method="POST" id="role-form" action="" class="auth-form">
      <?= \App\Core\CSRF::field() ?>
      <div class="form-group">
        <label class="form-label">Administrator</label>
        <input type="text" id="role-admin-name" class="form-input" disabled />
      </div>
      <div class="form-group">
        <label for="role-select" class="form-label">Select Role</label>
        <select name="role" id="role-select" class="form-input" style="padding-left:1rem;">
          <option value="admin">Administrator (Standard)</option>
          <option value="superadmin">Superadministrator (Full Access)</option>
          <option value="moderator">Moderator</option>
          <option value="finance">Finance Manager</option>
        </select>
      </div>
      <div style="display:flex; gap:.75rem; margin-top:1.5rem;">
        <button type="submit" class="btn-primary" style="flex:1;">Update Role</button>
        <button type="button" class="btn-ghost" onclick="closeRoleModal()" style="flex:1;">Cancel</button>
      </div>
    </form>
  </div>
</div>
<div id="role-overlay" onclick="closeRoleModal()" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.7); z-index:1998;"></div>

<script>
function openRoleModal(id, name, currentRole) {
  document.getElementById('role-form').action = '<?= BASE_PATH ?>/admin/admins/update-role/' + id;
  document.getElementById('role-admin-name').value = name;
  document.getElementById('role-select').value = currentRole;
  document.getElementById('role-modal').style.display = 'flex';
  document.getElementById('role-overlay').style.display = 'block';
  document.body.style.overflow = 'hidden';
}
function closeRoleModal() {
  document.getElementById('role-modal').style.display = 'none';
  document.getElementById('role-overlay').style.display = 'none';
  document.body.style.overflow = '';
}
</script>

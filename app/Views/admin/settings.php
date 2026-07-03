<div class="admin-page-header">
  <h1 class="admin-page-title">Site Settings</h1>
  <div style="display:flex; align-items:center; gap:1rem; flex-wrap:wrap;">
    <?php if (!empty($lastSync)): ?>
      <span style="font-size: 0.68rem; color: var(--admin-text-dim);">
        Exchange Rates Last Updated: <strong style="color: var(--admin-gold);"><?= date('Y-m-d H:i', strtotime($lastSync)) ?></strong>
      </span>
    <?php endif; ?>
    <form method="POST" action="<?= BASE_PATH ?>/admin/settings/sync-rates">
      <?= \App\Core\CSRF::field() ?>
      <button type="submit" class="admin-btn admin-btn-primary" style="display:flex; align-items:center; gap:0.4rem;">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 4v6h-6M1 20v-6h6"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
        Sync Exchange Rates
      </button>
    </form>
  </div>
</div>

<div class="admin-panel" style="max-width:720px;">
  <div class="admin-panel-header">
    <h2 class="admin-panel-title">Configuration</h2>
    <span style="font-size:.65rem; color:rgba(196,184,150,.3); letter-spacing:.1em;">Changes take effect immediately</span>
  </div>

  <div class="admin-settings-list">
    <?php foreach ($settingRows as $row): ?>
    <form method="POST" action="<?= BASE_PATH ?>/admin/settings/save" class="admin-setting-row">
      <?= \App\Core\CSRF::field() ?>
      <input type="hidden" name="key" value="<?= htmlspecialchars($row['key']) ?>">

      <div class="admin-setting-info">
        <span class="admin-setting-key"><?= htmlspecialchars($row['key']) ?></span>
        <?php if (!empty($row['description'])): ?>
          <span class="admin-setting-desc"><?= htmlspecialchars($row['description']) ?></span>
        <?php endif; ?>
      </div>

      <div class="admin-setting-control">
        <?php if ($row['type'] === 'boolean'): ?>
          <select name="value" class="form-input admin-setting-input">
            <option value="1" <?= $row['value'] === '1' ? 'selected' : '' ?>>Enabled</option>
            <option value="0" <?= $row['value'] === '0' ? 'selected' : '' ?>>Disabled</option>
          </select>
        <?php else: ?>
          <input type="text" name="value" class="form-input admin-setting-input"
                 value="<?= htmlspecialchars($row['value']) ?>" />
        <?php endif; ?>
        <button type="submit" class="admin-btn admin-btn-sm admin-btn-primary">Save</button>
      </div>
    </form>
    <?php endforeach; ?>
  </div>
</div>

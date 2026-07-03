<div class="admin-page-header">
  <h1 class="admin-page-title">Performer Payouts</h1>
</div>

<div class="admin-dashboard-grid">

  <!-- Performers with pending balance -->
  <div class="admin-panel">
    <div class="admin-panel-header">
      <h2 class="admin-panel-title">Pending Balances</h2>
    </div>
    <?php if (empty($pendingBalances)): ?>
      <p class="admin-empty">No pending balances.</p>
    <?php else: ?>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead><tr><th>Performer</th><th>Balance (ZAR)</th><th>Process Payout</th></tr></thead>
        <tbody>
          <?php foreach ($pendingBalances as $pb): ?>
          <tr>
            <td><?= htmlspecialchars($pb['display_name']) ?></td>
            <td style="color:var(--gold); font-weight:600;">R <?= number_format((float)$pb['earnings_balance'], 2) ?></td>
            <td>
              <button class="admin-btn admin-btn-sm admin-btn-primary"
                      onclick="openPayoutModal(<?= (int)$pb['id'] ?>, '<?= htmlspecialchars(addslashes($pb['display_name'])) ?>', <?= (float)$pb['earnings_balance'] ?>)">
                Pay Out
              </button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

  <!-- Payout history -->
  <div class="admin-panel">
    <div class="admin-panel-header">
      <h2 class="admin-panel-title">Payout History</h2>
    </div>
    <?php if (empty($payouts)): ?>
      <p class="admin-empty">No payouts processed yet.</p>
    <?php else: ?>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead><tr><th>Performer</th><th>Amount</th><th>Method</th><th>Reference</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
          <?php foreach ($payouts as $po): ?>
          <tr>
            <td><?= htmlspecialchars($po['performer_name']) ?></td>
            <td>R <?= number_format((float)$po['amount'], 2) ?></td>
            <td style="text-transform:uppercase; font-size:.7rem;"><?= htmlspecialchars($po['method']) ?></td>
            <td style="font-size:.7rem; color:rgba(196,184,150,.4);"><?= htmlspecialchars($po['reference'] ?? '—') ?></td>
            <td><span class="status-pill status-<?= htmlspecialchars($po['status']) ?>"><?= ucfirst($po['status']) ?></span></td>
            <td style="font-size:.72rem; color:rgba(196,184,150,.4);"><?= date('d M Y', strtotime($po['created_at'])) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

</div>

<!-- Payout modal -->
<div id="payout-modal" class="admin-modal" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="payout-modal-title">
  <div class="admin-modal-card">
    <h2 class="admin-modal-title" id="payout-modal-title">Process Payout</h2>
    <form method="POST" action="<?= BASE_PATH ?>/admin/payout/process" class="auth-form">
      <?= \App\Core\CSRF::field() ?>
      <input type="hidden" name="performer_id" id="payout-performer-id" value="">

      <div class="form-group">
        <label class="form-label">Performer</label>
        <input type="text" id="payout-performer-name" class="form-input" disabled />
      </div>
      <div class="form-group">
        <label for="payout-amount" class="form-label">Amount (ZAR)</label>
        <div class="input-wrap">
          <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
          <input type="number" id="payout-amount" name="amount" class="form-input"
                 step="0.01" min="0.01" required />
        </div>
      </div>
      <div class="form-group">
        <label for="payout-method" class="form-label">Method</label>
        <select name="method" id="payout-method" class="form-input" style="padding-left:1rem;">
          <option value="eft">EFT</option>
          <option value="bank_transfer">Bank Transfer</option>
          <option value="paypal">PayPal</option>
          <option value="other">Other</option>
        </select>
      </div>
      <div class="form-group">
        <label for="payout-ref" class="form-label">Reference <span class="form-label-note">(optional)</span></label>
        <div class="input-wrap">
          <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
          <input type="text" id="payout-ref" name="reference" class="form-input" placeholder="Bank ref / transaction ID" />
        </div>
      </div>
      <div style="display:flex; gap:.75rem; margin-top:.5rem;">
        <button type="submit" class="btn-primary" style="flex:1;">Confirm Payout</button>
        <button type="button" class="btn-ghost" onclick="closePayoutModal()" style="flex:1;">Cancel</button>
      </div>
    </form>
  </div>
</div>
<div id="payout-overlay" onclick="closePayoutModal()" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.7); z-index:1998;"></div>

<script>
function openPayoutModal(id, name, balance) {
  document.getElementById('payout-performer-id').value   = id;
  document.getElementById('payout-performer-name').value = name;
  document.getElementById('payout-amount').value         = balance.toFixed(2);
  document.getElementById('payout-modal').style.display  = 'flex';
  document.getElementById('payout-overlay').style.display = 'block';
  document.body.style.overflow = 'hidden';
}
function closePayoutModal() {
  document.getElementById('payout-modal').style.display  = 'none';
  document.getElementById('payout-overlay').style.display = 'none';
  document.body.style.overflow = '';
}
</script>

<?php use App\Core\Lang; ?>

<div class="credits-page">
  <div class="credits-inner" style="max-width:520px;">
    <div class="confirm-card" style="text-align:center;">

      <div class="payment-result-icon payment-success-icon" aria-hidden="true">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="20 6 9 17 4 12"/></svg>
      </div>

      <h1 class="confirm-title">Payment Successful</h1>
      <p class="confirm-subtitle">Your credits have been added to your account.</p>

      <?php if (!empty($ref)): ?>
        <div class="confirm-reference" style="margin:1.5rem 0;">
          <span class="confirm-ref-label">Reference</span>
          <span class="confirm-ref-value"><?= htmlspecialchars($ref) ?></span>
        </div>
      <?php endif; ?>

      <div class="credits-balance-display" style="margin:1.5rem 0;">
        <span class="credits-balance-value"><?= number_format($balance, 2) ?></span>
        <span class="credits-balance-unit">credits</span>
      </div>
      <p style="font-size:.75rem; color:rgba(196,184,150,.4); margin-bottom:2rem;">Your current balance</p>

      <div style="display:flex; flex-direction:column; gap:.75rem;">
        <a href="<?= BASE_PATH ?>/performers" class="btn-primary btn-block btn-lg">Browse Performers</a>
        <a href="<?= BASE_PATH ?>/account?tab=history" class="btn-ghost btn-block">View Transaction History</a>
      </div>

    </div>
  </div>
</div>

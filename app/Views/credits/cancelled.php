<?php use App\Core\Lang; ?>

<div class="credits-page">
  <div class="credits-inner" style="max-width:520px;">
    <div class="confirm-card" style="text-align:center;">

      <div class="payment-result-icon payment-cancel-icon" aria-hidden="true">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
      </div>

      <h1 class="confirm-title">Payment Cancelled</h1>
      <p class="confirm-subtitle">No charge was made. Your account has not been affected.</p>

      <div style="display:flex; flex-direction:column; gap:.75rem; margin-top:2rem;">
        <a href="<?= BASE_PATH ?>/credits" class="btn-primary btn-block btn-lg">Try Again</a>
        <a href="<?= BASE_PATH ?>/" class="btn-ghost btn-block">Return Home</a>
      </div>

    </div>
  </div>
</div>

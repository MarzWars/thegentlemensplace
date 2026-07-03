<?php
use App\Core\Lang;
$credits = (float)$package['credits'] + (float)$package['bonus_credits'];
$showConversion = ($currency !== 'ZAR');
?>

<div class="credits-page">
  <div class="credits-inner" style="max-width:580px;">
    <div class="confirm-card">

      <div class="confirm-header">
        <div class="confirm-icon" aria-hidden="true">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
        </div>
        <h1 class="confirm-title">Confirm Your Purchase</h1>
        <p class="confirm-subtitle">Review your order before proceeding to payment.</p>
      </div>

      <!-- Order summary -->
      <div class="confirm-summary">
        <div class="confirm-row">
          <span class="confirm-label">Package</span>
          <span class="confirm-value"><?= htmlspecialchars($package['name']) ?></span>
        </div>
        <div class="confirm-row">
          <span class="confirm-label">Credits</span>
          <span class="confirm-value">
            <?= number_format((float)$package['credits'], 0) ?>
            <?php if ((float)$package['bonus_credits'] > 0): ?>
              <span class="confirm-bonus">+<?= number_format((float)$package['bonus_credits'], 0) ?> bonus</span>
            <?php endif; ?>
          </span>
        </div>
        <div class="confirm-row">
          <span class="confirm-label">Connection time</span>
          <span class="confirm-value">~<?= number_format($credits, 0) ?> minutes</span>
        </div>

        <?php if ($showConversion): ?>
        <!-- Show display currency price -->
        <div class="confirm-row">
          <span class="confirm-label">Price (<?= htmlspecialchars($currency) ?>)</span>
          <span class="confirm-value" style="color:var(--cream);">
            <?= htmlspecialchars($displaySymbol) ?><?= number_format($displayPrice, 2) ?>
          </span>
        </div>
        <!-- Show ZAR conversion note -->
        <div class="confirm-row confirm-row-total">
          <span class="confirm-label">
            Charged via PayFast
            <span style="display:block; font-size:.6rem; color:rgba(196,184,150,.35); margin-top:2px; text-transform:none; letter-spacing:0;">
              Converted to ZAR at current rate
            </span>
          </span>
          <span class="confirm-value confirm-total-price">R <?= number_format($zarAmount, 2) ?></span>
        </div>
        <?php else: ?>
        <div class="confirm-row confirm-row-total">
          <span class="confirm-label">Total</span>
          <span class="confirm-value confirm-total-price">R <?= number_format($zarAmount, 2) ?></span>
        </div>
        <?php endif; ?>
      </div>

      <!-- Reference -->
      <div class="confirm-reference">
        <span class="confirm-ref-label">Order Reference</span>
        <span class="confirm-ref-value"><?= htmlspecialchars($reference) ?></span>
      </div>

      <!-- Sandbox notice -->
      <?php if (PAYFAST_SANDBOX): ?>
      <div class="form-alert form-alert-success" style="margin-bottom:1.5rem;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <div>
          <strong>Sandbox Mode</strong> — No real payment will be processed.
          In production, "Proceed to Payment" redirects to PayFast's secure checkout in ZAR.
        </div>
      </div>
      <?php endif; ?>

      <!-- Actions -->
      <div class="confirm-actions">
        <?php if (PAYFAST_SANDBOX): ?>
          <form method="POST" action="<?= BASE_PATH ?>/payment/simulate" style="margin-bottom: 0.5rem;">
            <?= \App\Core\CSRF::field() ?>
            <input type="hidden" name="uuid" value="<?= htmlspecialchars($uuid) ?>">
            <button type="submit" class="btn-primary btn-block btn-lg confirm-pay-btn">
              Simulate Payment (Offline Sandbox)
            </button>
          </form>
          <form method="POST" action="<?= BASE_PATH ?>/payment/checkout">
            <?= \App\Core\CSRF::field() ?>
            <input type="hidden" name="uuid" value="<?= htmlspecialchars($uuid) ?>">
            <button type="submit" class="btn-ghost btn-block" style="border: 1px solid var(--gold); color: var(--gold);">
              Pay via PayFast Sandbox Gateway (Online)
            </button>
          </form>
        <?php else: ?>
          <form method="POST" action="<?= BASE_PATH ?>/payment/checkout">
            <?= \App\Core\CSRF::field() ?>
            <input type="hidden" name="uuid" value="<?= htmlspecialchars($uuid) ?>">
            <button type="submit" class="btn-primary btn-block btn-lg confirm-pay-btn">
              Proceed to Secure Payment
            </button>
          </form>
        <?php endif; ?>
        <a href="<?= BASE_PATH ?>/credits" class="btn-ghost btn-block" style="margin-top:.75rem; text-align:center;">
          ← Back to Packages
        </a>
      </div>

      <p class="confirm-legal">
        By proceeding you agree to our <a href="<?= BASE_PATH ?>/terms">Terms of Service</a>.
        Payment processed securely by PayFast in ZAR. We never store card details.
      </p>

    </div>
  </div>
</div>

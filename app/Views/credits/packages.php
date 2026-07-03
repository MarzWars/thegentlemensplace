<?php
use App\Core\Lang;
use App\Services\CurrencyService;
$symbol = CurrencyService::symbol($currency);
?>

<!-- ── Balance hero ── -->
<div class="credits-hero">
  <div class="credits-hero-inner">
    <p class="section-eyebrow" style="justify-content:center; margin-bottom:.75rem;">Your Balance</p>
    <div class="credits-balance-display">
      <span class="credits-balance-value"><?= number_format($balance, 2) ?></span>
      <span class="credits-balance-unit">credits</span>
    </div>
    <p class="credits-hero-sub">
      <?php if ($balance >= 1): ?>
        Approximately <strong><?= number_format($balance, 0) ?> minutes</strong> of connection time available.
      <?php else: ?>
        Purchase credits below to start connecting with performers.
      <?php endif; ?>
    </p>

    <!-- Currency switcher -->
    <div class="credits-currency-row">
      <span class="credits-currency-label">Display prices in:</span>
      <?php foreach (CurrencyService::CURRENCIES as $code => $info): ?>
        <a href="?currency=<?= $code ?>"
           class="credits-currency-btn <?= $code === $currency ? 'active' : '' ?>">
          <?= htmlspecialchars($info['symbol']) ?> <?= $code ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- ── Packages ── -->
<div class="credits-page">
  <div class="credits-inner">

    <div class="credits-section-header">
      <p class="section-eyebrow">Choose a Package</p>
      <h1 class="section-heading">Simple <em>Credit</em> Packages</h1>
      <p class="section-body">No subscriptions. No hidden fees. Credits never expire.</p>
    </div>

    <?php if (!empty($packages[0]['_demo'])): ?>
    <div class="demo-notice" style="margin-bottom:2rem;">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      Sandbox / testing mode — no real payment will be taken.
    </div>
    <?php endif; ?>

    <div class="credits-packages-grid">
      <?php foreach ($packages as $pkg):
        $credits       = (float)$pkg['credits'];
        $bonus         = (float)$pkg['bonus_credits'];
        $total         = $credits + $bonus;
        $displayPrice  = $pkg['display_price'];
        $sym           = $pkg['display_symbol'];
        $zarAmount     = $pkg['price_zar_actual'];
        $isFeatured    = (bool)$pkg['is_featured'];
        $minutesApprox = number_format($total, 0);
      ?>
      <div class="credits-package-card <?= $isFeatured ? 'package-featured' : '' ?>">
        <?php if ($isFeatured): ?>
          <div class="package-badge">Most Popular</div>
        <?php endif; ?>

        <div class="package-name"><?= htmlspecialchars($pkg['name']) ?></div>

        <div class="package-price">
          <span class="package-currency"><?= htmlspecialchars($sym) ?></span><?= number_format($displayPrice, 2) ?>
        </div>

        <?php if ($currency !== 'ZAR'): ?>
        <div class="package-zar-note">≈ R <?= number_format($zarAmount, 2) ?> charged via PayFast</div>
        <?php endif; ?>

        <div class="package-credits-display">
          <span class="package-credits-main"><?= number_format($credits, 0) ?></span>
          <span class="package-credits-label">credits</span>
          <?php if ($bonus > 0): ?>
            <span class="package-bonus-tag">+<?= number_format($bonus, 0) ?> bonus</span>
          <?php endif; ?>
        </div>

        <div class="package-minutes">~<?= $minutesApprox ?> minutes of connection time</div>

        <ul class="package-perks">
          <li>
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
            Access all performers
          </li>
          <li>
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
            Credits never expire
          </li>
          <?php if ($bonus > 0): ?>
          <li>
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
            <?= number_format($bonus, 0) ?> bonus credits included
          </li>
          <?php endif; ?>
          <?php if ($isFeatured): ?>
          <li>
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
            Priority performer matching
          </li>
          <?php endif; ?>
        </ul>

        <form method="POST" action="<?= BASE_PATH ?>/credits/purchase">
          <?= \App\Core\CSRF::field() ?>
          <input type="hidden" name="package_id" value="<?= (int)$pkg['id'] ?>">
          <input type="hidden" name="currency"   value="<?= htmlspecialchars($currency) ?>">
          <button type="submit" class="<?= $isFeatured ? 'btn-primary' : 'btn-ghost' ?> btn-block package-buy-btn">
            <?= $isFeatured ? 'Choose Plan' : 'Get Started' ?>
          </button>
        </form>

      </div>
      <?php endforeach; ?>
    </div>

    <!-- Trust strip -->
    <div class="credits-value-row">
      <div class="credits-value-item">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        <span>100% Discreet billing</span>
      </div>
      <div class="credits-value-item">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
        <span>Secure payment via PayFast</span>
      </div>
      <div class="credits-value-item">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <span>Credits never expire</span>
      </div>
      <div class="credits-value-item">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
        <span>Instant credit top-up</span>
      </div>
    </div>

    <!-- Recent transactions -->
    <?php if (!empty($recentTx)): ?>
    <div class="credits-recent">
      <h2 class="account-sub-heading">Recent Purchases</h2>
      <div class="account-table-wrap">
        <table class="account-table">
          <thead>
            <tr>
              <th>Package</th><th>Date</th><th>Charged (ZAR)</th><th>Credits</th><th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentTx as $tx): ?>
            <tr>
              <td><?= htmlspecialchars($tx['package_name'] ?? $tx['item_name']) ?></td>
              <td><?= date('d M Y', strtotime($tx['created_at'])) ?></td>
              <td>R <?= number_format((float)$tx['amount_zar'], 2) ?></td>
              <td><?= number_format((float)$tx['credits_purchased'] + (float)$tx['bonus_credits'], 0) ?></td>
              <td><span class="status-pill status-<?= htmlspecialchars($tx['status']) ?>"><?= ucfirst($tx['status']) ?></span></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div style="margin-top:1rem; text-align:right;">
        <a href="<?= BASE_PATH ?>/credits/history" style="font-size:.65rem; letter-spacing:.15em; text-transform:uppercase; color:var(--gold-dim); text-decoration:none;">
          View Full History →
        </a>
      </div>
    </div>
    <?php endif; ?>

    <p class="credits-legal-note">
      1 credit = 1 minute. Rates vary per performer.
      <?php if ($currency !== 'ZAR'): ?>
        Prices shown in <?= $currency ?> are indicative. PayFast charges in ZAR at the current exchange rate.
      <?php endif; ?>
      By purchasing you agree to our <a href="<?= BASE_PATH ?>/terms">Terms of Service</a>.
    </p>

  </div>
</div>

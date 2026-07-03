<?php
use App\Core\Lang;
use App\Services\CurrencyService;
$histCurrency = defined('CURRENCY') ? CURRENCY : 'EUR';
$zarSymbol    = CurrencyService::symbol('ZAR');
?>

<div class="credits-hero">
  <div class="credits-hero-inner">
    <p class="section-eyebrow" style="justify-content:center; margin-bottom:.75rem;">Current Balance</p>
    <div class="credits-balance-display">
      <span class="credits-balance-value"><?= number_format($balance, 2) ?></span>
      <span class="credits-balance-unit">credits</span>
    </div>
  </div>
</div>

<div class="credits-page">
  <div class="credits-inner">

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:2rem; flex-wrap:wrap; gap:1rem;">
      <h1 class="section-heading" style="margin:0;">Credit <em>History</em></h1>
      <a href="<?= BASE_PATH ?>/credits" class="btn-primary">Buy More Credits</a>
    </div>

    <!-- Purchases -->
    <h2 class="account-sub-heading">Purchases</h2>
    <?php if (empty($transactions)): ?>
      <div class="account-empty" style="margin-bottom:2.5rem;">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
        <p>No purchases yet. <a href="<?= BASE_PATH ?>/credits">Buy your first credits.</a></p>
      </div>
    <?php else: ?>
      <div class="account-table-wrap" style="margin-bottom:2.5rem;">
        <table class="account-table">
          <thead>
            <tr>
              <th>Reference</th>
              <th>Package</th>
              <th>Date</th>
              <th>Charged (ZAR)</th>
              <th>Approx. Display</th>
              <th>Credits</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($transactions as $tx): ?>
            <tr>
              <td style="font-size:.65rem; letter-spacing:.08em; color:rgba(196,184,150,.4);">
                <?= htmlspecialchars($tx['merchant_reference']) ?>
              </td>
              <td><?= htmlspecialchars($tx['package_name'] ?? $tx['item_name']) ?></td>
              <td><?= date('d M Y, H:i', strtotime($tx['created_at'])) ?></td>
              <td><?= $zarSymbol ?> <?= number_format((float)$tx['amount_zar'], 2) ?></td>
              <td style="color:var(--gold-dim); font-size:.78rem;">
                <?= CurrencyService::format(
                      CurrencyService::fromZAR((float)$tx['amount_zar'], $histCurrency),
                      $histCurrency
                    ) ?>
              </td>
              <td>
                <?= number_format((float)$tx['credits_purchased'], 0) ?>
                <?php if ((float)$tx['bonus_credits'] > 0): ?>
                  <span style="font-size:.6rem; color:var(--gold-dim);">+<?= number_format((float)$tx['bonus_credits'], 0) ?></span>
                <?php endif; ?>
              </td>
              <td><span class="status-pill status-<?= htmlspecialchars($tx['status']) ?>"><?= ucfirst($tx['status']) ?></span></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

    <!-- Credit ledger -->
    <h2 class="account-sub-heading">Credit Ledger</h2>
    <?php if (empty($ledger)): ?>
      <div class="account-empty">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
        <p>No ledger entries yet.</p>
      </div>
    <?php else: ?>
      <div class="account-table-wrap">
        <table class="account-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Type</th>
              <th>Amount</th>
              <th>Balance After</th>
              <th>Notes</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($ledger as $entry):
              $isDebit = (float)$entry['amount'] < 0;
            ?>
            <tr>
              <td><?= date('d M Y, H:i', strtotime($entry['created_at'])) ?></td>
              <td>
                <span class="status-pill <?= $isDebit ? 'status-failed' : 'status-completed' ?>">
                  <?= ucfirst(str_replace('_', ' ', $entry['type'])) ?>
                </span>
              </td>
              <td style="color:<?= $isDebit ? 'var(--error-lt)' : 'var(--success-lt)' ?>; font-weight:600;">
                <?= $isDebit ? '' : '+' ?><?= number_format((float)$entry['amount'], 4) ?>
              </td>
              <td><?= number_format((float)$entry['balance_after'], 4) ?></td>
              <td style="font-size:.72rem; color:rgba(196,184,150,.4);">
                <?= htmlspecialchars($entry['notes'] ?? '—') ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

  </div>
</div>

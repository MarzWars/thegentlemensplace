<?php use App\Services\CurrencyService; ?>

<div class="admin-page-header">
  <h1 class="admin-page-title">Transactions</h1>
  <div style="font-size:.7rem; color:rgba(196,184,150,.35); letter-spacing:.1em;">
    Amounts stored in ZAR · Display: <?= htmlspecialchars($currency) ?>
  </div>
</div>

<?php if (empty($transactions)): ?>
  <p class="admin-empty">No transactions yet.</p>
<?php else: ?>
<div class="admin-table-wrap">
  <table class="admin-table">
    <thead>
      <tr>
        <th>Reference</th><th>User</th><th>Package</th>
        <th>ZAR</th><th><?= htmlspecialchars($currency) ?></th>
        <th>Credits</th><th>Status</th><th>Date</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($transactions as $tx): ?>
      <tr>
        <td style="font-size:.65rem; color:rgba(196,184,150,.4); letter-spacing:.08em;">
          <?= htmlspecialchars($tx['merchant_reference']) ?>
        </td>
        <td><?= htmlspecialchars($tx['username']) ?></td>
        <td><?= htmlspecialchars($tx['package_name'] ?? $tx['item_name']) ?></td>
        <td>R <?= number_format((float)$tx['amount_zar'], 2) ?></td>
        <td style="color:var(--gold-dim);">
          <?= CurrencyService::format(
                CurrencyService::fromZAR((float)$tx['amount_zar'], $currency),
                $currency
              ) ?>
        </td>
        <td>
          <?= number_format((float)$tx['credits_purchased'], 0) ?>
          <?php if ((float)$tx['bonus_credits'] > 0): ?>
            <span style="font-size:.6rem; color:var(--gold-dim);">+<?= number_format((float)$tx['bonus_credits'], 0) ?></span>
          <?php endif; ?>
        </td>
        <td><span class="status-pill status-<?= htmlspecialchars($tx['status']) ?>"><?= ucfirst($tx['status']) ?></span></td>
        <td style="font-size:.72rem; color:rgba(196,184,150,.4);">
          <?= date('d M Y, H:i', strtotime($tx['created_at'])) ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

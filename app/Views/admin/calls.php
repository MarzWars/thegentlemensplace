<div class="admin-page-header">
  <h1 class="admin-page-title">Calls</h1>
</div>

<?php if (empty($calls)): ?>
  <p class="admin-empty">No calls yet.</p>
<?php else: ?>
<div class="admin-table-wrap">
  <table class="admin-table">
    <thead>
      <tr>
        <th>User</th><th>Performer</th><th>Duration</th>
        <th>Credits Used</th><th>Rate/min</th><th>Status</th><th>Date</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($calls as $call): ?>
      <tr>
        <td><?= htmlspecialchars($call['username']) ?></td>
        <td><?= htmlspecialchars($call['performer_name']) ?></td>
        <td><?= $call['duration_seconds'] ? gmdate('i:s', $call['duration_seconds']) : '—' ?></td>
        <td><?= number_format((float)$call['credits_used'], 2) ?></td>
        <td><?= number_format((float)$call['rate_per_minute'], 0) ?> cr</td>
        <td><span class="status-pill status-<?= htmlspecialchars($call['status']) ?>"><?= ucfirst(str_replace('_',' ',$call['status'])) ?></span></td>
        <td style="font-size:.72rem; color:rgba(196,184,150,.4);">
          <?= date('d M Y, H:i', strtotime($call['created_at'])) ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

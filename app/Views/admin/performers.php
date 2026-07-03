<div class="admin-page-header">
  <h1 class="admin-page-title">Performers</h1>
  <div style="display:flex; gap:1rem; align-items:center; flex-wrap:wrap;">
    <div class="admin-filter-tabs">
      <?php foreach (['all' => 'All', 'pending' => 'Pending', 'active' => 'Active', 'suspended' => 'Suspended'] as $val => $label): ?>
        <a href="?filter=<?= $val ?>" class="admin-filter-tab <?= $filter === $val ? 'active' : '' ?>"><?= $label ?></a>
      <?php endforeach; ?>
    </div>
    <button class="admin-btn admin-btn-primary" onclick="openAddPerformerModal()">+ Add Performer</button>
  </div>
</div>

<?php if (empty($performers)): ?>
  <p class="admin-empty">No performers found for this filter.</p>
<?php else: ?>
<div class="admin-table-wrap">
  <table class="admin-table">
    <thead>
      <tr>
        <th>Name</th><th>Age</th><th>Category</th><th>Rate</th>
        <th>Rating</th><th>Calls</th><th>Status</th><th>Online</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($performers as $p): ?>
      <tr>
        <td>
          <a href="<?= BASE_PATH ?>/performer/<?= htmlspecialchars($p['slug']) ?>" target="_blank" style="color:var(--gold-dim); font-weight:600;">
            <?= htmlspecialchars($p['display_name']) ?>
          </a>
        </td>
        <td><?= (int)$p['age'] ?></td>
        <td style="font-size:.72rem;"><?= htmlspecialchars($p['category']) ?></td>
        <td><?= number_format((float)$p['rate_per_minute'], 0) ?> cr/min</td>
        <td><?= number_format((float)$p['rating_avg'], 1) ?> ★</td>
        <td><?= number_format((int)$p['total_calls']) ?></td>
        <td><span class="status-pill status-<?= htmlspecialchars($p['status']) ?>"><?= ucfirst(str_replace('_',' ',$p['status'])) ?></span></td>
        <td>
          <span class="admin-online-dot <?= $p['online_status'] ? 'is-online' : 'is-offline' ?>"></span>
          <?= $p['online_status'] ? 'Online' : 'Offline' ?>
        </td>
        <td class="admin-actions-cell">
          <!-- Edit Performer Button -->
          <button class="admin-btn admin-btn-sm admin-btn-primary"
                  onclick="openEditPerformerModal(
                    <?= (int)$p['id'] ?>,
                    '<?= htmlspecialchars(addslashes($p['display_name'])) ?>',
                    <?= (int)$p['age'] ?>,
                    '<?= htmlspecialchars(addslashes($p['phone_number'])) ?>',
                    <?= (float)$p['rate_per_minute'] ?>,
                    '<?= htmlspecialchars(addslashes($p['languages'])) ?>',
                    '<?= htmlspecialchars($p['category']) ?>',
                    '<?= htmlspecialchars(addslashes($p['bio'] ?? '')) ?>',
                    '<?= htmlspecialchars($p['profile_photo'] ?? '') ?>',
                    '<?= htmlspecialchars($p['cover_photo'] ?? '') ?>',
                    '<?= htmlspecialchars($p['short_video'] ?? '') ?>',
                    '<?= htmlspecialchars($p['voice_sample'] ?? '') ?>',
                    <?= (int)$p['video_enabled'] ?>,
                    <?= (float)$p['video_min_credits'] ?>,
                    <?= (int)$p['video_min_minutes'] ?>,
                    <?= (float)$p['video_rate_per_minute'] ?>
                  )">
            Edit
          </button>

          <?php if ($p['status'] === 'pending_approval'): ?>
            <form method="POST" action="<?= BASE_PATH ?>/admin/performer/approve/<?= (int)$p['id'] ?>" style="display:inline;">
              <?= \App\Core\CSRF::field() ?>
              <button type="submit" class="admin-btn admin-btn-sm admin-btn-success">Approve</button>
            </form>
          <?php endif; ?>
          <?php if ($p['status'] === 'active'): ?>
            <form method="POST" action="<?= BASE_PATH ?>/admin/performer/suspend/<?= (int)$p['id'] ?>" style="display:inline;">
              <?= \App\Core\CSRF::field() ?>
              <button type="submit" class="admin-btn admin-btn-sm admin-btn-danger"
                      onclick="return confirm('Suspend <?= htmlspecialchars(addslashes($p['display_name'])) ?>?')">
                Suspend
              </button>
            </form>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<!-- Add Performer Modal -->
<div id="add-performer-modal" class="admin-modal" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="add-performer-title">
  <div class="admin-modal-card" style="max-width:540px;">
    <h2 class="admin-modal-title" id="add-performer-title">Add New Performer</h2>
    <form method="POST" action="<?= BASE_PATH ?>/admin/performers/add" enctype="multipart/form-data" class="auth-form">
      <?= \App\Core\CSRF::field() ?>

      <div class="form-row">
        <div class="form-group">
          <label for="perf-username" class="form-label">Username</label>
          <div class="input-wrap">
            <svg class="input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <input type="text" id="perf-username" name="username" class="form-input" placeholder="model_username" required style="padding-left:2.4rem;" />
          </div>
        </div>
        <div class="form-group">
          <label for="perf-email" class="form-label">Email Address</label>
          <div class="input-wrap">
            <svg class="input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            <input type="email" id="perf-email" name="email" class="form-input" placeholder="model@thegentlemensplace.eu" required style="padding-left:2.4rem;" />
          </div>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="perf-password" class="form-label">Password (min 10 chars)</label>
          <div class="input-wrap">
            <svg class="input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <input type="password" id="perf-password" name="password" class="form-input" placeholder="Strong password" required style="padding-left:2.4rem;" />
          </div>
        </div>
        <div class="form-group">
          <label for="perf-display" class="form-label">Display Name</label>
          <div class="input-wrap">
            <svg class="input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2a10 10 0 1 0 10 10H12V2z"/><path d="M12 2a10 10 0 0 1 10 10h-10V2z"/></svg>
            <input type="text" id="perf-display" name="display_name" class="form-input" placeholder="Stage Name" required style="padding-left:2.4rem;" />
          </div>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="perf-age" class="form-label">Age</label>
          <div class="input-wrap">
            <svg class="input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <input type="number" id="perf-age" name="age" class="form-input" placeholder="21" min="18" required style="padding-left:2.4rem;" />
          </div>
        </div>
        <div class="form-group">
          <label for="perf-phone" class="form-label">Phone Number</label>
          <div class="input-wrap">
            <svg class="input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.8a16 16 0 0 0 6.29 6.29l.95-.95a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            <input type="text" id="perf-phone" name="phone_number" class="form-input" placeholder="+27821234567" required style="padding-left:2.4rem;" />
          </div>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="perf-rate" class="form-label">Rate per Minute (Credits)</label>
          <div class="input-wrap">
            <svg class="input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
            <input type="number" id="perf-rate" name="rate_per_minute" class="form-input" placeholder="5" min="1" step="0.5" required style="padding-left:2.4rem;" />
          </div>
        </div>
        <div class="form-group">
          <label for="perf-languages" class="form-label">Languages</label>
          <div class="input-wrap">
            <svg class="input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            <input type="text" id="perf-languages" name="languages" class="form-input" placeholder="English, Afrikaans" required style="padding-left:2.4rem;" />
          </div>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="perf-category" class="form-label">Primary Category</label>
          <select name="category" id="perf-category" class="form-input" style="padding-left:1rem;">
            <option value="chat">Chat</option>
            <option value="roleplay">Roleplay</option>
            <option value="fantasy">Fantasy</option>
            <option value="couples">Couples</option>
            <option value="mature">Mature</option>
            <option value="fetish">Fetish</option>
          </select>
        </div>
        <div class="form-group">
          <label for="perf-photo" class="form-label">Profile Photo</label>
          <div class="input-wrap">
            <svg class="input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 19V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2z"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            <input type="file" id="perf-photo" name="photo" class="form-input" accept="image/jpeg,image/png,image/webp" style="padding-left:2.4rem;" />
          </div>
        </div>
      </div>

      <div class="form-section-title" style="font-size: 0.7rem; color: var(--gold); text-transform: uppercase; margin-top: 1rem; border-top: 1px solid rgba(201,168,76,.1); padding-top: 0.8rem; font-weight: 700; letter-spacing: 0.1em;">Video Call Settings</div>
      <div class="form-row">
        <div class="form-group" style="flex-direction: row; align-items: center; gap: 0.5rem; justify-content: flex-start; margin-top: 0.5rem;">
          <input type="checkbox" id="perf-video-enabled" name="video_enabled" value="1" checked />
          <label for="perf-video-enabled" class="form-label" style="margin: 0; cursor: pointer;">Enable Video Calls</label>
        </div>
        <div class="form-group">
          <label for="perf-video-rate" class="form-label">Video Rate per Minute After (Credits)</label>
          <div class="input-wrap">
            <svg class="input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
            <input type="number" id="perf-video-rate" name="video_rate_per_minute" class="form-input" placeholder="5" min="0" step="0.5" required value="5" style="padding-left:2.4rem;" />
          </div>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="perf-video-min-credits" class="form-label">Video Min Entry Fee (Credits)</label>
          <div class="input-wrap">
            <svg class="input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
            <input type="number" id="perf-video-min-credits" name="video_min_credits" class="form-input" placeholder="15" min="0" step="0.5" required value="15" style="padding-left:2.4rem;" />
          </div>
        </div>
        <div class="form-group">
          <label for="perf-video-min-minutes" class="form-label">Video Included Minutes</label>
          <div class="input-wrap">
            <svg class="input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
            <input type="number" id="perf-video-min-minutes" name="video_min_minutes" class="form-input" placeholder="10" min="1" step="1" required value="10" style="padding-left:2.4rem;" />
          </div>
        </div>
      </div>

      <div style="display:flex; gap:.75rem; margin-top:1.5rem;">
        <button type="submit" class="btn-primary" style="flex:1;">Create Performer</button>
        <button type="button" class="btn-ghost" onclick="closeAddPerformerModal()" style="flex:1;">Cancel</button>
      </div>
    </form>
  </div>
</div>
<div id="add-performer-overlay" onclick="closeAddPerformerModal()" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.7); z-index:1998;"></div>

<!-- Edit Performer Modal -->
<div id="edit-performer-modal" class="admin-modal" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="edit-performer-title">
  <div class="admin-modal-card" style="max-width:540px;">
    <h2 class="admin-modal-title" id="edit-performer-title">Edit Performer Profile</h2>
    <form method="POST" id="edit-performer-form" action="" enctype="multipart/form-data" class="auth-form">
      <?= \App\Core\CSRF::field() ?>

      <div class="form-row">
        <div class="form-group">
          <label for="edit-perf-display" class="form-label">Display Name</label>
          <div class="input-wrap">
            <svg class="input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2a10 10 0 1 0 10 10H12V2z"/><path d="M12 2a10 10 0 0 1 10 10h-10V2z"/></svg>
            <input type="text" id="edit-perf-display" name="display_name" class="form-input" required style="padding-left:2.4rem;" />
          </div>
        </div>
        <div class="form-group">
          <label for="edit-perf-age" class="form-label">Age</label>
          <div class="input-wrap">
            <svg class="input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <input type="number" id="edit-perf-age" name="age" class="form-input" min="18" required style="padding-left:2.4rem;" />
          </div>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="edit-perf-phone" class="form-label">Phone Number</label>
          <div class="input-wrap">
            <svg class="input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.8a16 16 0 0 0 6.29 6.29l.95-.95a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            <input type="text" id="edit-perf-phone" name="phone_number" class="form-input" required style="padding-left:2.4rem;" />
          </div>
        </div>
        <div class="form-group">
          <label for="edit-perf-rate" class="form-label">Rate per Minute (Credits)</label>
          <div class="input-wrap">
            <svg class="input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
            <input type="number" id="edit-perf-rate" name="rate_per_minute" class="form-input" min="1" step="0.5" required style="padding-left:2.4rem;" />
          </div>
        </div>
      </div>

      <div class="form-group">
        <label for="edit-perf-languages" class="form-label">Languages</label>
        <div class="input-wrap">
          <svg class="input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          <input type="text" id="edit-perf-languages" name="languages" class="form-input" required style="padding-left:2.4rem;" />
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="edit-perf-category" class="form-label">Primary Category</label>
          <select name="category" id="edit-perf-category" class="form-input" style="padding-left:1rem;">
            <option value="chat">Chat</option>
            <option value="roleplay">Roleplay</option>
            <option value="fantasy">Fantasy</option>
            <option value="couples">Couples</option>
            <option value="mature">Mature</option>
            <option value="fetish">Fetish</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Upload New Photos (Optional)</label>
          <div style="display:flex; gap:.5rem;">
            <input type="file" name="profile_photo" class="form-input" accept="image/*" placeholder="Profile" style="padding-left:.5rem; font-size:.65rem;" />
            <input type="file" name="cover_photo" class="form-input" accept="image/*" placeholder="Cover" style="padding-left:.5rem; font-size:.65rem;" />
          </div>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="edit-perf-video" class="form-label">Short Video (10s MP4, Max 20MB)</label>
          <input type="file" id="edit-perf-video" name="short_video" class="form-input" accept="video/mp4" style="padding-left:1rem; font-size:.65rem;" />
        </div>
        <div class="form-group">
          <label for="edit-perf-voice" class="form-label">Voice Sample (MP3/MP4, Max 10MB)</label>
          <input type="file" id="edit-perf-voice" name="voice_sample" class="form-input" accept="audio/*,video/mp4" style="padding-left:1rem; font-size:.65rem;" />
        </div>
      </div>

      <div class="form-section-title" style="font-size: 0.7rem; color: var(--gold); text-transform: uppercase; margin-top: 1rem; border-top: 1px solid rgba(201,168,76,.1); padding-top: 0.8rem; font-weight: 700; letter-spacing: 0.1em;">Video Call Settings</div>
      <div class="form-row">
        <div class="form-group" style="flex-direction: row; align-items: center; gap: 0.5rem; justify-content: flex-start; margin-top: 0.5rem;">
          <input type="checkbox" id="edit-perf-video-enabled" name="video_enabled" value="1" />
          <label for="edit-perf-video-enabled" class="form-label" style="margin: 0; cursor: pointer;">Enable Video Calls</label>
        </div>
        <div class="form-group">
          <label for="edit-perf-video-rate" class="form-label">Video Rate per Minute After (Credits)</label>
          <div class="input-wrap">
            <svg class="input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
            <input type="number" id="edit-perf-video-rate" name="video_rate_per_minute" class="form-input" min="0" step="0.5" required style="padding-left:2.4rem;" />
          </div>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="edit-perf-video-min-credits" class="form-label">Video Min Entry Fee (Credits)</label>
          <div class="input-wrap">
            <svg class="input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
            <input type="number" id="edit-perf-video-min-credits" name="video_min_credits" class="form-input" min="0" step="0.5" required style="padding-left:2.4rem;" />
          </div>
        </div>
        <div class="form-group">
          <label for="edit-perf-video-min-minutes" class="form-label">Video Included Minutes</label>
          <div class="input-wrap">
            <svg class="input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
            <input type="number" id="edit-perf-video-min-minutes" name="video_min_minutes" class="form-input" min="1" step="1" required style="padding-left:2.4rem;" />
          </div>
        </div>
      </div>

      <div class="form-group">
        <label for="edit-perf-bio" class="form-label">Bio</label>
        <textarea id="edit-perf-bio" name="bio" class="form-input" style="min-height:90px; padding:1rem; font-family:sans-serif;" placeholder="Biography summary..."></textarea>
      </div>

      <div id="edit-media-previews" style="margin-top:1rem; border-top:1px solid rgba(201,168,76,.1); padding-top:1rem; display:none; flex-direction:column; gap:.75rem;">
        <span style="font-size:.6rem; text-transform:uppercase; letter-spacing:.1em; color:var(--gold-dim);">Current Media (Click to delete):</span>
        <div style="display:flex; gap:.75rem; flex-wrap:wrap;">
          <button type="button" id="admin-del-profile" class="admin-btn admin-btn-sm admin-btn-danger" style="display:none; padding:.3rem .6rem; font-size:.62rem;" onclick="deleteAdminMedia('profile_photo')">Delete Profile Photo</button>
          <button type="button" id="admin-del-cover" class="admin-btn admin-btn-sm admin-btn-danger" style="display:none; padding:.3rem .6rem; font-size:.62rem;" onclick="deleteAdminMedia('cover_photo')">Delete Cover Photo</button>
          <button type="button" id="admin-del-video" class="admin-btn admin-btn-sm admin-btn-danger" style="display:none; padding:.3rem .6rem; font-size:.62rem;" onclick="deleteAdminMedia('short_video')">Delete Video</button>
          <button type="button" id="admin-del-voice" class="admin-btn admin-btn-sm admin-btn-danger" style="display:none; padding:.3rem .6rem; font-size:.62rem;" onclick="deleteAdminMedia('voice_sample')">Delete Voice Sample</button>
        </div>
      </div>

      <div style="display:flex; gap:.75rem; margin-top:1.25rem;">
        <button type="submit" class="btn-primary" style="flex:1;">Update Profile</button>
        <button type="button" class="btn-ghost" onclick="closeEditPerformerModal()" style="flex:1;">Cancel</button>
      </div>
    </form>
  </div>
</div>
<div id="edit-performer-overlay" onclick="closeEditPerformerModal()" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.7); z-index:1998;"></div>

<form id="admin-delete-media-form" method="POST" action="" style="display:none;">
  <?= \App\Core\CSRF::field() ?>
  <input type="hidden" name="media_type" value="" />
</form>

<script>
function openAddPerformerModal() {
  document.getElementById('add-performer-modal').style.display = 'flex';
  document.getElementById('add-performer-overlay').style.display = 'block';
  document.body.style.overflow = 'hidden';
}
function closeAddPerformerModal() {
  document.getElementById('add-performer-modal').style.display = 'none';
  document.getElementById('add-performer-overlay').style.display = 'none';
  document.body.style.overflow = '';
}

function openEditPerformerModal(id, displayName, age, phoneNumber, rate, languages, category, bio, profilePhoto, coverPhoto, shortVideo, voiceSample, videoEnabled, videoMinCredits, videoMinMinutes, videoRatePerMinute) {
  document.getElementById('edit-performer-form').action   = '<?= BASE_PATH ?>/admin/performer/edit/' + id;
  document.getElementById('edit-perf-display').value       = displayName;
  document.getElementById('edit-perf-age').value           = age;
  document.getElementById('edit-perf-phone').value         = phoneNumber;
  document.getElementById('edit-perf-rate').value          = rate;
  document.getElementById('edit-perf-languages').value     = languages;
  document.getElementById('edit-perf-category').value      = category;
  document.getElementById('edit-perf-bio').value           = bio;

  // Video call settings fields
  document.getElementById('edit-perf-video-enabled').checked   = parseInt(videoEnabled) === 1;
  document.getElementById('edit-perf-video-rate').value        = videoRatePerMinute;
  document.getElementById('edit-perf-video-min-credits').value = videoMinCredits;
  document.getElementById('edit-perf-video-min-minutes').value = videoMinMinutes;

  // Manage current media indicators
  const previewsDiv = document.getElementById('edit-media-previews');
  const delProfile = document.getElementById('admin-del-profile');
  const delCover = document.getElementById('admin-del-cover');
  const delVideo = document.getElementById('admin-del-video');
  const delVoice = document.getElementById('admin-del-voice');

  document.getElementById('admin-delete-media-form').action = '<?= BASE_PATH ?>/admin/performer/delete-media/' + id;

  let hasMedia = false;
  if (profilePhoto) { delProfile.style.display = 'inline-block'; hasMedia = true; } else { delProfile.style.display = 'none'; }
  if (coverPhoto) { delCover.style.display = 'inline-block'; hasMedia = true; } else { delCover.style.display = 'none'; }
  if (shortVideo) { delVideo.style.display = 'inline-block'; hasMedia = true; } else { delVideo.style.display = 'none'; }
  if (voiceSample) { delVoice.style.display = 'inline-block'; hasMedia = true; } else { delVoice.style.display = 'none'; }

  if (hasMedia) {
    previewsDiv.style.display = 'flex';
  } else {
    previewsDiv.style.display = 'none';
  }

  document.getElementById('edit-performer-modal').style.display   = 'flex';
  document.getElementById('edit-performer-overlay').style.display = 'block';
  document.body.style.overflow = 'hidden';
}
function closeEditPerformerModal() {
  document.getElementById('edit-performer-modal').style.display   = 'none';
  document.getElementById('edit-performer-overlay').style.display = 'none';
  document.body.style.overflow = '';
}
function deleteAdminMedia(mediaType) {
  if (confirm('Are you sure you want to delete this ' + mediaType.replace('_', ' ') + '?')) {
    const form = document.getElementById('admin-delete-media-form');
    form.querySelector('input[name="media_type"]').value = mediaType;
    form.submit();
  }
}
</script>

<?php
use App\Core\Lang;

// ── Prepare variables ─────────────────────────────────────
$name        = htmlspecialchars($performer['display_name']);
$age         = (int)$performer['age'];
$bio         = nl2br(htmlspecialchars($performer['bio'] ?? ''));
$rate        = number_format((float)$performer['rate_per_minute'], 0);
$ratingAvg   = number_format((float)$performer['rating_avg'], 1);
$ratingCount = number_format((int)$performer['rating_count']);
$callCount   = number_format((int)$performer['total_calls']);
$langs       = htmlspecialchars($performer['languages'] ?? 'English');
$online      = (bool)$performer['online_status'];
$cats        = array_filter(array_map('trim', explode(',', $performer['category'])));
$initials    = strtoupper(substr($performer['display_name'], 0, 2));
$hasPhoto    = !empty($performer['profile_photo']);
$isLoggedIn  = !empty($_SESSION['user_id']);
$userCredits = number_format((float)($_SESSION['credits'] ?? 0), 0);
$locale      = Lang::locale();

// Format total talk time from total_minutes
$totalMins = (int)($performer['total_minutes'] ?? 0);
if ($totalMins >= 60) {
    $talkTime = floor($totalMins / 60) . 'h ' . ($totalMins % 60) . 'm';
} elseif ($totalMins > 0) {
    $talkTime = $totalMins . 'm';
} else {
    $talkTime = '—';
}
?>

<div class="profile-page">

  <!-- ══════════════════════════════════════════════════════
       HERO — two-column layout
  ══════════════════════════════════════════════════════ -->
  <div class="profile-hero">

    <!-- ── Left: Photo + gallery ── -->
    <div class="profile-photo-col">

      <!-- Main photo / avatar -->
      <?php if ($hasPhoto): ?>
        <div class="profile-main-photo-wrap">
          <img
            src="<?= BASE_PATH ?>/<?= htmlspecialchars($performer['profile_photo']) ?>"
            alt="<?= $name ?>"
            class="profile-main-photo"
            width="600" height="800"
            loading="eager"
          />
          <!-- Online indicator overlay -->
          <div class="profile-photo-status <?= $online ? 'is-online' : 'is-offline' ?>">
            <span class="status-dot-sm"></span>
            <?= $online ? Lang::t('featured.online') : Lang::t('featured.offline') ?>
          </div>
        </div>
      <?php else: ?>
        <div class="profile-avatar-lg" style="background:linear-gradient(145deg,#3d0e18 0%,#1a0e06 60%,#0a0805 100%)">
          <span><?= $initials ?></span>
          <div class="profile-photo-status <?= $online ? 'is-online' : 'is-offline' ?>">
            <span class="status-dot-sm"></span>
            <?= $online ? Lang::t('featured.online') : Lang::t('featured.offline') ?>
          </div>
        </div>
      <?php endif; ?>

      <!-- Gallery thumbnails -->
      <?php if (!empty($photos)): ?>
      <div class="profile-gallery" role="list" aria-label="Photo gallery">
        <?php foreach ($photos as $photo): ?>
          <button class="profile-gallery-thumb-btn" role="listitem"
                  onclick="openLightbox('<?= BASE_PATH ?>/<?= htmlspecialchars($photo['file_path']) ?>')"
                  aria-label="View photo">
            <img
              src="<?= BASE_PATH ?>/<?= htmlspecialchars($photo['thumbnail_path'] ?: $photo['file_path']) ?>"
              alt="<?= $name ?>"
              class="profile-gallery-thumb"
              width="200" height="200"
              loading="lazy"
            />
          </button>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <!-- Performer Intro Video Trigger -->
      <?php if (!empty($performer['short_video'])): ?>
        <div class="profile-video-trigger-wrap">
          <button type="button" class="btn-video-play" onclick="openVideoLightbox('<?= BASE_PATH ?>/<?= htmlspecialchars($performer['short_video']) ?>')" aria-label="Play video intro">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>
            Play Video Sample
          </button>
        </div>
      <?php endif; ?>

      <!-- Performer Voice Sample Trigger -->
      <?php if (!empty($performer['voice_sample'])): ?>
        <div class="profile-voice-trigger-wrap">
          <button type="button" class="btn-voice-play btn-video-play custom-audio-player-btn" aria-label="Play voice sample">
            <svg class="icon-play" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="margin-right:0.25rem;"><polygon points="5 3 19 12 5 21 5 3"/></svg>
            <svg class="icon-pause" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="display:none; margin-right:0.25rem;"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>
            <span class="audio-btn-text">Play Voice Sample</span>
            <audio class="hidden-audio-element" src="<?= BASE_PATH ?>/<?= htmlspecialchars($performer['voice_sample']) ?>" preload="metadata"></audio>
          </button>
        </div>
      <?php endif; ?>

      <!-- Quick stats strip -->
      <div class="profile-quick-stats">
        <div class="profile-quick-stat">
          <span class="pqs-value"><?= $callCount ?></span>
          <span class="pqs-label">Calls</span>
        </div>
        <div class="profile-quick-stat">
          <span class="pqs-value"><?= $talkTime ?></span>
          <span class="pqs-label">Talk Time</span>
        </div>
        <div class="profile-quick-stat">
          <span class="pqs-value"><?= $ratingAvg ?></span>
          <span class="pqs-label">Rating</span>
        </div>
        <div class="profile-quick-stat">
          <span class="pqs-value"><?= $ratingCount ?></span>
          <span class="pqs-label">Reviews</span>
        </div>
        <div class="profile-quick-stat">
          <span class="pqs-value"><?= $rate ?></span>
          <span class="pqs-label">Cr/min</span>
        </div>
      </div>

    </div>

    <!-- ── Right: Info ── -->
    <div class="profile-info-col">

      <!-- Breadcrumb back link -->
      <a href="<?= Lang::base() ?>/performers" class="auth-back-link">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
        <?= htmlspecialchars(Lang::t('nav.performers')) ?>
      </a>

      <!-- Category tags -->
      <div class="performer-card-cats" style="margin-top:1.25rem;">
        <?php foreach ($cats as $cat): ?>
          <span class="performer-cat-tag"><?= ucfirst($cat) ?></span>
        <?php endforeach; ?>
      </div>

      <!-- Name + meta -->
      <h1 class="profile-name"><?= $name ?></h1>

      <div class="profile-meta-row">
        <span class="profile-meta-item">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          Age <?= $age ?>
        </span>
        <span class="profile-meta-sep" aria-hidden="true">·</span>
        <span class="profile-meta-item">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
          <?= $langs ?>
        </span>
        <span class="profile-meta-sep" aria-hidden="true">·</span>
        <span class="profile-meta-item">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.8a16 16 0 0 0 6.29 6.29l.95-.95a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          <?= $callCount ?> calls
        </span>
      </div>

      <!-- Star rating -->
      <div class="profile-rating" aria-label="Rating: <?= $ratingAvg ?> out of 5">
        <?php for ($i = 1; $i <= 5; $i++): ?>
          <svg width="17" height="17" viewBox="0 0 24 24" aria-hidden="true"
               fill="<?= $i <= round((float)$performer['rating_avg']) ? 'var(--gold)' : 'none' ?>"
               stroke="var(--gold-dim)" stroke-width="1.5">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
          </svg>
        <?php endfor; ?>
        <span class="profile-rating-score"><?= $ratingAvg ?></span>
        <span class="profile-rating-count">(<?= $ratingCount ?> reviews)</span>
      </div>

      <!-- Rate card -->
      <div class="profile-rate-card" style="display:flex; flex-direction:column; gap:12px;">
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(201,168,76,0.1); padding-bottom:8px;">
          <div class="profile-rate-value" style="margin:0;">
            <?= $rate ?>
            <span>credit / min</span>
          </div>
          <span style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; color:rgba(196,184,150,0.5);">Voice Call</span>
        </div>
        
        <?php if ($performer['video_enabled']): ?>
        <div style="display:flex; justify-content:space-between; align-items:center;">
          <div class="profile-rate-value" style="margin:0; font-size:1.15rem; display: flex; align-items: baseline; gap: 4px;">
            <?= number_format((float)$performer['video_min_credits'], 0) ?>
            <span style="font-size:0.75rem; color:rgba(196,184,150,0.6); font-weight:normal;">cr / <?= (int)$performer['video_min_minutes'] ?> min, then <?= number_format((float)$performer['video_rate_per_minute'], 0) ?> cr/min</span>
          </div>
          <span style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--gold-dim);">Video Call</span>
        </div>
        <?php endif; ?>

        <div class="profile-rate-note" style="margin-top:4px;">
          <?php if ($isLoggedIn): ?>
            Your balance: <strong><?= $userCredits ?> credits</strong>
            <?php if ((float)($_SESSION['credits'] ?? 0) < (float)$performer['rate_per_minute']): ?>
              &nbsp;·&nbsp; <a href="<?= Lang::base() ?>/credits" style="color:var(--gold-dim);">Top up</a>
            <?php endif; ?>
          <?php else: ?>
            Credits deducted per minute of connection time
          <?php endif; ?>
        </div>
      </div>

      <!-- Bio -->
      <div class="profile-bio"><?= $bio ?></div>

      <!-- ── CTA ── -->
      <div class="profile-cta-area" style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
        <?php if ($isLoggedIn): ?>
          <?php if ($canCall && $online): ?>
            <form method="POST" action="<?= Lang::base() ?>/call/request" style="margin:0;">
              <?= \App\Core\CSRF::field() ?>
              <input type="hidden" name="performer_id" value="<?= (int)$performer['id'] ?>">
              <input type="hidden" name="call_type" value="voice">
              <button type="submit" class="btn-primary btn-lg profile-connect-btn" style="padding:0.9rem 1.8rem; font-size:0.8rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.8a16 16 0 0 0 6.29 6.29l.95-.95a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                <?= htmlspecialchars(Lang::t('featured.connect')) ?> (Voice)
              </button>
            </form>

            <?php if ($performer['video_enabled']): ?>
              <form method="POST" action="<?= Lang::base() ?>/call/request" style="margin:0;">
                <?= \App\Core\CSRF::field() ?>
                <input type="hidden" name="performer_id" value="<?= (int)$performer['id'] ?>">
                <input type="hidden" name="call_type" value="video">
                <button type="submit" class="btn-primary btn-lg profile-connect-btn" style="padding:0.9rem 1.8rem; font-size:0.8rem; background:linear-gradient(135deg, #c9a84c 0%, #a28028 100%);">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M23 7a2 2 0 0 0-2.45-1.45L16 7V5a2 2 0 0 0-2-2H2a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2l4.55 1.45A2 2 0 0 0 23 17V7z"/></svg>
                  Connect Video
                </button>
              </form>
            <?php endif; ?>
          <?php elseif (!$online): ?>
            <button class="btn-ghost btn-lg" disabled aria-disabled="true" style="opacity:.4; cursor:not-allowed;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
              <?= htmlspecialchars(Lang::t('featured.offline')) ?>
            </button>
          <?php else: ?>
            <a href="<?= Lang::base() ?>/credits" class="btn-primary btn-lg">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
              <?= htmlspecialchars(Lang::t('pricing.get_started')) ?> — Buy Credits
            </a>
          <?php endif; ?>
        <?php else: ?>
          <a href="<?= Lang::base() ?>/register" class="btn-primary btn-lg">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <?= htmlspecialchars(Lang::t('nav.join_now')) ?>
          </a>
          <a href="<?= Lang::base() ?>/login" class="btn-ghost btn-lg">
            <?= htmlspecialchars(Lang::t('nav.sign_in')) ?>
          </a>
        <?php endif; ?>
      </div>

      <!-- Trust badges -->
      <div class="profile-trust-row">
        <span>
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          <?= htmlspecialchars(Lang::t('hero.trust_1')) ?>
        </span>
        <span>
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <?= htmlspecialchars(Lang::t('hero.trust_2')) ?>
        </span>
      </div>

    </div>
  </div>

  <!-- ══════════════════════════════════════════════════════
       REVIEWS
  ══════════════════════════════════════════════════════ -->
  <div class="profile-reviews-section">
    <div class="profile-reviews-inner">

      <div class="profile-reviews-header">
        <h2 class="account-sub-heading" style="border:none; padding:0; margin:0;">Member Reviews</h2>
        <?php if (!empty($reviews)): ?>
          <div class="profile-reviews-summary">
            <div class="reviews-avg-score"><?= $ratingAvg ?></div>
            <div class="reviews-avg-stars">
              <?php for ($i = 1; $i <= 5; $i++): ?>
                <svg width="14" height="14" viewBox="0 0 24 24" aria-hidden="true"
                     fill="<?= $i <= round((float)$performer['rating_avg']) ? 'var(--gold)' : 'none' ?>"
                     stroke="var(--gold-dim)" stroke-width="1.5">
                  <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
              <?php endfor; ?>
              <span style="font-size:.65rem; color:rgba(196,184,150,.4); margin-left:.35rem;"><?= $ratingCount ?> reviews</span>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <?php if (empty($reviews)): ?>
        <div class="account-empty" style="margin-top:1.5rem;">
          <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          <p>No reviews yet — be the first to connect with <?= $name ?>.</p>
        </div>
      <?php else: ?>
        <div class="reviews-grid" style="margin-top:1.5rem;">
          <?php foreach ($reviews as $review): ?>
          <div class="review-card">
            <div class="review-header">
              <div class="review-stars" aria-label="Rating <?= (int)$review['rating'] ?> out of 5">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                  <svg width="13" height="13" viewBox="0 0 24 24" aria-hidden="true"
                       fill="<?= $i <= (int)$review['rating'] ? 'var(--gold)' : 'none' ?>"
                       stroke="var(--gold-dim)" stroke-width="1.5">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                  </svg>
                <?php endfor; ?>
              </div>
              <span class="review-meta">
                <?= htmlspecialchars($review['username']) ?>
                &nbsp;·&nbsp;
                <?= date('M Y', strtotime($review['created_at'])) ?>
              </span>
            </div>
            <?php if (!empty($review['comment'])): ?>
              <p class="review-body"><?= htmlspecialchars($review['comment']) ?></p>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    </div>
  </div>

  <!-- ══════════════════════════════════════════════════════
       FOOTER
  ══════════════════════════════════════════════════════ -->
  <footer>
    <div class="footer-inner">
      <div class="footer-top">
        <div class="footer-brand">
          <div class="footer-brand-name"><?= htmlspecialchars(Lang::t('meta.site_name')) ?></div>
          <p class="footer-brand-tagline"><?= htmlspecialchars(Lang::t('footer.tagline')) ?></p>
        </div>
        <div class="footer-col">
          <p class="footer-col-title"><?= htmlspecialchars(Lang::t('footer.explore')) ?></p>
          <ul class="footer-links">
            <li><a href="<?= Lang::base() ?>/performers"><?= htmlspecialchars(Lang::t('nav.performers')) ?></a></li>
            <li><a href="<?= Lang::base() ?>/#how-it-works"><?= htmlspecialchars(Lang::t('nav.how_it_works')) ?></a></li>
            <li><a href="<?= Lang::base() ?>/#pricing"><?= htmlspecialchars(Lang::t('nav.credits')) ?></a></li>
          </ul>
        </div>
        <div class="footer-col">
          <p class="footer-col-title"><?= htmlspecialchars(Lang::t('footer.account')) ?></p>
          <ul class="footer-links">
            <?php if ($isLoggedIn): ?>
              <li><a href="<?= Lang::base() ?>/account"><?= htmlspecialchars(Lang::t('nav.my_account')) ?></a></li>
              <li><a href="<?= Lang::base() ?>/credits"><?= htmlspecialchars(Lang::t('footer.buy')) ?></a></li>
              <li><a href="<?= Lang::base() ?>/logout"><?= htmlspecialchars(Lang::t('nav.sign_out')) ?></a></li>
            <?php else: ?>
              <li><a href="<?= Lang::base() ?>/login"><?= htmlspecialchars(Lang::t('nav.sign_in')) ?></a></li>
              <li><a href="<?= Lang::base() ?>/register"><?= htmlspecialchars(Lang::t('footer.register')) ?></a></li>
            <?php endif; ?>
          </ul>
        </div>
        <div class="footer-col">
          <p class="footer-col-title"><?= htmlspecialchars(Lang::t('footer.legal')) ?></p>
          <ul class="footer-links">
            <li><a href="<?= Lang::base() ?>/terms"><?= htmlspecialchars(Lang::t('footer.terms')) ?></a></li>
            <li><a href="<?= Lang::base() ?>/privacy"><?= htmlspecialchars(Lang::t('footer.privacy')) ?></a></li>
            <li><a href="<?= Lang::base() ?>/2257"><?= htmlspecialchars(Lang::t('footer.usc')) ?></a></li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        <p class="footer-copy"><?= htmlspecialchars(Lang::t('footer.copy', ['year' => date('Y')])) ?></p>
        <p class="footer-copy">Designed &amp; Maintained by <a href="https://lexdigitals.co.za" target="_blank" rel="noopener noreferrer" style="color: inherit; text-decoration: underline;">Lex Digitals</a></p>
        <p class="footer-copy"><?= htmlspecialchars(Lang::t('footer.adults')) ?> &nbsp;·&nbsp; support@thegentlemensplace.eu</p>
      </div>
    </div>
  </footer>

</div>

<!-- ── Lightbox (for gallery photos) ── -->
<div id="lightbox" class="lightbox" role="dialog" aria-modal="true" aria-label="Photo viewer" style="display:none;">
  <button class="lightbox-close" onclick="closeLightbox()" aria-label="Close">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
  </button>
  <img id="lightbox-img" src="" alt="<?= $name ?>" class="lightbox-img" />
</div>
<div id="lightbox-overlay" onclick="closeLightbox()" style="display:none;"></div>

<!-- ── Video Lightbox ── -->
<div id="video-lightbox" class="lightbox" role="dialog" aria-modal="true" aria-label="Video viewer" style="display:none;">
  <button class="lightbox-close" onclick="closeVideoLightbox()" aria-label="Close">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
  </button>
  <video id="lightbox-video" src="" controls controlsList="nodownload" class="lightbox-video-player"></video>
</div>
<div id="video-lightbox-overlay" onclick="closeVideoLightbox()" style="display:none; position:fixed; inset:0; z-index:8999; background:rgba(0,0,0,.92); backdrop-filter:blur(4px);"></div>

<script>
function openLightbox(src) {
  document.getElementById('lightbox-img').src = src;
  document.getElementById('lightbox').style.display = 'flex';
  document.getElementById('lightbox-overlay').style.display = 'block';
  document.body.style.overflow = 'hidden';
}
function closeLightbox() {
  document.getElementById('lightbox').style.display = 'none';
  document.getElementById('lightbox-overlay').style.display = 'none';
  document.body.style.overflow = '';
}
function openVideoLightbox(src) {
  const video = document.getElementById('lightbox-video');
  video.src = src;
  document.getElementById('video-lightbox').style.display = 'flex';
  document.getElementById('video-lightbox-overlay').style.display = 'block';
  document.body.style.overflow = 'hidden';

  // Pause custom audio playback if active
  document.querySelectorAll('.hidden-audio-element').forEach(audio => {
    audio.pause();
    const player = audio.closest('.custom-audio-player');
    if (player) {
      player.querySelector('.icon-play').style.display = 'block';
      player.querySelector('.icon-pause').style.display = 'none';
    }
  });

  video.play().catch(err => console.log('Autoplay prevented:', err));
}
function closeVideoLightbox() {
  const video = document.getElementById('lightbox-video');
  video.pause();
  video.src = '';
  document.getElementById('video-lightbox').style.display = 'none';
  document.getElementById('video-lightbox-overlay').style.display = 'none';
  document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeLightbox();
    closeVideoLightbox();
  }
});

// Custom Audio Player logic
document.querySelectorAll('.custom-audio-player-btn').forEach(btn => {
  const audio = btn.querySelector('.hidden-audio-element');
  const playIcon = btn.querySelector('.icon-play');
  const pauseIcon = btn.querySelector('.icon-pause');
  const btnText = btn.querySelector('.audio-btn-text');

  // Play/Pause toggle
  btn.addEventListener('click', () => {
    if (audio.paused) {
      // Pause all other audio players on page
      document.querySelectorAll('.hidden-audio-element').forEach(otherAudio => {
        if (otherAudio !== audio) {
          otherAudio.pause();
          const otherBtn = otherAudio.closest('.custom-audio-player-btn');
          if (otherBtn) {
            otherBtn.querySelector('.icon-play').style.display = 'block';
            otherBtn.querySelector('.icon-pause').style.display = 'none';
            otherBtn.querySelector('.audio-btn-text').textContent = 'Play Voice Sample';
            otherBtn.classList.remove('is-playing');
          }
        }
      });
      audio.play();
      playIcon.style.display = 'none';
      pauseIcon.style.display = 'block';
      btnText.textContent = 'Pause Voice Sample';
      btn.classList.add('is-playing');
    } else {
      audio.pause();
      playIcon.style.display = 'block';
      pauseIcon.style.display = 'none';
      btnText.textContent = 'Play Voice Sample';
      btn.classList.remove('is-playing');
    }
  });

  // Reset when audio ends
  audio.addEventListener('ended', () => {
    playIcon.style.display = 'block';
    pauseIcon.style.display = 'none';
    btnText.textContent = 'Play Voice Sample';
    btn.classList.remove('is-playing');
  });
});
</script>

<?php
$isLoggedIn = !empty($_SESSION['user_id']);
$username   = htmlspecialchars($_SESSION['username'] ?? '');

// Currency-aware pricing for the home page pricing section
// Uses the same CurrencyService as the credits page
use App\Services\CurrencyService;
use App\Core\Lang;

$homeCurrency = defined('CURRENCY') ? CURRENCY : 'EUR';
$homeSymbol   = CurrencyService::symbol($homeCurrency);

// Dynamic package data for the home page preview
$starterPrice     = 150.00;
$starterCredits   = 15.0;
$starterBonus     = 0.0;

$gentlemanPrice   = 350.00;
$gentlemanCredits = 40.0;
$gentlemanBonus   = 5.0;

$elitePrice       = 700.00;
$eliteCredits     = 90.0;
$eliteBonus       = 15.0;

try {
    $db = \App\Config\Database::getInstance();
    $pkgStmt = $db->query("SELECT name, price_zar, credits, bonus_credits FROM credit_packages WHERE is_active = 1");
    $dbPkgs = $pkgStmt->fetchAll();
    foreach ($dbPkgs as $p) {
        $pName = strtolower($p['name']);
        if ($pName === 'starter') {
            $starterPrice   = (float)$p['price_zar'];
            $starterCredits = (float)$p['credits'];
            $starterBonus   = (float)$p['bonus_credits'];
        } elseif ($pName === 'gentleman') {
            $gentlemanPrice   = (float)$p['price_zar'];
            $gentlemanCredits = (float)$p['credits'];
            $gentlemanBonus   = (float)$p['bonus_credits'];
        } elseif ($pName === 'elite') {
            $elitePrice   = (float)$p['price_zar'];
            $eliteCredits = (float)$p['credits'];
            $eliteBonus   = (float)$p['bonus_credits'];
        }
    }
} catch (\Exception $e) {
    // Fail silently, use default fallbacks
}

$homePrices = [
    'starter'   => CurrencyService::format(CurrencyService::fromZAR($starterPrice, $homeCurrency), $homeCurrency),
    'gentleman' => CurrencyService::format(CurrencyService::fromZAR($gentlemanPrice, $homeCurrency), $homeCurrency),
    'elite'     => CurrencyService::format(CurrencyService::fromZAR($elitePrice, $homeCurrency), $homeCurrency),
];
?>

<!-- ═══════════════════════════════════════════════════════
     HERO
════════════════════════════════════════════════════════ -->
<section id="hero">
  <div class="hero-bg-base"></div>
  <div class="hero-texture" aria-hidden="true"></div>
  <div class="hero-vignette" aria-hidden="true"></div>
  <canvas id="particle-canvas" aria-hidden="true"></canvas>

  <div class="hero-content">
    <p class="hero-eyebrow"><?= htmlspecialchars(Lang::t('hero.eyebrow')) ?></p>
    <h1 class="hero-title">
      <?= Lang::t('hero.title_1') ?><br><em><?= Lang::t('hero.title_em') ?></em>
    </h1>
    <p class="hero-subtitle">
      <?= htmlspecialchars(Lang::t('hero.subtitle')) ?>
    </p>
    <div class="hero-actions">
      <?php if ($isLoggedIn): ?>
        <a href="<?= BASE_PATH ?>/performers" class="btn-primary btn-lg"><?= htmlspecialchars(Lang::t('hero.browse')) ?></a>
        <a href="<?= BASE_PATH ?>/credits" class="btn-ghost btn-lg"><?= htmlspecialchars(Lang::t('hero.topup')) ?></a>
      <?php else: ?>
        <a href="<?= BASE_PATH ?>/register" class="btn-primary btn-lg"><?= htmlspecialchars(Lang::t('hero.join')) ?></a>
        <a href="<?= BASE_PATH ?>/login" class="btn-ghost btn-lg"><?= htmlspecialchars(Lang::t('hero.signin')) ?></a>
      <?php endif; ?>
    </div>
    <div class="hero-trust">
      <span><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg> <?= htmlspecialchars(Lang::t('hero.trust_1')) ?></span>
      <span><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> <?= htmlspecialchars(Lang::t('hero.trust_2')) ?></span>
      <span><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> <?= htmlspecialchars(Lang::t('hero.trust_3')) ?></span>
    </div>
  </div>

  <div class="hero-scroll-cue" aria-hidden="true">
    <span class="scroll-cue-text"><?= htmlspecialchars(Lang::t('hero.discover')) ?></span>
    <span class="scroll-cue-line"></span>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════
     STATS STRIP
════════════════════════════════════════════════════════ -->
<div class="stats-strip">
  <div class="stats-inner">
    <div class="stat-item reveal">
      <span class="stat-value">500+</span>
      <span class="stat-label"><?= htmlspecialchars(Lang::t('stats.performers')) ?></span>
    </div>
    <div class="stat-divider" aria-hidden="true"></div>
    <div class="stat-item reveal delay-1">
      <span class="stat-value">50K+</span>
      <span class="stat-label"><?= htmlspecialchars(Lang::t('stats.members')) ?></span>
    </div>
    <div class="stat-divider" aria-hidden="true"></div>
    <div class="stat-item reveal delay-2">
      <span class="stat-value">4.9★</span>
      <span class="stat-label"><?= htmlspecialchars(Lang::t('stats.rating')) ?></span>
    </div>
    <div class="stat-divider" aria-hidden="true"></div>
    <div class="stat-item reveal delay-3">
      <span class="stat-value">100%</span>
      <span class="stat-label"><?= htmlspecialchars(Lang::t('stats.private')) ?></span>
    </div>
  </div>
</div>

<?php
// ── Demo performers for when DB is empty ─────────────────
$demoFeatured = [
  [
    'slug'           => 'isabella-rose',
    'display_name'   => 'Isabella Rose',
    'age'            => 24,
    'bio'            => 'Sophisticated, playful and endlessly curious. I love deep conversations that go wherever the night takes us.',
    'category'       => 'chat,roleplay',
    'languages'      => 'English, French',
    'rate_per_minute'=> '2.00',
    'rating_avg'     => '4.9',
    'rating_count'   => 312,
    'total_calls'    => 1840,
    'online_status'  => 1,
    'profile_photo'  => null,
    '_initials'      => 'IR',
    '_gradient'      => 'linear-gradient(160deg,#3d0e18 0%,#1a0e06 60%,#0a0805 100%)',
    '_accent'        => '#c9a84c',
  ],
  [
    'slug'           => 'sophia-lane',
    'display_name'   => 'Sophia Lane',
    'age'            => 27,
    'bio'            => 'Warm, witty and wonderfully unpredictable. Every call is a new adventure — I promise you won\'t be bored.',
    'category'       => 'fantasy,mature',
    'languages'      => 'English',
    'rate_per_minute'=> '3.00',
    'rating_avg'     => '4.8',
    'rating_count'   => 204,
    'total_calls'    => 1120,
    'online_status'  => 1,
    'profile_photo'  => null,
    '_initials'      => 'SL',
    '_gradient'      => 'linear-gradient(160deg,#0e1a3d 0%,#060e1a 60%,#0a0805 100%)',
    '_accent'        => '#9a7a30',
  ],
  [
    'slug'           => 'victoria-black',
    'display_name'   => 'Victoria Black',
    'age'            => 29,
    'bio'            => 'Dominant energy, velvet voice. I set the tone and you follow. If that sounds like your kind of evening, let\'s talk.',
    'category'       => 'roleplay,fetish',
    'languages'      => 'English, German',
    'rate_per_minute'=> '4.00',
    'rating_avg'     => '5.0',
    'rating_count'   => 98,
    'total_calls'    => 540,
    'online_status'  => 0,
    'profile_photo'  => null,
    '_initials'      => 'VB',
    '_gradient'      => 'linear-gradient(160deg,#1a0a1a 0%,#0a0a0a 60%,#0a0805 100%)',
    '_accent'        => '#c9a84c',
  ],
  [
    'slug'           => 'amara-gold',
    'display_name'   => 'Amara Gold',
    'age'            => 23,
    'bio'            => 'Sweet on the surface, fire underneath. I love making you laugh before I make you breathless.',
    'category'       => 'chat,couples',
    'languages'      => 'English, Afrikaans',
    'rate_per_minute'=> '2.00',
    'rating_avg'     => '4.7',
    'rating_count'   => 176,
    'total_calls'    => 920,
    'online_status'  => 1,
    'profile_photo'  => null,
    '_initials'      => 'AG',
    '_gradient'      => 'linear-gradient(160deg,#2a1a06 0%,#1a0e06 60%,#0a0805 100%)',
    '_accent'        => '#e0c06a',
  ],
  [
    'slug'           => 'celeste-noir',
    'display_name'   => 'Celeste Noir',
    'age'            => 31,
    'bio'            => 'Mysterious, poetic, and deeply attentive. I listen as much as I speak — and I remember everything.',
    'category'       => 'mature,fantasy',
    'languages'      => 'English, Spanish',
    'rate_per_minute'=> '3.50',
    'rating_avg'     => '4.9',
    'rating_count'   => 267,
    'total_calls'    => 1560,
    'online_status'  => 1,
    'profile_photo'  => null,
    '_initials'      => 'CN',
    '_gradient'      => 'linear-gradient(160deg,#1a0a2a 0%,#0e060e 60%,#0a0805 100%)',
    '_accent'        => '#9a7a30',
  ],
];

$featuredList = !empty($featured) ? $featured : $demoFeatured;
?>

<!-- ═══════════════════════════════════════════════════════
     FEATURED PERFORMERS
════════════════════════════════════════════════════════ -->
<section id="featured-performers">
  <div class="featured-inner">

    <div class="featured-header reveal">
      <div class="featured-header-text">
        <p class="section-eyebrow"><?= htmlspecialchars(Lang::t('featured.eyebrow')) ?></p>
        <h2 class="section-heading"><?= Lang::t('featured.heading') ?> <em><?= Lang::t('featured.heading_em') ?></em></h2>
        <p class="section-body"><?= htmlspecialchars(Lang::t('featured.subheading')) ?></p>
      </div>
      <a href="<?= BASE_PATH ?>/performers" class="featured-view-all">
        <?= htmlspecialchars(Lang::t('featured.view_all')) ?>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
      </a>
    </div>

    <div class="featured-grid">
      <?php foreach ($featuredList as $i => $p):
        $name     = htmlspecialchars($p['display_name']);
        $slug     = htmlspecialchars($p['slug']);
        $age      = (int)$p['age'];
        $bio      = htmlspecialchars($p['bio'] ?? '');
        $rate     = number_format((float)$p['rate_per_minute'], 0);
        $rating   = number_format((float)$p['rating_avg'], 1);
        $reviews  = number_format((int)$p['rating_count']);
        $online   = (bool)$p['online_status'];
        $cats     = array_slice(array_map('trim', explode(',', $p['category'])), 0, 2);
        $langs    = htmlspecialchars($p['languages'] ?? 'English');
        $initials = $p['_initials'] ?? strtoupper(substr($p['display_name'], 0, 2));
        $gradient = $p['_gradient'] ?? 'linear-gradient(160deg,#3d0e18,#0a0805)';
        $accent   = $p['_accent']   ?? '#c9a84c';
        $hasPhoto = !empty($p['profile_photo']);
        $delay    = ['', 'delay-1', 'delay-2', 'delay-1', 'delay-2'][$i] ?? '';
        // First card is large (featured hero), rest are standard
        $isHero   = ($i === 0);
      ?>
      <article class="featured-card <?= $isHero ? 'featured-card-hero' : '' ?> reveal <?= $delay ?>"
               aria-label="<?= $name ?>">

        <!-- Background / photo -->
        <a href="<?= BASE_PATH ?>/performer/<?= $slug ?>" class="featured-card-bg" tabindex="-1" aria-hidden="true">
          <?php if ($hasPhoto): ?>
            <img src="<?= BASE_PATH ?>/<?= htmlspecialchars($p['profile_photo']) ?>"
                 alt="<?= $name ?>" class="featured-card-img" 
                 width="400" height="600"
                 loading="<?= $i === 0 ? 'eager' : 'lazy' ?>" />
          <?php else: ?>
            <div class="featured-card-avatar" style="background:<?= $gradient ?>">
              <span class="featured-card-initials" style="color:<?= $accent ?>"><?= $initials ?></span>
            </div>
          <?php endif; ?>
          <!-- Gradient overlay -->
          <div class="featured-card-overlay"></div>
        </a>

        <!-- Online badge -->
        <div class="featured-card-status <?= $online ? 'is-online' : 'is-offline' ?>">
          <span class="status-dot-sm"></span>
          <?= $online ? htmlspecialchars(Lang::t('featured.online')) : htmlspecialchars(Lang::t('featured.offline')) ?>
        </div>

        <!-- Rate badge -->
        <div class="featured-card-rate-badge">
          <?= $rate ?> <?= htmlspecialchars(Lang::t('featured.cr_min')) ?>
        </div>

        <!-- Content overlay at bottom -->
        <div class="featured-card-content">
          <!-- Categories -->
          <div class="featured-card-cats">
            <?php foreach ($cats as $cat): ?>
              <span class="featured-cat-tag"><?= ucfirst($cat) ?></span>
            <?php endforeach; ?>
          </div>

          <h3 class="featured-card-name">
            <a href="<?= BASE_PATH ?>/performer/<?= $slug ?>"><?= $name ?></a>
          </h3>

          <p class="featured-card-meta">Age <?= $age ?> &nbsp;·&nbsp; <?= $langs ?></p>

          <?php if ($isHero): ?>
          <p class="featured-card-bio"><?= mb_strimwidth($bio, 0, 110, '…') ?></p>
          <?php endif; ?>

          <div class="featured-card-footer">
            <div class="featured-card-rating">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="var(--gold)" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              <span><?= $rating ?></span>
              <span class="featured-rating-count">(<?= $reviews ?>)</span>
            </div>
            <a href="<?= BASE_PATH ?>/performer/<?= $slug ?>"
               class="featured-card-cta <?= $online ? 'cta-live' : '' ?>">
              <?= $online ? htmlspecialchars(Lang::t('featured.connect')) : htmlspecialchars(Lang::t('featured.view')) ?>
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
          </div>
        </div>

      </article>
      <?php endforeach; ?>
    </div>

    <div class="featured-footer reveal">
      <a href="<?= BASE_PATH ?>/performers" class="btn-ghost btn-lg">
        <?= htmlspecialchars(Lang::t('featured.explore_all')) ?>
      </a>
    </div>

  </div>
</section>

<!-- ═══════════════════════════════════════════════════════
     HOW IT WORKS
════════════════════════════════════════════════════════ -->
<section id="how-it-works">
  <div class="section-inner">
    <div class="section-header reveal">
      <p class="section-eyebrow"><?= htmlspecialchars(Lang::t('how.eyebrow')) ?></p>
      <h2 class="section-heading"><?= Lang::t('how.heading') ?> <em><?= Lang::t('how.heading_em') ?></em></h2>
      <p class="section-body"><?= htmlspecialchars(Lang::t('how.body')) ?></p>
    </div>

    <div class="steps-grid">
      <div class="step-card reveal delay-1">
        <div class="step-number" aria-hidden="true">01</div>
        <div class="step-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <h3 class="step-title"><?= htmlspecialchars(Lang::t('how.step1_title')) ?></h3>
        <p class="step-body"><?= htmlspecialchars(Lang::t('how.step1_body')) ?></p>
      </div>

      <div class="step-card reveal delay-2">
        <div class="step-number" aria-hidden="true">02</div>
        <div class="step-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
        </div>
        <h3 class="step-title"><?= htmlspecialchars(Lang::t('how.step2_title')) ?></h3>
        <p class="step-body"><?= str_replace(':url', BASE_PATH . '/credits', Lang::t('how.step2_body')) ?></p>
      </div>

      <div class="step-card reveal delay-3">
        <div class="step-number" aria-hidden="true">03</div>
        <div class="step-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.8a16 16 0 0 0 6.29 6.29l.95-.95a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        </div>
        <h3 class="step-title"><?= htmlspecialchars(Lang::t('how.step3_title')) ?></h3>
        <p class="step-body"><?= htmlspecialchars(Lang::t('how.step3_body')) ?></p>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════
     CALL PERMISSIONS & GUIDE (HOW TO)
════════════════════════════════════════════════════════ -->
<style>
.webrtc-tab-btn:hover {
  background: rgba(201,168,76, 0.05) !important;
  color: var(--gold) !important;
  border-color: var(--gold) !important;
}
@media (max-width: 600px) {
  .webrtc-tabs-nav {
    flex-direction: column;
    align-items: stretch;
  }
}
</style>

<section id="webrtc-guide" style="background: var(--charcoal-mid); border-top: 1px solid rgba(201,168,76,.1); border-bottom: 1px solid rgba(201,168,76,.1);">
  <div class="section-inner">
    <div class="section-header reveal" style="text-align:center; max-width:640px; margin:0 auto 4rem;">
      <p class="section-eyebrow" style="justify-content:center;"><?= htmlspecialchars(Lang::t('webrtc.eyebrow')) ?></p>
      <h2 class="section-heading"><?= Lang::t('webrtc.heading') ?> <em><?= Lang::t('webrtc.heading_em') ?></em></h2>
      <p class="section-body" style="margin:0 auto;"><?= htmlspecialchars(Lang::t('webrtc.body')) ?></p>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:2rem;">
      <!-- Step 1 -->
      <div style="background:var(--black); border:1px solid rgba(201,168,76,.08); padding:2.25rem 2rem; border-radius:12px;">
        <h4 style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.1em; color:var(--gold); margin-bottom:0.75rem; display:flex; align-items:center; gap:8px;">
          <span style="display:inline-grid; place-items:center; width:22px; height:22px; border-radius:50%; border:1px solid var(--gold-dim); font-size:0.65rem;">1</span>
          <?= htmlspecialchars(Lang::t('webrtc.step1_title')) ?>
        </h4>
        <p style="font-size:0.82rem; line-height:1.6; color:rgba(196,184,150,.65);">
          <?= Lang::t('webrtc.step1_body') ?>
        </p>
      </div>

      <!-- Step 2 -->
      <div style="background:var(--black); border:1px solid rgba(201,168,76,.08); padding:2.25rem 2rem; border-radius:12px;">
        <h4 style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.1em; color:var(--gold); margin-bottom:0.75rem; display:flex; align-items:center; gap:8px;">
          <span style="display:inline-grid; place-items:center; width:22px; height:22px; border-radius:50%; border:1px solid var(--gold-dim); font-size:0.65rem;">2</span>
          <?= htmlspecialchars(Lang::t('webrtc.step2_title')) ?>
        </h4>
        <p style="font-size:0.82rem; line-height:1.6; color:rgba(196,184,150,.65);">
          <?= Lang::t('webrtc.step2_body') ?>
        </p>
      </div>

      <!-- Step 3 -->
      <div style="background:var(--black); border:1px solid rgba(201,168,76,.08); padding:2.25rem 2rem; border-radius:12px;">
        <h4 style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.1em; color:var(--gold); margin-bottom:0.75rem; display:flex; align-items:center; gap:8px;">
          <span style="display:inline-grid; place-items:center; width:22px; height:22px; border-radius:50%; border:1px solid var(--gold-dim); font-size:0.65rem;">3</span>
          <?= htmlspecialchars(Lang::t('webrtc.step3_title')) ?>
        </h4>
        <p style="font-size:0.82rem; line-height:1.6; color:rgba(196,184,150,.65);">
          <?= Lang::t('webrtc.step3_body') ?>
        </p>
      </div>
    </div>

    <!-- Browser Specific Guide Tabs -->
    <div class="webrtc-tabs-container reveal delay-2" style="margin-top: 4rem; background: var(--black); border: 1px solid rgba(201,168,76,.15); padding: 2.5rem 2.25rem; border-radius: 12px;">
      <h3 style="font-family: var(--ff-serif); font-size: 1.4rem; color: var(--cream); margin-bottom: 1.5rem; text-align: center;"><?= htmlspecialchars(Lang::t('webrtc.tab_title')) ?></h3>
      
      <!-- Tab Header Buttons -->
      <div class="webrtc-tabs-nav" style="display: flex; justify-content: center; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
        <button onclick="switchTab('chrome')" class="webrtc-tab-btn active-tab" id="tab-btn-chrome" style="background: transparent; color: var(--gold); border: 1px solid var(--gold); padding: 0.6rem 1.5rem; font-family: var(--ff-sans); font-size: 0.65rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; cursor: pointer; transition: all 0.3s;">
          <?= htmlspecialchars(Lang::t('webrtc.btn_chrome')) ?>
        </button>
        <button onclick="switchTab('safari')" class="webrtc-tab-btn" id="tab-btn-safari" style="background: transparent; color: var(--cream-dim); border: 1px solid rgba(201,168,76,.15); padding: 0.6rem 1.5rem; font-family: var(--ff-sans); font-size: 0.65rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; cursor: pointer; transition: all 0.3s;">
          <?= htmlspecialchars(Lang::t('webrtc.btn_safari')) ?>
        </button>
        <button onclick="switchTab('firefox')" class="webrtc-tab-btn" id="tab-btn-firefox" style="background: transparent; color: var(--cream-dim); border: 1px solid rgba(201,168,76,.15); padding: 0.6rem 1.5rem; font-family: var(--ff-sans); font-size: 0.65rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; cursor: pointer; transition: all 0.3s;">
          <?= htmlspecialchars(Lang::t('webrtc.btn_firefox')) ?>
        </button>
      </div>

      <!-- Tab Content: Chrome -->
      <div id="tab-content-chrome" class="webrtc-tab-content" style="display: block;">
        <div style="display: flex; gap: 1.5rem; flex-direction: column;">
          <div style="display: flex; gap: 1rem; align-items: flex-start;">
            <span style="background: rgba(201,168,76,.1); color: var(--gold); font-family: var(--ff-serif); font-size: 1.1rem; font-weight: 700; width: 36px; height: 36px; border-radius: 50%; display: grid; place-items: center; flex-shrink: 0;">1</span>
            <div>
              <h4 style="font-size: 0.9rem; color: var(--cream); margin-bottom: 0.25rem;"><?= htmlspecialchars(Lang::t('webrtc.chrome_step1_title')) ?></h4>
              <p style="font-size: 0.85rem; color: rgba(196,184,150,.65); line-height: 1.6;"><?= htmlspecialchars(Lang::t('webrtc.chrome_step1_body')) ?></p>
            </div>
          </div>
          <div style="display: flex; gap: 1rem; align-items: flex-start;">
            <span style="background: rgba(201,168,76,.1); color: var(--gold); font-family: var(--ff-serif); font-size: 1.1rem; font-weight: 700; width: 36px; height: 36px; border-radius: 50%; display: grid; place-items: center; flex-shrink: 0;">2</span>
            <div>
              <h4 style="font-size: 0.9rem; color: var(--cream); margin-bottom: 0.25rem;"><?= htmlspecialchars(Lang::t('webrtc.chrome_step2_title')) ?></h4>
              <p style="font-size: 0.85rem; color: rgba(196,184,150,.65); line-height: 1.6;"><?= htmlspecialchars(Lang::t('webrtc.chrome_step2_body')) ?></p>
            </div>
          </div>
          <div style="display: flex; gap: 1rem; align-items: flex-start;">
            <span style="background: rgba(201,168,76,.1); color: var(--gold); font-family: var(--ff-serif); font-size: 1.1rem; font-weight: 700; width: 36px; height: 36px; border-radius: 50%; display: grid; place-items: center; flex-shrink: 0;">3</span>
            <div>
              <h4 style="font-size: 0.9rem; color: var(--cream); margin-bottom: 0.25rem;"><?= htmlspecialchars(Lang::t('webrtc.chrome_step3_title')) ?></h4>
              <p style="font-size: 0.85rem; color: rgba(196,184,150,.65); line-height: 1.6;"><?= htmlspecialchars(Lang::t('webrtc.chrome_step3_body')) ?></p>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab Content: Safari -->
      <div id="tab-content-safari" class="webrtc-tab-content" style="display: none;">
        <div style="display: flex; gap: 1.5rem; flex-direction: column;">
          <div style="display: flex; gap: 1.5rem; justify-content: space-between; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 250px;">
              <h4 style="font-size: 0.9rem; color: var(--gold); margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;"><?= htmlspecialchars(Lang::t('webrtc.safari_heading_ios')) ?></h4>
              <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.85rem; color: rgba(196,184,150,.65); line-height: 1.6; padding-left: 0;">
                <li>• <?= Lang::t('webrtc.safari_ios_1') ?></li>
                <li>• <?= Lang::t('webrtc.safari_ios_2') ?></li>
                <li>• <?= Lang::t('webrtc.safari_ios_3') ?></li>
                <li>• <?= Lang::t('webrtc.safari_ios_4') ?></li>
                <li>• <?= Lang::t('webrtc.safari_ios_5') ?></li>
              </ul>
            </div>
            <div style="flex: 1; min-width: 250px; border-left: 1px solid rgba(201,168,76,.1); padding-left: 1.5rem;">
              <h4 style="font-size: 0.9rem; color: var(--gold); margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;"><?= htmlspecialchars(Lang::t('webrtc.safari_heading_mac')) ?></h4>
              <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.85rem; color: rgba(196,184,150,.65); line-height: 1.6; padding-left: 0;">
                <li>• <?= Lang::t('webrtc.safari_mac_1') ?></li>
                <li>• <?= Lang::t('webrtc.safari_mac_2') ?></li>
                <li>• <?= Lang::t('webrtc.safari_mac_3') ?></li>
                <li>• <?= Lang::t('webrtc.safari_mac_4') ?></li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab Content: Firefox -->
      <div id="tab-content-firefox" class="webrtc-tab-content" style="display: none;">
        <div style="display: flex; gap: 1.5rem; flex-direction: column;">
          <div style="display: flex; gap: 1rem; align-items: flex-start;">
            <span style="background: rgba(201,168,76,.1); color: var(--gold); font-family: var(--ff-serif); font-size: 1.1rem; font-weight: 700; width: 36px; height: 36px; border-radius: 50%; display: grid; place-items: center; flex-shrink: 0;">1</span>
            <div>
              <h4 style="font-size: 0.9rem; color: var(--cream); margin-bottom: 0.25rem;"><?= htmlspecialchars(Lang::t('webrtc.firefox_step1_title')) ?></h4>
              <p style="font-size: 0.85rem; color: rgba(196,184,150,.65); line-height: 1.6;"><?= htmlspecialchars(Lang::t('webrtc.firefox_step1_body')) ?></p>
            </div>
          </div>
          <div style="display: flex; gap: 1rem; align-items: flex-start;">
            <span style="background: rgba(201,168,76,.1); color: var(--gold); font-family: var(--ff-serif); font-size: 1.1rem; font-weight: 700; width: 36px; height: 36px; border-radius: 50%; display: grid; place-items: center; flex-shrink: 0;">2</span>
            <div>
              <h4 style="font-size: 0.9rem; color: var(--cream); margin-bottom: 0.25rem;"><?= htmlspecialchars(Lang::t('webrtc.firefox_step2_title')) ?></h4>
              <p style="font-size: 0.85rem; color: rgba(196,184,150,.65); line-height: 1.6;"><?= htmlspecialchars(Lang::t('webrtc.firefox_step2_body')) ?></p>
            </div>
          </div>
          <div style="display: flex; gap: 1rem; align-items: flex-start;">
            <span style="background: rgba(201,168,76,.1); color: var(--gold); font-family: var(--ff-serif); font-size: 1.1rem; font-weight: 700; width: 36px; height: 36px; border-radius: 50%; display: grid; place-items: center; flex-shrink: 0;">3</span>
            <div>
              <h4 style="font-size: 0.9rem; color: var(--cream); margin-bottom: 0.25rem;"><?= htmlspecialchars(Lang::t('webrtc.firefox_step3_title')) ?></h4>
              <p style="font-size: 0.85rem; color: rgba(196,184,150,.65); line-height: 1.6;"><?= htmlspecialchars(Lang::t('webrtc.firefox_step3_body')) ?></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Small Javascript for interactive tabs -->
<script>
function switchTab(browser) {
  // Hide all content tabs
  document.querySelectorAll('.webrtc-tab-content').forEach(function(el) {
    el.style.display = 'none';
  });
  // Deactivate all tab buttons
  document.querySelectorAll('.webrtc-tab-btn').forEach(function(el) {
    el.style.color = 'var(--cream-dim)';
    el.style.borderColor = 'rgba(201,168,76,.15)';
    el.style.background = 'transparent';
  });
  // Show chosen tab
  document.getElementById('tab-content-' + browser).style.display = 'block';
  // Activate chosen button
  var btn = document.getElementById('tab-btn-' + browser);
  btn.style.color = 'var(--gold)';
  btn.style.borderColor = 'var(--gold)';
  btn.style.background = 'rgba(201,168,76, 0.05)';
}
</script>

<!-- ═══════════════════════════════════════════════════════
     FEATURES
════════════════════════════════════════════════════════ -->
<section id="features">
  <div class="section-inner">
    <div class="section-header reveal" style="text-align:center; max-width:560px; margin:0 auto 4rem;">
      <p class="section-eyebrow" style="justify-content:center;"><?= htmlspecialchars(Lang::t('features.eyebrow')) ?></p>
      <h2 class="section-heading"><?= Lang::t('features.heading') ?> <em><?= Lang::t('features.heading_em') ?></em></h2>
    </div>

    <div class="features-grid">
      <!-- 1st card: Total Privacy -->
      <div class="feature-card reveal delay-1">
        <div class="card-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <h3 class="card-title"><?= htmlspecialchars(Lang::t('features.privacy_title')) ?></h3>
        <p class="card-body"><?= htmlspecialchars(Lang::t('features.privacy_body')) ?></p>
      </div>

      <!-- 2nd card: Verified Performers -->
      <div class="feature-card reveal delay-2">
        <div class="card-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <h3 class="card-title"><?= htmlspecialchars(Lang::t('features.verified_title')) ?></h3>
        <p class="card-body"><?= htmlspecialchars(Lang::t('features.verified_body')) ?></p>
      </div>

      <!-- 3rd card: Pay-As-You-Go -->
      <div class="feature-card reveal delay-3">
        <div class="card-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
        </div>
        <h3 class="card-title"><?= htmlspecialchars(Lang::t('features.credits_title')) ?></h3>
        <p class="card-body"><?= htmlspecialchars(Lang::t('features.credits_body')) ?></p>
      </div>

      <!-- 4th card: Available 24/7 -->
      <div class="feature-card reveal delay-1">
        <div class="card-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <h3 class="card-title"><?= htmlspecialchars(Lang::t('features.available_title')) ?></h3>
        <p class="card-body"><?= htmlspecialchars(Lang::t('features.available_body')) ?></p>
      </div>

      <!-- 5th card: HD Video & Voice -->
      <div class="feature-card reveal delay-2">
        <div class="card-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M23 7a2 2 0 0 0-2.45-1.45L16 7V5a2 2 0 0 0-2-2H2a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2l4.55 1.45A2 2 0 0 0 23 17V7z"/></svg>
        </div>
        <h3 class="card-title"><?= htmlspecialchars(Lang::t('features.audio_title')) ?></h3>
        <p class="card-body"><?= htmlspecialchars(Lang::t('features.audio_body')) ?></p>
      </div>

      <!-- 6th card: In-Call Text Chat -->
      <div class="feature-card reveal delay-3">
        <div class="card-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        </div>
        <h3 class="card-title"><?= htmlspecialchars(Lang::t('features.rated_title')) ?></h3>
        <p class="card-body"><?= htmlspecialchars(Lang::t('features.rated_body')) ?></p>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════
     PRICING / CREDITS
════════════════════════════════════════════════════════ -->
<section id="pricing">
  <div class="section-inner">
    <div class="section-header reveal" style="text-align:center; max-width:520px; margin:0 auto 4rem;">
      <p class="section-eyebrow" style="justify-content:center;"><?= htmlspecialchars(Lang::t('pricing.eyebrow')) ?></p>
      <h2 class="section-heading"><?= Lang::t('pricing.heading') ?> <em><?= Lang::t('pricing.heading_em') ?></em></h2>
      <p class="section-body" style="margin:0 auto;"><?= htmlspecialchars(Lang::t('pricing.body')) ?></p>
    </div>

    <div class="pricing-grid">
      <div class="pricing-card reveal delay-1">
        <div class="pricing-name"><?= htmlspecialchars(Lang::t('pricing.starter')) ?></div>
        <div class="pricing-price"><?= htmlspecialchars($homePrices['starter']) ?></div>
        <div class="pricing-credits">
          <?= number_format($starterCredits, 0) ?> Credits
          <?php if ($starterBonus > 0): ?>
            <span class="pricing-bonus">+<?= number_format($starterBonus, 0) ?> Bonus</span>
          <?php endif; ?>
        </div>
        <ul class="pricing-perks">
          <li><?= htmlspecialchars(Lang::t('pricing.perk_access')) ?></li>
          <li><?= htmlspecialchars(Lang::t('pricing.perk_expire')) ?></li>
        </ul>
        <a href="<?= BASE_PATH ?>/<?= $isLoggedIn ? 'credits' : 'register' ?>" class="btn-ghost btn-block"><?= htmlspecialchars(Lang::t('pricing.get_started')) ?></a>
      </div>

      <div class="pricing-card pricing-featured reveal delay-2">
        <div class="pricing-badge"><?= htmlspecialchars(Lang::t('pricing.popular')) ?></div>
        <div class="pricing-name"><?= htmlspecialchars(Lang::t('pricing.gentleman')) ?></div>
        <div class="pricing-price"><?= htmlspecialchars($homePrices['gentleman']) ?></div>
        <div class="pricing-credits">
          <?= number_format($gentlemanCredits, 0) ?> Credits
          <?php if ($gentlemanBonus > 0): ?>
            <span class="pricing-bonus">+<?= number_format($gentlemanBonus, 0) ?> Bonus</span>
          <?php endif; ?>
        </div>
        <ul class="pricing-perks">
          <li><?= htmlspecialchars(Lang::t('pricing.perk_priority')) ?></li>
          <li><?= htmlspecialchars(Lang::t('pricing.perk_expire')) ?></li>
          <li><?= htmlspecialchars(Lang::t('pricing.perk_bonus')) ?></li>
        </ul>
        <a href="<?= BASE_PATH ?>/<?= $isLoggedIn ? 'credits' : 'register' ?>" class="btn-primary btn-block"><?= htmlspecialchars(Lang::t('pricing.choose')) ?></a>
      </div>

      <div class="pricing-card reveal delay-3">
        <div class="pricing-name"><?= htmlspecialchars(Lang::t('pricing.elite')) ?></div>
        <div class="pricing-price"><?= htmlspecialchars($homePrices['elite']) ?></div>
        <div class="pricing-credits">
          <?= number_format($eliteCredits, 0) ?> Credits
          <?php if ($eliteBonus > 0): ?>
            <span class="pricing-bonus">+<?= number_format($eliteBonus, 0) ?> Bonus</span>
          <?php endif; ?>
        </div>
        <ul class="pricing-perks">
          <li><?= htmlspecialchars(Lang::t('pricing.perk_vip')) ?></li>
          <li><?= htmlspecialchars(Lang::t('pricing.perk_expire')) ?></li>
          <li><?= htmlspecialchars(Lang::t('pricing.perk_max')) ?></li>
        </ul>
        <a href="<?= BASE_PATH ?>/<?= $isLoggedIn ? 'credits' : 'register' ?>" class="btn-ghost btn-block"><?= htmlspecialchars(Lang::t('pricing.go_elite')) ?></a>
      </div>
    </div>

    <p class="pricing-note reveal"><?= htmlspecialchars(Lang::t('pricing.note')) ?></p>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════
     CTA BANNER
════════════════════════════════════════════════════════ -->
<?php if (!$isLoggedIn): ?>
<section id="cta-banner">
  <div class="cta-inner reveal">
    <div class="cta-text">
      <h2 class="cta-heading"><?= htmlspecialchars(Lang::t('cta.heading')) ?></h2>
      <p class="cta-sub"><?= htmlspecialchars(Lang::t('cta.body')) ?></p>
    </div>
    <div class="cta-actions">
      <a href="<?= BASE_PATH ?>/register" class="btn-primary btn-lg"><?= htmlspecialchars(Lang::t('cta.register')) ?></a>
      <a href="<?= BASE_PATH ?>/performers" class="btn-ghost btn-lg"><?= htmlspecialchars(Lang::t('cta.browse')) ?></a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════
     FOOTER
════════════════════════════════════════════════════════ -->
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
          <li><a href="<?= BASE_PATH ?>/performers"><?= htmlspecialchars(Lang::t('nav.performers')) ?></a></li>
          <li><a href="<?= BASE_PATH ?>/#how-it-works"><?= htmlspecialchars(Lang::t('nav.how_it_works')) ?></a></li>
          <li><a href="<?= BASE_PATH ?>/#pricing"><?= htmlspecialchars(Lang::t('nav.credits')) ?></a></li>
        </ul>
      </div>
      <div class="footer-col">
        <p class="footer-col-title"><?= htmlspecialchars(Lang::t('footer.account')) ?></p>
        <ul class="footer-links">
          <?php if ($isLoggedIn): ?>
            <li><a href="<?= BASE_PATH ?>/account"><?= htmlspecialchars(Lang::t('nav.my_account')) ?></a></li>
            <li><a href="<?= BASE_PATH ?>/credits"><?= htmlspecialchars(Lang::t('footer.buy')) ?></a></li>
            <li><a href="<?= BASE_PATH ?>/logout"><?= htmlspecialchars(Lang::t('nav.sign_out')) ?></a></li>
          <?php else: ?>
            <li><a href="<?= BASE_PATH ?>/login"><?= htmlspecialchars(Lang::t('nav.sign_in')) ?></a></li>
            <li><a href="<?= BASE_PATH ?>/register"><?= htmlspecialchars(Lang::t('footer.register')) ?></a></li>
          <?php endif; ?>
        </ul>
      </div>
      <div class="footer-col">
        <p class="footer-col-title"><?= htmlspecialchars(Lang::t('footer.legal')) ?></p>
        <ul class="footer-links">
          <li><a href="<?= BASE_PATH ?>/terms"><?= htmlspecialchars(Lang::t('footer.terms')) ?></a></li>
          <li><a href="<?= BASE_PATH ?>/privacy"><?= htmlspecialchars(Lang::t('footer.privacy')) ?></a></li>
          <li><a href="<?= BASE_PATH ?>/2257"><?= htmlspecialchars(Lang::t('footer.usc')) ?></a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p class="footer-copy"><?= htmlspecialchars(Lang::t('footer.copy', ['year' => date('Y')])) ?></p>
      <p class="footer-copy">Designed &amp; Maintained by <a href="https://lexdigitals.co.za" target="_blank" rel="noopener noreferrer" style="color: inherit; text-decoration: underline;">Lex Digitals</a></p>

      <p class="footer-copy" style="display:flex; align-items:center; justify-content:center; gap:0.5rem; flex-wrap:wrap;">
        <?= htmlspecialchars(Lang::t('footer.adults')) ?> &nbsp;·&nbsp; support@thegentlemensplace.eu
        &nbsp;·&nbsp;
        <a href="https://www.rtalabel.org" target="_blank" rel="noopener noreferrer" style="display:inline-block; vertical-align:middle; line-height:0;">
          <img src="<?= BASE_PATH ?>/rta_logo.svg" alt="Restricted to Adults (RTA)" style="border:0; height:18px; width:auto;" />
        </a>
      </p>
    </div>
  </div>
</footer>

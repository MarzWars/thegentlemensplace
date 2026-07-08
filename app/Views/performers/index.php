<?php
// ── Demo performers shown when DB is empty ────────────────
$demoPerformers = [
  [
    'id'             => 1,
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
    'status'         => 'active',
    '_demo'          => true,
    '_initials'      => 'IR',
    '_color'         => 'linear-gradient(145deg,#6b1a2a,#2a1a0a)',
  ],
  [
    'id'             => 2,
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
    'status'         => 'active',
    '_demo'          => true,
    '_initials'      => 'SL',
    '_color'         => 'linear-gradient(145deg,#1a2a4a,#0a1a2a)',
  ],
  [
    'id'             => 3,
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
    'status'         => 'active',
    '_demo'          => true,
    '_initials'      => 'VB',
    '_color'         => 'linear-gradient(145deg,#0a0a0a,#2a1a2a)',
  ],
  [
    'id'             => 4,
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
    'status'         => 'active',
    '_demo'          => true,
    '_initials'      => 'AG',
    '_color'         => 'linear-gradient(145deg,#2a1a0a,#4a2a0a)',
  ],
  [
    'id'             => 5,
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
    'status'         => 'active',
    '_demo'          => true,
    '_initials'      => 'CN',
    '_color'         => 'linear-gradient(145deg,#1a0a2a,#2a0a1a)',
  ],
  [
    'id'             => 6,
    'slug'           => 'luna-voss',
    'display_name'   => 'Luna Voss',
    'age'            => 25,
    'bio'            => 'Playful, creative and completely in the moment. I bring the fantasy — you just have to show up.',
    'category'       => 'roleplay,chat',
    'languages'      => 'English',
    'rate_per_minute'=> '2.50',
    'rating_avg'     => '4.6',
    'rating_count'   => 143,
    'total_calls'    => 780,
    'online_status'  => 0,
    'profile_photo'  => null,
    'status'         => 'active',
    '_demo'          => true,
    '_initials'      => 'LV',
    '_color'         => 'linear-gradient(145deg,#0a1a0a,#1a2a1a)',
  ],
];

// Use DB results if available, otherwise fall back to demo
$list    = !empty($performers) ? $performers : $demoPerformers;
$isDemo  = empty($performers);

$categories = ['chat', 'roleplay', 'fantasy', 'couples', 'mature', 'fetish'];
$activeCategory = $filters['category'] ?? '';
$activeSort     = $filters['sort'] ?? 'popular';

$itemListElements = [];
$position = 1;
foreach ($list as $p) {
    $itemListElements[] = [
        '@type' => 'ListItem',
        'position' => $position++,
        'item' => [
            '@type' => 'Person',
            'name' => htmlspecialchars($p['display_name']),
            'url' => BASE_URL . BASE_PATH . '/performer/' . htmlspecialchars($p['slug'])
        ]
    ];
}

$jsonLdData = [
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => $title ?? 'Performers',
    'itemListElement' => $itemListElements
];
$jsonLd = json_encode($jsonLdData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
?>

<!-- ── Page header ── -->
<div class="performers-hero">
  <div class="performers-hero-inner">
    <p class="section-eyebrow" style="justify-content:center; margin-bottom:1rem;">Our Performers</p>
    <h1 class="performers-hero-title">Meet the <em>Collection</em></h1>
    <p class="performers-hero-sub">Verified, exclusive performers available for private connections.</p>
  </div>
</div>

<!-- ── Filters bar ── -->
<div class="performers-filters-bar">
  <div class="performers-filters-inner">

    <!-- Category pills -->
    <div class="filter-pills" role="group" aria-label="Filter by category">
      <a href="<?= BASE_PATH ?>/performers"
         class="filter-pill <?= $activeCategory === '' ? 'active' : '' ?>">All</a>
      <?php foreach ($categories as $cat): ?>
        <a href="<?= BASE_PATH ?>/performers/category/<?= urlencode($cat) ?><?= $activeSort !== 'popular' ? '?sort='.$activeSort : '' ?>"
           class="filter-pill <?= $activeCategory === $cat ? 'active' : '' ?>">
          <?= ucfirst($cat) ?>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- Sort + online toggle -->
    <div class="filter-controls">
      <label class="filter-online-toggle">
        <input type="checkbox" id="online-toggle" <?= !empty($filters['online_only']) ? 'checked' : '' ?> />
        <span class="toggle-track"><span class="toggle-thumb"></span></span>
        <span class="toggle-label">Online only</span>
      </label>

      <select class="filter-sort" id="sort-select" aria-label="Sort performers">
        <option value="popular" <?= $activeSort === 'popular' ? 'selected' : '' ?>>Most Popular</option>
        <option value="rating"  <?= $activeSort === 'rating'  ? 'selected' : '' ?>>Top Rated</option>
        <option value="newest"  <?= $activeSort === 'newest'  ? 'selected' : '' ?>>Newest</option>
      </select>
    </div>

  </div>
</div>

<!-- ── Demo notice ── -->
<?php if ($isDemo): ?>
<div class="demo-notice">
  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
  More performers coming soon!
</div>
<?php endif; ?>

<!-- ── Grid ── -->
<style>
  .premium-card {
    border: 1px solid #c9a84c !important;
    box-shadow: 0 0 15px rgba(201, 168, 76, 0.15);
    position: relative;
  }
  .premium-card::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    box-shadow: inset 0 0 20px rgba(201, 168, 76, 0.05);
    pointer-events: none;
    z-index: 5;
  }
</style>
<div class="performers-page">
  <div class="performers-grid" id="performers-grid">
    <?php foreach ($list as $p): ?>
    <?php
      $name     = htmlspecialchars($p['display_name']);
      $slug     = htmlspecialchars($p['slug']);
      $age      = (int)$p['age'];
      $bio      = htmlspecialchars($p['bio'] ?? '');
      $rate     = number_format((float)$p['rate_per_minute'], 0);
      $rating   = number_format((float)$p['rating_avg'], 1);
      $reviews  = number_format((int)$p['rating_count']);
      $calls    = number_format((int)$p['total_calls']);
      $online   = (bool)$p['online_status'];
      $cats     = array_slice(explode(',', $p['category']), 0, 2);
      $langs    = htmlspecialchars($p['languages'] ?? 'English');
      $initials = $p['_initials'] ?? strtoupper(substr($p['display_name'], 0, 2));
      $bgColor  = $p['_color'] ?? 'linear-gradient(145deg,#1a1610,#2a1a0a)';
      $hasPhoto = !empty($p['profile_photo']);
      $isPremium= ($p['tier'] ?? '') === 'premium';
    ?>
    <article class="performer-card reveal <?= $isPremium ? 'premium-card' : '' ?>" aria-label="<?= $name ?>">
      <?php if ($isPremium): ?>
        <div class="premium-badge-wrapper" style="position: absolute; top: -10px; right: -10px; z-index: 10;">
          <div style="background: linear-gradient(135deg, #c9a84c 0%, #e2c974 50%, #b29239 100%); color: #111; font-weight: bold; font-size: 0.7rem; padding: 4px 10px; border-radius: 4px; box-shadow: 0 4px 10px rgba(0,0,0,0.5); letter-spacing: 0.05em; text-transform: uppercase;">Premium</div>
        </div>
      <?php endif; ?>

      <!-- Photo / avatar -->
      <a href="<?= BASE_PATH ?>/performer/<?= $slug ?>" class="performer-card-photo-wrap" tabindex="-1" aria-hidden="true">
        <?php if ($hasPhoto): ?>
          <img
            src="<?= BASE_PATH ?>/<?= htmlspecialchars($p['profile_photo']) ?>"
            alt="<?= $name ?>"
            class="performer-card-photo"
            width="400" height="600"
            loading="lazy"
          />
        <?php else: ?>
          <div class="performer-card-avatar" style="background:<?= $bgColor ?>">
            <span class="performer-card-initials"><?= $initials ?></span>
          </div>
        <?php endif; ?>

        <!-- Online badge -->
        <div class="performer-card-status <?= $online ? 'is-online' : 'is-offline' ?>" aria-label="<?= $online ? 'Online' : 'Offline' ?>">
          <span class="status-dot-sm"></span>
          <?= $online ? 'Online' : 'Offline' ?>
        </div>

        <!-- Rate badge -->
        <div class="performer-card-rate">
          <?= $rate ?> cr<span class="rate-slash">/</span>min
        </div>
      </a>

      <!-- Info -->
      <div class="performer-card-body">
        <div class="performer-card-top">
          <div>
            <h2 class="performer-card-name">
              <a href="<?= BASE_PATH ?>/performer/<?= $slug ?>"><?= $name ?></a>
            </h2>
            <p class="performer-card-meta">Age <?= $age ?> &nbsp;·&nbsp; <?= $langs ?></p>
          </div>
          <div class="performer-card-rating" aria-label="Rating <?= $rating ?>">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <span><?= $rating ?></span>
            <span class="rating-count-sm">(<?= $reviews ?>)</span>
          </div>
        </div>

        <!-- Categories -->
        <div class="performer-card-cats">
          <?php foreach ($cats as $cat): ?>
            <span class="performer-cat-tag"><?= ucfirst(trim($cat)) ?></span>
          <?php endforeach; ?>
        </div>

        <!-- Bio snippet -->
        <p class="performer-card-bio"><?= mb_strimwidth($bio, 0, 100, '…') ?></p>

        <!-- Footer -->
        <div class="performer-card-footer">
          <div class="performer-card-calls">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.8a16 16 0 0 0 6.29 6.29l.95-.95a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            <?= $calls ?> calls
          </div>
          <a href="<?= BASE_PATH ?>/performer/<?= $slug ?>" class="performer-card-cta <?= $online ? '' : 'cta-offline' ?>">
            <?= $online ? 'Connect Now' : 'View Profile' ?>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
          </a>
        </div>
      </div>

    </article>
    <?php endforeach; ?>
  </div>

  <!-- Pagination (only shown with real DB data) -->
  <?php if (!$isDemo && $totalPages > 1): ?>
  <nav class="performers-pagination" aria-label="Pagination">
    <?php $catPrefix = $activeCategory ? '/category/' . urlencode($activeCategory) : ''; ?>
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <a href="<?= BASE_PATH ?>/performers<?= $catPrefix ?>?page=<?= $i ?><?= $activeSort !== 'popular' ? '&sort='.$activeSort : '' ?>"
         class="page-btn <?= $filters['page'] === $i ? 'active' : '' ?>"
         aria-label="Page <?= $i ?>" <?= $filters['page'] === $i ? 'aria-current="page"' : '' ?>>
        <?= $i ?>
      </a>
    <?php endfor; ?>
  </nav>
  <?php endif; ?>

</div>

<script>
// Filter controls — rebuild URL and navigate
(function () {
  const base = '<?= BASE_PATH ?>/performers<?= $activeCategory ? '/category/' . urlencode($activeCategory) : '' ?>';

  function buildUrl() {
    const params = new URLSearchParams(window.location.search);
    const sort   = document.getElementById('sort-select')?.value;
    const online = document.getElementById('online-toggle')?.checked;
    if (sort && sort !== 'popular') params.set('sort', sort); else params.delete('sort');
    if (online) params.set('online', '1'); else params.delete('online');
    params.delete('page');
    const qs = params.toString();
    window.location.href = base + (qs ? '?' + qs : '');
  }

  document.getElementById('sort-select')?.addEventListener('change', buildUrl);
  document.getElementById('online-toggle')?.addEventListener('change', buildUrl);
})();
</script>

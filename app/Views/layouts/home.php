<?php
use App\Core\Lang;
$locale      = Lang::locale();
$currentPath = '/' . ltrim($_GET['url'] ?? '', '/');
// Strip locale prefix from path for hreflang building
$cleanPath   = preg_replace('#^/(' . implode('|', array_keys(Lang::$supported)) . ')(/|$)#', '/', $currentPath);
$cleanPath   = '/' . ltrim($cleanPath, '/');
$canonicalUrl = BASE_URL . BASE_PATH . Lang::prefix() . ($cleanPath === '/' ? '' : $cleanPath);
$pageTitle    = htmlspecialchars($title    ?? Lang::t('meta.home_title'));
$pageDesc     = htmlspecialchars($metaDesc ?? Lang::t('meta.home_desc'));
$pageKeywords = htmlspecialchars($metaKeywords ?? Lang::t('meta.home_keywords'));
// OG Image: use per-page image or fall back to branded default
$ogImage      = !empty($ogImageUrl)
    ? htmlspecialchars($ogImageUrl)
    : BASE_URL . BASE_PATH . '/Assets/img/og-default.jpg';
$isHomePage   = ($cleanPath === '/' || $cleanPath === '');
?>
<!DOCTYPE html>
<html lang="<?= $locale ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="RATING" content="RTA-5042-1996-1400-1577-RTA" />
  <meta name="theme-color" content="#0a0805" />

  <!-- ── SEO: Google Analytics 4 (consent-aware) ── -->
  <!-- Default consent state: denied until user accepts cookies -->
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('consent', 'default', {
      'analytics_storage': 'denied',
      'ad_storage':        'denied',
      'wait_for_update':   500
    });
    gtag('js', new Date());
    gtag('config', 'G-TRV7G9DE1N', {'send_page_view': true});
  </script>
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-TRV7G9DE1N"></script>

  <!-- ── SEO: Title, Description, Keywords, Author ── -->
  <title><?= $pageTitle ?></title>
  <meta name="description" content="<?= $pageDesc ?>" />
  <meta name="keywords"    content="<?= $pageKeywords ?>" />
  <meta name="author"      content="<?= htmlspecialchars(Lang::t('meta.author')) ?>" />
  <meta name="robots"      content="<?= ($metaRobots ?? 'index, follow') ?>" />

  <!-- ── SEO: Canonical ── -->
  <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>" />

  <!-- ── SEO: hreflang alternates ── -->
  <?php foreach (Lang::$supported as $code => $label): ?>
  <link rel="alternate" hreflang="<?= $code ?>" href="<?= htmlspecialchars(Lang::hreflangUrl($code, $currentPath)) ?>" />
  <?php endforeach; ?>
  <!-- x-default points to English -->
  <link rel="alternate" hreflang="x-default" href="<?= htmlspecialchars(Lang::hreflangUrl('en', $currentPath)) ?>" />

  <!-- ── Open Graph ── -->
  <meta property="og:type"        content="website" />
  <meta property="og:locale"      content="<?= $locale ?>_<?= strtoupper($locale) ?>" />
  <meta property="og:title"       content="<?= $pageTitle ?>" />
  <meta property="og:description" content="<?= $pageDesc ?>" />
  <meta property="og:url"         content="<?= htmlspecialchars($canonicalUrl) ?>" />
  <meta property="og:site_name"   content="<?= htmlspecialchars(Lang::t('meta.site_name')) ?>" />
  <meta property="og:image"       content="<?= $ogImage ?>" />
  <meta property="og:image:width"  content="1200" />
  <meta property="og:image:height" content="630" />
  <meta property="og:image:alt"    content="<?= htmlspecialchars(Lang::t('meta.site_name')) ?> — <?= htmlspecialchars(Lang::t('footer.tagline')) ?>" />

  <!-- ── Twitter / X Cards ── -->
  <meta name="twitter:card"        content="summary_large_image" />
  <meta name="twitter:site"        content="<?= htmlspecialchars(Lang::t('meta.twitter_site')) ?>" />
  <meta name="twitter:title"       content="<?= $pageTitle ?>" />
  <meta name="twitter:description" content="<?= $pageDesc ?>" />
  <meta name="twitter:image"       content="<?= $ogImage ?>" />

  <!-- ── Resource hints ── -->
  <link rel="dns-prefetch" href="//fonts.googleapis.com" />
  <link rel="dns-prefetch" href="//fonts.gstatic.com" />
  <link rel="dns-prefetch" href="//www.googletagmanager.com" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="preload" href="<?= BASE_PATH ?>/Assets/css/main.css" as="style" />

  <!-- ── Favicon ── -->
  <link rel="icon" type="image/svg+xml" href="<?= BASE_PATH ?>/rta_logo.svg" />
  <link rel="shortcut icon" href="<?= BASE_PATH ?>/Assets/img/favicon.ico" />

  <link rel="stylesheet" href="<?= BASE_PATH ?>/Assets/css/main.css" />

  <!-- ── JSON-LD Structured Data ── -->
  <?php if ($isHomePage): ?>
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@graph": [
      {
        "@type": "WebSite",
        "@id": "<?= BASE_URL ?>#website",
        "name": <?= json_encode(Lang::t('meta.site_name')) ?>,
        "url": "<?= BASE_URL ?>",
        "description": <?= json_encode(Lang::t('meta.home_desc')) ?>,
        "inLanguage": [<?= implode(',', array_map(fn($c) => '"' . $c . '"', array_keys(Lang::$supported))) ?>],
        "potentialAction": {
          "@type": "SearchAction",
          "target": {
            "@type": "EntryPoint",
            "urlTemplate": "<?= BASE_URL ?>/performers?category={search_term_string}"
          },
          "query-input": "required name=search_term_string"
        }
      },
      {
        "@type": "Organization",
        "@id": "<?= BASE_URL ?>#organization",
        "name": <?= json_encode(Lang::t('meta.site_name')) ?>,
        "url": "<?= BASE_URL ?>",
        "logo": "<?= BASE_URL . BASE_PATH ?>/Assets/img/og-default.jpg",
        "contactPoint": {
          "@type": "ContactPoint",
          "email": "support@thegentlemensplace.eu",
          "contactType": "customer service"
        }
      },
      {
        "@type": "FAQPage",
        "mainEntity": [
          {
            "@type": "Question",
            "name": "How do I start a private phone sex or video call?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Create a free account in under two minutes, purchase credits, then browse our verified performers and click Connect. You'll receive a private connection instantly."
            }
          },
          {
            "@type": "Question",
            "name": "How much does it cost for a private adult chat session?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "We operate on a pay-per-minute credit system. There are no subscriptions or hidden fees. Credit packages start from R150 and credits never expire. Each performer sets their own per-minute rate."
            }
          },
          {
            "@type": "Question",
            "name": "Is my identity kept private?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Yes. No real names are required. Your billing is discreet and your activity is fully encrypted. We never share your information with third parties."
            }
          },
          {
            "@type": "Question",
            "name": "Are the adult performers verified?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Every performer on The Gentleman's Place is manually reviewed and approved before going live. All performers are over 18 and hold verified documentation."
            }
          }
        ]
      }
    ]
  }
  </script>
  <?php endif; ?>
  <?php if (!empty($jsonLd)): ?>
  <script type="application/ld+json"><?= $jsonLd ?></script>
  <?php endif; ?>

</head>
<body>

<?php if (defined('MAINTENANCE_MODE') && MAINTENANCE_MODE): ?>
  <?php if (!empty($_SESSION['admin_id'])): ?>
    <div style="background: #6b1a2a; color: #f0e8d0; text-align: center; padding: 0.6rem 1rem; font-size: 0.8rem; font-weight: 600; letter-spacing: 0.05em; border-bottom: 1px solid rgba(201,168,76,.2); position: relative; z-index: 10001; font-family: sans-serif;">
      ⚠️ SITE IS CURRENTLY IN MAINTENANCE MODE (Visible only to logged-in administrators)
    </div>
  <?php elseif (!empty($_SESSION['user_id']) || !empty($_SESSION['performer_id'])): ?>
    <div style="background: #6b1a2a; color: #f0e8d0; text-align: center; padding: 0.6rem 1rem; font-size: 0.8rem; font-weight: 600; letter-spacing: 0.05em; border-bottom: 1px solid rgba(201,168,76,.2); position: relative; z-index: 10001; font-family: sans-serif;">
      ⚠️ THE SITE IS CURRENTLY UNDERGOING SCHEDULED MAINTENANCE.
    </div>
  <?php endif; ?>
<?php endif; ?>

<div id="cursor"></div>
<div id="cursor-ring"></div>

<!-- PRELOADER -->
<div id="preloader" role="status" aria-label="Loading">
  <div class="preloader-crest">
    <div class="preloader-line">Est. MMXXV</div>
    <div class="preloader-monogram">GC</div>
    <div class="preloader-line"><?= htmlspecialchars(Lang::t('meta.site_name')) ?></div>
  </div>
  <div class="preloader-bar-wrap"><div class="preloader-bar"></div></div>
  <div class="preloader-pct">0%</div>
</div>

<!-- AGE GATE -->
<?php if (!isset($_COOKIE['age_confirmed'])): ?>
<div id="age-gate" class="age-gate-overlay" role="dialog" aria-modal="true" aria-labelledby="age-gate-title">
  <div class="age-gate-modal">
    <div class="age-gate-monogram">GC</div>
    <h2 class="age-gate-title" id="age-gate-title"><?= htmlspecialchars(Lang::t('age_gate.title')) ?></h2>
    <p class="age-gate-body"><?= Lang::t('age_gate.body') ?></p>
    <ul class="age-gate-list">
      <li><?= htmlspecialchars(Lang::t('age_gate.item_1')) ?></li>
      <li><?= htmlspecialchars(Lang::t('age_gate.item_2')) ?></li>
      <li><?= str_replace(
            [':terms', ':privacy'],
            [
              '<a href="' . BASE_PATH . '/terms">' . htmlspecialchars(Lang::t('footer.terms')) . '</a>',
              '<a href="' . BASE_PATH . '/privacy">' . htmlspecialchars(Lang::t('footer.privacy')) . '</a>',
            ],
            htmlspecialchars(Lang::t('age_gate.item_3'))
          ) ?></li>
    </ul>
    <div class="age-gate-actions">
      <button id="age-enter-btn" class="btn-primary"><?= htmlspecialchars(Lang::t('age_gate.enter')) ?></button>
      <a href="https://google.com" class="age-gate-exit"><?= htmlspecialchars(Lang::t('age_gate.exit')) ?></a>
    </div>
    <p class="age-gate-legal"><?= htmlspecialchars(Lang::t('age_gate.legal')) ?></p>
  </div>
</div>
<script>
document.getElementById('age-enter-btn').addEventListener('click', function () {
  var d = new Date();
  d.setTime(d.getTime() + 30 * 24 * 60 * 60 * 1000);
  document.cookie = 'age_confirmed=1;expires=' + d.toUTCString() + ';path=/;SameSite=Lax';
  document.getElementById('age-gate').style.display = 'none';
});
</script>
<?php endif; ?>

<!-- NAVBAR -->
<header id="navbar">
  <div class="nav-inner">
    <a href="<?= Lang::base() ?>/" class="nav-logo" aria-label="<?= htmlspecialchars(Lang::t('meta.site_name')) ?> — Home">
      <img src="<?= BASE_PATH ?>/Assets/img/logo.png" alt="<?= htmlspecialchars(Lang::t('meta.site_name')) ?>" style="height: 90px; width: auto; margin-top: 5px;" />
    </a>

    <nav class="nav-links" aria-label="Main navigation">
      <a href="<?= Lang::base() ?>/performers"><?= htmlspecialchars(Lang::t('nav.performers')) ?></a>
      <a href="<?= Lang::base() ?>/#how-it-works"><?= htmlspecialchars(Lang::t('nav.how_it_works')) ?></a>
      <a href="<?= Lang::base() ?>/#pricing"><?= htmlspecialchars(Lang::t('nav.credits')) ?></a>
    </nav>

    <div class="nav-actions">
      <?php if (!empty($_SESSION['user_id'])): ?>
        <span class="nav-credit-badge">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          <?= Lang::t('nav.credit_badge', ['count' => number_format((float)($_SESSION['credits'] ?? 0), 0)]) ?>
        </span>
        <a href="<?= Lang::base() ?>/account" class="nav-link-btn"><?= htmlspecialchars(Lang::t('nav.my_account')) ?></a>
        <a href="<?= Lang::base() ?>/logout" class="btn-ghost-sm"><?= htmlspecialchars(Lang::t('nav.sign_out')) ?></a>
      <?php else: ?>
        <a href="<?= Lang::base() ?>/login" class="nav-link-btn"><?= htmlspecialchars(Lang::t('nav.sign_in')) ?></a>
        <a href="<?= Lang::base() ?>/register" class="btn-primary-sm"><?= htmlspecialchars(Lang::t('nav.join_now')) ?></a>
      <?php endif; ?>

      <!-- Language switcher -->
      <div class="lang-switcher" id="lang-switcher">
        <button class="lang-switcher-btn" aria-haspopup="true" aria-expanded="false" aria-label="<?= htmlspecialchars(Lang::t('lang.switcher_label')) ?>">
          <span class="lang-current"><?= strtoupper($locale) ?></span>
          <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <ul class="lang-dropdown" role="menu">
          <?php foreach (Lang::$supported as $code => $label): ?>
          <li role="none">
            <a href="<?= htmlspecialchars(Lang::hreflangUrl($code, $currentPath)) ?>"
               role="menuitem"
               class="lang-option <?= $code === $locale ? 'lang-active' : '' ?>"
               hreflang="<?= $code ?>"
               lang="<?= $code ?>">
              <span class="lang-code"><?= strtoupper($code) ?></span>
              <span class="lang-name"><?= htmlspecialchars($label) ?></span>
            </a>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <button class="nav-hamburger" id="hamburger" aria-label="Toggle menu" aria-expanded="false" aria-controls="nav-drawer">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

<!-- MOBILE DRAWER -->
<div id="nav-drawer" role="dialog" aria-label="Mobile navigation" aria-hidden="true">
  <button class="drawer-close" id="drawer-close" aria-label="Close menu">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
  </button>
  <div class="drawer-logo">
    <img src="<?= BASE_PATH ?>/Assets/img/logo.png" alt="<?= htmlspecialchars(Lang::t('meta.site_name')) ?>" style="height: 75px; width: auto;" />
  </div>
  <nav class="drawer-links">
    <a href="<?= Lang::base() ?>/performers"><?= htmlspecialchars(Lang::t('nav.performers')) ?></a>
    <a href="<?= Lang::base() ?>/#how-it-works"><?= htmlspecialchars(Lang::t('nav.how_it_works')) ?></a>
    <a href="<?= Lang::base() ?>/#pricing"><?= htmlspecialchars(Lang::t('nav.credits')) ?></a>
    <?php if (!empty($_SESSION['user_id'])): ?>
      <a href="<?= Lang::base() ?>/account"><?= htmlspecialchars(Lang::t('nav.my_account')) ?></a>
      <a href="<?= Lang::base() ?>/logout"><?= htmlspecialchars(Lang::t('nav.sign_out')) ?></a>
    <?php else: ?>
      <a href="<?= Lang::base() ?>/login"><?= htmlspecialchars(Lang::t('nav.sign_in')) ?></a>
      <a href="<?= Lang::base() ?>/register" class="drawer-cta"><?= htmlspecialchars(Lang::t('nav.join_now')) ?></a>
    <?php endif; ?>
    <!-- Language options in drawer -->
    <div class="drawer-lang-row">
      <?php foreach (Lang::$supported as $code => $label): ?>
        <a href="<?= htmlspecialchars(Lang::hreflangUrl($code, $currentPath)) ?>"
           class="drawer-lang-btn <?= $code === $locale ? 'active' : '' ?>"
           hreflang="<?= $code ?>" lang="<?= $code ?>"
           title="<?= htmlspecialchars($label) ?>">
          <?= strtoupper($code) ?>
        </a>
      <?php endforeach; ?>
    </div>
  </nav>
</div>
<div id="drawer-overlay"></div>

<!-- FLASH MESSAGES -->
<?php if (!empty($_SESSION['flash_error'])): ?>
  <div class="flash flash-error" role="alert"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
  <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>
<?php if (!empty($_SESSION['flash_success'])): ?>
  <div class="flash flash-success" role="alert"><?= htmlspecialchars($_SESSION['flash_success']) ?></div>
  <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<?= $content ?>

<!-- SITE FOOTER — rendered for all pages using this layout -->
<?php
// home/index.php includes its own footer inline, so skip it here for that view
// All other views (credits, performers, etc.) get the footer from the layout
if (!isset($skipLayoutFooter)):
?>
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
          <li><a href="https://client.thegentlemensplace.eu">Work With Us</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <p class="footer-col-title"><?= htmlspecialchars(Lang::t('footer.account')) ?></p>
        <ul class="footer-links">
          <?php if (!empty($_SESSION['user_id'])): ?>
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
      <div class="footer-col">
        <p class="footer-col-title">Partners</p>
        <ul class="footer-links">
          <li><a href="https://thepornmap.com" target="_blank" rel="noopener noreferrer">best porn sites</a></li>
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
<?php endif; ?>

<button id="back-top" aria-label="Back to top">
  <svg viewBox="0 0 24 24"><polyline points="18 15 12 9 6 15"/></svg>
</button>

<?php require APP_ROOT . '/Views/partials/cookie-banner.php'; ?>
<script src="<?= BASE_PATH ?>/Assets/js/main.js"></script>
</body>
</html>

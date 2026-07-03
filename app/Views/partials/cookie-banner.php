<?php
use App\Core\Lang;
// Only render if consent cookie not yet set
if (isset($_COOKIE['tgp_cookie_consent'])) return;
?>
<!-- ══════════════════════════════════════════════════════
     GDPR COOKIE CONSENT BANNER
     Consent stored in cookie: tgp_cookie_consent
     Value: JSON {"essential":true,"analytics":bool,"marketing":bool}
══════════════════════════════════════════════════════ -->
<div id="cookie-banner" class="cookie-banner" role="dialog" aria-modal="true"
     aria-labelledby="cookie-banner-title" aria-live="polite">

  <!-- Simple banner (shown first) -->
  <div class="cookie-simple" id="cookie-simple">
    <div class="cookie-simple-text">
      <p class="cookie-title" id="cookie-banner-title">
        <?= htmlspecialchars(Lang::t('cookies.title')) ?>
      </p>
      <p class="cookie-body">
        <?= htmlspecialchars(Lang::t('cookies.body')) ?>
        <a href="<?= BASE_PATH ?>/privacy" class="cookie-link">
          <?= htmlspecialchars(Lang::t('cookies.privacy_link')) ?>
        </a>
      </p>
    </div>
    <div class="cookie-simple-actions">
      <button class="cookie-btn cookie-btn-primary" id="cookie-accept-all">
        <?= htmlspecialchars(Lang::t('cookies.accept_all')) ?>
      </button>
      <button class="cookie-btn cookie-btn-ghost" id="cookie-essential-only">
        <?= htmlspecialchars(Lang::t('cookies.essential_only')) ?>
      </button>
      <button class="cookie-btn cookie-btn-text" id="cookie-manage-btn">
        <?= htmlspecialchars(Lang::t('cookies.manage')) ?>
      </button>
    </div>
  </div>

  <!-- Preferences panel (shown when "Manage" clicked) -->
  <div class="cookie-preferences" id="cookie-preferences" hidden>
    <div class="cookie-pref-header">
      <p class="cookie-title"><?= htmlspecialchars(Lang::t('cookies.manage')) ?></p>
      <button class="cookie-close-btn" id="cookie-pref-back" aria-label="<?= htmlspecialchars(Lang::t('cookies.close')) ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        <?= htmlspecialchars(Lang::t('cookies.back')) ?>
      </button>
    </div>

    <div class="cookie-pref-list">
      <!-- Essential — always on -->
      <div class="cookie-pref-item">
        <div class="cookie-pref-info">
          <span class="cookie-pref-name"><?= htmlspecialchars(Lang::t('cookies.essential_title')) ?></span>
          <span class="cookie-pref-desc"><?= htmlspecialchars(Lang::t('cookies.essential_desc')) ?></span>
        </div>
        <div class="cookie-toggle cookie-toggle-locked" aria-label="<?= htmlspecialchars(Lang::t('cookies.always_enabled')) ?>">
          <span class="cookie-toggle-on"><?= htmlspecialchars(Lang::t('cookies.always_on')) ?></span>
        </div>
      </div>

      <!-- Analytics -->
      <div class="cookie-pref-item">
        <div class="cookie-pref-info">
          <span class="cookie-pref-name"><?= htmlspecialchars(Lang::t('cookies.analytics_title')) ?></span>
          <span class="cookie-pref-desc"><?= htmlspecialchars(Lang::t('cookies.analytics_desc')) ?></span>
        </div>
        <label class="cookie-toggle-wrap" aria-label="<?= htmlspecialchars(Lang::t('cookies.analytics_title')) ?>">
          <input type="checkbox" id="cookie-analytics" class="cookie-toggle-input" />
          <span class="cookie-toggle-track"><span class="cookie-toggle-thumb"></span></span>
        </label>
      </div>

      <!-- Marketing -->
      <div class="cookie-pref-item">
        <div class="cookie-pref-info">
          <span class="cookie-pref-name"><?= htmlspecialchars(Lang::t('cookies.marketing_title')) ?></span>
          <span class="cookie-pref-desc"><?= htmlspecialchars(Lang::t('cookies.marketing_desc')) ?></span>
        </div>
        <label class="cookie-toggle-wrap" aria-label="<?= htmlspecialchars(Lang::t('cookies.marketing_title')) ?>">
          <input type="checkbox" id="cookie-marketing" class="cookie-toggle-input" />
          <span class="cookie-toggle-track"><span class="cookie-toggle-thumb"></span></span>
        </label>
      </div>
    </div>

    <div class="cookie-pref-actions">
      <button class="cookie-btn cookie-btn-primary" id="cookie-save-prefs">
        <?= htmlspecialchars(Lang::t('cookies.save')) ?>
      </button>
      <button class="cookie-btn cookie-btn-ghost" id="cookie-accept-all-2">
        <?= htmlspecialchars(Lang::t('cookies.accept_all')) ?>
      </button>
    </div>
  </div>

</div>

<script>
(function () {
  var banner      = document.getElementById('cookie-banner');
  var simple      = document.getElementById('cookie-simple');
  var prefs       = document.getElementById('cookie-preferences');
  var COOKIE_NAME = 'tgp_cookie_consent';
  var COOKIE_DAYS = 365;

  function setCookie(value) {
    var d = new Date();
    d.setTime(d.getTime() + COOKIE_DAYS * 24 * 60 * 60 * 1000);
    // SameSite=Lax works on both http (localhost) and https (production)
    document.cookie = COOKIE_NAME + '=' + encodeURIComponent(JSON.stringify(value))
      + ';expires=' + d.toUTCString()
      + ';path=/;SameSite=Lax';
    
    // Set document root attributes for immediate CSS/JS availability
    document.documentElement.dataset.cookieAnalytics = value.analytics ? 'true' : 'false';
    document.documentElement.dataset.cookieMarketing = value.marketing ? 'true' : 'false';
    
    // ── GA4 Consent Mode update ──
    if (typeof gtag === 'function') {
      gtag('consent', 'update', {
        'analytics_storage': value.analytics ? 'granted' : 'denied',
        'ad_storage':        value.marketing ? 'granted' : 'denied'
      });
    }
    
    // Dispatch event
    document.dispatchEvent(new CustomEvent('cookieConsentChanged', { detail: value }));
  }

  function dismiss() {
    banner.classList.add('cookie-banner-hidden');
    setTimeout(function () { banner.remove(); }, 500);
  }

  function acceptAll() {
    setCookie({ essential: true, analytics: true, marketing: true });
    dismiss();
  }

  function essentialOnly() {
    setCookie({ essential: true, analytics: false, marketing: false });
    dismiss();
  }

  function savePrefs() {
    var analytics = document.getElementById('cookie-analytics').checked;
    var marketing = document.getElementById('cookie-marketing').checked;
    setCookie({ essential: true, analytics: analytics, marketing: marketing });
    dismiss();
  }

  // Button wiring
  document.getElementById('cookie-accept-all').addEventListener('click', acceptAll);
  document.getElementById('cookie-accept-all-2').addEventListener('click', acceptAll);
  document.getElementById('cookie-essential-only').addEventListener('click', essentialOnly);
  document.getElementById('cookie-save-prefs').addEventListener('click', savePrefs);

  document.getElementById('cookie-manage-btn').addEventListener('click', function () {
    simple.hidden = true;
    prefs.hidden  = false;
  });

  document.getElementById('cookie-pref-back').addEventListener('click', function () {
    prefs.hidden  = true;
    simple.hidden = false;
  });

  // Keyboard: Escape closes to simple view
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && !prefs.hidden) {
      prefs.hidden  = true;
      simple.hidden = false;
    }
  });

  // Animate in after a short delay (don't block page load)
  setTimeout(function () {
    banner.classList.add('cookie-banner-visible');
  }, 1200);
})();
</script>

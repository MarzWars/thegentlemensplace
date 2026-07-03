<div class="auth-page">
  <div class="auth-card">

    <div class="auth-card-header">
      <a href="<?= BASE_PATH ?>/" class="auth-logo" aria-label="Home"><span class="auth-logo-mark">GC</span></a>
      <h1 class="auth-title">Set New Password</h1>
      <p class="auth-subtitle">Choose a strong password for your account.</p>
    </div>

    <?php if (!empty($error)): ?>
      <div class="form-alert form-alert-error" role="alert">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form class="auth-form" method="POST" action="<?= BASE_PATH ?>/reset-password" novalidate id="reset-form">
      <?= \App\Core\CSRF::field() ?>
      <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>" />

      <div class="form-group">
        <label for="password" class="form-label">New Password</label>
        <div class="input-wrap">
          <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <input
            type="password" id="password" name="password" class="form-input"
            placeholder="Minimum 8 characters"
            autocomplete="new-password" minlength="8" required
          />
          <button type="button" class="input-toggle-pw" aria-label="Toggle password visibility" tabindex="-1">
            <svg class="eye-show" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            <svg class="eye-hide" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
          </button>
        </div>
        <div class="pw-strength">
          <div class="pw-strength-bar"><div class="pw-strength-fill" id="pw-strength-fill"></div></div>
          <span class="pw-strength-label" id="pw-strength-label"></span>
        </div>
      </div>

      <button type="submit" class="btn-primary btn-block btn-submit">
        <span class="btn-text">Update Password</span>
        <span class="btn-spinner" aria-hidden="true"></span>
      </button>
    </form>

  </div>
</div>

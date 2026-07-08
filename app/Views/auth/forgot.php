<div class="auth-page">
  <div class="auth-card">

    <div class="auth-card-header">
      <a href="<?= BASE_PATH ?>/" class="auth-logo" aria-label="Home">
        <img src="<?= BASE_PATH ?>/Assets/img/logo.png" alt="The Gentleman's Place" style="height: 100px; width: auto; margin-bottom: 1rem;" />
      </a>
      <h1 class="auth-title">Reset Password</h1>
      <p class="auth-subtitle">We'll send a reset link to your inbox.</p>
    </div>

    <a href="<?= BASE_PATH ?>/login" class="auth-back-link">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
      Back to Sign In
    </a>

    <?php if (!empty($error)): ?>
      <div class="form-alert form-alert-error" role="alert">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form class="auth-form" method="POST" action="<?= BASE_PATH ?>/forgot-password" novalidate id="forgot-form">
      <?= \App\Core\CSRF::field() ?>

      <div class="form-group">
        <label for="email" class="form-label">Email Address</label>
        <div class="input-wrap">
          <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          <input
            type="email" id="email" name="email" class="form-input"
            placeholder="you@example.com"
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
            autocomplete="email" required
          />
        </div>
      </div>

      <button type="submit" class="btn-primary btn-block btn-submit" id="forgot-submit">
        <span class="btn-text">Send Reset Link</span>
        <span class="btn-spinner" aria-hidden="true"></span>
      </button>
    </form>

    <div class="auth-card-footer">
      <p>Remembered it? <a href="<?= BASE_PATH ?>/login">Sign in here</a></p>
    </div>

  </div>
</div>

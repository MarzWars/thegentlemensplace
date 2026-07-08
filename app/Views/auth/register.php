<div class="auth-page">
  <div class="auth-card auth-card-wide">

    <div class="auth-card-header">
      <a href="<?= BASE_PATH ?>/" class="auth-logo" aria-label="Home">
        <img src="<?= BASE_PATH ?>/Assets/img/logo.png" alt="The Gentleman's Place" style="height: 100px; width: auto; margin-bottom: 1rem;" />
      </a>
      <h1 class="auth-title">Create Your Account</h1>
      <p class="auth-subtitle">Join thousands of members. Completely private.</p>
    </div>

    <?php if (!empty($errors)): ?>
      <div class="form-alert form-alert-error" role="alert">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        Please correct the errors below.
      </div>
    <?php endif; ?>

    <form class="auth-form" method="POST" action="<?= BASE_PATH ?>/register" novalidate id="register-form">
      <?= \App\Core\CSRF::field() ?>

      <div class="form-row">
        <div class="form-group">
          <label for="username" class="form-label">Username</label>
          <div class="input-wrap">
            <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <input
              type="text"
              id="username"
              name="username"
              class="form-input <?= !empty($errors['username']) ? 'input-error' : '' ?>"
              placeholder="GentlemanX"
              value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
              autocomplete="username"
              minlength="3"
              maxlength="50"
              required
            />
          </div>
          <?php if (!empty($errors['username'])): ?>
            <p class="field-error"><?= htmlspecialchars($errors['username'][0]) ?></p>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label for="email" class="form-label">Email Address</label>
          <div class="input-wrap">
            <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            <input
              type="email"
              id="email"
              name="email"
              class="form-input <?= !empty($errors['email']) ? 'input-error' : '' ?>"
              placeholder="you@example.com"
              value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
              autocomplete="email"
              required
            />
          </div>
          <?php if (!empty($errors['email'])): ?>
            <p class="field-error"><?= htmlspecialchars($errors['email'][0]) ?></p>
          <?php endif; ?>
        </div>
      </div>

      <div class="form-group">
        <label for="password" class="form-label">Password</label>
        <div class="input-wrap">
          <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <input
            type="password"
            id="password"
            name="password"
            class="form-input <?= !empty($errors['password']) ? 'input-error' : '' ?>"
            placeholder="Minimum 8 characters"
            autocomplete="new-password"
            minlength="8"
            required
          />
          <button type="button" class="input-toggle-pw" aria-label="Toggle password visibility" tabindex="-1">
            <svg class="eye-show" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            <svg class="eye-hide" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
          </button>
        </div>
        <div class="pw-strength" id="pw-strength" aria-live="polite">
          <div class="pw-strength-bar"><div class="pw-strength-fill" id="pw-strength-fill"></div></div>
          <span class="pw-strength-label" id="pw-strength-label"></span>
        </div>
        <?php if (!empty($errors['password'])): ?>
          <p class="field-error"><?= htmlspecialchars($errors['password'][0]) ?></p>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label for="dob" class="form-label">Date of Birth <span class="form-label-note">(Must be 18+)</span></label>
        <div class="input-wrap">
          <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          <input
            type="date"
            id="dob"
            name="dob"
            class="form-input <?= !empty($errors['dob']) ? 'input-error' : '' ?>"
            value="<?= htmlspecialchars($_POST['dob'] ?? '') ?>"
            max="<?= date('Y-m-d', strtotime('-18 years')) ?>"
            required
          />
        </div>
        <?php if (!empty($errors['dob'])): ?>
          <p class="field-error"><?= htmlspecialchars($errors['dob'][0]) ?></p>
        <?php endif; ?>
      </div>

      <div class="form-checks">
        <label class="form-check <?= !empty($errors['agree_age']) ? 'check-error' : '' ?>">
          <input type="checkbox" name="agree_age" value="1" <?= !empty($_POST['agree_age']) ? 'checked' : '' ?> required />
          <span class="check-box" aria-hidden="true"></span>
          <span class="check-label">I confirm I am 18 years of age or older</span>
        </label>

        <label class="form-check <?= !empty($errors['agree_toc']) ? 'check-error' : '' ?>">
          <input type="checkbox" name="agree_toc" value="1" <?= !empty($_POST['agree_toc']) ? 'checked' : '' ?> required />
          <span class="check-box" aria-hidden="true"></span>
          <span class="check-label">I agree to the <a href="<?= BASE_PATH ?>/terms" target="_blank">Terms of Service</a> and <a href="<?= BASE_PATH ?>/privacy" target="_blank">Privacy Policy</a></span>
        </label>
      </div>

      <button type="submit" class="btn-primary btn-block btn-submit" id="register-submit">
        <span class="btn-text">Create My Account</span>
        <span class="btn-spinner" aria-hidden="true"></span>
      </button>
    </form>

    <div class="auth-card-footer">
      <p>Already have an account? <a href="<?= BASE_PATH ?>/login">Sign in here</a></p>
    </div>

  </div>
</div>

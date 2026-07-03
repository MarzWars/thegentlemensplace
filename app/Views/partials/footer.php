<?php
use App\Core\Lang;
$isLoggedIn = !empty($_SESSION['user_id']);
?>
<footer>
  <div class="footer-inner">
    <div class="footer-categories" style="padding-bottom: 2.5rem; margin-bottom: 2.5rem; border-bottom: 1px solid rgba(201,168,76,.08);">
      <p class="footer-col-title" style="margin-bottom: 1rem;">Popular Categories</p>
      <ul class="footer-categories-list" style="display: flex; flex-wrap: wrap; gap: 1rem 2rem; list-style: none;">
        <?php
          $categories = ['chat', 'roleplay', 'fantasy', 'couples', 'mature', 'fetish'];
          foreach ($categories as $cat):
        ?>
          <li>
            <a href="<?= Lang::base() ?>/performers/category/<?= urlencode($cat) ?>" style="font-family: var(--ff-elegant); font-size: .95rem; color: rgba(196,184,150,.45); text-decoration: none; transition: color .3s;" onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='rgba(196,184,150,.45)'">
              <?= ucfirst($cat) ?> Performers
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
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

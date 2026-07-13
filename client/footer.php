<footer>
  <div class="footer-inner">
    <div class="footer-top">
      <div class="footer-brand">
        <div class="footer-brand-name">The Gentleman's Place</div>
        <p class="footer-brand-tagline">Independent Performer Portal.</p>
      </div>
      <div class="footer-col">
        <p class="footer-col-title">Performers</p>
        <ul class="footer-links">
          <li><a href="/#how-it-works">How It Works</a></li>
          <li><a href="/#tiers">Tiers</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <p class="footer-col-title">Account</p>
        <ul class="footer-links">
          <?php if (isLoggedIn()): ?>
            <li><a href="dashboard.php">My Dashboard</a></li>
            <li><a href="rooms.php">Rooms</a></li>
            <li><a href="logout.php">Sign Out</a></li>
          <?php else: ?>
            <li><a href="login.php">Sign In</a></li>
            <li><a href="register.php">Register</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p class="footer-copy">&copy; <?= date('Y') ?> The Gentleman's Place. All rights reserved.</p>
    </div>
  </div>
</footer>

<button id="back-top" aria-label="Back to top">
  <svg viewBox="0 0 24 24"><polyline points="18 15 12 9 6 15"/></svg>
</button>

<script src="/Assets/js/main.js"></script>
</body>
</html>

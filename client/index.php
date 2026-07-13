<?php
$pageTitle = 'Work with Us - Independent Performers';
require_once 'header.php';
?>
    <div class="hero" style="min-height: 80vh; display: flex; align-items: center; justify-content: center; text-align: center; background: linear-gradient(135deg, #111, #1a1a1a); padding: 4rem 1rem;">
      <div style="max-width: 800px;">
        <h1 style="font-size: 3.5rem; color: #c9a84c; margin-bottom: 1rem; font-family: 'Cinzel', serif;">Become an Independent Performer</h1>
        <p style="font-size: 1.25rem; color: #f0e8d0; margin-bottom: 2.5rem; opacity: 0.9;">Set your own hours, determine your own rates, and keep up to 90% of your earnings. Join the most premium platform today.</p>
        <div>
          <a href="register.php" class="btn-primary">Start Earning Now</a>
          <a href="#tiers" class="btn-ghost" style="margin-left: 1rem;">View Pricing</a>
        </div>
      </div>
    </div>

    <section id="how-it-works" style="padding: 5rem 1rem; background: #0a0805; color: #f0e8d0;">
      <div style="max-width: 1000px; margin: 0 auto; text-align: center;">
        <h2 style="font-size: 2.5rem; color: #c9a84c; margin-bottom: 3rem; font-family: 'Cinzel', serif;">How It Works</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
          <div style="background: #111; padding: 2.5rem 2rem; border-radius: 8px; border: 1px solid rgba(201,168,76,0.1);">
            <div style="font-size: 2rem; color: #c9a84c; margin-bottom: 1rem;">1</div>
            <h3 style="font-size: 1.5rem; margin-bottom: 1rem;">Sign Up</h3>
            <p style="opacity: 0.8;">Create your account and choose between our Free or Premium tiers.</p>
          </div>
          <div style="background: #111; padding: 2.5rem 2rem; border-radius: 8px; border: 1px solid rgba(201,168,76,0.1);">
            <div style="font-size: 2rem; color: #c9a84c; margin-bottom: 1rem;">2</div>
            <h3 style="font-size: 1.5rem; margin-bottom: 1rem;">Set Your Profile</h3>
            <p style="opacity: 0.8;">Upload your photo, set your per-minute rate, and add your bank details.</p>
          </div>
          <div style="background: #111; padding: 2.5rem 2rem; border-radius: 8px; border: 1px solid rgba(201,168,76,0.1);">
            <div style="font-size: 2rem; color: #c9a84c; margin-bottom: 1rem;">3</div>
            <h3 style="font-size: 1.5rem; margin-bottom: 1rem;">Receive Calls</h3>
            <p style="opacity: 0.8;">Log into the Rooms dashboard and answer calls directly from clients.</p>
          </div>
        </div>
      </div>
    </section>

    <section id="tiers" style="padding: 5rem 1rem; background: #111; color: #f0e8d0;">
      <div style="max-width: 1000px; margin: 0 auto; text-align: center;">
        <h2 style="font-size: 2.5rem; color: #c9a84c; margin-bottom: 3rem; font-family: 'Cinzel', serif;">Pricing Tiers</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
          <!-- Free Tier -->
          <div style="background: #1a1a1a; padding: 3rem 2rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
            <h3 style="font-size: 2rem; margin-bottom: 1rem; color: #fff;">Free Tier</h3>
            <div style="font-size: 3rem; font-weight: bold; margin-bottom: 2rem;">€0<span style="font-size: 1rem; font-weight: normal; opacity: 0.6;">/mo</span></div>
            <ul style="list-style: none; padding: 0; margin-bottom: 2rem; text-align: left;">
              <li style="margin-bottom: 1rem;">✓ List your profile</li>
              <li style="margin-bottom: 1rem;">✓ Set custom rates</li>
              <li style="margin-bottom: 1rem;">✓ <strong>20% Platform Fee</strong> per call</li>
            </ul>
            <a href="register.php?tier=free" class="btn-ghost" style="display: block; width: 100%; text-align: center; text-decoration: none;">Join for Free</a>
          </div>
          <!-- Premium Tier -->
          <div style="background: #1a1a1a; padding: 3rem 2rem; border-radius: 8px; border: 2px solid #c9a84c; position: relative;">
            <div style="position: absolute; top: -15px; left: 50%; transform: translateX(-50%); background: #c9a84c; color: #000; padding: 0.25rem 1rem; border-radius: 20px; font-weight: bold; font-size: 0.875rem;">RECOMMENDED</div>
            <h3 style="font-size: 2rem; margin-bottom: 1rem; color: #c9a84c;">Premium Tier</h3>
            <div style="font-size: 3rem; font-weight: bold; margin-bottom: 2rem; color: #c9a84c;">€25<span style="font-size: 1rem; font-weight: normal; opacity: 0.6; color: #fff;">/mo</span></div>
            <ul style="list-style: none; padding: 0; margin-bottom: 2rem; text-align: left;">
              <li style="margin-bottom: 1rem;">✓ Boosted profile visibility</li>
              <li style="margin-bottom: 1rem;">✓ Set custom rates</li>
              <li style="margin-bottom: 1rem;">✓ <strong>Only 10% Platform Fee</strong> per call</li>
            </ul>
            <a href="register.php?tier=premium" class="btn-primary" style="display: block; width: 100%; text-align: center; text-decoration: none;">Join Premium</a>
          </div>
        </div>
      </div>
    </section>

<?php require_once 'footer.php'; ?>

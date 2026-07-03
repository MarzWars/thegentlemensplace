/* ============================================================
   THE GENTLEMAN'S PLACE — main.js  (Enhanced Edition)
   Parallax · Magnetic Cursor · Tilt · Scramble · Kinetics
   ============================================================ */
(function () {
  'use strict';

  /* ── Mobile detection (used throughout to skip expensive features) ── */
  const isMobile  = window.innerWidth < 860;
  const isTouch   = window.matchMedia('(hover: none)').matches;
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const isLowEnd  = isMobile || isTouch || reduceMotion;

  /* ── Utility: RAF-throttled scroll/resize bus ─────────── */
  const raf = window.requestAnimationFrame;
  let scrollY = window.scrollY;
  let ticking  = false;
  const scrollCallbacks  = [];
  const resizeCallbacks  = [];

  window.addEventListener('scroll', () => {
    scrollY = window.scrollY;
    if (!ticking) {
      raf(() => { scrollCallbacks.forEach(fn => fn(scrollY)); ticking = false; });
      ticking = true;
    }
  }, { passive: true });

  const resizeObs = new ResizeObserver(() =>
    resizeCallbacks.forEach(fn => fn())
  );
  resizeObs.observe(document.documentElement);

  /* ── Utility: lerp & clamp ─────────────────────────────── */
  const lerp   = (a, b, t) => a + (b - a) * t;
  const clamp  = (v, lo, hi) => Math.max(lo, Math.min(hi, v));
  const mapRange = (v, inMin, inMax, outMin, outMax) =>
    outMin + ((v - inMin) / (inMax - inMin)) * (outMax - outMin);

  /* ── Preloader ─────────────────────────────────────────── */
  const preloader = document.getElementById('preloader');

  function runPreloader() {
    if (!preloader) { initAll(); return; }
    const bar = preloader.querySelector('.preloader-bar');
    const pct = preloader.querySelector('.preloader-pct');
    let loaded = 0;

    (function step() {
      loaded = Math.min(loaded + Math.random() * 14, 100);
      if (bar) bar.style.width = loaded + '%';
      if (pct) pct.textContent = Math.floor(loaded) + '%';
      if (loaded < 100) {
        setTimeout(step, 55 + Math.random() * 75);
      } else {
        setTimeout(() => {
          preloader.classList.add('hidden');
          revealHero();
          initAll();
        }, 380);
      }
    })();
  }

  function revealHero() {
    const heroEls = document.querySelectorAll(
      '#hero .hero-eyebrow, #hero .hero-title, #hero .hero-subtitle, #hero .hero-actions, #hero .hero-trust'
    );
    heroEls.forEach((el, i) => {
      setTimeout(() => el.classList.add('visible'), i * 120);
    });
  }

  /* ── Grain Overlay ─────────────────────────────────────── */
  function initGrain() {
    if (isLowEnd) return; // skip on mobile — GPU-heavy animated overlay
    const grain = document.createElement('div');
    grain.id = 'grain-overlay';
    grain.setAttribute('aria-hidden', 'true');
    document.body.appendChild(grain);

    // Animated grain via SVG turbulence filter
    const svg = `<svg xmlns='http://www.w3.org/2000/svg' width='300' height='300'>
      <filter id='n'>
        <feTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/>
        <feColorMatrix type='saturate' values='0'/>
      </filter>
      <rect width='300' height='300' filter='url(#n)' opacity='0.08'/>
    </svg>`;
    const url = `url("data:image/svg+xml,${encodeURIComponent(svg)}")`;
    grain.style.cssText = `
      position:fixed;inset:0;z-index:9990;pointer-events:none;
      background-image:${url};background-size:300px 300px;
      opacity:0.28;mix-blend-mode:overlay;will-change:transform;
    `;

    let gx = 0, gy = 0;
    (function animGrain() {
      gx = (Math.random() - 0.5) * 300;
      gy = (Math.random() - 0.5) * 300;
      grain.style.backgroundPosition = `${gx}px ${gy}px`;
      setTimeout(() => raf(animGrain), 80);
    })();
  }

  /* ── Magnetic Custom Cursor ────────────────────────────── */
  function initCursor() {
    const dot  = document.getElementById('cursor');
    const ring = document.getElementById('cursor-ring');
    if (!dot || !ring || window.innerWidth < 600 || isTouch) return; // also skip on touch

    let mx = 0, my = 0, rx = 0, ry = 0;
    let isHovering = false;
    let activeTarget = null;
    let magnetX = 0, magnetY = 0;

    document.addEventListener('mousemove', e => {
      mx = e.clientX;
      my = e.clientY;
      dot.style.left = mx + 'px';
      dot.style.top  = my + 'px';
    });

    // Magnetic pull on interactive elements
    const magnetEls = document.querySelectorAll(
      'a, button, .btn, .nav-logo, .credits-package-card, .product-card'
    );
    magnetEls.forEach(el => {
      el.addEventListener('mouseenter', () => { isHovering = true; activeTarget = el; });
      el.addEventListener('mouseleave', () => {
        isHovering = false; activeTarget = null;
        magnetX = 0; magnetY = 0;
        el.style.transform = '';
      });
      el.addEventListener('mousemove', e => {
        if (!activeTarget) return;
        const rect = el.getBoundingClientRect();
        const cx = rect.left + rect.width  / 2;
        const cy = rect.top  + rect.height / 2;
        const dx = e.clientX - cx;
        const dy = e.clientY - cy;
        const strength = el.matches('.btn, button') ? 0.22 : 0.08;
        magnetX = dx * strength;
        magnetY = dy * strength;
        el.style.transform = `translate(${magnetX}px, ${magnetY}px)`;
        el.style.transition = 'transform 0.15s ease';
      });
    });

    (function animRing() {
      rx = lerp(rx, mx + magnetX * 0.5, 0.13);
      ry = lerp(ry, my + magnetY * 0.5, 0.13);
      ring.style.left = rx + 'px';
      ring.style.top  = ry + 'px';

      if (isHovering) {
        ring.style.width  = '64px';
        ring.style.height = '64px';
        ring.style.borderColor = 'rgba(201,168,76,0.8)';
        ring.style.mixBlendMode = 'normal';
      } else {
        ring.style.width  = '36px';
        ring.style.height = '36px';
        ring.style.borderColor = 'rgba(201,168,76,0.4)';
      }
      raf(animRing);
    })();
  }

  /* ── Parallax Engine ───────────────────────────────────── */
  function initParallax() {
    if (isLowEnd) return; // skip parallax on mobile — causes jank
    // ── Hero deep parallax ──
    const heroBg      = document.querySelector('#hero .hero-bg, #hero .hero-video-wrap, #hero canvas');
    const heroContent = document.querySelector('#hero .hero-content');
    const heroDeco    = document.querySelectorAll('#hero .hero-deco, #hero .hero-rule, #hero .section-rule');

    // ── Section-level parallax layers ──
    const layers = [
      ...document.querySelectorAll('[data-parallax]'),
    ];

    // Auto-register elements with data attributes
    document.querySelectorAll('[data-depth]').forEach(el => {
      layers.push({ el, depth: parseFloat(el.dataset.depth) || 0.15 });
    });

    scrollCallbacks.push(sy => {
      // Hero section parallax
      if (heroBg) {
        const y = sy * 0.42;
        heroBg.style.transform = `translate3d(0, ${y}px, 0)`;
        heroBg.style.willChange = 'transform';
      }
      if (heroContent) {
        const y = sy * 0.18;
        const o = clamp(1 - sy / 600, 0, 1);
        heroContent.style.transform  = `translate3d(0, ${y}px, 0)`;
        heroContent.style.opacity    = o;
        heroContent.style.willChange = 'transform, opacity';
      }
      heroDeco.forEach(el => {
        el.style.transform = `translate3d(0, ${sy * 0.06}px, 0)`;
      });

      // Generic parallax layers
      layers.forEach(item => {
        const el    = item.el || item;
        const depth = parseFloat(el.dataset?.depth || item.depth || 0.15);
        const rect  = el.getBoundingClientRect();
        const center = rect.top + rect.height / 2;
        const vMid   = window.innerHeight / 2;
        const offset = (vMid - center) * depth;
        el.style.transform  = `translate3d(0, ${offset}px, 0)`;
        el.style.willChange = 'transform';
      });

      // Floating decorative rule lines (thin horizontal dividers)
      document.querySelectorAll('.section-rule, .ornament-line').forEach((el, i) => {
        const speed = i % 2 === 0 ? 0.04 : -0.04;
        el.style.transform = `scaleX(${1 + Math.sin(sy * 0.0015 + i) * 0.08})`;
      });
    });
  }

  /* ── 3D Tilt Cards ─────────────────────────────────────── */
  function initTilt() {
    if (isLowEnd) return; // skip 3D tilt on mobile — rAF loops per card
    const cards = document.querySelectorAll(
      '.credits-package-card, .product-card, .feature-card, .age-gate-modal, .confirm-card'
    );

    cards.forEach(card => {
      let lerpX = 0, lerpY = 0, targetX = 0, targetY = 0;
      let animId = null;

      card.addEventListener('mousemove', e => {
        const rect = card.getBoundingClientRect();
        const cx   = rect.left + rect.width  / 2;
        const cy   = rect.top  + rect.height / 2;
        targetX    = mapRange(e.clientY - cy, -rect.height / 2, rect.height / 2, 6, -6);
        targetY    = mapRange(e.clientX - cx, -rect.width  / 2, rect.width  / 2, -6, 6);

        // Highlight sheen
        const px = ((e.clientX - rect.left) / rect.width)  * 100;
        const py = ((e.clientY - rect.top)  / rect.height) * 100;
        card.style.setProperty('--shine-x', px + '%');
        card.style.setProperty('--shine-y', py + '%');
        card.classList.add('tilt-active');
      });

      card.addEventListener('mouseleave', () => {
        targetX = 0; targetY = 0;
        card.classList.remove('tilt-active');
        card.style.removeProperty('--shine-x');
        card.style.removeProperty('--shine-y');
      });

      (function animTilt() {
        lerpX = lerp(lerpX, targetX, 0.09);
        lerpY = lerp(lerpY, targetY, 0.09);
        if (Math.abs(lerpX) > 0.01 || Math.abs(lerpY) > 0.01 || targetX !== 0 || targetY !== 0) {
          card.style.transform    = `perspective(900px) rotateX(${lerpX}deg) rotateY(${lerpY}deg) translateZ(4px)`;
          card.style.willChange   = 'transform';
        }
        animId = raf(animTilt);
      })();
    });
  }

  /* ── Navbar ────────────────────────────────────────────── */
  function initNav() {
    const nav      = document.getElementById('navbar');
    const burger   = document.getElementById('hamburger');
    const drawer   = document.getElementById('nav-drawer');
    const overlay  = document.getElementById('drawer-overlay');
    const closeBtn = document.getElementById('drawer-close');

    if (nav) {
      let lastSY = 0;
      scrollCallbacks.push(sy => {
        nav.classList.toggle('scrolled', sy > 60);
        // Hide nav on fast scroll down, reveal on scroll up (desktop only)
        const delta = sy - lastSY;
          if (isLowEnd) { lastSY = sy; return; } // skip hide/show on mobile
        if (sy > 120) {
          nav.style.transform = delta > 4 ? 'translateY(-100%)' : 'translateY(0)';
          nav.style.transition = 'transform .35s cubic-bezier(0.25,0.46,0.45,0.94), background .4s, border-color .4s';
        } else {
          nav.style.transform = '';
        }
        lastSY = sy;
      });
    }

    function openDrawer() {
      if (!drawer) return;
      drawer.classList.add('open');
      drawer.setAttribute('aria-hidden', 'false');
      if (overlay) overlay.classList.add('show');
      if (burger)  { burger.classList.add('active'); burger.setAttribute('aria-expanded', 'true'); }
      document.body.style.overflow = 'hidden';
    }
    function closeDrawer() {
      if (!drawer) return;
      drawer.classList.remove('open');
      drawer.setAttribute('aria-hidden', 'true');
      if (overlay) overlay.classList.remove('show');
      if (burger)  { burger.classList.remove('active'); burger.setAttribute('aria-expanded', 'false'); }
      document.body.style.overflow = '';
    }

    if (burger)   burger.addEventListener('click', openDrawer);
    if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
    if (overlay)  overlay.addEventListener('click', closeDrawer);
    if (drawer)   drawer.querySelectorAll('a').forEach(a => a.addEventListener('click', closeDrawer));
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDrawer(); });
  }

  /* ── Particle Canvas ───────────────────────────────────── */
  function initParticles() {
    let canvas = document.getElementById('particle-canvas');
    // Move canvas to <body> so the fixed-position covers the full viewport
    if (canvas && canvas.parentElement !== document.body) {
      document.body.appendChild(canvas);
    }
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    let W, H, particles = [];
    // Initialise to viewport centre — W/H aren't set yet so use window directly
    let mouseX = window.innerWidth / 2, mouseY = window.innerHeight / 2;

    function resize() {
      // Use window dimensions: offsetWidth on a canvas element can be 0 before layout
      W = canvas.width  = window.innerWidth;
      H = canvas.height = window.innerHeight;
    }

    document.addEventListener('mousemove', e => {
      // clientX/clientY are viewport-relative, matching the fixed canvas perfectly
      mouseX = e.clientX;
      mouseY = e.clientY;
    });

    class Particle {
      constructor() { this.reset(true); }
      reset(initial) {
        this.x     = Math.random() * W;
        this.y     = initial ? Math.random() * H : H + 5;
        this.size  = Math.random() * 1.8 + 0.2;
        this.speedX = (Math.random() - 0.5) * 0.2;
        this.speedY = -(Math.random() * 0.28 + 0.06);
        this.life    = 0;
        this.maxLife = Math.random() * 320 + 160;
        this.color   = Math.random() > 0.6 ? '201,168,76' : Math.random() > 0.5 ? '240,232,208' : '160,36,60';
        this.wobble  = Math.random() * Math.PI * 2;
        this.wobbleSpeed = (Math.random() - 0.5) * 0.015;
      }
      update() {
        this.wobble  += this.wobbleSpeed;
        this.x       += this.speedX + Math.sin(this.wobble) * 0.12;
        this.y       += this.speedY;
        this.life++;
        const t = this.life / this.maxLife;
        this.alpha = t < 0.1 ? t * 4 * 0.45 : t > 0.75 ? (1 - t) * 4 * 0.45 : 0.45;

        // Subtle mouse repulsion (desktop only)
        if (!isLowEnd) {
          const dx = this.x - mouseX;
          const dy = this.y - mouseY;
          const dist = Math.sqrt(dx * dx + dy * dy);
          if (dist < 100) {
            const force = (100 - dist) / 100 * 0.4;
            this.x += (dx / dist) * force;
            this.y += (dy / dist) * force;
          }
        }

        if (this.life >= this.maxLife || this.y < -10) this.reset(false);
      }
      draw() {
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(${this.color},${this.alpha})`;
        ctx.fill();
      }
    }

    resize();
    // More particles to fill the full viewport rather than just the hero section
    const particleCount = isLowEnd ? 40 : 220;
    particles = Array.from({ length: particleCount }, () => new Particle());
    resizeCallbacks.push(resize);

    // Connecting lines between close particles
    function drawConnections() {
      for (let i = 0; i < particles.length; i++) {
        for (let j = i + 1; j < particles.length; j++) {
          const dx = particles[i].x - particles[j].x;
          const dy = particles[i].y - particles[j].y;
          const dist = Math.sqrt(dx * dx + dy * dy);
          if (dist < 90) {
            const alpha = (1 - dist / 90) * 0.06;
            ctx.beginPath();
            ctx.moveTo(particles[i].x, particles[i].y);
            ctx.lineTo(particles[j].x, particles[j].y);
            ctx.strokeStyle = `rgba(201,168,76,${alpha})`;
            ctx.lineWidth = 0.4;
            ctx.stroke();
          }
        }
      }
    }

    let frameSkip = 0;
    (function loop() {
      // On mobile throttle to ~30fps by skipping every other frame
      if (isLowEnd) {
        frameSkip++;
        if (frameSkip % 2 === 0) { raf(loop); return; }
      }
      ctx.clearRect(0, 0, W, H);
      if (!isLowEnd) drawConnections();
      particles.forEach(p => { p.update(); p.draw(); });
      raf(loop);
    })();
  }

  /* ── Text Scramble ─────────────────────────────────────── */
  const CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789ᚠᛒᛗᛟ᛫';

  function scrambleText(el, finalText, duration = 800) {
    let frame = 0;
    const totalFrames = Math.ceil(duration / 16);
    const original = finalText || el.textContent;
    let animId;

    (function tick() {
      const progress = frame / totalFrames;
      const revealed = Math.floor(progress * original.length);
      let display = '';
      for (let i = 0; i < original.length; i++) {
        if (original[i] === ' ') { display += ' '; continue; }
        if (i < revealed) {
          display += original[i];
        } else {
          display += CHARS[Math.floor(Math.random() * CHARS.length)];
        }
      }
      el.textContent = display;
      frame++;
      if (frame <= totalFrames) {
        animId = raf(tick);
      } else {
        el.textContent = original;
      }
    })();
  }

  function initScramble() {
    if (isLowEnd) return; // skip text scramble on mobile
    const targets = document.querySelectorAll('[data-scramble], .hero-title, .section-heading');
    const obs = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          scrambleText(entry.target, entry.target.dataset.scramble || entry.target.textContent);
          obs.unobserve(entry.target);
        }
      });
    }, { threshold: 0.3 });
    targets.forEach(el => obs.observe(el));

    // Re-scramble on hover for heading-level elements
    document.querySelectorAll('.nav-links a, .hero-eyebrow').forEach(el => {
      el.addEventListener('mouseenter', () => {
        scrambleText(el, el.textContent, 360);
      });
    });
  }

  /* ── Scroll Reveal — Staggered ─────────────────────────── */
  function initReveal() {
    const items = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-up');
    if (!items.length) return;

    const obs = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          // Stagger siblings inside same parent
          const siblings = [...e.target.parentElement.children].filter(
            c => c.classList.contains('reveal') || c.classList.contains('reveal-up') ||
                 c.classList.contains('reveal-left') || c.classList.contains('reveal-right')
          );
          const idx = siblings.indexOf(e.target);
          e.target.style.transitionDelay = `${idx * 80}ms`;
          e.target.classList.add('visible');
          obs.unobserve(e.target);
        }
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    items.forEach(el => obs.observe(el));
  }

  /* ── Kinetic Number Counter ────────────────────────────── */
  function initCounters() {
    const counters = document.querySelectorAll('[data-count]');
    if (!counters.length) return;

    const obs = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        const el      = entry.target;
        const target  = parseFloat(el.dataset.count);
        const decimals = (el.dataset.count.split('.')[1] || '').length;
        const prefix  = el.dataset.prefix || '';
        const suffix  = el.dataset.suffix || '';
        const duration = 1800;
        const start    = performance.now();

        (function tick(now) {
          const elapsed  = now - start;
          const progress = clamp(elapsed / duration, 0, 1);
          // Ease out cubic
          const ease = 1 - Math.pow(1 - progress, 3);
          const value = (target * ease).toFixed(decimals);
          el.textContent = prefix + value + suffix;
          if (progress < 1) raf(tick);
        })(start);

        obs.unobserve(el);
      });
    }, { threshold: 0.6 });

    counters.forEach(el => obs.observe(el));
  }

  /* ── Horizontal Scroll Carousel ────────────────────────── */
  function initHorizontalScroll() {
    const tracks = document.querySelectorAll('[data-hscroll]');
    tracks.forEach(track => {
      let isDown = false, startX, scrollLeft;

      track.style.cursor = 'grab';
      track.addEventListener('mousedown', e => {
        isDown = true;
        track.style.cursor = 'grabbing';
        startX = e.pageX - track.offsetLeft;
        scrollLeft = track.scrollLeft;
      });
      track.addEventListener('mouseleave', () => { isDown = false; track.style.cursor = 'grab'; });
      track.addEventListener('mouseup',    () => { isDown = false; track.style.cursor = 'grab'; });
      track.addEventListener('mousemove', e => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - track.offsetLeft;
        const walk = (x - startX) * 1.6;
        track.scrollLeft = scrollLeft - walk;
      });

      // Smooth momentum scroll
      let velocity = 0, lastX = 0;
      track.addEventListener('touchstart',  e => { lastX = e.touches[0].clientX; velocity = 0; }, { passive: true });
      track.addEventListener('touchmove',   e => {
        const x   = e.touches[0].clientX;
        velocity  = lastX - x;
        lastX     = x;
        track.scrollLeft += velocity;
      }, { passive: true });
    });
  }

  /* ── Scroll Progress Bar ───────────────────────────────── */
  function initScrollProgress() {
    const bar = document.createElement('div');
    bar.id = 'scroll-progress';
    bar.setAttribute('aria-hidden', 'true');
    bar.style.cssText = `
      position:fixed;top:0;left:0;height:2px;z-index:10001;
      background:linear-gradient(90deg,var(--burgundy-glow),var(--gold),var(--gold-lt));
      box-shadow:0 0 8px var(--gold);width:0%;pointer-events:none;
      transition:width .05s linear;will-change:width;
    `;
    document.body.appendChild(bar);

    scrollCallbacks.push(sy => {
      const total = document.documentElement.scrollHeight - window.innerHeight;
      bar.style.width = (sy / total * 100) + '%';
    });
  }

  /* ── Section Ambient Glow ──────────────────────────────── */
  function initAmbientGlow() {
    if (isLowEnd) return; // skip ambient glow on mobile
    const sections = document.querySelectorAll('section[id], .page-section');
    if (!sections.length) return;

    scrollCallbacks.push(sy => {
      sections.forEach(sec => {
        const rect   = sec.getBoundingClientRect();
        const center = rect.top + rect.height / 2;
        const dist   = Math.abs(center - window.innerHeight / 2);
        const alpha  = clamp(1 - dist / (window.innerHeight * 0.7), 0, 0.6);
        sec.style.setProperty('--ambient-alpha', alpha);
      });
    });
  }

  /* ── Back to Top ───────────────────────────────────────── */
  function initBackTop() {
    const btn = document.getElementById('back-top');
    if (!btn) return;
    scrollCallbacks.push(sy => btn.classList.toggle('show', sy > 500));
    btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

    // Pulse ring on show
    btn.addEventListener('transitionend', () => {
      if (btn.classList.contains('show')) btn.classList.add('pulse');
    });
  }

  /* ── Smooth Anchor Scroll ──────────────────────────────── */
  function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(a => {
      a.addEventListener('click', e => {
        const id = a.getAttribute('href');
        if (id === '#') return;
        const target = document.querySelector(id);
        if (!target) return;
        e.preventDefault();
        const navH = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--nav-h')) || 72;
        const top  = target.getBoundingClientRect().top + window.scrollY - navH - 16;
        window.scrollTo({ top, behavior: 'smooth' });
      });
    });
  }

  /* ── Language Switcher ─────────────────────────────────── */
  function initLangSwitcher() {
    const switcher = document.getElementById('lang-switcher');
    if (!switcher) return;
    const btn = switcher.querySelector('.lang-switcher-btn');
    if (!btn) return;

    btn.addEventListener('click', e => {
      e.stopPropagation();
      const open = switcher.classList.toggle('open');
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    document.addEventListener('click', () => {
      switcher.classList.remove('open');
      btn.setAttribute('aria-expanded', 'false');
    });
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape') { switcher.classList.remove('open'); btn.setAttribute('aria-expanded', 'false'); }
    });
  }

  /* ── Flash Auto-Dismiss ────────────────────────────────── */
  function initFlash() {
    document.querySelectorAll('.flash').forEach(el => {
      setTimeout(() => {
        el.style.transition = 'opacity .6s, transform .6s';
        el.style.opacity    = '0';
        el.style.transform  = 'translateY(-8px)';
        setTimeout(() => el.remove(), 650);
      }, 5000);
    });
  }

  /* ── Password Toggle ───────────────────────────────────── */
  function initPasswordToggles() {
    document.querySelectorAll('.input-toggle-pw').forEach(btn => {
      btn.addEventListener('click', () => {
        const wrap  = btn.closest('.input-wrap');
        const input = wrap.querySelector('input');
        const show  = btn.querySelector('.eye-show');
        const hide  = btn.querySelector('.eye-hide');
        if (!input) return;
        const isText = input.type === 'text';
        input.type = isText ? 'password' : 'text';
        if (show) show.style.display = isText ? 'block' : 'none';
        if (hide) hide.style.display = isText ? 'none'  : 'block';
      });
    });
  }

  /* ── Password Strength Meter ───────────────────────────── */
  function initPasswordStrength() {
    const input = document.getElementById('password') || document.getElementById('new_password');
    const fill  = document.getElementById('pw-strength-fill');
    const label = document.getElementById('pw-strength-label');
    if (!input || !fill || !label) return;

    input.addEventListener('input', () => {
      const v = input.value;
      let score = 0;
      if (v.length >= 8)            score++;
      if (v.length >= 12)           score++;
      if (/[A-Z]/.test(v))         score++;
      if (/[0-9]/.test(v))         score++;
      if (/[^A-Za-z0-9]/.test(v))  score++;

      const levels = [
        { pct: '0%',   color: 'transparent',        text: '' },
        { pct: '25%',  color: 'var(--error-lt)',     text: 'Weak' },
        { pct: '50%',  color: '#e09040',             text: 'Fair' },
        { pct: '75%',  color: 'var(--gold)',         text: 'Good' },
        { pct: '100%', color: 'var(--success-lt)',   text: 'Strong' },
      ];
      const lvl = v.length === 0 ? levels[0] : levels[Math.min(score, 4)];
      fill.style.width      = lvl.pct;
      fill.style.background = lvl.color;
      label.textContent     = lvl.text;
    });
  }

  /* ── Auth Form Submit Spinner ──────────────────────────── */
  function initAuthForms() {
    ['login-form','register-form','forgot-form','reset-form','account-profile-form','account-security-form'].forEach(id => {
      const form = document.getElementById(id);
      if (!form) return;
      form.addEventListener('submit', () => {
        const btn = form.querySelector('.btn-submit');
        if (btn) btn.classList.add('loading');
      });
    });
  }

  /* ── Reveal on Scroll — Hero Image Parallax Override ────── */
  function initHeroKinetics() {
    const hero = document.getElementById('hero');
    if (!hero) return;

    // Subtle vignette that deepens as you scroll
    const vignette = document.createElement('div');
    vignette.style.cssText = `
      position:absolute;inset:0;pointer-events:none;z-index:2;
      background:radial-gradient(ellipse at 50% 50%, transparent 30%, rgba(10,8,5,0) 60%, rgba(10,8,5,0.9) 100%);
      transition:opacity .3s;
    `;
    hero.appendChild(vignette);

    scrollCallbacks.push(sy => {
      const progress = clamp(sy / window.innerHeight, 0, 1);
      vignette.style.opacity = String(0.4 + progress * 0.6);
    });
  }

  /* ── Staggered Link Hover Underline ────────────────────── */
  function initNavLinkKinetics() {
    document.querySelectorAll('.nav-links a').forEach(link => {
      link.addEventListener('mouseenter', () => {
        link.style.setProperty('--underline-duration', '0.28s');
      });
    });
  }

  /* ── Typewriter for Subtitles ──────────────────────────── */
  function initTypewriter() {
    if (isLowEnd) return; // skip typewriter on mobile
    document.querySelectorAll('[data-typewrite]').forEach(el => {
      const phrases   = el.dataset.typewrite.split('|');
      const cursor    = document.createElement('span');
      cursor.className = 'tw-cursor';
      cursor.style.cssText = 'display:inline-block;width:2px;height:1em;background:var(--gold);margin-left:3px;animation:twBlink .75s step-end infinite;vertical-align:middle;';
      el.appendChild(cursor);

      let pi = 0, ci = 0, deleting = false;

      // Inject keyframe once
      if (!document.getElementById('tw-style')) {
        const s = document.createElement('style');
        s.id = 'tw-style';
        s.textContent = '@keyframes twBlink{0%,100%{opacity:1}50%{opacity:0}}';
        document.head.appendChild(s);
      }

      function type() {
        const phrase = phrases[pi];
        if (!deleting) {
          el.childNodes[0].textContent = phrase.slice(0, ++ci);
          if (ci === phrase.length) { deleting = true; setTimeout(type, 1800); return; }
          setTimeout(type, 68 + Math.random() * 40);
        } else {
          el.childNodes[0].textContent = phrase.slice(0, --ci);
          if (ci === 0) { deleting = false; pi = (pi + 1) % phrases.length; setTimeout(type, 400); return; }
          setTimeout(type, 34);
        }
      }

      // Wrap text in a text node first
      const text = el.textContent.replace(cursor.textContent, '');
      el.textContent = '';
      el.appendChild(document.createTextNode(''));
      el.appendChild(cursor);
      type();
    });
  }

  /* ── Cookie Consent Helper ─────────────────────────────── */
  function initCookieConsentTracking() {
    function getCookieConsent() {
      const match = document.cookie.match(/(?:^|; )tgp_cookie_consent=([^;]*)/);
      if (match) {
        try {
          return JSON.parse(decodeURIComponent(match[1]));
        } catch (e) {
          return null;
        }
      }
      return null;
    }

    function updateRootAttributes(consent) {
      if (consent) {
        document.documentElement.dataset.cookieAnalytics = consent.analytics ? 'true' : 'false';
        document.documentElement.dataset.cookieMarketing = consent.marketing ? 'true' : 'false';
      } else {
        document.documentElement.dataset.cookieAnalytics = 'false';
        document.documentElement.dataset.cookieMarketing = 'false';
      }
    }

    // Initialize root dataset values from existing consent cookie
    const consent = getCookieConsent();
    updateRootAttributes(consent);

    // Expose utility globally
    window.getCookieConsent = getCookieConsent;

    // Listen for dynamically dispatched consent changes (e.g. from the banner)
    document.addEventListener('cookieConsentChanged', function(e) {
      updateRootAttributes(e.detail);
    });
  }

  /* ── Init All ──────────────────────────────────────────── */
  function initAll() {
    initGrain();
    initCursor();
    initParallax();
    initTilt();
    initNav();
    initParticles();
    initReveal();
    initCounters();
    initHorizontalScroll();
    initScrollProgress();
    initAmbientGlow();
    initBackTop();
    initSmoothScroll();
    initLangSwitcher();
    initFlash();
    initPasswordToggles();
    initPasswordStrength();
    initAuthForms();
    initHeroKinetics();
    initNavLinkKinetics();
    initTypewriter();
    initCookieConsentTracking();
  }

  /* ── Boot ──────────────────────────────────────────────── */
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', runPreloader);
  } else {
    runPreloader();
  }

})();
/* ═══════════════════════════════════════════
   L.A. COUTURE — MAIN JS
   ═══════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {

  /* ── Scroll Reveal ─────────────────────── */
  const revealEls = document.querySelectorAll('.reveal');
  if (revealEls.length) {
    const revealObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            revealObserver.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
    );
    revealEls.forEach((el) => revealObserver.observe(el));
  }

  /* ── Hamburger / Mobile Dropdown ────────── */
  const hamburger = document.querySelector('.hamburger');
  const drawer    = document.getElementById('navDrawer') || document.querySelector('.nav-drawer');
  const overlay   = document.getElementById('navOverlay') || document.querySelector('.nav-overlay');

  function openDrawer() {
    if (!drawer) return;
    drawer.classList.add('open');
    if (overlay) overlay.classList.add('open');
    if (hamburger) {
      hamburger.classList.add('is-open');
      hamburger.setAttribute('aria-expanded', 'true');
    }
  }

  function closeDrawer() {
    if (!drawer) return;
    drawer.classList.remove('open');
    if (overlay) overlay.classList.remove('open');
    if (hamburger) {
      hamburger.classList.remove('is-open');
      hamburger.setAttribute('aria-expanded', 'false');
    }
  }

  if (hamburger) {
    hamburger.addEventListener('click', (e) => {
      e.stopPropagation();
      drawer?.classList.contains('open') ? closeDrawer() : openDrawer();
    });
  }

  // Tap overlay or anywhere outside to close
  overlay?.addEventListener('click', closeDrawer);
  document.addEventListener('click', (e) => {
    if (!drawer?.classList.contains('open')) return;
    if (!drawer.contains(e.target) && !hamburger?.contains(e.target)) closeDrawer();
  });

  // Close when a menu link is tapped
  drawer?.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', closeDrawer);
  });

  // Close on Escape
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeDrawer();
  });

  /* ── Filter Buttons (Collections & Gallery) */
  document.querySelectorAll('.products-filter').forEach((filterBar) => {
    filterBar.querySelectorAll('.filter-btn').forEach((btn) => {
      btn.addEventListener('click', () => {
        filterBar.querySelectorAll('.filter-btn').forEach((b) => b.classList.remove('active'));
        btn.classList.add('active');

        const filterValue = btn.dataset.filter || btn.dataset.gfilter || 'all';
        const grid = filterBar.nextElementSibling;
        if (!grid) return;

        grid.querySelectorAll('[data-cat], [data-gfilter]').forEach((item) => {
          const val = item.dataset.cat || item.dataset.gfilter;
          item.style.display = (filterValue === 'all' || val === filterValue) ? '' : 'none';
        });
      });
    });
  });

  /* ── Gallery Lightbox (index page) ──────── */
  const lightbox    = document.querySelector('.lightbox');
  const lightboxImg = lightbox?.querySelector('.lightbox-img');

  if (lightbox && lightboxImg) {
    const lbClose = lightbox.querySelector('.lightbox-close');

    const closeLightbox = () => {
      lightbox.classList.remove('active', 'open');
      lightboxImg.src = '';
      document.body.style.overflow = '';
    };

    document.querySelectorAll('.gallery-item').forEach((item) => {
      item.addEventListener('click', (e) => {
        e.preventDefault();
        const img = item.querySelector('img');
        const src = item.dataset.src || img?.src || '';
        if (!src) return;
        lightboxImg.src = src;
        lightboxImg.alt = img?.alt || '';
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
      });
    });

    lbClose?.addEventListener('click', closeLightbox);
    lightbox.addEventListener('click', (e) => { if (e.target === lightbox) closeLightbox(); });
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && lightbox.classList.contains('active')) closeLightbox();
    });
  }

  /* ── FAQ Accordion ───────────────────────── */
  document.querySelectorAll('.faq-item h4').forEach((heading) => {
    const body = heading.nextElementSibling;
    if (!body) return;
    body.classList.add('hidden');                  // collapsed by default
    heading.addEventListener('click', () => {
      const parent = heading.closest('.faq-item');
      const isOpen = !body.classList.contains('hidden');
      // Collapse all
      document.querySelectorAll('.faq-item').forEach((fi) => {
        fi.classList.remove('open');
        fi.querySelector('p')?.classList.add('hidden');
      });
      if (!isOpen) {
        body.classList.remove('hidden');
        parent?.classList.add('open');
      }
    });
  });

  /* ── Newsletter Form ─────────────────────── */
  const newsletterForm = document.querySelector('#newsletter-form');
  newsletterForm?.addEventListener('submit', (e) => {
    e.preventDefault();
    const input = newsletterForm.querySelector('input[type="email"]');
    if (!input?.value.trim()) return;
    const msg = document.querySelector('#newsletter-msg');
    if (msg) {
      msg.textContent = "Thanks for joining — we'll be in touch soon.";
      msg.className = 'newsletter-msg success';
    }
    newsletterForm.reset();
  });

  /* ── Contact Form fallback ───────────────── */
  const contactForm = document.querySelector('#contact-form');
  if (contactForm && !contactForm.dataset.handled) {
    contactForm.dataset.handled = 'main';
    contactForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const msg = document.querySelector('#form-msg');
      if (msg) {
        msg.textContent = 'Thank you! We will respond within 24 hours.';
        msg.className = 'form-msg success';
      }
      contactForm.reset();
    });
  }

  /* ── Active nav link highlight ───────────── */
  const currentPage = window.location.pathname.split('/').pop() || 'index.html';
  document.querySelectorAll('.nav-links a, .nav-drawer a').forEach((link) => {
    const href = link.getAttribute('href');
    if (href && (href === currentPage || href.startsWith(currentPage.replace('.html', '')))) {
      link.classList.add('active');
    }
  });

});

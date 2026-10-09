/**
 * BrandStory – Abu Dhabi landing page
 * Vanilla JS, no jQuery. Each feature is an isolated init function.
 */
(function () {
  'use strict';

  const $ = (sel, ctx = document) => ctx.querySelector(sel);
  const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const isDesktopNav = () => window.matchMedia('(min-width: 1200px)').matches;

  /* ---------- Sticky header ---------- */
  function initHeader() {
    const header = $('.site-header');
    if (!header) return;
    const update = () => header.classList.toggle('is-scrolled', window.scrollY > 40);
    update();
    window.addEventListener('scroll', update, { passive: true });
  }

  /* ---------- Navigation: mobile drawer + dropdowns ---------- */
  function initNav() {
    const toggle = $('.nav-toggle');
    const nav = $('#main-nav');
    if (!toggle || !nav) return;

    const setOpen = (open) => {
      toggle.setAttribute('aria-expanded', String(open));
      toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
      nav.classList.toggle('is-open', open);
      document.body.classList.toggle('nav-open', open);
    };

    toggle.addEventListener('click', () => setOpen(toggle.getAttribute('aria-expanded') !== 'true'));

    // Dropdown toggles (click on mobile; click/keyboard on desktop, hover handled in CSS)
    const items = $$('.has-dropdown', nav);
    const closeAll = (except) => items.forEach((item) => {
      if (item === except) return;
      item.classList.remove('is-open');
      $('.main-nav__link', item).setAttribute('aria-expanded', 'false');
    });

    items.forEach((item) => {
      const btn = $('.main-nav__link', item);
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const open = !item.classList.contains('is-open');
        closeAll(item);
        item.classList.toggle('is-open', open);
        btn.setAttribute('aria-expanded', String(open));
      });
    });

    // Close menus after choosing a link
    $$('a', nav).forEach((a) => a.addEventListener('click', () => { closeAll(); setOpen(false); }));
    document.addEventListener('click', (e) => { if (isDesktopNav() && !nav.contains(e.target)) closeAll(); });
    document.addEventListener('keydown', (e) => {
      if (e.key !== 'Escape') return;
      closeAll();
      if (nav.classList.contains('is-open')) { setOpen(false); toggle.focus(); }
    });
    window.addEventListener('resize', () => { if (isDesktopNav()) setOpen(false); });
  }

  /* ---------- Accordion (single item open, animated height) ---------- */
  function initAccordions() {
    $$('[data-accordion]').forEach((accordion) => {
      const items = $$('.accordion__item', accordion);

      const animate = (panel, open) => {
        if (prefersReducedMotion) { panel.hidden = !open; panel.style.height = ''; return; }
        if (open) {
          panel.hidden = false;
          const h = panel.scrollHeight;
          panel.style.height = '0px';
          requestAnimationFrame(() => { panel.style.height = h + 'px'; });
        } else {
          panel.style.height = panel.scrollHeight + 'px';
          requestAnimationFrame(() => { panel.style.height = '0px'; });
        }
        panel.addEventListener('transitionend', function done(e) {
          if (e.propertyName !== 'height') return;
          if (!open) panel.hidden = true;
          panel.style.height = '';
          panel.removeEventListener('transitionend', done);
        });
      };

      const setItem = (item, open) => {
        const trigger = $('.accordion__trigger', item);
        const panel = $('.accordion__panel', item);
        if (item.classList.contains('is-open') === open) return;
        item.classList.toggle('is-open', open);
        trigger.setAttribute('aria-expanded', String(open));
        animate(panel, open);
      };

      items.forEach((item) => {
        $('.accordion__trigger', item).addEventListener('click', () => {
          const willOpen = !item.classList.contains('is-open');
          items.forEach((other) => { if (other !== item) setItem(other, false); });
          setItem(item, willOpen);
        });
      });
    });
  }

  /* ---------- Logo marquee: duplicate items for a seamless loop ---------- */
  function initMarquee() {
    $$('[data-marquee] .marquee__track').forEach((track) => {
      Array.from(track.children).forEach((li) => {
        const clone = li.cloneNode(true);
        clone.setAttribute('aria-hidden', 'true');
        $$('img', clone).forEach((img) => { img.alt = ''; });
        track.appendChild(clone);
      });
    });
  }

  /* ---------- Count-up statistics ---------- */
  function initCounters() {
    const counters = $$('[data-count]');
    if (!counters.length) return;
    const run = (el) => {
      const target = parseInt(el.dataset.count, 10);
      if (prefersReducedMotion) { el.textContent = target; return; }
      const duration = 1600;
      const start = performance.now();
      const tick = (now) => {
        const p = Math.min((now - start) / duration, 1);
        const eased = 1 - Math.pow(1 - p, 3);
        el.textContent = Math.round(target * eased);
        if (p < 1) requestAnimationFrame(tick);
      };
      el.textContent = '0';
      requestAnimationFrame(tick);
    };
    if (!('IntersectionObserver' in window)) return;
    const io = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        run(entry.target);
        io.unobserve(entry.target);
      });
    }, { threshold: 0.6 });
    counters.forEach((c) => io.observe(c));
  }

  /* ---------- Carousels (Swiper) ---------- */
  function initSliders() {
    if (typeof window.Swiper === 'undefined') return;

    if ($('[data-team-slider]')) {
      new window.Swiper('[data-team-slider]', {
        slidesPerView: 'auto',
        centeredSlides: true,
        loop: true,
        spaceBetween: 24,
        speed: 600,
        grabCursor: true,
        keyboard: { enabled: true },
        autoplay: prefersReducedMotion ? false : { delay: 3500, disableOnInteraction: false, pauseOnMouseEnter: true },
        navigation: { prevEl: '[data-team-prev]', nextEl: '[data-team-next]' },
        a11y: { prevSlideMessage: 'Previous photo', nextSlideMessage: 'Next photo' },
        breakpoints: { 768: { spaceBetween: 60 } },
      });
    }

    if ($('[data-cases-slider]')) {
      new window.Swiper('[data-cases-slider]', {
        slidesPerView: 1.15,
        spaceBetween: 16,
        speed: 600,
        grabCursor: true,
        keyboard: { enabled: true },
        navigation: { prevEl: '[data-cases-prev]', nextEl: '[data-cases-next]' },
        breakpoints: {
          640: { slidesPerView: 2, spaceBetween: 20 },
          992: { slidesPerView: 3, spaceBetween: 27 },
        },
      });
    }
  }

  /* ---------- Contact modal + form ----------
   * Opened by [data-open-modal] elements or any link to "#quote" (WordPress menus / Elementor buttons).
   * The "Interested in" field is prefilled from data-subject, the nearest card title, or the link text
   * of a location link.
   * If window.brandstoryData is present (WordPress build) the form posts to admin-ajax.php;
   * otherwise (static build) it validates and shows a success message.
   */
  function initModal() {
    const modal = $('#contact-modal');
    const form = $('#contact-form');
    if (!modal || !form) return;
    const status = $('.form-status', form);
    const subject = $('#f-subject', form);
    const submitBtn = $('button[type="submit"]', form);
    const wp = window.brandstoryData || null;
    const t = (key, fallback) => (wp && wp.i18n && wp.i18n[key]) || fallback;
    let lastTrigger = null;

    const subjectFor = (trigger) => {
      if (!trigger) return '';
      if (trigger.dataset.subject) return trigger.dataset.subject;
      const card = trigger.closest('.service-card, .bs-service-card, .card');
      const title = card && card.querySelector('h3');
      if (title) return title.textContent.trim();
      if (trigger.closest('.locations')) return trigger.textContent.trim();
      return '';
    };

    const open = (trigger) => {
      lastTrigger = trigger;
      const s = subjectFor(trigger);
      if (s) subject.value = s;
      status.textContent = '';
      status.className = 'form-status';
      if (typeof modal.showModal === 'function') modal.showModal(); else modal.setAttribute('open', '');
      document.body.classList.remove('nav-open');
      $('#main-nav')?.classList.remove('is-open');
      $('.nav-toggle')?.setAttribute('aria-expanded', 'false');
      setTimeout(() => $('#f-name', form).focus(), 50);
    };
    const close = () => { modal.close ? modal.close() : modal.removeAttribute('open'); };

    document.addEventListener('click', (e) => {
      const trigger = e.target.closest('[data-open-modal], a[href="#quote"], a[href$="/#quote"]');
      if (!trigger) return;
      e.preventDefault();
      open(trigger);
    });
    if (window.location.hash === '#quote') open(null);

    $$('[data-close-modal]', modal).forEach((el) => el.addEventListener('click', close));
    modal.addEventListener('click', (e) => { if (e.target === modal) close(); }); // backdrop click
    modal.addEventListener('close', () => { if (lastTrigger) lastTrigger.focus(); });

    const setStatus = (msg, type) => {
      status.textContent = msg;
      status.className = 'form-status' + (type ? ' is-' + type : '');
    };

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      let valid = true;
      $$('input:not([type="hidden"]):not([name="website"]), textarea', form).forEach((field) => {
        const ok = field.checkValidity();
        field.classList.toggle('is-invalid', !ok);
        field.setAttribute('aria-invalid', String(!ok));
        if (!ok) valid = false;
      });
      if (!valid) {
        setStatus(t('invalid', 'Please fill in the highlighted fields.'), 'error');
        $('.is-invalid', form)?.focus();
        return;
      }

      const done = () => {
        setStatus(t('success', 'Thank you! Our team will contact you shortly.'), 'success');
        form.reset();
        setTimeout(close, 2200);
      };

      if (!wp) { done(); return; } // Static build: no backend.

      submitBtn.disabled = true;
      try {
        const body = new FormData(form);
        body.set('action', 'brandstory_contact');
        body.set('nonce', wp.nonce);
        const res = await fetch(wp.ajaxUrl, { method: 'POST', body, credentials: 'same-origin' });
        const json = await res.json();
        if (json && json.success) done();
        else setStatus((json && json.data && json.data.message) || t('error', 'Something went wrong.'), 'error');
      } catch (err) {
        setStatus(t('error', 'Something went wrong.'), 'error');
      } finally {
        submitBtn.disabled = false;
      }
    });
  }

  /* ---------- Footer year ---------- */
  function initYear() {
    $$('[data-year]').forEach((el) => { el.textContent = new Date().getFullYear(); });
  }

  document.addEventListener('DOMContentLoaded', () => {
    initHeader();
    initNav();
    initAccordions();
    initMarquee();
    initCounters();
    initSliders();
    initModal();
    initYear();
  });
})();

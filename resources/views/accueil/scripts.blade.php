<script>
  // Navigation scroll effect
  const siteHeader = document.getElementById('siteHeader');
  const navbar = document.getElementById('navbar');

  function updateNavbarScrollState() {
    const scrolled = window.scrollY > 50;
    if (siteHeader) {
      siteHeader.classList.toggle('scrolled', scrolled);
    }
    if (navbar) {
      navbar.classList.toggle('scrolled', scrolled);
    }
  }

  window.addEventListener('scroll', updateNavbarScrollState);
  updateNavbarScrollState();

  // Mobile menu toggle
  const menuToggle = document.getElementById('menuToggle');

  function openMenu() {
    if (siteHeader) {
      siteHeader.classList.add('mobile-menu-open');
    }
    if (navbar) {
      navbar.classList.add('mobile-menu-open');
    }
    document.body.classList.add('mobile-menu-open');
    if (menuToggle) {
      menuToggle.setAttribute('aria-expanded', 'true');
      menuToggle.setAttribute('aria-label', 'Fermer le menu');
    }
  }

  function closeMenu() {
    if (siteHeader) {
      siteHeader.classList.remove('mobile-menu-open');
    }
    if (navbar) {
      navbar.classList.remove('mobile-menu-open');
    }
    document.body.classList.remove('mobile-menu-open');
    if (menuToggle) {
      menuToggle.setAttribute('aria-expanded', 'false');
      menuToggle.setAttribute('aria-label', 'Ouvrir le menu');
    }
  }

  function toggleMenu() {
    if (navbar?.classList.contains('mobile-menu-open')) {
      closeMenu();
    } else {
      openMenu();
    }
  }

  document.querySelectorAll('.nav-links a').forEach(link => {
    link.addEventListener('click', () => {
      if (window.innerWidth <= 1100) {
        closeMenu();
      }
    });
  });

  // Sur mobile / écran réduit : #contact → formulaire (#contact-form)
  // Couvre navbar "Contact", bouton hero "Envoyer vos STL", etc.
  const CONTACT_MOBILE_BREAKPOINT = 1100;

  function isReducedViewport() {
    return window.innerWidth <= CONTACT_MOBILE_BREAKPOINT;
  }

  function contactHashForViewport() {
    return isReducedViewport() ? '#contact-form' : '#contact';
  }

  function isContactHash(hash) {
    return hash === '#contact' || hash === '#contact-form';
  }

  function resolveContactHref(href) {
    try {
      const url = new URL(href, window.location.origin);
      if (!isContactHash(url.hash)) {
        return null;
      }

      url.hash = contactHashForViewport();
      return url.pathname + url.search + url.hash;
    } catch (error) {
      return null;
    }
  }

  function syncContactLinks() {
    document.querySelectorAll('a[href*="#contact"]').forEach((link) => {
      const href = link.getAttribute('href');
      if (!href) return;

      const nextHref = resolveContactHref(href);
      if (nextHref) {
        link.setAttribute('href', nextHref);
      }
    });
  }

  function scrollToContactTargetFromHash() {
    if (!isContactHash(window.location.hash)) return;

    const preferredId = isReducedViewport() ? 'contact-form' : 'contact';
    const target = document.getElementById(preferredId)
      || document.getElementById('contact-form')
      || document.getElementById('contact');

    if (!target) return;

    const desiredHash = '#' + (target.id || preferredId);
    if (window.location.hash !== desiredHash) {
      history.replaceState(null, '', desiredHash);
    }

    requestAnimationFrame(() => {
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  }

  syncContactLinks();
  window.addEventListener('resize', syncContactLinks);
  window.addEventListener('load', scrollToContactTargetFromHash);
  window.addEventListener('hashchange', scrollToContactTargetFromHash);

  document.querySelectorAll('a[href*="#contact"]').forEach((link) => {
    link.addEventListener('click', (event) => {
      const href = link.getAttribute('href') || '';
      const resolved = resolveContactHref(href);
      if (!resolved) return;

      const url = new URL(resolved, window.location.origin);
      const samePath = url.pathname === window.location.pathname;

      if (!samePath) {
        link.setAttribute('href', resolved);
        return;
      }

      const target = document.querySelector(url.hash);
      if (!target) return;

      event.preventDefault();
      event.stopImmediatePropagation();

      if (isReducedViewport()) {
        closeMenu();
      }

      history.pushState(null, '', url.hash);
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  });

  document.querySelectorAll('[data-nav-dropdown]').forEach((item) => {
    const toggle = item.querySelector('[data-nav-dropdown-toggle]');
    if (!toggle) return;

    toggle.addEventListener('click', (event) => {
      event.preventDefault();
      event.stopPropagation();

      const willOpen = !item.classList.contains('is-open');
      document.querySelectorAll('[data-nav-dropdown].is-open').forEach((openItem) => {
        if (openItem !== item) {
          openItem.classList.remove('is-open');
          openItem.querySelector('[data-nav-dropdown-toggle]')?.setAttribute('aria-expanded', 'false');
        }
      });

      item.classList.toggle('is-open', willOpen);
      toggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
    });
  });

  document.addEventListener('click', (event) => {
    if (event.target.closest('[data-nav-dropdown]')) return;
    document.querySelectorAll('[data-nav-dropdown].is-open').forEach((item) => {
      item.classList.remove('is-open');
      item.querySelector('[data-nav-dropdown-toggle]')?.setAttribute('aria-expanded', 'false');
    });
  });

  document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;
    document.querySelectorAll('[data-nav-dropdown].is-open').forEach((item) => {
      item.classList.remove('is-open');
      item.querySelector('[data-nav-dropdown-toggle]')?.setAttribute('aria-expanded', 'false');
    });
  });

  // Scroll reveal animation
  const revealElements = document.querySelectorAll('.reveal');

  function checkReveal() {
    const triggerBottom = window.innerHeight * 0.85;
    revealElements.forEach((element, index) => {
      const elementTop = element.getBoundingClientRect().top;
      if (elementTop < triggerBottom) {
        setTimeout(() => {
          element.classList.add('active');
        }, index * 40);
      }
    });
  }

  window.addEventListener('scroll', checkReveal);
  window.addEventListener('load', checkReveal);

  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      let hash = this.getAttribute('href');
      if (!hash || hash === '#') return;

      if (isContactHash(hash)) {
        hash = contactHashForViewport();
      }

      const target = document.querySelector(hash);
      if (!target) return;

      e.preventDefault();
      history.pushState(null, '', hash);
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  });

  // Counter animation for stats (lit les valeurs affichées depuis la config vitrine)
  function parseStatValue(text) {
    const trimmed = String(text || '').trim();
    const match = trimmed.match(/^([\d][\d\s.,]*)\s*(.*)$/);
    if (!match) {
      return null;
    }

    const number = parseFloat(match[1].replace(/\s/g, '').replace(',', '.'));
    if (Number.isNaN(number)) {
      return null;
    }

    return { number, suffix: match[2] || '' };
  }

  function animateCounter(element, parsed) {
    const { number, suffix } = parsed;
    let current = 0;
    const increment = number / 50;
    const timer = setInterval(() => {
      current += increment;
      if (current >= number) {
        current = number;
        clearInterval(timer);
      }
      element.textContent = Math.floor(current) + suffix;
    }, 30);
  }

  const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.querySelectorAll('.stat-value').forEach((stat) => {
          const parsed = parseStatValue(stat.textContent);
          if (parsed) {
            stat.textContent = '0' + parsed.suffix;
            animateCounter(stat, parsed);
          }
        });
        statsObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.5 });

  const heroCard = document.querySelector('.hero-card');
  if (heroCard) statsObserver.observe(heroCard);

  // Parallax effect on shapes (desktop uniquement)
  if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
    window.addEventListener('mousemove', (e) => {
      const shapes = document.querySelectorAll('.shape');
      const x = e.clientX / window.innerWidth;
      const y = e.clientY / window.innerHeight;
      shapes.forEach((shape, index) => {
        const speed = (index + 1) * 20;
        shape.style.transform = `translate(${x * speed}px, ${y * speed}px)`;
      });
    });
  }

  window.addEventListener('resize', () => {
    if (window.innerWidth > 1100 && navbar.classList.contains('mobile-menu-open')) {
      closeMenu();
    }
  });

  // Hero Slider
  const slides = document.querySelectorAll('.hero-slide');
  const dots = document.querySelectorAll('.slider-dot');
  let currentSlide = 0;

  function showSlide(index) {
    slides.forEach(s => s.classList.remove('active'));
    dots.forEach(d => d.classList.remove('active'));
    slides[index].classList.add('active');
    dots[index].classList.add('active');
    currentSlide = index;
  }

  function nextSlide() {
    showSlide((currentSlide + 1) % slides.length);
  }

  if (slides.length > 0) {
    setInterval(nextSlide, 3500);
    dots.forEach((dot, i) => {
      dot.addEventListener('click', () => showSlide(i));
    });
  }

  // Galerie — lightbox
  (function initGalleryLightbox() {
    const lightbox = document.getElementById('galleryLightbox');
    const siteHeader = document.getElementById('siteHeader');
    if (!lightbox) return;

    if (lightbox.parentElement !== document.body) {
      document.body.appendChild(lightbox);
    }

    const isPremium = lightbox.classList.contains('gallery-lightbox--premium');
    const imageEl = document.getElementById('galleryLightboxImage');
    const titleEl = document.getElementById('galleryLightboxTitle');
    const descEl = document.getElementById('galleryLightboxDesc');
    const titleInlineEl = document.getElementById('galleryLightboxTitleInline');
    const counterEl = document.getElementById('galleryLightboxCounter');
    const progressEl = document.getElementById('galleryLightboxProgress');
    const thumbsEl = document.getElementById('galleryLightboxThumbs');
    const captionEl = lightbox.querySelector('.gallery-lightbox-caption');
    const prevBtn = lightbox.querySelector('[data-gallery-lightbox-prev]');
    const nextBtn = lightbox.querySelector('[data-gallery-lightbox-next]');
    const closeEls = lightbox.querySelectorAll('[data-gallery-lightbox-close]');
    let activeGroup = 'default';
    let items = collectItems(activeGroup);
    let currentIndex = 0;
    let lastFocused = null;
    let imageSwapTimer = null;

    function getGalleryScope(element) {
      if (element) {
        return element.closest('[data-gallery-scope], #travaux') || document;
      }

      return document.querySelector('[data-gallery-scope]') || document.getElementById('travaux') || document;
    }

    function collectItems(group, scope) {
      const root = scope || document;
      return Array.from(root.querySelectorAll('[data-gallery-item]')).filter((item) => {
        if (item.hidden) return false;

        return (item.dataset.galleryGroup || 'default') === group;
      });
    }

    function refreshItems() {
      items = collectItems(activeGroup, getGalleryScope());
      buildThumbs();
    }

    function setCaption(title, description) {
      titleEl.textContent = title || '';
      descEl.textContent = description || '';
      titleEl.hidden = !title;
      descEl.hidden = !description;
      if (titleInlineEl) {
        titleInlineEl.textContent = title || '';
        titleInlineEl.hidden = !title;
      }
      if (captionEl) {
        captionEl.hidden = !title && !description;
      }
    }

    function updateProgress(index) {
      if (!progressEl || items.length <= 1) return;
      progressEl.style.width = `${((index + 1) / items.length) * 100}%`;
    }

    function updateThumbs(index) {
      if (!thumbsEl) return;
      thumbsEl.querySelectorAll('.gallery-lightbox-thumb').forEach((thumb, thumbIndex) => {
        thumb.classList.toggle('is-active', thumbIndex === index);
        thumb.setAttribute('aria-selected', thumbIndex === index ? 'true' : 'false');
      });

      const activeThumb = thumbsEl.querySelector('.gallery-lightbox-thumb.is-active');
      activeThumb?.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
    }

    function buildThumbs() {
      if (!thumbsEl) return;
      thumbsEl.innerHTML = '';

      items.forEach((item, index) => {
        const src = item.dataset.gallerySrc || '';
        if (!src) return;

        const thumb = document.createElement('button');
        thumb.type = 'button';
        thumb.className = 'gallery-lightbox-thumb';
        thumb.setAttribute('role', 'tab');
        thumb.setAttribute('aria-label', item.dataset.galleryTitle || `Image ${index + 1}`);

        const img = document.createElement('img');
        img.src = src;
        img.alt = '';
        img.loading = 'eager';
        img.decoding = 'async';
        thumb.appendChild(img);

        thumb.addEventListener('click', () => showAt(index));
        thumbsEl.appendChild(thumb);
      });
    }

    function applyImage(src, title) {
      if (!isPremium) {
        imageEl.src = src;
        imageEl.alt = title || 'Image galerie';
        return;
      }

      window.clearTimeout(imageSwapTimer);
      imageEl.classList.remove('is-entering');
      imageEl.classList.add('is-changing');

      imageSwapTimer = window.setTimeout(() => {
        imageEl.src = src;
        imageEl.alt = title || 'Image galerie';
        imageEl.classList.remove('is-changing');
        imageEl.classList.add('is-entering');
        window.setTimeout(() => imageEl.classList.remove('is-entering'), 480);
      }, 160);
    }

    function showAt(index) {
      const item = items[index];
      if (!item) return;

      currentIndex = index;
      const src = item.dataset.gallerySrc || '';
      const title = item.dataset.galleryTitle || '';
      const description = item.dataset.galleryDescription || '';

      applyImage(src, title);
      setCaption(title, description);

      if (counterEl) {
        counterEl.textContent = `${index + 1} / ${items.length}`;
      }

      updateProgress(index);
      updateThumbs(index);

      if (prevBtn) prevBtn.disabled = index <= 0;
      if (nextBtn) nextBtn.disabled = index >= items.length - 1;
    }

    function open(index) {
      lastFocused = document.activeElement;
      if (thumbsEl) {
        buildThumbs();
      }
      showAt(index);
      lightbox.hidden = false;
      lightbox.setAttribute('aria-hidden', 'false');
      document.body.classList.add('gallery-lightbox-open');
      siteHeader?.setAttribute('aria-hidden', 'true');
      requestAnimationFrame(() => {
        lightbox.classList.add('is-active');
      });
      lightbox.querySelector('.gallery-lightbox-close')?.focus();
    }

    function close() {
      lightbox.classList.remove('is-active');
      window.clearTimeout(imageSwapTimer);
      imageEl.classList.remove('is-changing', 'is-entering');

      window.setTimeout(() => {
        lightbox.hidden = true;
        lightbox.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('gallery-lightbox-open');
        siteHeader?.removeAttribute('aria-hidden');
        imageEl.removeAttribute('src');
        if (lastFocused && typeof lastFocused.focus === 'function') {
          lastFocused.focus();
        }
      }, isPremium ? 320 : 220);
    }

    buildThumbs();

    if (!document.querySelector('[data-gallery-item]')) return;

    document.addEventListener('click', (event) => {
      const item = event.target.closest('[data-gallery-item]');
      if (!item || item.hidden) return;

      activeGroup = item.dataset.galleryGroup || 'default';
      items = collectItems(activeGroup, getGalleryScope(item));
      const index = items.indexOf(item);
      if (index >= 0) open(index);
    });

    document.addEventListener('keydown', (event) => {
      const item = event.target.closest('[data-gallery-item]');
      if (!item || item.hidden) return;
      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        activeGroup = item.dataset.galleryGroup || 'default';
        items = collectItems(activeGroup, getGalleryScope(item));
        const index = items.indexOf(item);
        if (index >= 0) open(index);
      }
    });

    prevBtn?.addEventListener('click', () => {
      if (currentIndex > 0) showAt(currentIndex - 1);
    });

    nextBtn?.addEventListener('click', () => {
      if (currentIndex < items.length - 1) showAt(currentIndex + 1);
    });

    closeEls.forEach((el) => el.addEventListener('click', close));

    document.addEventListener('keydown', (event) => {
      if (lightbox.hidden || !lightbox.classList.contains('is-active')) return;

      if (event.key === 'Escape') {
        close();
        return;
      }

      if (event.key === 'ArrowLeft' && currentIndex > 0) {
        showAt(currentIndex - 1);
      }

      if (event.key === 'ArrowRight' && currentIndex < items.length - 1) {
        showAt(currentIndex + 1);
      }
    });
  })();

  // Formulaire de contact — état d'envoi (spinner + « Envoi en cours »)
  (function initContactSubmit() {
    const form = document.querySelector('[data-contact-form]');
    if (!form) return;

    const btn = form.querySelector('[data-contact-submit]');
    if (!btn) return;

    form.addEventListener('submit', () => {
      if (btn.classList.contains('is-loading')) return;

      btn.classList.add('is-loading');
      btn.setAttribute('aria-busy', 'true');

      const label = btn.querySelector('.contact-submit__label');
      if (label) label.textContent = 'Envoi en cours';
    });
  })();

  // FAQ accordion
  (function () {
    const accordion = document.querySelector('[data-faq-accordion]');
    if (!accordion) return;

    const items = accordion.querySelectorAll('[data-faq-item]');

    function closeItem(item) {
      const trigger = item.querySelector('[data-faq-trigger]');
      const panel = item.querySelector('[data-faq-panel]');
      if (!trigger || !panel || panel.hidden) return;

      const startHeight = panel.scrollHeight;
      panel.style.height = startHeight + 'px';
      panel.offsetHeight;
      panel.style.height = '0px';
      item.classList.remove('is-open');
      trigger.setAttribute('aria-expanded', 'false');

      const onEnd = (event) => {
        if (event.propertyName !== 'height') return;
        panel.removeEventListener('transitionend', onEnd);
        panel.hidden = true;
        panel.style.height = '';
      };
      panel.addEventListener('transitionend', onEnd);
    }

    function openItem(item) {
      const trigger = item.querySelector('[data-faq-trigger]');
      const panel = item.querySelector('[data-faq-panel]');
      if (!trigger || !panel) return;

      panel.hidden = false;
      panel.style.height = '0px';
      panel.offsetHeight;
      const endHeight = panel.scrollHeight;
      panel.style.height = endHeight + 'px';
      item.classList.add('is-open');
      trigger.setAttribute('aria-expanded', 'true');

      const onEnd = (event) => {
        if (event.propertyName !== 'height') return;
        panel.removeEventListener('transitionend', onEnd);
        if (item.classList.contains('is-open')) {
          panel.style.height = 'auto';
        }
      };
      panel.addEventListener('transitionend', onEnd);
    }

    items.forEach((item) => {
      const trigger = item.querySelector('[data-faq-trigger]');
      const panel = item.querySelector('[data-faq-panel]');
      if (!trigger || !panel) return;

      if (trigger.getAttribute('aria-expanded') === 'true') {
        item.classList.add('is-open');
        panel.style.height = 'auto';
      }

      trigger.addEventListener('click', () => {
        const isOpen = item.classList.contains('is-open');

        items.forEach((other) => {
          if (other !== item) closeItem(other);
        });

        if (isOpen) {
          closeItem(item);
        } else {
          openItem(item);
        }
      });
    });
  })();

  // Témoignages : slider avec flèches gauche / droite
  (function () {
    const slider = document.querySelector('[data-temoignages-slider]');
    if (!slider) return;

    const viewport = slider.querySelector('[data-temoignages-viewport]');
    const slides = Array.from(slider.querySelectorAll('[data-temoignage-slide]'));
    const prevBtn = slider.querySelector('[data-temoignages-prev]');
    const nextBtn = slider.querySelector('[data-temoignages-next]');
    const dotsContainer = document.querySelector('[data-temoignages-dots]');

    if (!viewport || slides.length === 0) return;

    function stepSize() {
      if (slides.length < 2) return slides[0]?.offsetWidth || viewport.clientWidth;
      return slides[1].offsetLeft - slides[0].offsetLeft;
    }

    function maxScroll() {
      return Math.max(0, viewport.scrollWidth - viewport.clientWidth);
    }

    function pageCount() {
      const step = stepSize();
      if (step <= 0) return 1;
      return Math.round(maxScroll() / step) + 1;
    }

    function currentPage() {
      const step = stepSize();
      if (step <= 0) return 0;
      return Math.round(viewport.scrollLeft / step);
    }

    function buildDots() {
      if (!dotsContainer) return;
      dotsContainer.innerHTML = '';
      const count = pageCount();
      if (count <= 1) return;

      for (let i = 0; i < count; i++) {
        const dot = document.createElement('button');
        dot.type = 'button';
        dot.className = 'temoignages-slider__dot';
        dot.tabIndex = -1;
        dot.addEventListener('click', () => {
          viewport.scrollTo({ left: i * stepSize(), behavior: 'smooth' });
        });
        dotsContainer.appendChild(dot);
      }
    }

    function updateUi() {
      const atStart = viewport.scrollLeft <= 4;
      const atEnd = viewport.scrollLeft >= maxScroll() - 4;

      if (prevBtn) prevBtn.disabled = atStart;
      if (nextBtn) nextBtn.disabled = atEnd;

      if (dotsContainer) {
        const page = currentPage();
        dotsContainer.querySelectorAll('.temoignages-slider__dot').forEach((dot, index) => {
          dot.classList.toggle('active', index === page);
        });
      }
    }

    function scrollByStep(direction) {
      viewport.scrollBy({ left: direction * stepSize(), behavior: 'smooth' });
    }

    prevBtn?.addEventListener('click', () => scrollByStep(-1));
    nextBtn?.addEventListener('click', () => scrollByStep(1));

    let scrollFrame = null;
    viewport.addEventListener('scroll', () => {
      if (scrollFrame) return;
      scrollFrame = requestAnimationFrame(() => {
        scrollFrame = null;
        updateUi();
      });
    }, { passive: true });

    let resizeTimer = null;
    window.addEventListener('resize', () => {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(() => {
        buildDots();
        updateUi();
      }, 150);
    });

    buildDots();
    updateUi();
  })();
</script>

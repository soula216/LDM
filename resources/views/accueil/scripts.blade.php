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
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
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
    const gallerySection = document.getElementById('travaux');
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
    let items = collectItems();
    let currentIndex = 0;
    let lastFocused = null;
    let imageSwapTimer = null;

    function collectItems() {
      const scope = gallerySection || document;
      return Array.from(scope.querySelectorAll('[data-gallery-item]')).filter((item) => !item.hidden);
    }

    function refreshItems() {
      items = collectItems();
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

    if (items.length === 0) return;

    const galleryRoot = gallerySection || document;

    galleryRoot.addEventListener('click', (event) => {
      const item = event.target.closest('[data-gallery-item]');
      if (!item || item.hidden) return;
      const index = items.indexOf(item);
      if (index >= 0) open(index);
    });

    galleryRoot.addEventListener('keydown', (event) => {
      const item = event.target.closest('[data-gallery-item]');
      if (!item || item.hidden) return;
      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
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
</script>

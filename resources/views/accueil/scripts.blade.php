<script>
  // Navigation scroll effect
  const navbar = document.getElementById('navbar');
  window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  });

  // Mobile menu toggle
  function toggleMenu() {
    const navLinks = document.querySelector('.nav-links');
    navLinks.style.display = navLinks.style.display === 'flex' ? 'none' : 'flex';
    navLinks.style.flexDirection = 'column';
    navLinks.style.position = 'absolute';
    navLinks.style.top = '70px';
    navLinks.style.left = '0';
    navLinks.style.width = '100%';
    navLinks.style.background = 'rgba(241, 245, 249, 0.98)';
    navLinks.style.padding = '2rem';
    navLinks.style.gap = '1.5rem';
  }

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

  // Counter animation for stats
  function animateCounter(element, target) {
    let current = 0;
    const increment = target / 50;
    const timer = setInterval(() => {
      current += increment;
      if (current >= target) {
        current = target;
        clearInterval(timer);
      }
      element.textContent = Math.floor(current);
    }, 30);
  }

  const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const statValues = entry.target.querySelectorAll('.stat-value');
        const targets = [15, 5000, 99];
        statValues.forEach((stat, index) => {
          animateCounter(stat, targets[index]);
        });
        statsObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.5 });

  const heroCard = document.querySelector('.hero-card');
  if (heroCard) statsObserver.observe(heroCard);

  // Parallax effect on shapes
  window.addEventListener('mousemove', (e) => {
    const shapes = document.querySelectorAll('.shape');
    const x = e.clientX / window.innerWidth;
    const y = e.clientY / window.innerHeight;
    shapes.forEach((shape, index) => {
      const speed = (index + 1) * 20;
      shape.style.transform = `translate(${x * speed}px, ${y * speed}px)`;
    });
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
</script>

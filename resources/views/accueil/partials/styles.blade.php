    :root {
      /* Palette claire, moderne et professionnelle (fond light, accents bleus) */
      --primary: #0284c7;
      --primary-light: #38bdf8;
      --primary-glow: rgba(56, 189, 248, 0.45);
      --secondary: #0ea5e9;
      --accent: #22c55e;
      --bg: #f1f5f9;
      --bg-card: #ffffff;
      --bg-card-hover: #e0f2fe;
      --dark: #0f172a;
      --text: #0f172a;
      --text-muted: #64748b;
      --border: rgba(148, 163, 184, 0.3);
      --gradient-1: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
      --gradient-2: linear-gradient(135deg, #22c55e 0%, #0ea5e9 100%);
      --gradient-3: linear-gradient(135deg, #38bdf8 0%, #0ea5e9 100%);
      --top-bar-height: 2.35rem;
      --header-logo-height: 7.5rem;
      --header-nav-padding-y: 2.4rem;
      --site-header-offset: calc(var(--top-bar-height) + 0.7rem + var(--header-nav-padding-y) + var(--header-logo-height));
      --inner-hero-gap: 2rem;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html {
      scroll-behavior: smooth;
    }

    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--bg);
      color: var(--text);
      overflow-x: hidden;
      line-height: 1.7;
    }

    /* Animated Background */
    .bg-animation {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
      overflow: hidden;
    }

    .bg-animation::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: 
        radial-gradient(circle at 20% 80%, rgba(0, 168, 204, 0.12) 0%, transparent 40%),
        radial-gradient(circle at 80% 20%, rgba(8, 145, 178, 0.08) 0%, transparent 40%),
        radial-gradient(circle at 40% 40%, rgba(6, 182, 212, 0.06) 0%, transparent 30%);
      animation: bgMove 20s ease-in-out infinite;
    }

    @@keyframes bgMove {
      0%, 100% { transform: translate(0, 0) rotate(0deg); }
      33% { transform: translate(2%, 2%) rotate(1deg); }
      66% { transform: translate(-1%, 1%) rotate(-1deg); }
    }

    .floating-shapes {
      position: absolute;
      width: 100%;
      height: 100%;
    }

    .shape {
      position: absolute;
      border: 1px solid rgba(0, 168, 204, 0.2);
      border-radius: 50%;
      animation: float 15s ease-in-out infinite;
    }

    .shape:nth-child(1) { width: 300px; height: 300px; top: 10%; left: 5%; animation-delay: 0s; }
    .shape:nth-child(2) { width: 200px; height: 200px; top: 60%; right: 10%; animation-delay: -5s; }
    .shape:nth-child(3) { width: 150px; height: 150px; bottom: 20%; left: 30%; animation-delay: -10s; }

    @@keyframes float {
      0%, 100% { transform: translateY(0) rotate(0deg); opacity: 0.3; }
      50% { transform: translateY(-30px) rotate(180deg); opacity: 0.6; }
    }

    /* Header & Navigation */
    .site-header {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 1000;
      transition: background 0.4s ease, backdrop-filter 0.4s ease, border-color 0.4s ease;
    }

    .top-bar {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      min-height: var(--top-bar-height);
      padding: 0.55rem 5% 0.15rem;
      background: none;
      border: none;
      transition: background 0.4s ease, backdrop-filter 0.4s ease, border-color 0.4s ease;
    }

    .site-header.scrolled {
      background: rgba(241, 245, 249, 0.95);
      backdrop-filter: blur(20px);
      border-bottom: 1px solid var(--border);
    }

    .site-header.scrolled .top-bar {
      background: transparent;
      backdrop-filter: none;
      border-bottom: none;
      padding-bottom: 0.15rem;
    }

    .top-bar-inner {
      display: inline-flex;
      align-items: center;
      gap: 0.85rem;
    }

    .top-bar-label {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 0.625rem;
      font-weight: 600;
      letter-spacing: 0.18em;
      text-transform: uppercase;
      line-height: 1;
      opacity: 0.72;
      transition: color 0.35s ease, opacity 0.35s ease;
    }

    .top-bar-accent {
      width: 1.75rem;
      height: 1px;
      background: currentColor;
      opacity: 0.22;
      flex-shrink: 0;
      transition: opacity 0.35s ease, width 0.35s ease;
    }

    .top-bar-inner:hover .top-bar-accent {
      width: 2.25rem;
      opacity: 0.38;
    }

    .site-header:not(.scrolled) .top-bar-label,
    .site-header:not(.scrolled) .top-bar-accent {
      color: #f8fafc;
    }

    .site-header.scrolled .top-bar-label,
    .site-header.scrolled .top-bar-accent {
      color: var(--text-muted);
    }

    .site-header.mobile-menu-open .top-bar {
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
    }

    nav {
      position: relative;
      width: 100%;
      padding: 1.2rem 5%;
      display: flex;
      justify-content: space-between;
      align-items: center;
      transition: all 0.4s ease;
    }

    nav.scrolled {
      background: rgba(241, 245, 249, 0.95);
      backdrop-filter: blur(20px);
      padding: 0.8rem 5%;
      border-bottom: 1px solid var(--border);
    }

    .site-header.scrolled nav.scrolled {
      background: transparent;
      backdrop-filter: none;
      border-bottom: none;
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      text-decoration: none;
    }

    .logo img,
    .logo .logo-img {
      height: 120px;
      width: auto;
      object-fit: contain;
    }

    nav:not(.scrolled) .logo-img-scrolled {
      display: none;
    }

    nav.scrolled .logo-img-header {
      display: none;
    }

    .footer-brand .logo img {
      height: 120px;
    }

    .nav-links {
      display: flex;
      gap: 2.5rem;
      list-style: none;
    }

    .nav-links a {
      color: var(--text-muted);
      text-decoration: none;
      font-weight: 500;
      font-size: 0.95rem;
      position: relative;
      transition: color 0.3s ease;
    }

    .nav-links a::after {
      content: '';
      position: absolute;
      bottom: -5px;
      left: 0;
      width: 0;
      height: 2px;
      background: var(--gradient-1);
      transition: width 0.3s ease;
    }

    .nav-links a:hover {
      color: var(--primary);
    }

    .nav-links a:hover::after {
      width: 100%;
    }

    /* Couleurs du menu selon l'état du header */
    nav:not(.scrolled) .nav-links a {
      color: #e5e7eb;
    }

    nav:not(.scrolled) .nav-links a::after {
      background: #e5e7eb;
    }

    nav.scrolled .nav-links a {
      color: var(--text-muted);
    }

    nav.scrolled .nav-links a::after {
      background: var(--primary);
    }

    .nav-mobile-right {
      display: none;
      align-items: center;
      gap: 1rem;
    }

    @@media (min-width: 1101px) {
      nav {
        justify-content: flex-start;
      }

      .nav-links {
        margin-left: auto;
      }
    }

    .nav-espace-client-mobile {
      display: none;
      color: var(--text-muted);
      text-decoration: none;
      font-weight: 500;
      font-size: 0.9rem;
      white-space: nowrap;
      transition: color 0.3s ease;
    }

    .nav-espace-client-mobile:hover {
      color: var(--primary);
    }

    nav:not(.scrolled) .nav-espace-client-mobile {
      color: #e5e7eb;
    }

    nav:not(.scrolled) .nav-espace-client-mobile:hover {
      color: #ffffff;
    }

    nav.scrolled .nav-espace-client-mobile {
      color: var(--text-muted);
    }

    .menu-toggle {
      display: none;
      flex-direction: column;
      justify-content: center;
      gap: 5px;
      cursor: pointer;
      padding: 5px;
      background: none;
      border: none;
      width: 35px;
      height: 35px;
    }

    .menu-toggle span {
      display: block;
      width: 25px;
      height: 2px;
      background: #ffffff;
      transition: all 0.3s ease;
      transform-origin: center;
    }

    nav.scrolled .menu-toggle span {
      background: var(--text);
    }

    nav.mobile-menu-open .menu-toggle span {
      background: #ffffff !important;
    }

    nav.mobile-menu-open .menu-toggle span:nth-child(1) {
      transform: translateY(7px) rotate(45deg);
    }

    nav.mobile-menu-open .menu-toggle span:nth-child(2) {
      opacity: 0;
      transform: scaleX(0);
    }

    nav.mobile-menu-open .menu-toggle span:nth-child(3) {
      transform: translateY(-7px) rotate(-45deg);
    }

    /* Hero Section with Slider */
    .hero {
      min-height: 100vh;
      display: flex;
      align-items: center;
      padding: calc(8rem + var(--top-bar-height)) 5% 4rem;
      position: relative;
      overflow: hidden;
    }

    /* Hero Slider Background */
    .hero-slider {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 0;
    }

    .hero-slide {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      opacity: 0;
      transition: opacity 0.6s ease-in-out;
      background-size: cover;
      background-position: center;
    }

    .hero-slide.active {
      opacity: 1;
    }

    .hero-slide::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(15, 23, 42, 0.7) 50%, rgba(15, 23, 42, 0.95) 100%);
    }

    .hero-slide-1 {
      background-image: url('https://images.unsplash.com/photo-1606811841689-23dfddce3e95?w=1920&q=80');
    }

    .hero-slide-2 {
      background-image: url('https://images.unsplash.com/photo-1629909613654-28e377c37b09?w=1920&q=80');
    }

    .hero-slide-3 {
      background-image: url('https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?w=1920&q=80');
    }

    /* Slider Dots */
    .slider-dots {
      position: absolute;
      bottom: 2rem;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      gap: 0.75rem;
      z-index: 10;
    }

    .slider-dot {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      background: rgba(0, 168, 204, 0.35);
      cursor: pointer;
      transition: all 0.4s ease;
      border: 2px solid transparent;
    }

    .slider-dot.active {
      background: var(--primary);
      transform: scale(1.2);
      border-color: var(--primary);
      box-shadow: 0 0 20px var(--primary-glow);
    }

    .slider-dot:hover {
      background: rgba(0, 168, 204, 0.6);
    }

    .hero-content {
      max-width: 700px;
      z-index: 1;
    }

    .hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.5rem 1rem;
      background: rgba(0, 168, 204, 0.12);
      border: 1px solid rgba(0, 168, 204, 0.25);
      border-radius: 50px;
      font-size: 0.85rem;
      color: var(--primary);
      margin-bottom: 1.5rem;
      opacity: 0;
      transform: translateY(20px);
      animation: fadeInUp 0.8s ease forwards;
      animation-delay: 0.1s;
    }

    .hero-badge i {
      animation: pulse 2s ease-in-out infinite;
    }

    @@keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.5; }
    }

    .hero h1 {
      font-family: 'Space Grotesk', sans-serif;
      font-size: clamp(2.5rem, 6vw, 4.5rem);
      font-weight: 700;
      line-height: 1.1;
      margin-bottom: 1.5rem;
      color: #f9fafb;
      opacity: 0;
      transform: translateY(30px);
      animation: fadeInUp 0.8s ease forwards;
      animation-delay: 0.2s;
    }

    .hero h1 span {
      background: var(--gradient-1);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .hero p {
      font-size: 1.15rem;
      color: rgba(241, 245, 249, 0.9);
      margin-bottom: 2.5rem;
      max-width: 550px;
      opacity: 0;
      transform: translateY(30px);
      animation: fadeInUp 0.8s ease forwards;
      animation-delay: 0.3s;
    }

    .hero-buttons {
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
      opacity: 0;
      transform: translateY(30px);
      animation: fadeInUp 0.8s ease forwards;
      animation-delay: 0.4s;
    }

    .btn {
      padding: 1rem 2rem;
      border-radius: 50px;
      font-weight: 600;
      font-size: 1rem;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      transition: all 0.4s ease;
      cursor: pointer;
      border: none;
    }

    .btn-primary {
      background: var(--gradient-1);
      color: #fff;
      box-shadow: 0 4px 30px var(--primary-glow);
    }

    .btn-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 40px var(--primary-glow);
    }

    .btn-secondary {
      background: transparent;
      color: #fff;
      border: 1px solid var(--primary);
    }

    .btn-secondary:hover {
      border-color: var(--primary);
      color: var(--primary);
      background: rgba(14, 165, 233, 0.1);
    }

    .btn-whatsapp {
      background: #25D366;
      color: #fff;
      box-shadow: 0 4px 24px rgba(37, 211, 102, 0.35);
    }

    .btn-whatsapp:hover {
      background: #1ebe5d;
      transform: translateY(-3px);
      box-shadow: 0 8px 32px rgba(37, 211, 102, 0.45);
    }

    .btn-whatsapp i {
      font-size: 1.15em;
    }

    .gallery-more__btn.btn-secondary {
      color: var(--text);
      border-color: var(--primary);
    }

    .gallery-more__btn.btn-secondary:hover {
      color: var(--primary);
      background: rgba(14, 165, 233, 0.08);
    }

    @@keyframes fadeInUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Hero Visual */
    .hero-visual {
      position: absolute;
      right: 5%;
      top: 50%;
      transform: translateY(-50%);
      width: 45%;
      max-width: 600px;
      opacity: 0;
      animation: fadeIn 1s ease forwards;
      animation-delay: 1s;
    }

    @@keyframes fadeIn {
      to { opacity: 1; }
    }

    .hero-card {
      background: transparent;
      border: none;
      border-radius: 30px;
      padding: 2rem;
      position: relative;
      overflow: hidden;
    }

    .hero-card-icon {
      width: 80px;
      height: 80px;
      background: var(--gradient-1);
      border-radius: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      color: #fff;
      margin-bottom: 1.5rem;
      animation: float 3s ease-in-out infinite;
    }

    .hero-card h3 {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 1.5rem;
      margin-bottom: 0.5rem;
      color: #f9fafb;
    }

    .hero-card p {
      color: rgba(241, 245, 249, 0.88);
      font-size: 0.95rem;
      margin-bottom: 1.5rem;
    }

    .hero-stats {
      display: flex;
      gap: 2rem;
      padding-top: 1.5rem;
      border-top: none;
    }

    .stat {
      text-align: center;
    }

    .stat-value {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 2rem;
      font-weight: 700;
      color: #e0f2fe;
    }

    .stat-label {
      font-size: 0.85rem;
      color: rgba(241, 245, 249, 0.78);
    }

    /* Section Styles */
    section {
      padding: 6rem 5%;
    }

    .section-header {
      text-align: center;
      max-width: 600px;
      margin: 0 auto 4rem;
    }

    .section-label {
      display: inline-block;
      font-size: 0.85rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 2px;
      color: var(--primary);
      margin-bottom: 1rem;
    }

    .section-title {
      font-family: 'Space Grotesk', sans-serif;
      font-size: clamp(2rem, 4vw, 3rem);
      font-weight: 700;
      margin-bottom: 1rem;
    }

    .section-subtitle {
      color: var(--text-muted);
      font-size: 1.1rem;
    }

    /* Services Section */
    .services {
      background: radial-gradient(circle at top, rgba(191, 219, 254, 0.7), transparent 55%),
        linear-gradient(180deg, transparent 0%, rgba(191, 219, 254, 0.6) 100%);
    }

    .services-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 2.5rem;
      max-width: 1600px;
      margin: 0 auto;
      align-items: stretch;
      perspective: 1200px;
    }

    .service-card {
      background: radial-gradient(circle at top left, rgba(248, 250, 252, 0.85), rgba(241, 245, 249, 0.95));
      border-radius: 26px;
      padding: 1.75rem 2.4rem 2.4rem;
      position: relative;
      overflow: hidden;
      box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
      transform-origin: center bottom;
      transition: transform 0.45s cubic-bezier(0.23, 1, 0.32, 1),
        box-shadow 0.35s ease,
        filter 0.35s ease;
    }

    .service-card::before {
      content: '';
      position: absolute;
      inset: -1px;
      border-radius: inherit;
      background: conic-gradient(from 180deg, rgba(14, 165, 233, 0.7), rgba(37, 99, 235, 0.5), rgba(14, 165, 233, 0.7));
      opacity: 0;
      transition: opacity 0.4s ease;
      z-index: 0;
    }

    .service-card::after {
      content: '';
      position: absolute;
      inset: 1px;
      border-radius: 24px;
      background: rgba(255, 255, 255, 0.96);
      z-index: 0;
    }

    .service-card:hover {
      transform: translateY(-12px) scale(1.05) rotate3d(1, 1, 0, 6deg);
      box-shadow: 0 30px 80px rgba(15, 23, 42, 0.22);
      filter: brightness(1.02);
    }

    .service-card:hover::before {
      opacity: 0.8;
      animation: serviceGlow 6s linear infinite;
    }

    @@keyframes serviceGlow {
      0% {
        transform: rotate(0deg);
      }
      100% {
        transform: rotate(360deg);
      }
    }

    .service-icon {
      width: fit-content;
      height: auto;
      background: none;
      border-radius: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 5.5rem;
      line-height: 1;
      color: var(--primary);
      margin: 0 auto 0.75rem;
      position: relative;
      z-index: 1;
      transition: transform 0.4s ease, color 0.4s ease;
    }

    .service-icon i {
      display: block;
      line-height: 1;
    }

    .service-icon-img {
      width: 96px;
      height: 96px;
      object-fit: contain;
      display: block;
      transition: transform 0.4s ease;
    }

    .service-card:hover .service-icon-img {
      transform: scale(1.1);
    }

    .service-card:hover .service-icon {
      background: none;
      color: var(--primary);
      transform: translateY(-4px) scale(1.06);
      box-shadow: none;
    }

    .service-card h3 {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 1.45rem;
      margin-bottom: 0.75rem;
      position: relative;
      z-index: 1;
      color: var(--dark);
      text-align: center;
      transition: transform 0.35s ease, color 0.35s ease;
    }

    .service-card h3::after {
      content: '';
      position: absolute;
      left: 50%;
      bottom: -0.55rem;
      width: 0;
      height: 3px;
      border-radius: 999px;
      background: var(--gradient-1);
      transform: translateX(-50%);
      transition: width 0.35s ease;
    }

    .service-card p {
      color: var(--text-muted);
      font-size: 0.96rem;
      position: relative;
      z-index: 1;
      text-align: center;
      transition: transform 0.35s ease, color 0.35s ease;
    }

    .service-card:hover h3 {
      transform: translateY(-2px);
      color: var(--dark);
    }

    .service-card:hover h3::after {
      width: 52px;
    }

    .service-card:hover p {
      transform: translateY(1px);
      color: var(--text-muted);
    }

    /* Process Section */
    .process-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 2rem;
      max-width: 1200px;
      margin: 0 auto;
      position: relative;
    }

    .process-step {
      text-align: center;
      position: relative;
    }

    .process-step:not(:nth-child(4n)):not(:last-child)::after {
      content: '';
      position: absolute;
      top: 40px;
      left: calc(50% + 40px);
      width: calc(100% + 2rem - 80px);
      height: 2px;
      background: linear-gradient(90deg, var(--primary), var(--secondary), var(--accent));
      opacity: 0.3;
      z-index: 0;
      pointer-events: none;
    }

    .process-number {
      width: 80px;
      height: 80px;
      background: var(--bg-card);
      border: 2px solid var(--primary);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Space Grotesk', sans-serif;
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--primary);
      margin: 0 auto 1.5rem;
      position: relative;
      z-index: 1;
      transition: all 0.4s ease;
    }

    .process-step:hover .process-number {
      background: var(--gradient-1);
      color: #fff;
      transform: scale(1.15);
      box-shadow: 0 0 40px rgba(0, 168, 204, 0.45);
    }

    .process-step h3 {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 1.2rem;
      margin-bottom: 0.5rem;
    }

    .process-step p {
      color: var(--text-muted);
      font-size: 0.9rem;
    }

    /* Features Section */
    .features {
      background: var(--bg-card);
    }

    .features-container {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 4rem;
      max-width: 1200px;
      margin: 0 auto;
      align-items: center;
    }

    .features-content h2 {
      font-family: 'Space Grotesk', sans-serif;
      font-size: clamp(2rem, 4vw, 2.8rem);
      font-weight: 700;
      margin-bottom: 1.5rem;
    }

    .features-content h2 span {
      color: #0284c7;
    }

    .features-list {
      list-style: none;
      margin-top: 2rem;
    }

    .features-list li {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 1rem 0;
      border-bottom: 1px solid var(--border);
    }

    .features-list li i {
      width: 40px;
      height: 40px;
      background: rgba(0, 168, 204, 0.12);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--primary);
    }

    .features-visual {
      position: relative;
    }

    .features-card {
      background: var(--primary);
      border: 1px solid var(--border);
      border-radius: 30px;
      padding: 3rem;
      text-align: center;
    }

    .features-card-icon {
      width: 120px;
      height: 120px;
      background: var(--gradient-3);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 3rem;
      color: #fff;
      margin: 0 auto 2rem;
      animation: glow 3s ease-in-out infinite;
    }

    @@keyframes glow {
      0%, 100% { box-shadow: 0 0 30px var(--primary-glow); }
      50% { box-shadow: 0 0 60px rgba(0, 168, 204, 0.5); }
    }

    .features-card h3 {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 1.8rem;
      margin-bottom: 0.5rem;
      color: #fff;
    }

    .features-card p {
      color: rgba(255, 255, 255, 0.9);
    }

    /* Partners Section */
    .partners {
      position: relative;
      padding: 6.5rem 5% 7rem;
      background:
        radial-gradient(ellipse 80% 55% at 50% 0%, rgba(186, 230, 253, 0.45) 0%, transparent 70%),
        linear-gradient(180deg, #f8fafc 0%, #ffffff 48%, #f1f5f9 100%);
      overflow: hidden;
    }

    .partners__container {
      position: relative;
      z-index: 1;
      max-width: 1240px;
      margin: 0 auto;
    }

    .partners__bg {
      position: absolute;
      inset: 0;
      pointer-events: none;
      overflow: hidden;
    }

    .partners__mesh {
      position: absolute;
      inset: 0;
      opacity: 0.35;
      background-image:
        linear-gradient(rgba(148, 163, 184, 0.08) 1px, transparent 1px),
        linear-gradient(90deg, rgba(148, 163, 184, 0.08) 1px, transparent 1px);
      background-size: 48px 48px;
      mask-image: radial-gradient(ellipse 70% 60% at 50% 40%, #000 20%, transparent 75%);
      -webkit-mask-image: radial-gradient(ellipse 70% 60% at 50% 40%, #000 20%, transparent 75%);
    }

    .partners__orb {
      position: absolute;
      border-radius: 50%;
      filter: blur(70px);
      opacity: 0.55;
    }

    .partners__orb--1 {
      width: 420px;
      height: 420px;
      top: -120px;
      left: -100px;
      background: rgba(14, 165, 233, 0.28);
    }

    .partners__orb--2 {
      width: 360px;
      height: 360px;
      right: -80px;
      top: 20%;
      background: rgba(37, 99, 235, 0.22);
    }

    .partners__orb--3 {
      width: 280px;
      height: 280px;
      left: 35%;
      bottom: -100px;
      background: rgba(56, 189, 248, 0.2);
    }

    .partners-showcase__head {
      text-align: center;
      max-width: 720px;
      margin: 0 auto 3rem;
    }

    .partners-showcase__badge {
      display: inline-flex;
      align-items: center;
      gap: 0.55rem;
      padding: 0.5rem 1rem;
      margin-bottom: 1.25rem;
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.85);
      border: 1px solid rgba(14, 165, 233, 0.22);
      box-shadow: 0 8px 24px rgba(14, 165, 233, 0.1);
      font-size: 0.78rem;
      font-weight: 700;
      letter-spacing: 0.14em;
      text-transform: uppercase;
      color: #0369a1;
    }

    .partners-showcase__badge i {
      font-size: 0.9rem;
      color: #0ea5e9;
    }

    .partners-showcase__title {
      font-family: 'Space Grotesk', sans-serif;
      font-size: clamp(2.1rem, 4.5vw, 3.2rem);
      font-weight: 700;
      line-height: 1.12;
      letter-spacing: -0.03em;
      color: #0f172a;
      margin-bottom: 1rem;
    }

    .partners-showcase__subtitle {
      color: #64748b;
      font-size: clamp(1rem, 2vw, 1.12rem);
      line-height: 1.7;
      max-width: 620px;
      margin: 0 auto;
    }

    .partners-showcase__meta {
      margin-top: 1.35rem;
    }

    .partners-showcase__pill {
      display: inline-flex;
      align-items: center;
      padding: 0.45rem 0.95rem;
      border-radius: 999px;
      background: linear-gradient(135deg, rgba(14, 165, 233, 0.12) 0%, rgba(37, 99, 235, 0.08) 100%);
      border: 1px solid rgba(14, 165, 233, 0.18);
      font-size: 0.82rem;
      font-weight: 600;
      color: #0369a1;
    }

    .partners-showcase {
      position: relative;
      padding: 2rem 1.5rem 1.35rem;
      border-radius: 28px;
      background:
        linear-gradient(145deg, rgba(255, 255, 255, 0.94) 0%, rgba(248, 250, 252, 0.88) 100%);
      border: 1px solid rgba(148, 163, 184, 0.22);
      box-shadow:
        0 28px 64px rgba(15, 23, 42, 0.08),
        0 8px 24px rgba(14, 165, 233, 0.06),
        inset 0 1px 0 rgba(255, 255, 255, 0.95);
      overflow: hidden;
    }

    .partners-showcase__rim {
      position: absolute;
      inset: 0;
      border-radius: inherit;
      padding: 1px;
      background: linear-gradient(135deg, rgba(14, 165, 233, 0.35), rgba(37, 99, 235, 0.08), rgba(14, 165, 233, 0.2));
      -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
      -webkit-mask-composite: xor;
      mask-composite: exclude;
      pointer-events: none;
    }

    .partners-showcase__glow {
      position: absolute;
      top: -40%;
      left: 50%;
      width: 70%;
      height: 80%;
      transform: translateX(-50%);
      background: radial-gradient(ellipse at center, rgba(14, 165, 233, 0.14) 0%, transparent 68%);
      pointer-events: none;
    }

    .partners-showcase__hint {
      position: relative;
      z-index: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      margin: 1.25rem 0 0;
      font-size: 0.8rem;
      font-weight: 500;
      color: #94a3b8;
      letter-spacing: 0.02em;
    }

    .partners-showcase__hint i {
      font-size: 0.75rem;
      color: #0ea5e9;
    }

    .partners-carousel {
      position: relative;
      z-index: 1;
    }

    .partners-carousel__viewport {
      overflow: hidden;
      padding: 0.35rem 0;
      mask-image: linear-gradient(90deg, transparent 0%, #000 10%, #000 90%, transparent 100%);
      -webkit-mask-image: linear-gradient(90deg, transparent 0%, #000 10%, #000 90%, transparent 100%);
    }

    .partners-carousel__fade {
      position: absolute;
      top: 0;
      bottom: 0;
      width: min(100px, 10vw);
      z-index: 2;
      pointer-events: none;
    }

    .partners-carousel__fade--left {
      left: 0;
      background: linear-gradient(90deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 255, 255, 0) 100%);
    }

    .partners-carousel__fade--right {
      right: 0;
      background: linear-gradient(270deg, rgba(248, 250, 252, 0.98) 0%, rgba(248, 250, 252, 0) 100%);
    }

    .partners-carousel__track {
      display: flex;
      align-items: stretch;
      gap: 1.5rem;
      width: max-content;
      animation: partnersMarquee 46s linear infinite;
      will-change: transform;
    }

    .partners-showcase:hover .partners-carousel__track,
    .partners-showcase:focus-within .partners-carousel__track {
      animation-play-state: paused;
    }

    .partners-carousel__slide {
      flex: 0 0 auto;
    }

    .partners-card {
      position: relative;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 0.75rem;
      width: clamp(196px, 23vw, 260px);
      min-height: clamp(128px, 16vw, 156px);
      padding: 1.5rem 1.35rem 1.15rem;
      border-radius: 22px;
      background:
        linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
      border: 1px solid rgba(148, 163, 184, 0.2);
      box-shadow:
        0 14px 34px rgba(15, 23, 42, 0.07),
        inset 0 1px 0 rgba(255, 255, 255, 0.95);
      text-decoration: none;
      overflow: hidden;
      isolation: isolate;
      transition:
        transform 0.42s cubic-bezier(0.22, 1, 0.36, 1),
        box-shadow 0.42s ease,
        border-color 0.42s ease;
    }

    .partners-card__shine {
      position: absolute;
      inset: -40% auto -40% -60%;
      width: 45%;
      background: linear-gradient(105deg, transparent 0%, rgba(255, 255, 255, 0.75) 48%, transparent 100%);
      transform: translateX(-120%) skewX(-18deg);
      transition: transform 0.7s ease;
      pointer-events: none;
      z-index: 2;
    }

    .partners-card:hover {
      transform: translateY(-6px);
      border-color: rgba(14, 165, 233, 0.42);
      box-shadow:
        0 24px 48px rgba(14, 165, 233, 0.16),
        0 0 0 1px rgba(14, 165, 233, 0.08),
        inset 0 1px 0 rgba(255, 255, 255, 1);
    }

    .partners-card:hover .partners-card__shine {
      transform: translateX(280%) skewX(-18deg);
    }

    .partners-card__inner {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      flex: 1;
      min-height: 72px;
      overflow: hidden;
    }

    .partners-card__logo {
      max-width: 100%;
      max-height: 76px;
      width: auto;
      height: auto;
      object-fit: contain;
      transition: transform 0.42s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .partners-card:hover .partners-card__logo {
      transform: scale(1.16);
    }

    .partners-card__name {
      font-size: 0.72rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: #64748b;
      text-align: center;
      line-height: 1.3;
      transition: color 0.35s ease;
    }

    .partners-card:hover .partners-card__name {
      color: #0369a1;
    }

    @@keyframes partnersMarquee {
      from { transform: translateX(0); }
      to { transform: translateX(-50%); }
    }

    @@media (prefers-reduced-motion: reduce) {
      .partners-carousel__track {
        animation: none;
        flex-wrap: wrap;
        justify-content: center;
        width: 100%;
        gap: 1rem;
      }

      .partners-carousel__viewport {
        overflow: visible;
        mask-image: none;
        -webkit-mask-image: none;
      }

      .partners-carousel__fade {
        display: none;
      }

      .partners-card__shine {
        display: none;
      }
    }

    /* Pages internes (Services, Process, Academy) */
    .inner-page,
    .academy-page {
      min-height: 100vh;
    }

    /* Hero pages internes — offset = hauteur réelle du header fixe + marge sous la navbar */
    .inner-hero,
    .about-hero,
    .laboratory-hero,
    .academy-hero,
    .faq-hero {
      position: relative;
      min-height: 48vh;
      display: flex;
      align-items: flex-start;
      padding: calc(var(--site-header-offset) + var(--inner-hero-gap)) 5% 4rem;
      overflow: hidden;
    }

    .inner-hero-bg {
      position: absolute;
      inset: 0;
      background:
        radial-gradient(circle at 15% 20%, rgba(14, 165, 233, 0.28) 0%, transparent 42%),
        radial-gradient(circle at 85% 30%, rgba(37, 99, 235, 0.22) 0%, transparent 40%),
        linear-gradient(135deg, rgba(15, 23, 42, 0.96) 0%, rgba(15, 23, 42, 0.88) 50%, rgba(15, 23, 42, 0.97) 100%),
        url('https://images.unsplash.com/photo-1629909613654-28e377c37b09?w=1920&q=80') center/cover no-repeat;
      z-index: 0;
    }

    .inner-hero-content {
      position: relative;
      z-index: 1;
      max-width: 760px;
    }

    .inner-hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.55rem;
      padding: 0.5rem 1rem;
      background: rgba(0, 168, 204, 0.14);
      border: 1px solid rgba(0, 168, 204, 0.28);
      border-radius: 999px;
      color: #e0f2fe;
      font-size: 0.85rem;
      font-weight: 600;
      margin-bottom: 1.35rem;
    }

    .inner-hero h1 {
      font-family: 'Space Grotesk', sans-serif;
      font-size: clamp(2.1rem, 5vw, 3.4rem);
      font-weight: 700;
      line-height: 1.1;
      color: #f9fafb;
      margin-bottom: 1rem;
    }

    .inner-hero p {
      font-size: clamp(1rem, 2vw, 1.15rem);
      color: rgba(241, 245, 249, 0.88);
      max-width: 560px;
      line-height: 1.7;
    }

    .inner-body {
      position: relative;
      z-index: 1;
      padding: 0 5% 5rem;
    }

    .services--page .services-grid {
      max-width: 1180px;
      margin: 0 auto;
    }

    /* Page Services — honeycomb hexagonal */
    .inner-page--services {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    .inner-body.services--page {
      flex: 1 1 auto;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      width: 100%;
      min-height: min-content;
      padding: 3rem 5% 5rem;
    }

    .inner-body.services--page .inner-empty {
      flex-shrink: 0;
      margin: 0;
    }

    .services-honeycomb {
      --hex-size: 300px;
      --hex-overlap: calc(var(--hex-size) * 0.22);
      --hex-gap-x: 2rem;
      position: relative;
      width: 100%;
      max-width: 1180px;
      margin: 0 auto;
      padding: 1.5rem 0 2.5rem;
    }

    .services-honeycomb__grid {
      position: relative;
      z-index: 1;
      display: grid;
      grid-template-columns: var(--hex-size) var(--hex-size) var(--hex-size);
      grid-template-rows: repeat(4, auto);
      column-gap: var(--hex-gap-x);
      row-gap: 1rem;
      justify-content: center;
      align-items: center;
      width: fit-content;
      margin: 0 auto;
      padding: 4rem 3.5rem 4.5rem;
      overflow: hidden;
      isolation: isolate;
      border-radius: 12px;
      box-shadow:
        0 28px 64px rgba(15, 58, 92, 0.22),
        0 12px 32px rgba(2, 132, 199, 0.14);
    }

    .services-honeycomb__bg {
      position: absolute;
      inset: 0;
      pointer-events: none;
      z-index: 0;
      overflow: hidden;
    }

    .services-honeycomb__bg-base {
      position: absolute;
      inset: 0;
      background:
        radial-gradient(ellipse 88% 92% at 50% 44%, rgba(126, 186, 224, 0.92) 0%, rgba(93, 158, 198, 0.88) 26%, rgba(68, 133, 175, 0.9) 52%, rgba(52, 112, 155, 0.94) 76%, rgba(42, 95, 138, 0.97) 100%),
        linear-gradient(155deg, #8ec5e4 0%, #6aaccf 38%, #4f91b8 100%);
    }

    .services-honeycomb__bg-hex {
      position: absolute;
      clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
      border: 1px solid rgba(186, 230, 253, 0.22);
      background: linear-gradient(145deg, rgba(186, 230, 253, 0.14) 0%, rgba(56, 189, 248, 0.06) 100%);
      pointer-events: none;
    }

    .services-honeycomb__bg-hex--1 {
      width: 340px;
      height: 390px;
      top: -12%;
      left: -14%;
      opacity: 0.55;
      transform: rotate(-8deg);
    }

    .services-honeycomb__bg-hex--2 {
      width: 260px;
      height: 300px;
      top: 8%;
      right: -10%;
      opacity: 0.45;
      transform: rotate(6deg);
    }

    .services-honeycomb__bg-hex--3 {
      width: 420px;
      height: 480px;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      opacity: 0.3;
      border-color: rgba(186, 230, 253, 0.28);
      background: linear-gradient(160deg, rgba(186, 230, 253, 0.18) 0%, rgba(56, 189, 248, 0.08) 100%);
    }

    .services-honeycomb__bg-hex--4 {
      width: 200px;
      height: 230px;
      bottom: -6%;
      left: 6%;
      opacity: 0.4;
      transform: rotate(12deg);
    }

    .services-honeycomb__bg-hex--5 {
      width: 180px;
      height: 208px;
      bottom: 4%;
      right: 8%;
      opacity: 0.36;
      transform: rotate(-10deg);
    }

    .services-honeycomb__bg-hex--6 {
      width: 140px;
      height: 162px;
      top: 22%;
      left: 18%;
      opacity: 0.32;
    }

    .services-honeycomb__center-glow {
      position: absolute;
      top: 50%;
      left: 50%;
      width: min(420px, 58%);
      aspect-ratio: 0.866 / 1;
      transform: translate(-50%, -50%);
      display: flex;
      align-items: center;
      justify-content: center;
      pointer-events: none;
      z-index: 1;
      clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
      filter: drop-shadow(0 0 36px rgba(56, 189, 248, 0.45));
    }

    .services-honeycomb__center-label {
      position: relative;
      z-index: 3;
      margin: 0;
      padding: 0 10%;
      max-width: 92%;
      text-align: center;
      font-family: 'Space Grotesk', sans-serif;
      font-size: clamp(0.92rem, 2.2vw, 1.35rem);
      font-weight: 800;
      letter-spacing: 0.12em;
      line-height: 1.2;
      color: #0c4a6e;
      text-transform: uppercase;
      text-shadow: 0 1px 0 rgba(255, 255, 255, 0.75);
    }

    .services-honeycomb__center-glow-bloom {
      position: absolute;
      inset: 0;
      clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
      background: radial-gradient(
        ellipse 80% 75% at 50% 42%,
        rgba(186, 230, 253, 0.92) 0%,
        rgba(125, 211, 252, 0.65) 32%,
        rgba(56, 189, 248, 0.38) 58%,
        rgba(14, 165, 233, 0.12) 78%,
        transparent 95%
      );
      filter: blur(4px);
      animation: honeycombCenterShine 7s ease-in-out infinite;
    }

    .services-honeycomb__center-glow-core {
      position: absolute;
      inset: 8%;
      clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
      background: radial-gradient(
        ellipse 70% 65% at 50% 40%,
        rgba(224, 242, 254, 0.95) 0%,
        rgba(125, 211, 252, 0.72) 42%,
        rgba(56, 189, 248, 0.28) 72%,
        transparent 92%
      );
      border: 1px solid rgba(186, 230, 253, 0.55);
      box-shadow:
        inset 0 0 28px rgba(186, 230, 253, 0.55),
        0 0 40px rgba(56, 189, 248, 0.38);
      animation: honeycombCenterCore 5s ease-in-out infinite;
    }

    @@keyframes honeycombCenterShine {
      0%, 100% {
        opacity: 0.78;
        transform: scale(0.94);
      }
      50% {
        opacity: 1;
        transform: scale(1.03);
      }
    }

    @@keyframes honeycombCenterCore {
      0%, 100% {
        opacity: 0.82;
        filter: brightness(0.95);
      }
      50% {
        opacity: 1;
        filter: brightness(1.08);
      }
    }

    .services-hex-item {
      width: var(--hex-size);
      position: relative;
      justify-self: center;
      z-index: 2;
    }

    .services-hex-item[data-pos="top"] {
      grid-column: 2;
      grid-row: 1;
    }

    .services-hex-item[data-pos="left-upper"] {
      grid-column: 1;
      grid-row: 2;
      margin-top: calc(var(--hex-overlap) * -1);
    }

    .services-hex-item[data-pos="right-upper"] {
      grid-column: 3;
      grid-row: 2;
      margin-top: calc(var(--hex-overlap) * -1);
    }

    .services-hex-item[data-pos="left-lower"] {
      grid-column: 1;
      grid-row: 3;
      margin-top: calc(var(--hex-overlap) * -1);
    }

    .services-hex-item[data-pos="right-lower"] {
      grid-column: 3;
      grid-row: 3;
      margin-top: calc(var(--hex-overlap) * -1);
    }

    .services-hex-item[data-pos="bottom"] {
      grid-column: 2;
      grid-row: 4;
      margin-top: calc(var(--hex-overlap) * -1);
    }

    .services-hex-item.reveal,
    .services-hex-item.reveal.active {
      opacity: 1;
      transform: none;
    }

    .services-hex-link {
      display: block;
      text-decoration: none;
      color: inherit;
      outline: none;
      transition: transform 0.5s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .services-hex-link:focus-visible .services-hex-shell-wrap {
      outline: 3px solid rgba(56, 189, 248, 0.85);
      outline-offset: 4px;
    }

    .services-hex-shell-wrap {
      position: relative;
      width: var(--hex-size);
      aspect-ratio: 0.866 / 1;
      filter: drop-shadow(0 0 0 2px rgba(255, 255, 255, 0.95));
      transition:
        filter 0.5s ease,
        transform 0.5s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .services-hex-shell-wrap:has(.services-hex-shell.has-image) {
      filter: none;
    }

    .services-hex-shell {
      position: relative;
      width: 100%;
      height: 100%;
      clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
      overflow: hidden;
      background-color: #0c4a6e;
      background-position: center;
      background-size: cover;
      background-repeat: no-repeat;
      transition: background-size 0.6s cubic-bezier(0.22, 1, 0.36, 1), filter 0.5s ease;
    }

    .services-hex-shell.has-image {
      filter: brightness(1.08) saturate(1.05);
    }

    .services-hex-shell.is-placeholder {
      background-image: linear-gradient(135deg, #0369a1 0%, #0c4a6e 100%);
    }

    .services-hex-placeholder {
      position: absolute;
      inset: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 3.9rem;
      color: rgba(255, 255, 255, 0.35);
      pointer-events: none;
      transition: transform 0.5s cubic-bezier(0.22, 1, 0.36, 1), color 0.4s ease;
    }

    .services-hex-overlay {
      position: absolute;
      inset: 0;
      background: linear-gradient(
        180deg,
        rgba(0, 18, 32, 0.28) 0%,
        rgba(0, 18, 32, 0.52) 42%,
        rgba(0, 18, 32, 0.52) 58%,
        rgba(0, 18, 32, 0.28) 100%
      );
      pointer-events: none;
      z-index: 2;
      transition: background 0.5s ease, opacity 0.5s ease;
    }

    .services-hex-shell.has-image .services-hex-overlay {
      background: linear-gradient(
        180deg,
        rgba(0, 18, 32, 0.06) 0%,
        rgba(0, 18, 32, 0.16) 42%,
        rgba(0, 18, 32, 0.16) 58%,
        rgba(0, 18, 32, 0.06) 100%
      );
    }

    .services-hex-label {
      position: absolute;
      top: 50%;
      left: 50%;
      bottom: auto;
      transform: translate(-50%, -50%);
      width: 58%;
      max-width: 58%;
      text-align: center;
      font-family: 'Space Grotesk', sans-serif;
      font-size: clamp(1rem, 1.8vw, 1.25rem);
      font-weight: 700;
      letter-spacing: 0.03em;
      line-height: 1.22;
      color: #fff;
      text-shadow:
        0 1px 3px rgba(0, 0, 0, 0.95),
        0 3px 14px rgba(0, 0, 0, 0.75),
        0 0 24px rgba(0, 18, 32, 0.55);
      margin: 0;
      padding: 0;
      z-index: 5;
      overflow-wrap: break-word;
      word-break: normal;
      hyphens: none;
      text-wrap: balance;
      transition:
        transform 0.5s cubic-bezier(0.22, 1, 0.36, 1),
        color 0.4s ease,
        letter-spacing 0.4s ease,
        text-shadow 0.4s ease;
    }

    .services-hex-label.is-compact {
      width: 54%;
      max-width: 54%;
      font-size: clamp(0.82rem, 1.5vw, 1.04rem);
      line-height: 1.18;
      letter-spacing: 0.02em;
    }

    /* Page détail service */
    .service-detail-page {
      min-height: 100vh;
    }

    .service-detail-page .inner-hero-content {
      max-width: min(960px, 100%);
      width: 100%;
    }

    .service-detail-page .inner-hero h1 {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 100%;
      font-size: clamp(1.4rem, 3.6vw, 3.2rem);
    }

    .service-detail-page .inner-hero.inner-hero--has-summary {
      padding-top: calc(var(--site-header-offset) + var(--inner-hero-gap) + 1.5rem);
      min-height: 54vh;
    }

    .service-detail-back {
      display: inline-flex;
      align-items: center;
      gap: 0.55rem;
      margin-top: 1.25rem;
      color: rgba(226, 232, 240, 0.9);
      text-decoration: none;
      font-size: 0.88rem;
      font-weight: 600;
      transition: color 0.25s ease;
    }

    .service-detail-back:hover {
      color: #fff;
    }

    .service-detail-body {
      position: relative;
      z-index: 1;
      padding: 0 5% 5rem;
      margin-top: -1.5rem;
    }

    .service-detail-container {
      max-width: 860px;
      margin: 0 auto;
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 28px;
      box-shadow: 0 24px 56px rgba(15, 23, 42, 0.1);
      overflow: hidden;
    }

    .service-detail-figure {
      margin: 0;
      aspect-ratio: 16 / 9;
      background: #e2e8f0;
    }

    .service-detail-figure img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .service-detail-content {
      padding: 2rem 2.25rem 2.5rem;
    }

    .prose-vitrine {
      color: var(--text);
      line-height: 1.75;
    }

    .prose-vitrine h2,
    .prose-vitrine h3,
    .prose-vitrine h4 {
      font-family: 'Space Grotesk', sans-serif;
      color: var(--dark);
      margin: 1.75rem 0 0.75rem;
      line-height: 1.25;
    }

    .prose-vitrine h2 { font-size: 1.55rem; }
    .prose-vitrine h3 { font-size: 1.25rem; }

    .prose-vitrine p {
      margin-bottom: 1rem;
      color: var(--text-muted);
    }

    .prose-vitrine ul,
    .prose-vitrine ol {
      margin: 0 0 1rem 1.25rem;
      color: var(--text-muted);
    }

    .prose-vitrine li {
      margin-bottom: 0.35rem;
    }

    .prose-vitrine a {
      color: var(--primary);
      text-decoration: underline;
      text-underline-offset: 2px;
    }

    .prose-vitrine strong {
      color: var(--dark);
    }

    /* Page Process — timeline moderne */
    .process--page {
      padding-top: 1.5rem;
    }

    /* Page Galerie — design premium */
    .gallery-page-hero__bg {
      background:
        radial-gradient(circle at 18% 22%, rgba(14, 165, 233, 0.32) 0%, transparent 44%),
        radial-gradient(circle at 82% 28%, rgba(37, 99, 235, 0.24) 0%, transparent 42%),
        linear-gradient(135deg, rgba(15, 23, 42, 0.97) 0%, rgba(15, 23, 42, 0.9) 50%, rgba(15, 23, 42, 0.98) 100%),
        url('https://images.unsplash.com/photo-1606811971610-bc919627a65?w=1920&q=80') center/cover no-repeat;
    }

    .gallery-page-meta {
      display: inline-flex;
      align-items: baseline;
      gap: 0.45rem;
      margin-top: 1.35rem;
      padding: 0.55rem 1rem;
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.12);
      color: rgba(241, 245, 249, 0.92);
      font-size: 0.9rem;
    }

    .gallery-page-meta strong {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 1.15rem;
      color: #fff;
    }

    .gallery--page {
      padding-top: 2rem;
      padding-bottom: 6rem;
    }

    .gallery-showcase {
      max-width: 1080px;
      margin: 0 auto;
    }

    .gallery-showcase__head {
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      gap: 1.25rem;
      margin-bottom: 2rem;
      padding-bottom: 1.25rem;
      border-bottom: 1px solid rgba(148, 163, 184, 0.22);
    }

    .gallery-showcase__head-left {
      display: flex;
      flex-direction: column;
      gap: 0.45rem;
    }

    .gallery-showcase__pill {
      display: inline-flex;
      align-items: center;
      width: fit-content;
      padding: 0.3rem 0.7rem;
      border-radius: 999px;
      background: rgba(14, 165, 233, 0.1);
      border: 1px solid rgba(14, 165, 233, 0.2);
      color: var(--primary);
      font-size: 0.72rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .gallery-showcase__title {
      font-family: 'Space Grotesk', sans-serif;
      font-size: clamp(1.25rem, 2.2vw, 1.55rem);
      font-weight: 700;
      color: var(--dark);
      margin: 0;
      line-height: 1.2;
    }

    .gallery-showcase__hint {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      margin: 0;
      color: var(--text-muted);
      font-size: 0.86rem;
      line-height: 1.5;
      max-width: 240px;
      text-align: right;
    }

    .gallery-showcase__hint i {
      color: var(--primary);
      font-size: 0.8rem;
      flex-shrink: 0;
    }

    .gallery-page .gallery-grid-pro {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 1.15rem;
    }

    .gallery-page .gallery-tile.reveal {
      opacity: 0;
      transform: translateY(22px);
    }

    .gallery-page .gallery-tile.reveal.active {
      opacity: 1;
      transform: translateY(0);
      transition:
        opacity 0.55s cubic-bezier(0.22, 1, 0.36, 1),
        transform 0.55s cubic-bezier(0.22, 1, 0.36, 1);
      transition-delay: var(--gallery-delay, 0s);
    }

    .gallery-page .gallery-tile__btn {
      display: flex;
      flex-direction: column;
      gap: 0.7rem;
      width: 100%;
      padding: 0;
      border: none;
      background: transparent;
      cursor: zoom-in;
      text-align: left;
    }

    .gallery-page .gallery-tile__btn:focus-visible {
      outline: none;
    }

    .gallery-page .gallery-tile__btn:focus-visible .gallery-tile__visual {
      outline: 3px solid rgba(14, 165, 233, 0.45);
      outline-offset: 3px;
    }

    .gallery-page .gallery-tile__visual {
      position: relative;
      width: 100%;
      aspect-ratio: 5 / 4;
      max-height: 188px;
      border-radius: 16px;
      overflow: hidden;
      background: #eef2f7;
      border: 1px solid rgba(148, 163, 184, 0.22);
      box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
      transition:
        transform 0.38s cubic-bezier(0.22, 1, 0.36, 1),
        box-shadow 0.38s ease,
        border-color 0.38s ease;
    }

    .gallery-page .gallery-tile__btn:hover .gallery-tile__visual,
    .gallery-page .gallery-tile__btn:focus-visible .gallery-tile__visual {
      transform: translateY(-4px);
      border-color: rgba(14, 165, 233, 0.35);
      box-shadow:
        0 14px 32px rgba(14, 165, 233, 0.1),
        0 20px 40px rgba(15, 23, 42, 0.08);
    }

    .gallery-page .gallery-tile__visual img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
      transition: transform 0.55s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .gallery-page .gallery-tile__btn:hover .gallery-tile__visual img {
      transform: scale(1.06);
    }

    .gallery-page .gallery-tile__overlay {
      position: absolute;
      inset: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(180deg, rgba(15, 23, 42, 0.08) 0%, rgba(15, 23, 42, 0.55) 100%);
      opacity: 0;
      transition: opacity 0.32s ease;
    }

    .gallery-page .gallery-tile__btn:hover .gallery-tile__overlay,
    .gallery-page .gallery-tile__btn:focus-visible .gallery-tile__overlay {
      opacity: 1;
    }

    .gallery-page .gallery-tile__expand {
      width: 40px;
      height: 40px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(255, 255, 255, 0.94);
      color: var(--primary);
      font-size: 0.85rem;
      box-shadow: 0 10px 24px rgba(15, 23, 42, 0.18);
      transform: scale(0.88);
      transition: transform 0.32s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .gallery-page .gallery-tile__btn:hover .gallery-tile__expand {
      transform: scale(1);
    }

    .gallery-page .gallery-tile__meta {
      display: flex;
      align-items: flex-start;
      gap: 0.65rem;
      padding: 0 0.15rem;
    }

    .gallery-page .gallery-tile__index {
      flex-shrink: 0;
      font-family: 'Space Grotesk', sans-serif;
      font-size: 0.68rem;
      font-weight: 700;
      letter-spacing: 0.1em;
      color: rgba(14, 165, 233, 0.85);
      margin-top: 0.15rem;
    }

    .gallery-page .gallery-tile__copy {
      min-width: 0;
    }

    .gallery-page .gallery-tile__copy h3 {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 0.92rem;
      font-weight: 600;
      color: var(--dark);
      margin: 0;
      line-height: 1.35;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .gallery-page .gallery-tile__copy p {
      margin: 0.2rem 0 0;
      font-size: 0.78rem;
      color: var(--text-muted);
      line-height: 1.4;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    /* Page À propos — layout éditorial + sidebar médias */
    .about-page {
      min-height: 100vh;
      background:
        radial-gradient(circle at 0% 0%, rgba(14, 165, 233, 0.06) 0%, transparent 42%),
        radial-gradient(circle at 100% 12%, rgba(37, 99, 235, 0.05) 0%, transparent 38%),
        linear-gradient(180deg, #f8fafc 0%, #ffffff 42%, #f1f5f9 100%);
    }

    .about-hero__bg {
      position: absolute;
      inset: 0;
      background:
        radial-gradient(circle at 14% 20%, rgba(14, 165, 233, 0.3) 0%, transparent 46%),
        radial-gradient(circle at 86% 18%, rgba(59, 130, 246, 0.22) 0%, transparent 44%),
        linear-gradient(160deg, rgba(15, 23, 42, 0.98) 0%, rgba(15, 23, 42, 0.92) 55%, rgba(15, 23, 42, 0.99) 100%),
        url('https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?w=1920&q=80') center/cover no-repeat;
      z-index: 0;
    }

    .about-hero__mesh {
      position: absolute;
      inset: 0;
      background-image:
        linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
      background-size: 48px 48px;
      mask-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.55), transparent 88%);
      z-index: 0;
      pointer-events: none;
    }

    .about-hero__content {
      position: relative;
      z-index: 1;
      max-width: 760px;
    }

    .about-hero__badge {
      display: inline-flex;
      align-items: center;
      gap: 0.55rem;
      padding: 0.45rem 0.95rem;
      background: rgba(14, 165, 233, 0.14);
      border: 1px solid rgba(125, 211, 252, 0.28);
      border-radius: 999px;
      color: #e0f2fe;
      font-size: 0.8rem;
      font-weight: 600;
      letter-spacing: 0.04em;
      text-transform: uppercase;
      margin-bottom: 1.1rem;
    }

    .about-hero h1 {
      font-family: 'Space Grotesk', sans-serif;
      font-size: clamp(2rem, 4.8vw, 3.25rem);
      font-weight: 700;
      line-height: 1.06;
      color: #f8fafc;
      margin: 0;
      letter-spacing: -0.02em;
    }

    .about-hero__line {
      width: 4.5rem;
      height: 3px;
      margin-top: 1.35rem;
      border-radius: 999px;
      background: linear-gradient(90deg, #38bdf8, #2563eb);
      box-shadow: 0 0 24px rgba(56, 189, 248, 0.45);
    }

    .about-body {
      position: relative;
      padding: 0 5% 5.5rem;
      margin-top: -1.75rem;
    }

    .about-layout {
      display: grid;
      grid-template-columns: minmax(0, 1.35fr) minmax(300px, 0.85fr);
      gap: 1.75rem;
      align-items: start;
      max-width: 1240px;
      margin: 0 auto;
    }

    .about-layout--solo {
      grid-template-columns: minmax(0, 1fr);
      max-width: 820px;
    }

    .about-main__card {
      background: rgba(255, 255, 255, 0.88);
      border: 1px solid rgba(148, 163, 184, 0.2);
      border-radius: 28px;
      padding: 2.25rem 2.35rem 2.5rem;
      box-shadow:
        0 24px 60px rgba(15, 23, 42, 0.07),
        inset 0 1px 0 rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(12px);
    }

    .about-main__head {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 1rem;
      margin-bottom: 1.75rem;
      padding-bottom: 1.25rem;
      border-bottom: 1px solid rgba(148, 163, 184, 0.18);
    }

    .about-main__eyebrow {
      display: inline-flex;
      align-items: center;
      padding: 0.28rem 0.7rem;
      border-radius: 999px;
      background: rgba(14, 165, 233, 0.1);
      border: 1px solid rgba(14, 165, 233, 0.18);
      color: var(--primary);
      font-size: 0.72rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .about-main__meta {
      display: flex;
      flex-wrap: wrap;
      gap: 0.55rem;
      justify-content: flex-end;
    }

    .about-main__meta span {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      padding: 0.35rem 0.7rem;
      border-radius: 999px;
      background: #f8fafc;
      border: 1px solid rgba(148, 163, 184, 0.2);
      color: var(--text-muted);
      font-size: 0.78rem;
      font-weight: 600;
    }

    .about-main__meta i {
      color: var(--primary);
      font-size: 0.72rem;
    }

    .about-main__content {
      position: relative;
      padding-left: 1.35rem;
      border-left: 3px solid rgba(14, 165, 233, 0.35);
    }

    .about-main__content p {
      margin: 0 0 1.15rem;
      font-size: clamp(1.02rem, 1.6vw, 1.12rem);
      line-height: 1.85;
      color: #334155;
    }

    .about-main__content p:last-child {
      margin-bottom: 0;
    }

    .about-main__empty p {
      margin: 0;
      color: var(--text-muted);
      font-size: 1rem;
      line-height: 1.7;
    }

    .about-sidebar {
      position: sticky;
      top: calc(6.5rem + var(--top-bar-height));
    }

    .about-sidebar__card {
      background:
        linear-gradient(165deg, rgba(255, 255, 255, 0.92) 0%, rgba(248, 250, 252, 0.88) 100%);
      border: 1px solid rgba(148, 163, 184, 0.22);
      border-radius: 28px;
      padding: 1.35rem;
      box-shadow:
        0 28px 64px rgba(15, 23, 42, 0.09),
        inset 0 1px 0 rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(18px);
      overflow: hidden;
    }

    .about-sidebar__card::before {
      content: '';
      position: absolute;
      inset: 0 auto auto 0;
      width: 100%;
      height: 3px;
      background: linear-gradient(90deg, #0ea5e9, #3b82f6, #6366f1);
      opacity: 0.85;
    }

    .about-sidebar__card {
      position: relative;
    }

    .about-sidebar__head {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      margin-bottom: 1.15rem;
      padding-top: 0.35rem;
    }

    .about-sidebar__eyebrow {
      display: inline-flex;
      font-size: 0.68rem;
      font-weight: 700;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      color: var(--primary);
      margin-bottom: 0.35rem;
    }

    .about-sidebar__head h2 {
      margin: 0;
      font-family: 'Space Grotesk', sans-serif;
      font-size: 1.2rem;
      font-weight: 700;
      color: var(--dark);
      line-height: 1.2;
    }

    .about-sidebar__tabs {
      display: inline-flex;
      align-self: flex-start;
      padding: 0.28rem;
      border-radius: 999px;
      background: #f1f5f9;
      border: 1px solid rgba(148, 163, 184, 0.2);
      gap: 0.2rem;
    }

    .about-sidebar__tab {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      padding: 0.45rem 0.85rem;
      border: 0;
      border-radius: 999px;
      background: transparent;
      color: var(--text-muted);
      font-size: 0.78rem;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
    }

    .about-sidebar__tab.is-active {
      background: #fff;
      color: var(--dark);
      box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
    }

    .about-sidebar__tab i {
      font-size: 0.72rem;
      color: var(--primary);
    }

    .about-sidebar__mosaic {
      display: grid;
      grid-template-columns: minmax(0, 1fr);
      gap: 0.75rem;
      max-height: min(62vh, 520px);
      overflow: auto;
      padding-right: 0.15rem;
      scrollbar-width: thin;
      scrollbar-color: rgba(148, 163, 184, 0.45) transparent;
    }

    .about-sidebar__tile {
      position: relative;
      display: block;
      width: 100%;
      min-width: 0;
      padding: 0;
      border: 0;
      border-radius: 16px;
      overflow: hidden;
      cursor: pointer;
      aspect-ratio: 16/9;
      background: #e2e8f0;
      animation: aboutMediaIn 0.45s ease both;
      animation-delay: var(--about-media-delay, 0s);
    }

    .about-sidebar__tile.is-filtered-out {
      display: none;
    }

    .about-sidebar__tile img,
    .about-sidebar__tile-visual {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.45s ease;
    }

    .about-sidebar__tile-visual {
      background:
        linear-gradient(135deg, rgba(15, 23, 42, 0.88), rgba(30, 58, 138, 0.72)),
        center/cover no-repeat;
    }

    .about-sidebar__tile-overlay {
      position: absolute;
      inset: 0;
      z-index: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(to top, rgba(15, 23, 42, 0.62), rgba(15, 23, 42, 0.08));
      color: #fff;
      font-size: 1rem;
      opacity: 0;
      transition: opacity 0.28s ease;
    }

    .about-sidebar__tile-overlay--video i {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 2.75rem;
      height: 2.75rem;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.92);
      color: var(--primary);
      font-size: 0.85rem;
      box-shadow: 0 8px 24px rgba(15, 23, 42, 0.25);
    }

    .about-sidebar__tile:hover img,
    .about-sidebar__tile:focus-visible img,
    .about-sidebar__tile:hover .about-sidebar__tile-visual,
    .about-sidebar__tile:focus-visible .about-sidebar__tile-visual {
      transform: scale(1.06);
    }

    .about-sidebar__tile:hover .about-sidebar__tile-overlay,
    .about-sidebar__tile:focus-visible .about-sidebar__tile-overlay {
      opacity: 1;
    }

    .about-sidebar__tile-label {
      position: absolute;
      left: 0.55rem;
      right: 0.55rem;
      bottom: 0.55rem;
      z-index: 2;
      padding: 0.35rem 0.55rem;
      border-radius: 10px;
      background: rgba(15, 23, 42, 0.62);
      backdrop-filter: blur(6px);
      color: #f8fafc;
      font-size: 0.72rem;
      font-weight: 600;
      line-height: 1.3;
      text-align: left;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .about-sidebar__tile:focus-visible {
      outline: 2px solid var(--primary);
      outline-offset: 2px;
    }

    @keyframes aboutMediaIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .about-empty {
      max-width: 520px;
      margin: 2rem auto 0;
      text-align: center;
      padding: 3rem 1.75rem;
      border-radius: 24px;
      background: rgba(255, 255, 255, 0.9);
      border: 1px solid var(--border);
      box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
    }

    .about-empty__icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 3.5rem;
      height: 3.5rem;
      margin-bottom: 1rem;
      border-radius: 50%;
      background: rgba(14, 165, 233, 0.1);
      color: var(--primary);
      font-size: 1.35rem;
    }

    .about-empty h2 {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 1.35rem;
      color: var(--dark);
      margin: 0 0 0.5rem;
    }

    .about-empty p {
      margin: 0;
      color: var(--text-muted);
      line-height: 1.6;
    }

    .about-page .reveal {
      opacity: 0;
      transform: translateY(18px);
      transition: opacity 0.55s ease, transform 0.55s ease;
      transition-delay: var(--about-delay, 0s);
    }

    .about-page .reveal.active {
      opacity: 1;
      transform: translateY(0);
    }

    body.about-modal-open {
      overflow: hidden;
    }

    body.about-modal-open .site-header {
      visibility: hidden;
      opacity: 0;
      pointer-events: none;
    }

    .about-modal {
      position: fixed;
      inset: 0;
      z-index: 9999;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1rem;
    }

    .about-modal[hidden] {
      display: none !important;
    }

    .about-modal__backdrop {
      position: absolute;
      inset: 0;
      background: rgba(15, 23, 42, 0.82);
      backdrop-filter: blur(8px);
    }

    .about-modal__shell {
      position: relative;
      z-index: 1;
      width: min(1100px, 100%);
      max-height: 92dvh;
      display: flex;
      flex-direction: column;
    }

    .about-modal__close {
      position: absolute;
      top: -0.25rem;
      right: 0;
      z-index: 2;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 2.75rem;
      height: 2.75rem;
      border: 0;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.12);
      color: #fff;
      cursor: pointer;
      transition: background 0.2s ease, transform 0.2s ease;
    }

    .about-modal__close:hover {
      background: rgba(255, 255, 255, 0.22);
      transform: scale(1.05);
    }

    .about-modal__figure {
      margin: 0;
      display: flex;
      flex-direction: column;
      gap: 0.85rem;
    }

    .about-modal__figure img {
      width: 100%;
      max-height: 78dvh;
      object-fit: contain;
      border-radius: 16px;
      background: #0f172a;
    }

    .about-modal__figure figcaption {
      display: flex;
      flex-direction: column;
      gap: 0.25rem;
      color: #f8fafc;
      padding: 0 0.25rem;
    }

    .about-modal__figure figcaption strong {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 1.05rem;
    }

    .about-modal__figure figcaption span:empty {
      display: none;
    }

    .about-modal__figure figcaption span {
      color: rgba(241, 245, 249, 0.82);
      font-size: 0.92rem;
      line-height: 1.5;
    }

    .about-modal--video .about-modal__shell {
      width: min(960px, 100%);
    }

    .about-modal__video-wrap {
      display: flex;
      flex-direction: column;
      gap: 0.85rem;
    }

    .about-modal__player {
      position: relative;
      width: 100%;
      aspect-ratio: 16/9;
      border-radius: 16px;
      overflow: hidden;
      background: #0f172a;
    }

    .about-modal__player iframe,
    .about-modal__player video {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      border: 0;
    }

    .about-modal__video-title {
      margin: 0;
      color: #f8fafc;
      font-family: 'Space Grotesk', sans-serif;
      font-size: 1.05rem;
      font-weight: 600;
    }

    /* Page Laboratoire / Équipe */
    .laboratory-page {
      min-height: 100vh;
      background:
        radial-gradient(circle at 0% 0%, rgba(16, 185, 129, 0.05) 0%, transparent 40%),
        radial-gradient(circle at 100% 8%, rgba(14, 165, 233, 0.05) 0%, transparent 38%),
        linear-gradient(180deg, #f8fafc 0%, #ffffff 45%, #f1f5f9 100%);
    }

    .laboratory-hero__bg {
      position: absolute;
      inset: 0;
      background:
        radial-gradient(circle at 14% 22%, rgba(16, 185, 129, 0.28) 0%, transparent 46%),
        radial-gradient(circle at 86% 18%, rgba(14, 165, 233, 0.24) 0%, transparent 44%),
        linear-gradient(160deg, rgba(15, 23, 42, 0.98) 0%, rgba(15, 23, 42, 0.91) 55%, rgba(15, 23, 42, 0.99) 100%),
        url('https://images.unsplash.com/photo-1629909613654-28e377c37b09?w=1920&q=80') center/cover no-repeat;
      z-index: 0;
    }

    .laboratory-hero__mesh {
      position: absolute;
      inset: 0;
      background-image:
        linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
      background-size: 48px 48px;
      mask-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.55), transparent 88%);
      z-index: 0;
      pointer-events: none;
    }

    .laboratory-hero__content {
      position: relative;
      z-index: 1;
      max-width: 820px;
    }

    .laboratory-hero__badge {
      display: inline-flex;
      align-items: center;
      gap: 0.55rem;
      padding: 0.45rem 0.95rem;
      background: rgba(16, 185, 129, 0.14);
      border: 1px solid rgba(110, 231, 183, 0.28);
      border-radius: 999px;
      color: #d1fae5;
      font-size: 0.8rem;
      font-weight: 600;
      letter-spacing: 0.04em;
      text-transform: uppercase;
      margin-bottom: 1.1rem;
    }

    .laboratory-hero h1 {
      font-family: 'Space Grotesk', sans-serif;
      font-size: clamp(2rem, 4.8vw, 3.25rem);
      font-weight: 700;
      line-height: 1.06;
      color: #f8fafc;
      margin: 0 0 1rem;
      letter-spacing: -0.02em;
    }

    .laboratory-hero__lead {
      font-size: clamp(1rem, 2vw, 1.12rem);
      color: rgba(241, 245, 249, 0.9);
      max-width: 640px;
      line-height: 1.75;
      margin: 0;
    }

    .laboratory-hero__stats {
      display: flex;
      flex-wrap: wrap;
      gap: 0.75rem;
      margin-top: 1.5rem;
    }

    .laboratory-hero__stat {
      display: inline-flex;
      align-items: baseline;
      gap: 0.4rem;
      padding: 0.5rem 0.95rem;
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.12);
      color: rgba(241, 245, 249, 0.92);
      font-size: 0.82rem;
    }

    .laboratory-hero__stat strong {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 1.05rem;
      color: #fff;
    }

    .laboratory-body {
      padding: 0 5% 5.5rem;
      max-width: 1240px;
      margin: -1.5rem auto 0;
    }

    .laboratory-toolbar {
      margin-bottom: 1.75rem;
    }

    .laboratory-filters {
      display: flex;
      flex-wrap: wrap;
      gap: 0.55rem;
    }

    .laboratory-filter {
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
      padding: 0.55rem 1rem;
      border: 1px solid rgba(148, 163, 184, 0.22);
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.88);
      color: var(--text-muted);
      font-size: 0.82rem;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .laboratory-filter span {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 1.35rem;
      height: 1.35rem;
      padding: 0 0.35rem;
      border-radius: 999px;
      background: rgba(148, 163, 184, 0.14);
      font-size: 0.72rem;
    }

    .laboratory-filter.is-active {
      background: var(--primary);
      border-color: var(--primary);
      color: #fff;
      box-shadow: 0 8px 24px rgba(14, 165, 233, 0.25);
    }

    .laboratory-filter.is-active span {
      background: rgba(255, 255, 255, 0.2);
      color: #fff;
    }

    .laboratory-filter i {
      font-size: 0.75rem;
    }

    .laboratory-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 1.35rem;
    }

    .laboratory-card {
      display: flex;
      flex-direction: column;
      border-radius: 22px;
      background: rgba(255, 255, 255, 0.92);
      border: 1px solid rgba(148, 163, 184, 0.18);
      box-shadow: 0 16px 40px rgba(15, 23, 42, 0.07);
      overflow: hidden;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      animation: aboutMediaIn 0.45s ease both;
      animation-delay: var(--lab-delay, 0s);
    }

    .laboratory-card.is-filtered-out {
      display: none;
    }

    .laboratory-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 24px 52px rgba(15, 23, 42, 0.11);
    }

    .laboratory-card__btn {
      display: block;
      width: 100%;
      padding: 0;
      border: 0;
      background: transparent;
      cursor: pointer;
    }

    .laboratory-card__visual {
      position: relative;
      aspect-ratio: 4/3;
      overflow: hidden;
      background: #e2e8f0;
    }

    .laboratory-card__visual img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.45s ease;
    }

    .laboratory-card__overlay {
      position: absolute;
      inset: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(to top, rgba(15, 23, 42, 0.55), transparent 55%);
      color: #fff;
      font-size: 1.2rem;
      opacity: 0;
      transition: opacity 0.28s ease;
    }

    .laboratory-card__btn:hover .laboratory-card__visual img,
    .laboratory-card__btn:focus-visible .laboratory-card__visual img {
      transform: scale(1.06);
    }

    .laboratory-card__btn:hover .laboratory-card__overlay,
    .laboratory-card__btn:focus-visible .laboratory-card__overlay {
      opacity: 1;
    }

    .laboratory-card__btn:focus-visible {
      outline: 2px solid var(--primary);
      outline-offset: 2px;
    }

    .laboratory-card__body {
      padding: 1.1rem 1.2rem 1.25rem;
    }

    .laboratory-card__category {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      padding: 0.28rem 0.65rem;
      margin-bottom: 0.65rem;
      border-radius: 999px;
      background: rgba(16, 185, 129, 0.1);
      border: 1px solid rgba(16, 185, 129, 0.18);
      color: #059669;
      font-size: 0.68rem;
      font-weight: 700;
      letter-spacing: 0.06em;
      text-transform: uppercase;
    }

    .laboratory-card__body h2 {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 1.05rem;
      font-weight: 600;
      color: var(--dark);
      margin: 0;
      line-height: 1.35;
    }

    .laboratory-card__body p {
      margin: 0.4rem 0 0;
      font-size: 0.88rem;
      color: var(--text-muted);
      line-height: 1.55;
    }

    .laboratory-empty {
      max-width: 520px;
      margin: 2rem auto 0;
      text-align: center;
      padding: 3rem 1.75rem;
      border-radius: 24px;
      background: rgba(255, 255, 255, 0.9);
      border: 1px solid var(--border);
      box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
    }

    .laboratory-empty__icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 3.5rem;
      height: 3.5rem;
      margin-bottom: 1rem;
      border-radius: 50%;
      background: rgba(16, 185, 129, 0.1);
      color: #059669;
      font-size: 1.35rem;
    }

    .laboratory-empty h2 {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 1.35rem;
      color: var(--dark);
      margin: 0 0 0.5rem;
    }

    .laboratory-empty p {
      margin: 0;
      color: var(--text-muted);
      line-height: 1.6;
    }

    .laboratory-page .reveal {
      opacity: 0;
      transform: translateY(18px);
      transition: opacity 0.55s ease, transform 0.55s ease;
      transition-delay: var(--lab-delay, 0s);
    }

    .laboratory-page .reveal.active {
      opacity: 1;
      transform: translateY(0);
    }

    .process--page .process-timeline-wrap {
      max-width: 1040px;
      margin: 0 auto;
      position: relative;
    }

    .process--page .process-timeline-wrap::before {
      content: '';
      position: absolute;
      top: -3rem;
      right: -8%;
      width: 280px;
      height: 280px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(14, 165, 233, 0.12) 0%, transparent 70%);
      pointer-events: none;
    }

    .process--page .process-timeline-header {
      text-align: center;
      max-width: 560px;
      margin: 0 auto 3.25rem;
    }

    .process--page .process-timeline-count {
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
      padding: 0.45rem 0.95rem;
      margin-bottom: 0.85rem;
      border-radius: 999px;
      background: rgba(14, 165, 233, 0.1);
      border: 1px solid rgba(14, 165, 233, 0.22);
      color: var(--primary);
      font-size: 0.78rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .process--page .process-timeline-header p {
      color: var(--text-muted);
      font-size: 1.05rem;
      line-height: 1.65;
    }

    .process--page .process-timeline {
      position: relative;
      display: flex;
      flex-direction: column;
      gap: 0;
    }

    .process--page .process-timeline::before {
      content: '';
      position: absolute;
      top: 28px;
      bottom: 28px;
      left: 50%;
      width: 2px;
      transform: translateX(-50%);
      background: linear-gradient(180deg, var(--primary) 0%, var(--secondary) 50%, var(--accent) 100%);
      opacity: 0.22;
      border-radius: 999px;
    }

    .process--page .process-timeline-item {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
      gap: 2rem;
      align-items: center;
      padding: 1.25rem 0;
    }

    .process--page .process-timeline-item:nth-child(odd) .process-timeline-card {
      grid-column: 1;
      grid-row: 1;
      justify-self: end;
    }

    .process--page .process-timeline-item:nth-child(even) .process-timeline-card {
      grid-column: 3;
      grid-row: 1;
      justify-self: start;
    }

    .process--page .process-timeline-marker {
      grid-column: 2;
      grid-row: 1;
      width: 56px;
      height: 56px;
      border-radius: 50%;
      background: #fff;
      border: 3px solid var(--primary);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 2;
      box-shadow:
        0 0 0 8px var(--bg),
        0 10px 28px rgba(2, 132, 199, 0.18);
      transition: transform 0.35s ease, box-shadow 0.35s ease, border-color 0.35s ease;
    }

    .process--page .process-timeline-number {
      font-family: 'Space Grotesk', sans-serif;
      font-weight: 700;
      font-size: 0.92rem;
      color: var(--primary);
      letter-spacing: 0.04em;
    }

    .process--page .process-timeline-card {
      position: relative;
      width: 100%;
      max-width: 400px;
      background: rgba(255, 255, 255, 0.96);
      border: 1px solid rgba(148, 163, 184, 0.24);
      border-radius: 24px;
      padding: 1.65rem 1.75rem 1.55rem;
      box-shadow: 0 18px 42px rgba(15, 23, 42, 0.08);
      backdrop-filter: blur(12px);
      overflow: hidden;
      transition: transform 0.35s ease, box-shadow 0.35s ease, border-color 0.35s ease;
    }

    .process--page .process-timeline-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: var(--gradient-1);
      opacity: 0.9;
    }

    .process--page .process-timeline-card-top {
      display: flex;
      align-items: center;
      gap: 0.85rem;
      margin-bottom: 1rem;
    }

    .process--page .process-timeline-icon {
      width: 48px;
      height: 48px;
      border-radius: 14px;
      background: linear-gradient(135deg, rgba(14, 165, 233, 0.14) 0%, rgba(37, 99, 235, 0.08) 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--primary);
      font-size: 1.15rem;
      flex-shrink: 0;
    }

    .process--page .process-timeline-label {
      font-size: 0.72rem;
      font-weight: 700;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      color: var(--text-muted);
    }

    .process--page .process-timeline-card h3 {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 1.35rem;
      font-weight: 700;
      color: var(--dark);
      margin-bottom: 0.55rem;
      line-height: 1.25;
    }

    .process--page .process-timeline-card p {
      color: var(--text-muted);
      font-size: 0.95rem;
      line-height: 1.7;
      margin: 0;
    }

    .inner-empty {
      max-width: 520px;
      margin: 0 auto;
      text-align: center;
      padding: 3rem 1.75rem;
      border-radius: 24px;
      background: #fff;
      border: 1px solid var(--border);
      box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
      color: var(--text-muted);
    }

    .inner-empty-icon {
      width: 72px;
      height: 72px;
      margin: 0 auto 1rem;
      border-radius: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(14, 165, 233, 0.1);
      color: var(--primary);
      font-size: 1.7rem;
    }

    .inner-empty h2 {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 1.25rem;
      color: var(--dark);
      margin-bottom: 0.5rem;
    }

    /* LDM Academy page */

    .nav-links a.is-active {
      color: #ffffff;
    }

    nav:not(.scrolled) .nav-links a.is-active::after {
      width: 100%;
      background: #e5e7eb;
    }

    nav.scrolled .nav-links a.is-active {
      color: var(--primary);
    }

    nav.scrolled .nav-links a.is-active::after {
      width: 100%;
      background: var(--primary);
    }

    .academy-hero-bg {
      position: absolute;
      inset: 0;
      background:
        radial-gradient(circle at 15% 20%, rgba(14, 165, 233, 0.28) 0%, transparent 42%),
        radial-gradient(circle at 85% 30%, rgba(37, 99, 235, 0.22) 0%, transparent 40%),
        linear-gradient(135deg, rgba(15, 23, 42, 0.96) 0%, rgba(15, 23, 42, 0.88) 50%, rgba(15, 23, 42, 0.97) 100%),
        url('https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?w=1920&q=80') center/cover no-repeat;
      z-index: 0;
    }

    .academy-hero-bg::after {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(180deg, transparent 40%, rgba(241, 245, 249, 0.08) 100%);
    }

    .academy-hero-content {
      position: relative;
      z-index: 1;
      max-width: 760px;
    }

    .academy-hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.55rem;
      padding: 0.5rem 1rem;
      background: rgba(0, 168, 204, 0.14);
      border: 1px solid rgba(0, 168, 204, 0.28);
      border-radius: 999px;
      color: #e0f2fe;
      font-size: 0.85rem;
      font-weight: 600;
      margin-bottom: 1.35rem;
    }

    .academy-hero h1 {
      font-family: 'Space Grotesk', sans-serif;
      font-size: clamp(2.2rem, 5vw, 3.6rem);
      font-weight: 700;
      line-height: 1.1;
      color: #f9fafb;
      margin-bottom: 1rem;
    }

    .academy-hero p {
      font-size: clamp(1rem, 2vw, 1.15rem);
      color: rgba(241, 245, 249, 0.88);
      max-width: 560px;
      margin-bottom: 2rem;
      line-height: 1.7;
    }

    .academy-hero-stats {
      display: flex;
      flex-wrap: wrap;
      gap: 0.85rem;
    }

    .academy-hero-stat {
      min-width: 120px;
      padding: 0.9rem 1.1rem;
      border-radius: 16px;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.12);
      backdrop-filter: blur(10px);
    }

    .academy-hero-stat strong {
      display: block;
      font-family: 'Space Grotesk', sans-serif;
      font-size: 1.35rem;
      color: #f8fafc;
      line-height: 1.1;
      margin-bottom: 0.2rem;
    }

    .academy-hero-stat span {
      font-size: 0.78rem;
      color: rgba(226, 232, 240, 0.8);
      text-transform: uppercase;
      letter-spacing: 0.06em;
      font-weight: 600;
    }

    .academy-body {
      position: relative;
      z-index: 1;
      margin-top: -2rem;
      padding: 0 5% 5.5rem;
    }

    .academy-toolbar {
      max-width: 1180px;
      margin: 0 auto;
    }

    .academy-filters {
      display: flex;
      flex-wrap: wrap;
      gap: 0.7rem;
      margin-bottom: 1.75rem;
      padding: 0.85rem;
      border-radius: 20px;
      background: rgba(255, 255, 255, 0.92);
      border: 1px solid var(--border);
      box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
      backdrop-filter: blur(14px);
    }

    .academy-filter {
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
      border: 1px solid transparent;
      background: transparent;
      color: var(--text-muted);
      border-radius: 999px;
      padding: 0.65rem 0.95rem;
      font-size: 0.88rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.25s ease;
      font-family: inherit;
    }

    .academy-filter i {
      font-size: 0.85rem;
      opacity: 0.85;
    }

    .academy-filter span {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 1.4rem;
      height: 1.4rem;
      padding: 0 0.35rem;
      border-radius: 999px;
      background: rgba(148, 163, 184, 0.18);
      font-size: 0.72rem;
      font-weight: 700;
    }

    .academy-filter:hover {
      color: var(--primary);
      background: rgba(14, 165, 233, 0.08);
    }

    .academy-filter.is-active {
      color: #fff;
      background: var(--gradient-1);
      box-shadow: 0 8px 20px rgba(2, 132, 199, 0.25);
    }

    .academy-filter.is-active span {
      background: rgba(255, 255, 255, 0.22);
      color: #fff;
    }

    .academy-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 1.35rem;
    }

    .academy-card {
      display: flex;
      flex-direction: column;
      background: #fff;
      border: 1px solid rgba(148, 163, 184, 0.22);
      border-radius: 24px;
      padding: 1.45rem 1.35rem 1.25rem;
      box-shadow: 0 14px 34px rgba(15, 23, 42, 0.07);
      transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
      animation: academyCardIn 0.45s ease both;
      animation-delay: var(--delay, 0ms);
      min-height: 100%;
      position: relative;
      overflow: hidden;
    }

    .academy-card--has-cover {
      padding: 0;
      border-color: rgba(148, 163, 184, 0.28);
    }

    .academy-card-bg {
      position: absolute;
      inset: 0;
      background-image: var(--academy-cover);
      background-size: cover;
      background-position: center center;
      background-repeat: no-repeat;
      transition: transform 0.45s ease;
    }

    .academy-card-bg::after {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(
        160deg,
        rgba(255, 255, 255, 0.78) 0%,
        rgba(255, 255, 255, 0.62) 45%,
        rgba(248, 250, 252, 0.68) 100%
      );
    }

    .academy-card--has-cover:hover .academy-card-bg {
      transform: scale(1.03);
    }

    .academy-card-inner {
      position: relative;
      z-index: 1;
      display: flex;
      flex-direction: column;
      flex: 1;
      min-height: 100%;
      padding: 1.45rem 1.35rem 1.25rem;
    }

    .academy-card.is-hidden,
    .academy-card[hidden] {
      display: none !important;
    }

    @@keyframes academyCardIn {
      from {
        opacity: 0;
        transform: translateY(14px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .academy-card:hover {
      transform: translateY(-8px);
      border-color: rgba(14, 165, 233, 0.35);
      box-shadow: 0 24px 48px rgba(15, 23, 42, 0.12);
    }

    .academy-card-top {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 0.75rem;
      margin-bottom: 1.1rem;
    }

    .academy-card-icon {
      width: 48px;
      height: 48px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, rgba(14, 165, 233, 0.14), rgba(37, 99, 235, 0.1));
      color: var(--primary);
      font-size: 1.15rem;
      flex-shrink: 0;
    }

    .academy-card-category {
      display: inline-flex;
      align-items: center;
      padding: 0.3rem 0.7rem;
      border-radius: 999px;
      background: rgba(14, 165, 233, 0.1);
      color: var(--primary);
      font-size: 0.7rem;
      font-weight: 700;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      white-space: nowrap;
    }

    .academy-card h2 {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 1.15rem;
      color: var(--dark);
      margin-bottom: 0.55rem;
      line-height: 1.35;
      font-weight: 700;
    }

    .academy-card p {
      color: var(--text-muted);
      font-size: 0.92rem;
      line-height: 1.6;
      margin-bottom: 1.25rem;
      flex: 1;
    }

    .academy-card-muted {
      opacity: 0.85;
    }

    .academy-card-footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 0.75rem;
      padding-top: 1rem;
      border-top: 1px solid rgba(148, 163, 184, 0.18);
      margin-top: auto;
    }

    .academy-card-format {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      color: #dc2626;
      font-size: 0.8rem;
      font-weight: 700;
      letter-spacing: 0.04em;
    }

    .academy-download {
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
      padding: 0.65rem 0.95rem;
      border-radius: 12px;
      background: var(--gradient-1);
      color: #fff;
      text-decoration: none;
      font-size: 0.86rem;
      font-weight: 600;
      transition: transform 0.25s ease, box-shadow 0.25s ease;
      white-space: nowrap;
    }

    .academy-download:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 24px var(--primary-glow);
      color: #fff;
    }

    button.academy-download {
      border: none;
      cursor: pointer;
      font-family: inherit;
    }

    .academy-media-modal {
      position: fixed;
      inset: 0;
      z-index: 12000;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.25rem;
    }

    .academy-media-modal[hidden] {
      display: none !important;
    }

    .academy-media-modal__backdrop {
      position: absolute;
      inset: 0;
      background: rgba(2, 6, 23, 0.88);
      backdrop-filter: blur(10px);
    }

    .academy-media-modal__shell {
      position: relative;
      z-index: 1;
      width: min(960px, 100%);
      max-height: calc(100vh - 2.5rem);
      animation: academyModalIn 0.28s ease;
    }

    @@keyframes academyModalIn {
      from {
        opacity: 0;
        transform: translateY(12px) scale(0.98);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    .academy-media-modal__close {
      position: absolute;
      top: -0.25rem;
      right: 0;
      transform: translateY(-100%);
      width: 42px;
      height: 42px;
      border: none;
      border-radius: 12px;
      background: rgba(255, 255, 255, 0.12);
      color: #fff;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      transition: background 0.2s ease;
    }

    .academy-media-modal__close:hover {
      background: rgba(255, 255, 255, 0.22);
    }

    .academy-media-modal__figure {
      margin: 0;
      background: #0f172a;
      border-radius: 18px;
      overflow: hidden;
      box-shadow: 0 24px 60px rgba(0, 0, 0, 0.35);
    }

    .academy-media-modal__figure img {
      display: block;
      width: 100%;
      max-height: calc(100vh - 8rem);
      object-fit: contain;
      background: #020617;
    }

    .academy-media-modal__figure figcaption {
      padding: 0.85rem 1.1rem;
      color: #e2e8f0;
      font-size: 0.92rem;
      font-weight: 600;
      background: rgba(15, 23, 42, 0.96);
    }

    .academy-media-modal__figure figcaption:empty {
      display: none;
    }

    .academy-media-modal__video-wrap {
      background: #0f172a;
      border-radius: 18px;
      overflow: hidden;
      box-shadow: 0 24px 60px rgba(0, 0, 0, 0.35);
    }

    .academy-media-modal__player {
      position: relative;
      width: 100%;
      aspect-ratio: 16 / 9;
      background: #000;
    }

    .academy-media-modal__player iframe,
    .academy-media-modal__player video {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      border: 0;
      background: #000;
    }

    .academy-media-modal__video-title {
      margin: 0;
      padding: 0.85rem 1.1rem;
      color: #e2e8f0;
      font-size: 0.92rem;
      font-weight: 600;
      background: rgba(15, 23, 42, 0.96);
    }

    .academy-media-modal__video-title:empty {
      display: none;
    }

    body.academy-modal-open {
      overflow: hidden;
    }

    body.academy-modal-open .site-header {
      visibility: hidden;
      opacity: 0;
      pointer-events: none;
    }

    .academy-empty {
      max-width: 520px;
      margin: 0 auto;
      text-align: center;
      padding: 3rem 1.75rem;
      border-radius: 24px;
      background: #fff;
      border: 1px solid var(--border);
      box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
      color: var(--text-muted);
    }

    .academy-empty--filter {
      margin-top: 1rem;
    }

    .academy-empty-icon {
      width: 72px;
      height: 72px;
      margin: 0 auto 1rem;
      border-radius: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(14, 165, 233, 0.1);
      color: var(--primary);
      font-size: 1.7rem;
    }

    .academy-empty h2 {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 1.25rem;
      color: var(--dark);
      margin-bottom: 0.5rem;
    }

    .academy-empty p {
      font-size: 0.95rem;
      line-height: 1.65;
    }

    .academy-load-more {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.75rem;
      margin-top: 1.75rem;
      color: var(--text-muted);
      font-size: 0.92rem;
      font-weight: 600;
    }

    .academy-load-more[hidden] {
      display: none !important;
    }

    .academy-load-more-spinner {
      width: 1.25rem;
      height: 1.25rem;
      border-radius: 999px;
      border: 2px solid rgba(14, 165, 233, 0.2);
      border-top-color: var(--primary);
      animation: academySpin 0.7s linear infinite;
    }

    .academy-sentinel {
      height: 1px;
      width: 100%;
      margin-top: 0.5rem;
    }

    @@keyframes academySpin {
      to { transform: rotate(360deg); }
    }

    /* FAQ Page */
    .faq-page {
      min-height: 100vh;
    }

    .faq-hero-bg {
      position: absolute;
      inset: 0;
      background:
        radial-gradient(circle at 12% 18%, rgba(139, 92, 246, 0.22) 0%, transparent 42%),
        radial-gradient(circle at 88% 24%, rgba(0, 168, 204, 0.26) 0%, transparent 40%),
        linear-gradient(135deg, rgba(15, 23, 42, 0.97) 0%, rgba(30, 41, 59, 0.92) 55%, rgba(15, 23, 42, 0.98) 100%),
        url('https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?w=1920&q=80') center/cover no-repeat;
      z-index: 0;
    }

    .faq-hero-content {
      position: relative;
      z-index: 1;
      max-width: 780px;
    }

    .faq-hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.55rem;
      padding: 0.5rem 1rem;
      background: rgba(139, 92, 246, 0.14);
      border: 1px solid rgba(167, 139, 250, 0.32);
      border-radius: 999px;
      color: #ede9fe;
      font-size: 0.85rem;
      font-weight: 600;
      margin-bottom: 1.35rem;
    }

    .faq-hero h1 {
      font-family: 'Space Grotesk', sans-serif;
      font-size: clamp(2.1rem, 5vw, 3.5rem);
      font-weight: 700;
      line-height: 1.1;
      color: #f9fafb;
      margin-bottom: 1rem;
    }

    .faq-hero p {
      font-size: clamp(1rem, 2vw, 1.12rem);
      color: rgba(241, 245, 249, 0.88);
      max-width: 600px;
      line-height: 1.7;
    }

    .faq-hero-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      margin-top: 2rem;
    }

    .faq-hero-stat {
      display: flex;
      flex-direction: column;
      gap: 0.15rem;
      padding: 0.85rem 1.25rem;
      background: rgba(15, 23, 42, 0.55);
      border: 1px solid rgba(148, 163, 184, 0.22);
      border-radius: 14px;
      backdrop-filter: blur(8px);
    }

    .faq-hero-stat strong {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 1.35rem;
      color: #f8fafc;
      line-height: 1;
    }

    .faq-hero-stat span {
      font-size: 0.82rem;
      color: rgba(203, 213, 225, 0.9);
    }

    .faq-body {
      position: relative;
      z-index: 1;
      padding: 0 5% 5rem;
      margin-top: -1.5rem;
    }

    .faq-body-inner {
      max-width: 900px;
      margin: 0 auto;
    }

    .faq-intro {
      text-align: center;
      margin-bottom: 2rem;
    }

    .faq-intro p {
      display: inline-flex;
      align-items: center;
      gap: 0.55rem;
      padding: 0.75rem 1.35rem;
      background: rgba(15, 23, 42, 0.65);
      border: 1px solid rgba(0, 168, 204, 0.35);
      border-radius: 999px;
      color: #f1f5f9;
      font-size: 0.98rem;
      font-weight: 500;
      box-shadow: 0 4px 20px rgba(0, 168, 204, 0.08);
    }

    .faq-intro p i {
      color: var(--accent);
      font-size: 0.9rem;
      flex-shrink: 0;
    }

    .faq-accordion {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .faq-card {
      border-radius: 20px;
      overflow: hidden;
      border: 1px solid rgba(148, 163, 184, 0.18);
      background: rgba(15, 23, 42, 0.72);
      box-shadow: 0 8px 32px rgba(15, 23, 42, 0.35);
      transition: border-color 0.3s ease, box-shadow 0.3s ease, transform 0.3s ease;
    }

    .faq-card:hover {
      border-color: rgba(0, 168, 204, 0.28);
    }

    .faq-card.is-open {
      border-color: rgba(0, 168, 204, 0.45);
      box-shadow: 0 16px 48px rgba(0, 168, 204, 0.1);
    }

    .faq-card__trigger {
      width: 100%;
      display: grid;
      grid-template-columns: auto 1fr auto;
      align-items: center;
      gap: 1rem 1.25rem;
      padding: 1.35rem 1.5rem;
      background: transparent;
      border: none;
      cursor: pointer;
      text-align: left;
      color: #f8fafc;
    }

    .faq-card__index {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 0.8rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      color: var(--accent);
      padding: 0.45rem 0.65rem;
      background: rgba(0, 168, 204, 0.12);
      border-radius: 10px;
      min-width: 2.5rem;
      text-align: center;
    }

    .faq-card__question {
      font-family: 'Space Grotesk', sans-serif;
      font-size: clamp(1rem, 2.2vw, 1.12rem);
      font-weight: 600;
      line-height: 1.45;
      color: #f1f5f9;
    }

    .faq-card__toggle {
      width: 2.5rem;
      height: 2.5rem;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 12px;
      background: rgba(148, 163, 184, 0.12);
      color: var(--accent);
      position: relative;
      flex-shrink: 0;
      transition: background 0.25s ease, transform 0.25s ease;
    }

    .faq-card.is-open .faq-card__toggle {
      background: rgba(0, 168, 204, 0.2);
    }

    .faq-card__icon {
      position: absolute;
      font-size: 0.8rem;
      transition: opacity 0.2s ease, transform 0.25s ease;
    }

    .faq-card__icon--minus {
      opacity: 0;
      transform: scale(0.6);
    }

    .faq-card.is-open .faq-card__icon--plus {
      opacity: 0;
      transform: scale(0.6);
    }

    .faq-card.is-open .faq-card__icon--minus {
      opacity: 1;
      transform: scale(1);
    }

    .faq-card__panel {
      overflow: hidden;
      transition: height 0.38s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .faq-card__answer {
      margin: 0 1rem 1rem;
      padding: 1.35rem 1.5rem;
      border-radius: 14px;
      background: linear-gradient(135deg, #f0f9ff 0%, #ecfeff 45%, #f8fafc 100%);
      border: 1px solid rgba(186, 230, 253, 0.65);
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.85);
    }

    .faq-card__answer p {
      margin: 0;
      color: #334155;
      font-size: 1rem;
      line-height: 1.8;
    }

    .faq-cta {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      gap: 1.5rem;
      margin-top: 3rem;
      padding: 2rem 2.25rem;
      border-radius: 20px;
      background: linear-gradient(135deg, rgba(15, 23, 42, 0.82) 0%, rgba(30, 41, 59, 0.78) 100%);
      border: 1px solid rgba(0, 168, 204, 0.35);
      box-shadow: 0 12px 40px rgba(15, 23, 42, 0.35);
    }

    .faq-cta__content h2 {
      font-family: 'Space Grotesk', sans-serif;
      font-size: clamp(1.25rem, 2.5vw, 1.55rem);
      color: #f8fafc;
      margin-bottom: 0.45rem;
    }

    .faq-cta__content p {
      margin: 0;
      color: #e2e8f0;
      font-size: 1rem;
      font-weight: 500;
      line-height: 1.65;
      max-width: 480px;
    }

    .faq-cta__btn {
      display: inline-flex;
      align-items: center;
      gap: 0.65rem;
      padding: 0.9rem 1.5rem;
      border-radius: 12px;
      background: var(--accent);
      color: #fff;
      font-weight: 600;
      font-size: 0.95rem;
      text-decoration: none;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      white-space: nowrap;
    }

    .faq-cta__btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 28px rgba(0, 168, 204, 0.35);
    }

    .faq-empty {
      max-width: 520px;
      margin: 3rem auto 0;
      text-align: center;
      padding: 3rem 2rem;
      border-radius: 20px;
      border: 1px dashed rgba(148, 163, 184, 0.35);
      background: rgba(15, 23, 42, 0.5);
    }

    .faq-empty-icon {
      width: 4rem;
      height: 4rem;
      margin: 0 auto 1.25rem;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      background: rgba(0, 168, 204, 0.12);
      color: var(--accent);
      font-size: 1.5rem;
    }

    .faq-empty h2 {
      font-family: 'Space Grotesk', sans-serif;
      color: #f8fafc;
      margin-bottom: 0.5rem;
    }

    .faq-empty p {
      color: rgba(203, 213, 225, 0.85);
      margin: 0;
    }

    /* Contact Section — premium glass */
    .contact-section {
      padding: 6.5rem 5%;
      position: relative;
      overflow: hidden;
    }

    .contact-section__bg {
      position: absolute;
      inset: 0;
      pointer-events: none;
      overflow: hidden;
    }

    .contact-section__orb {
      position: absolute;
      border-radius: 50%;
      filter: blur(60px);
      opacity: 0.55;
      animation: contactOrbFloat 14s ease-in-out infinite;
    }

    .contact-section__orb--1 {
      width: 420px;
      height: 420px;
      top: -8%;
      left: -6%;
      background: radial-gradient(circle, rgba(56, 189, 248, 0.35) 0%, transparent 70%);
    }

    .contact-section__orb--2 {
      width: 360px;
      height: 360px;
      bottom: -10%;
      right: -4%;
      background: radial-gradient(circle, rgba(37, 99, 235, 0.28) 0%, transparent 70%);
      animation-delay: -4s;
    }

    .contact-section__orb--3 {
      width: 280px;
      height: 280px;
      top: 40%;
      left: 42%;
      background: radial-gradient(circle, rgba(34, 197, 94, 0.18) 0%, transparent 70%);
      animation-delay: -7s;
    }

    @@keyframes contactOrbFloat {
      0%, 100% { transform: translate(0, 0) scale(1); }
      50% { transform: translate(12px, -16px) scale(1.05); }
    }

    .contact-layout {
      max-width: 1180px;
      margin: 0 auto;
      display: flex;
      flex-direction: column;
      gap: 1.75rem;
      position: relative;
      z-index: 1;
    }

    .contact-row {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 1.75rem;
      align-items: stretch;
    }

    .contact-row--info-form {
      grid-template-columns: minmax(0, 1fr) minmax(0, 1.15fr);
    }

    .contact-row--single {
      grid-template-columns: minmax(0, 1fr);
      max-width: 760px;
      margin: 0 auto;
      width: 100%;
    }

    .contact-map {
      padding: 1.35rem 1.35rem 1.15rem;
    }

    .contact-map--inline {
      margin: 0;
      height: 100%;
      display: flex;
      flex-direction: column;
    }

    .contact-map--inline .contact-map__frame {
      flex: 1;
      min-height: 320px;
      height: auto;
    }

    .contact-map--below {
      margin: 0;
      width: 100%;
    }

    .contact-map__head {
      margin-bottom: 1rem;
    }

    .contact-map__title {
      font-family: 'Space Grotesk', sans-serif;
      font-size: clamp(1.25rem, 2.5vw, 1.55rem);
      font-weight: 700;
      color: var(--text);
      margin-bottom: 0.35rem;
    }

    .contact-map__address {
      color: var(--text-muted);
      font-size: 0.95rem;
      line-height: 1.5;
    }

    .contact-map__frame {
      position: relative;
      width: 100%;
      height: clamp(280px, 42vw, 420px);
      border-radius: 20px;
      overflow: hidden;
      border: 1px solid rgba(255, 255, 255, 0.72);
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.65);
      background: rgba(241, 245, 249, 0.65);
    }

    .contact-map__frame iframe {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      border: 0;
    }

    .contact-map__actions {
      display: flex;
      justify-content: flex-end;
      margin-top: 0.85rem;
    }

    .contact-map__link {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.65rem 1.1rem;
      border-radius: 999px;
      font-size: 0.9rem;
      font-weight: 600;
      color: var(--primary);
      text-decoration: none;
      background: rgba(255, 255, 255, 0.55);
      border: 1px solid rgba(14, 165, 233, 0.25);
      transition: background 0.25s ease, transform 0.25s ease, box-shadow 0.25s ease;
    }

    .contact-map__link:hover {
      background: rgba(255, 255, 255, 0.85);
      transform: translateY(-1px);
      box-shadow: 0 8px 24px rgba(14, 165, 233, 0.12);
    }

    .contact-glass {
      background: rgba(255, 255, 255, 0.52);
      backdrop-filter: blur(22px) saturate(160%);
      -webkit-backdrop-filter: blur(22px) saturate(160%);
      border-radius: 28px;
      border: 1px solid rgba(255, 255, 255, 0.72);
      box-shadow:
        0 4px 24px rgba(15, 23, 42, 0.04),
        0 24px 64px rgba(14, 165, 233, 0.08),
        inset 0 1px 0 rgba(255, 255, 255, 0.85);
      transition: box-shadow 0.4s ease, transform 0.4s ease;
    }

    .contact-glass:hover {
      box-shadow:
        0 8px 32px rgba(15, 23, 42, 0.06),
        0 32px 72px rgba(14, 165, 233, 0.12),
        inset 0 1px 0 rgba(255, 255, 255, 0.9);
    }

    .contact-card {
      padding: 2.25rem 2.35rem;
    }

    .contact-card h2 {
      font-family: 'Space Grotesk', sans-serif;
      font-size: clamp(1.85rem, 3vw, 2.45rem);
      margin-bottom: 0.85rem;
      color: var(--dark);
      line-height: 1.15;
      letter-spacing: -0.02em;
    }

    .contact-card__lead {
      color: var(--text-muted);
      font-size: 1rem;
      line-height: 1.7;
      max-width: 38ch;
    }

    .contact-tag {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.4rem 0.95rem;
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.65);
      border: 1px solid rgba(14, 165, 233, 0.22);
      color: var(--primary);
      font-size: 0.72rem;
      font-weight: 700;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      margin-bottom: 1.35rem;
      box-shadow: 0 4px 16px rgba(14, 165, 233, 0.08);
    }

    .contact-tag i {
      font-size: 0.78rem;
      opacity: 0.9;
    }

    .contact-items {
      margin-top: 2rem;
      display: grid;
      gap: 0.85rem;
    }

    .contact-glass-item {
      padding: 0.95rem 1.05rem;
      border-radius: 18px;
      background: rgba(255, 255, 255, 0.42);
      border: 1px solid rgba(255, 255, 255, 0.75);
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
      transition: background 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
    }

    .contact-glass-item:hover {
      background: rgba(255, 255, 255, 0.68);
      transform: translateX(4px);
      box-shadow:
        0 8px 24px rgba(14, 165, 233, 0.08),
        inset 0 1px 0 rgba(255, 255, 255, 0.8);
    }

    .contact-item {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .contact-item-icon {
      width: 44px;
      height: 44px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, rgba(14, 165, 233, 0.15), rgba(37, 99, 235, 0.12));
      border: 1px solid rgba(14, 165, 233, 0.2);
      color: var(--primary);
      font-size: 0.95rem;
      flex-shrink: 0;
      box-shadow: 0 6px 18px rgba(14, 165, 233, 0.12);
    }

    .contact-item-text h4 {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 0.92rem;
      font-weight: 600;
      margin-bottom: 0.15rem;
      color: var(--dark);
    }

    .contact-item-text p {
      font-size: 0.88rem;
      color: var(--text-muted);
      margin: 0;
      line-height: 1.45;
    }

    .contact-item-text p + p {
      margin-top: 0.1rem;
    }

    .contact-form-wrapper {
      padding: 2.25rem 2.35rem;
    }

    .contact-form-header {
      margin-bottom: 1.65rem;
      padding-bottom: 1.15rem;
      border-bottom: 1px solid rgba(148, 163, 184, 0.2);
    }

    .contact-form-title {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 1.35rem;
      font-weight: 700;
      color: var(--dark);
      margin-bottom: 0.35rem;
      letter-spacing: -0.01em;
    }

    .contact-form-subtitle {
      margin: 0;
      font-size: 0.86rem;
      color: var(--text-muted);
    }

    .contact-form-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 1.15rem 1rem;
    }

    .contact-form-grid .full-row {
      grid-column: 1 / -1;
    }

    .contact-label {
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--dark);
      margin-bottom: 0.4rem;
      display: block;
      letter-spacing: 0.02em;
    }

    .contact-label span {
      color: var(--primary);
    }

    .contact-label-optional {
      color: var(--text-muted) !important;
      font-weight: 500;
      font-size: 0.82em;
      text-transform: none;
      letter-spacing: normal;
    }

    .contact-file {
      width: 100%;
      padding: 0.85rem 1rem;
      border-radius: 14px;
      border: 1px dashed rgba(14, 165, 233, 0.35);
      background: rgba(255, 255, 255, 0.42);
      color: var(--text);
      font-size: 0.92rem;
      cursor: pointer;
      transition: border-color 0.25s ease, background 0.25s ease, box-shadow 0.25s ease;
    }

    .contact-file::file-selector-button {
      margin-right: 0.85rem;
      padding: 0.55rem 1rem;
      border: none;
      border-radius: 10px;
      background: var(--gradient-1);
      color: #fff;
      font-weight: 600;
      font-size: 0.85rem;
      cursor: pointer;
    }

    .contact-file:hover {
      border-color: rgba(14, 165, 233, 0.55);
      background: rgba(255, 255, 255, 0.58);
    }

    .contact-file:focus {
      outline: none;
      border-color: rgba(14, 165, 233, 0.65);
      box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
    }

    .contact-file-hint {
      margin-top: 0.45rem;
      font-size: 0.8rem;
      color: var(--text-muted);
    }

    .contact-input,
    .contact-textarea,
    .contact-select {
      width: 100%;
      border-radius: 14px;
      border: 1px solid rgba(148, 163, 184, 0.28);
      background: rgba(255, 255, 255, 0.55);
      backdrop-filter: blur(8px);
      color: var(--dark);
      padding: 0.82rem 1rem;
      font-size: 0.92rem;
      outline: none;
      transition:
        border-color 0.25s ease,
        box-shadow 0.25s ease,
        background 0.25s ease;
    }

    .contact-textarea {
      border-radius: 16px;
      min-height: 128px;
      resize: vertical;
    }

    .contact-input::placeholder,
    .contact-textarea::placeholder {
      color: #94a3b8;
    }

    .contact-input:focus,
    .contact-textarea:focus,
    .contact-select:focus {
      border-color: rgba(14, 165, 233, 0.55);
      box-shadow:
        0 0 0 3px rgba(14, 165, 233, 0.12),
        inset 0 1px 0 rgba(255, 255, 255, 0.8);
      background: rgba(255, 255, 255, 0.78);
    }

    .contact-actions {
      margin-top: 1.5rem;
      display: flex;
      justify-content: flex-end;
    }

    .contact-submit {
      width: 100%;
      justify-content: center;
      background: var(--gradient-1);
      color: #fff;
      border-radius: 14px;
      padding: 0.95rem 1.5rem;
      font-weight: 600;
      font-size: 0.95rem;
      border: 1px solid rgba(255, 255, 255, 0.25);
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 0.55rem;
      box-shadow:
        0 12px 32px rgba(14, 165, 233, 0.28),
        inset 0 1px 0 rgba(255, 255, 255, 0.25);
      transition: transform 0.28s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.28s ease;
    }

    .contact-submit:hover {
      transform: translateY(-2px);
      box-shadow:
        0 18px 40px rgba(14, 165, 233, 0.35),
        inset 0 1px 0 rgba(255, 255, 255, 0.3);
    }

    .contact-submit i {
      font-size: 0.9rem;
      transition: transform 0.25s ease;
    }

    .contact-submit:hover i {
      transform: translateX(3px);
    }

    .contact-submit__spinner {
      display: none;
      width: 1.05rem;
      height: 1.05rem;
      border: 2px solid rgba(255, 255, 255, 0.4);
      border-top-color: #fff;
      border-radius: 50%;
      flex: 0 0 auto;
      animation: contactSubmitSpin 0.7s linear infinite;
    }

    .contact-submit.is-loading {
      cursor: progress;
      opacity: 0.85;
      pointer-events: none;
    }

    .contact-submit.is-loading:hover {
      transform: none;
    }

    .contact-submit.is-loading .contact-submit__icon {
      display: none;
    }

    .contact-submit.is-loading .contact-submit__spinner {
      display: inline-block;
    }

    @@keyframes contactSubmitSpin {
      to {
        transform: rotate(360deg);
      }
    }

    .contact-alert {
      display: flex;
      align-items: flex-start;
      gap: 0.65rem;
      padding: 0.85rem 1rem;
      border-radius: 14px;
      margin-bottom: 1.25rem;
      font-size: 0.88rem;
      line-height: 1.5;
      backdrop-filter: blur(8px);
    }

    .contact-alert--success {
      background: rgba(240, 253, 244, 0.75);
      border: 1px solid rgba(34, 197, 94, 0.35);
      color: #166534;
    }

    .contact-alert--error {
      background: rgba(254, 242, 242, 0.75);
      border: 1px solid rgba(239, 68, 68, 0.35);
      color: #991b1b;
    }

    .contact-field-error {
      margin-top: 0.35rem;
      font-size: 0.78rem;
      color: #dc2626;
    }

    .contact-input--error,
    .contact-textarea.contact-input--error {
      border-color: rgba(239, 68, 68, 0.55);
      background: rgba(254, 242, 242, 0.45);
    }

    /* Gallery - Nos Travaux */
    .gallery-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1.5rem;
      max-width: 1200px;
      margin: 0 auto;
    }

    .gallery-more {
      display: flex;
      justify-content: center;
      margin-top: 1.5rem;
      max-width: 1200px;
      margin-left: auto;
      margin-right: auto;
    }

    .gallery-more__btn i {
      font-size: 0.85em;
      transition: transform 0.35s ease;
    }

    .gallery-more__btn[aria-expanded="true"] i {
      transform: rotate(180deg);
    }

    .gallery-item--extra[hidden] {
      display: none;
    }

    .gallery-item {
      position: relative;
      border-radius: 20px;
      overflow: hidden;
      aspect-ratio: 4/3;
      display: block;
      width: 100%;
      padding: 0;
      border: none;
      background: none;
      cursor: zoom-in;
      text-align: left;
    }

    .gallery-item:focus-visible {
      outline: 3px solid rgba(14, 165, 233, 0.55);
      outline-offset: 3px;
    }

    .gallery-item img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
      transition: transform 0.4s ease;
    }

    .gallery-item:hover img {
      transform: scale(1.05);
    }

    .gallery-overlay {
      position: absolute;
      inset: 0;
      background: linear-gradient(to top, rgba(0, 0, 0, 0.85), transparent 50%);
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
      padding: 1.5rem;
      opacity: 0;
      transition: opacity 0.4s ease;
    }

    .gallery-item:hover .gallery-overlay {
      opacity: 1;
    }

    .gallery-overlay h3 {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 1.2rem;
      margin-bottom: 0.3rem;
      color: #fff;
    }

    .gallery-overlay p {
      font-size: 0.9rem;
      color: rgba(255, 255, 255, 0.8);
    }

    .gallery-lightbox {
      position: fixed;
      inset: 0;
      z-index: 12000;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: clamp(0.75rem, 2vw, 1.5rem);
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.32s ease, visibility 0.32s ease;
    }

    .gallery-lightbox.is-active {
      opacity: 1;
      visibility: visible;
    }

    .gallery-lightbox[hidden] {
      display: none !important;
    }

    .gallery-lightbox-backdrop {
      position: absolute;
      inset: 0;
      background:
        radial-gradient(circle at 20% 10%, rgba(14, 165, 233, 0.18) 0%, transparent 42%),
        radial-gradient(circle at 80% 90%, rgba(37, 99, 235, 0.14) 0%, transparent 40%),
        rgba(2, 6, 23, 0.92);
      backdrop-filter: blur(14px);
    }

    .gallery-lightbox-shell {
      position: relative;
      z-index: 1;
      width: min(92vw, 1320px);
      height: min(88dvh, 100%);
      display: flex;
      flex-direction: column;
      gap: 0.85rem;
      transform: translateY(12px) scale(0.985);
      transition: transform 0.34s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .gallery-lightbox.is-active .gallery-lightbox-shell {
      transform: translateY(0) scale(1);
    }

    .gallery-lightbox-topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      padding: 0 0.25rem;
    }

    .gallery-lightbox-meta {
      display: inline-flex;
      align-items: center;
      gap: 0.65rem;
      min-width: 0;
    }

    .gallery-lightbox-badge {
      display: inline-flex;
      align-items: center;
      padding: 0.35rem 0.75rem;
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.12);
      color: #e2e8f0;
      font-size: 0.72rem;
      font-weight: 700;
      letter-spacing: 0.12em;
      text-transform: uppercase;
    }

    .gallery-lightbox-counter {
      color: rgba(226, 232, 240, 0.78);
      font-size: 0.82rem;
      font-weight: 600;
      letter-spacing: 0.04em;
    }

    .gallery-lightbox-stage {
      flex: 1;
      min-height: 0;
      display: grid;
      grid-template-columns: auto minmax(0, 1fr) auto;
      align-items: center;
      gap: clamp(0.65rem, 1.5vw, 1.25rem);
    }

    .gallery-lightbox-media {
      position: relative;
      height: 100%;
      min-height: 0;
      max-height: calc(80dvh - 6.5rem);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: clamp(0.45rem, 1vw, 0.85rem);
      border-radius: 22px;
      background: linear-gradient(145deg, rgba(15, 23, 42, 0.72), rgba(30, 41, 59, 0.55));
      border: 1px solid rgba(255, 255, 255, 0.12);
      box-shadow:
        0 30px 80px rgba(0, 0, 0, 0.55),
        inset 0 1px 0 rgba(255, 255, 255, 0.08);
      overflow: hidden;
    }

    .gallery-lightbox-media-glow {
      position: absolute;
      inset: 12% 8%;
      background: radial-gradient(circle, rgba(56, 189, 248, 0.16) 0%, transparent 68%);
      pointer-events: none;
    }

    .gallery-lightbox-media img {
      position: relative;
      z-index: 1;
      display: block;
      max-width: min(100%, 1000px);
      max-height: min(calc(80dvh - 9rem), 72vh);
      width: auto;
      height: auto;
      object-fit: contain;
      border-radius: 14px;
      box-shadow: 0 18px 48px rgba(0, 0, 0, 0.38);
    }

    .gallery-lightbox-caption {
      text-align: center;
      max-width: 920px;
      margin: 0 auto;
      padding: 0.95rem 1.25rem;
      border-radius: 16px;
      background: rgba(255, 255, 255, 0.06);
      border: 1px solid rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
    }

    .gallery-lightbox-caption:empty {
      display: none;
    }

    .gallery-lightbox-caption h3 {
      font-family: 'Space Grotesk', sans-serif;
      font-size: clamp(1.05rem, 2vw, 1.35rem);
      color: #f8fafc;
      margin-bottom: 0.3rem;
      font-weight: 700;
    }

    .gallery-lightbox-caption p {
      font-size: clamp(0.86rem, 1.6vw, 0.98rem);
      color: rgba(226, 232, 240, 0.84);
      line-height: 1.55;
    }

    .gallery-lightbox-caption h3:empty,
    .gallery-lightbox-caption p:empty {
      display: none;
    }

    .gallery-lightbox-close,
    .gallery-lightbox-nav {
      border: 1px solid rgba(255, 255, 255, 0.14);
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(8px);
      transition: background 0.22s ease, transform 0.22s ease, border-color 0.22s ease, box-shadow 0.22s ease;
    }

    .gallery-lightbox-close {
      width: 46px;
      height: 46px;
      border-radius: 14px;
      font-size: 1rem;
      flex-shrink: 0;
    }

    .gallery-lightbox-close:hover,
    .gallery-lightbox-nav:hover:not(:disabled) {
      background: rgba(255, 255, 255, 0.16);
      border-color: rgba(255, 255, 255, 0.24);
      transform: translateY(-1px);
      box-shadow: 0 10px 24px rgba(0, 0, 0, 0.25);
    }

    .gallery-lightbox-nav {
      width: 52px;
      height: 52px;
      border-radius: 16px;
      font-size: 1rem;
      flex-shrink: 0;
    }

    .gallery-lightbox-nav:disabled {
      opacity: 0.32;
      cursor: not-allowed;
      pointer-events: none;
    }

    body.gallery-lightbox-open {
      overflow: hidden;
    }

    body.gallery-lightbox-open .site-header {
      visibility: hidden;
      opacity: 0;
      pointer-events: none;
    }

    /* Lightbox premium — page Galerie */
    .gallery-lightbox--premium .gallery-lightbox-backdrop {
      background:
        radial-gradient(circle at 15% 20%, rgba(14, 165, 233, 0.22) 0%, transparent 45%),
        radial-gradient(circle at 85% 80%, rgba(37, 99, 235, 0.18) 0%, transparent 42%),
        rgba(2, 6, 23, 0.94);
      backdrop-filter: blur(18px);
      animation: galleryBackdropIn 0.5s ease;
    }

    @keyframes galleryBackdropIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    .gallery-lightbox--premium .gallery-lightbox-shell {
      gap: 1rem;
      width: min(92vw, 1280px);
      height: min(88dvh, 100%);
      transform: translateY(24px) scale(0.96);
      opacity: 0;
      transition:
        transform 0.5s cubic-bezier(0.22, 1, 0.36, 1),
        opacity 0.45s ease;
    }

    .gallery-lightbox--premium.is-active .gallery-lightbox-shell {
      transform: translateY(0) scale(1);
      opacity: 1;
    }

    .gallery-lightbox-progress {
      height: 3px;
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.1);
      overflow: hidden;
    }

    .gallery-lightbox-progress span {
      display: block;
      height: 100%;
      width: 0;
      border-radius: inherit;
      background: linear-gradient(90deg, var(--primary), var(--secondary));
      transition: width 0.45s cubic-bezier(0.22, 1, 0.36, 1);
      box-shadow: 0 0 12px rgba(14, 165, 233, 0.45);
    }

    .gallery-lightbox--premium .gallery-lightbox-title-inline {
      color: rgba(248, 250, 252, 0.92);
      font-size: 0.88rem;
      font-weight: 600;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: min(42vw, 420px);
      padding-left: 0.5rem;
      border-left: 1px solid rgba(255, 255, 255, 0.14);
    }

    .gallery-lightbox--premium .gallery-lightbox-media {
      border-radius: 28px;
      max-height: calc(78dvh - 7rem);
      background:
        linear-gradient(145deg, rgba(15, 23, 42, 0.82), rgba(30, 41, 59, 0.62));
      border: 1px solid rgba(255, 255, 255, 0.14);
      box-shadow:
        0 40px 100px rgba(0, 0, 0, 0.55),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
    }

    .gallery-lightbox--premium .gallery-lightbox-media img {
      border-radius: 18px;
      max-width: min(100%, 980px);
      max-height: min(calc(78dvh - 10rem), 70vh);
      transition:
        opacity 0.32s ease,
        transform 0.45s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .gallery-lightbox--premium .gallery-lightbox-media img.is-changing {
      opacity: 0;
      transform: scale(0.97);
    }

    .gallery-lightbox--premium .gallery-lightbox-media img.is-entering {
      animation: galleryImageEnter 0.48s cubic-bezier(0.22, 1, 0.36, 1);
    }

    @keyframes galleryImageEnter {
      from {
        opacity: 0;
        transform: scale(0.94) translateY(8px);
      }
      to {
        opacity: 1;
        transform: scale(1) translateY(0);
      }
    }

    .gallery-lightbox--premium .gallery-lightbox-nav {
      width: 56px;
      height: 56px;
      border-radius: 18px;
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.16);
      box-shadow: 0 12px 32px rgba(0, 0, 0, 0.28);
    }

    .gallery-lightbox--premium .gallery-lightbox-close {
      width: 48px;
      height: 48px;
      border-radius: 16px;
      background: rgba(255, 255, 255, 0.1);
    }

    .gallery-lightbox-topbar,
    .gallery-lightbox-progress,
    .gallery-lightbox-thumbs,
    .gallery-lightbox-caption {
      flex-shrink: 0;
    }

    .gallery-lightbox-thumbs {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      gap: 0.55rem;
      flex-wrap: nowrap;
      overflow-x: auto;
      width: 100%;
      min-height: 56px;
      padding: 0.35rem 0.25rem 0.15rem;
      scrollbar-width: thin;
      scrollbar-color: rgba(255, 255, 255, 0.25) transparent;
    }

    .gallery-lightbox-thumbs:empty {
      display: none;
    }

    .gallery-lightbox-thumb {
      flex: 0 0 auto;
      width: 72px;
      height: 54px;
      padding: 0;
      border: 2px solid transparent;
      border-radius: 12px;
      overflow: hidden;
      cursor: pointer;
      background: rgba(255, 255, 255, 0.06);
      opacity: 0.65;
      transform: scale(0.94);
      transition:
        opacity 0.25s ease,
        transform 0.25s ease,
        border-color 0.25s ease,
        box-shadow 0.25s ease;
    }

    .gallery-lightbox-thumb:hover {
      opacity: 0.92;
      transform: scale(1);
    }

    .gallery-lightbox-thumb.is-active {
      opacity: 1;
      transform: scale(1);
      border-color: rgba(56, 189, 248, 0.85);
      box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.22);
    }

    .gallery-lightbox-thumb img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .gallery-lightbox--premium .gallery-lightbox-caption {
      border-radius: 20px;
      padding: 1.1rem 1.35rem;
      background: rgba(15, 23, 42, 0.55);
      border: 1px solid rgba(255, 255, 255, 0.12);
      transform: translateY(8px);
      opacity: 0;
      transition:
        transform 0.4s cubic-bezier(0.22, 1, 0.36, 1),
        opacity 0.4s ease;
    }

    .gallery-lightbox--premium.is-active .gallery-lightbox-caption:not([hidden]) {
      transform: translateY(0);
      opacity: 1;
      transition-delay: 0.12s;
    }

    /* CTA Section */
    .cta {
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .cta::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 600px;
      height: 600px;
      background: radial-gradient(circle, rgba(0, 168, 204, 0.12) 0%, transparent 70%);
      pointer-events: none;
    }

    .cta-content {
      position: relative;
      z-index: 1;
    }

    .cta h2 {
      font-family: 'Space Grotesk', sans-serif;
      font-size: clamp(2rem, 5vw, 3.5rem);
      font-weight: 700;
      margin-bottom: 1rem;
    }

    .cta h2 span {
      background: var(--gradient-1);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .cta p {
      color: var(--text-muted);
      font-size: 1.2rem;
      margin-bottom: 2.5rem;
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
    }

    /* Footer */
    footer {
      background: var(--bg-card);
      padding: 4rem 5% 2rem;
      border-top: 1px solid var(--border);
    }

    .footer-content {
      max-width: 1180px;
      margin: 0 auto 3rem;
    }

    .footer-brand {
      text-align: center;
      padding-bottom: 2.5rem;
      margin-bottom: 2.5rem;
      border-bottom: 1px solid var(--border);
    }

    .footer-brand .logo {
      display: inline-flex;
      justify-content: center;
      margin-bottom: 1rem;
    }

    .footer-brand .social-links {
      justify-content: center;
    }

    .footer-columns {
      width: 100%;
      max-width: 1020px;
      margin-inline: auto;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: clamp(2rem, 4vw, 3rem);
      align-items: start;
    }

    .footer-column {
      min-width: 0;
    }

    .footer-brand p {
      color: var(--text-muted);
      font-size: 0.95rem;
      margin-bottom: 1.5rem;
    }

    .social-links {
      display: flex;
      gap: 1rem;
      align-items: center;
    }

    .footer-flag {
      width: 45px;
      height: 45px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      overflow: hidden;
    }

    .footer-flag img {
      width: 28px;
      height: auto;
      display: block;
      object-fit: contain;
    }

    .social-links a {
      width: 45px;
      height: 45px;
      background: rgba(0, 168, 204, 0.12);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--text-muted);
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .social-links a i {
      font-size: 1.1rem;
      line-height: 1;
    }

    .social-links a:hover {
      background: var(--gradient-1);
      color: #fff;
      transform: translateY(-3px);
    }

    .social-links--topbar {
      gap: 0.65rem;
    }

    .social-links--topbar .footer-flag {
      width: 36px;
      height: 36px;
    }

    .social-links--topbar .footer-flag img {
      width: 22px;
    }

    .social-links--topbar a {
      width: 36px;
      height: 36px;
      border-radius: 10px;
    }

    .social-links--topbar a i {
      font-size: 0.95rem;
    }

    .site-header:not(.scrolled) .social-links--topbar a {
      background: rgba(255, 255, 255, 0.12);
      color: rgba(248, 250, 252, 0.92);
    }

    .site-header:not(.scrolled) .social-links--topbar a:hover {
      background: var(--gradient-1);
      color: #fff;
    }

    .site-header.scrolled .social-links--topbar a {
      background: rgba(0, 168, 204, 0.12);
      color: var(--text-muted);
    }

    .site-header.scrolled .social-links--topbar a:hover {
      background: var(--gradient-1);
      color: #fff;
    }

    .footer-column h4 {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 1.1rem;
      margin-bottom: 1.25rem;
      text-align: left;
    }

    .footer-column ul {
      list-style: none;
    }

    .footer-column ul li {
      margin-bottom: 0.8rem;
    }

    .footer-column ul a {
      display: inline-flex;
      align-items: center;
      gap: 0.6rem;
      color: var(--text-muted);
      text-decoration: none;
      font-size: 0.95rem;
      transition: color 0.3s ease;
      line-height: 1.4;
    }

    .footer-column ul a i {
      flex-shrink: 0;
      width: 1rem;
      text-align: center;
      color: var(--primary);
    }

    .footer-column ul a.footer-link-with-icon {
      align-items: flex-start;
      white-space: normal;
    }

    .footer-column ul a.footer-link-with-icon span {
      min-width: 0;
      word-break: break-word;
    }

    .footer-column ul a:hover {
      color: var(--primary);
    }

    .footer-bottom {
      text-align: center;
      padding-top: 2rem;
      border-top: 1px solid var(--border);
      color: var(--text-muted);
      font-size: 0.9rem;
    }

    img {
      max-width: 100%;
      height: auto;
    }

    /* Scroll Animations */
    .reveal {
      opacity: 0;
      transform: translateY(50px);
      transition: all 0.8s ease;
    }

    .reveal.active {
      opacity: 1;
      transform: translateY(0);
    }

    /* Override pour animation hover des cartes services */
    .services .service-card.reveal.active:hover {
      transform: translateY(-12px) scale(1.05) rotate3d(1, 1, 0, 6deg);
    }

    /* Mobile Responsive */
    @@media (max-width: 1100px) {
      :root {
        --header-logo-height: 4.5rem;
        --header-nav-padding-y: 1.7rem;
      }

      .nav-links {
        display: none;
      }

      .nav-espace-client-desktop {
        display: none;
      }

      .nav-espace-client-mobile {
        display: inline-flex;
      }

      .nav-mobile-right {
        display: flex;
      }

      .menu-toggle {
        display: flex;
      }

      nav {
        justify-content: space-between;
        padding: 0.85rem 4%;
      }

      nav.scrolled {
        padding: 0.65rem 4%;
      }

      .site-header.mobile-menu-open {
        z-index: 11050;
      }

      nav.mobile-menu-open .nav-links {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        text-align: right;
        position: fixed;
        inset: 0;
        width: 100%;
        height: 100vh;
        height: 100dvh;
        margin: 0;
        padding: calc(5.5rem + var(--top-bar-height)) 1.5rem 2rem;
        gap: 1.15rem;
        background: rgba(15, 23, 42, 0.97);
        z-index: 11040;
        list-style: none;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
      }

      nav.mobile-menu-open .nav-links li {
        width: 100%;
        max-width: 280px;
      }

      nav.mobile-menu-open .nav-links a {
        display: block;
        width: 100%;
        color: #e2e8f0 !important;
        font-size: 1.05rem;
        font-weight: 500;
        padding: 0.35rem 0;
      }

      nav.mobile-menu-open .nav-links a:hover {
        color: #ffffff !important;
      }

      nav.mobile-menu-open .nav-links a::after {
        display: none;
      }

      nav.mobile-menu-open .logo,
      nav.mobile-menu-open .nav-mobile-right {
        position: relative;
        z-index: 11060;
      }

      nav.mobile-menu-open .nav-espace-client-mobile {
        color: #e2e8f0;
      }

      body.mobile-menu-open {
        overflow: hidden;
      }

      .logo img,
      .logo .logo-img {
        height: 72px;
      }
    }

    @@media (max-width: 1024px) {
      section {
        padding: 4.5rem 4%;
      }

      .contact-section {
        padding: 4.5rem 4%;
      }

      .academy-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .hero {
        flex-direction: column;
        justify-content: center;
        text-align: center;
        padding: calc(5.5rem + var(--top-bar-height)) 4% 2rem;
        min-height: auto;
      }

      .hero-visual {
        display: block;
        position: relative;
        right: auto;
        top: auto;
        transform: none;
        width: 100%;
        max-width: 340px;
        margin: 1.5rem auto 0;
        opacity: 1;
        animation: none;
      }

      .hero-card {
        padding: 1.35rem;
      }

      .hero-card-icon {
        width: 64px;
        height: 64px;
        font-size: 1.6rem;
        margin-bottom: 1rem;
      }

      .hero-card h3 {
        font-size: 1.2rem;
      }

      .hero-card p {
        font-size: 0.88rem;
        margin-bottom: 1rem;
      }

      .hero-stats {
        gap: 1.25rem;
        padding-top: 1rem;
      }

      .stat-value {
        font-size: 1.5rem;
      }

      .hero-content {
        max-width: 100%;
      }

      .hero p {
        margin-left: auto;
        margin-right: auto;
      }

      .hero-buttons {
        justify-content: center;
      }

      .process-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 2.5rem 1.5rem;
      }

      .process-step::after {
        display: none !important;
      }

      .process--page .process-timeline::before {
        left: 27px;
        transform: none;
      }

      .process--page .process-timeline-item {
        grid-template-columns: auto minmax(0, 1fr);
        gap: 1.35rem;
        padding: 0.85rem 0;
      }

      .process--page .process-timeline-item:nth-child(odd) .process-timeline-card,
      .process--page .process-timeline-item:nth-child(even) .process-timeline-card {
        grid-column: 2;
        grid-row: 1;
        justify-self: stretch;
        max-width: none;
      }

      .process--page .process-timeline-marker {
        grid-column: 1;
        grid-row: 1;
        width: 54px;
        height: 54px;
      }

      .process--page .process-timeline-header {
        margin-bottom: 2.5rem;
        text-align: left;
      }

      .gallery-page .gallery-grid-pro {
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
      }

      .gallery-page .gallery-tile__visual {
        max-height: 170px;
      }

      .about-body {
        padding: 0 4% 4rem;
        margin-top: -1.25rem;
      }

      .about-layout {
        grid-template-columns: 1fr;
        gap: 1.25rem;
      }

      .about-sidebar {
        position: static;
      }

      .about-main__card {
        padding: 1.85rem 1.75rem 2rem;
        border-radius: 22px;
      }

      .laboratory-body {
        padding: 0 4% 4rem;
      }

      .laboratory-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
      }

      .features-container {
        grid-template-columns: 1fr;
        gap: 2.5rem;
      }

      .partners {
        padding: 4.5rem 4% 5rem;
      }

      .partners-showcase__head {
        margin-bottom: 2rem;
      }

      .partners-showcase {
        padding: 1.35rem 0.85rem 1rem;
        border-radius: 22px;
      }

      .partners-showcase__hint {
        font-size: 0.74rem;
        padding: 0 0.5rem;
        text-align: center;
      }

      .partners-card {
        width: clamp(168px, 44vw, 220px);
        min-height: clamp(118px, 26vw, 140px);
        padding: 1.15rem 1rem 0.95rem;
        border-radius: 18px;
      }

      .partners-card__logo {
        max-height: 58px;
      }

      .partners-card__name {
        font-size: 0.66rem;
      }

      .partners-carousel__track {
        gap: 1rem;
        animation-duration: 36s;
      }

      .gallery-grid {
        grid-template-columns: repeat(2, 1fr);
      }

      .inner-body.services--page {
        padding: 2.5rem 1.25rem 3.5rem;
      }

      .services-honeycomb {
        --hex-gap-x: clamp(0.5rem, 1.8vw, 1.5rem);
        --hex-size: calc((100% - 2 * var(--hex-gap-x)) / 3);
        --hex-overlap: calc(var(--hex-size) * 0.21);
        width: 100%;
        max-width: 100%;
      }

      .services-honeycomb__grid {
        width: 100%;
        max-width: 100%;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        padding: 3rem 0.5rem 3.25rem;
        overflow: visible;
      }

      .services-hex-item,
      .services-hex-shell-wrap {
        width: 100%;
      }

      .services-honeycomb__bg-hex--1 { width: 260px; height: 300px; }
      .services-honeycomb__bg-hex--3 { width: 320px; height: 368px; }

      .services-honeycomb__center-glow {
        width: min(230px, 62%);
      }

      .services-honeycomb__center-label {
        font-size: clamp(0.82rem, 3.2vw, 1.1rem);
      }

      .inner-hero,
      .about-hero,
      .laboratory-hero,
      .academy-hero,
      .faq-hero {
        min-height: auto;
        padding: calc(var(--site-header-offset) + var(--inner-hero-gap)) 1.25rem 3rem;
      }

      .service-detail-page .inner-hero.inner-hero--has-summary {
        padding-top: calc(var(--site-header-offset) + var(--inner-hero-gap) + 1rem);
        min-height: auto;
      }

      .inner-body {
        padding: 0 1.25rem 3.5rem;
      }

      .academy-body {
        margin-top: -1.25rem;
        padding: 0 1.25rem 3.5rem;
      }

      .academy-filters {
        gap: 0.5rem;
        padding: 0.65rem;
        overflow-x: auto;
        flex-wrap: nowrap;
        -webkit-overflow-scrolling: touch;
      }

      .academy-filter {
        flex-shrink: 0;
        font-size: 0.82rem;
        padding: 0.55rem 0.8rem;
      }

      .academy-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
      }

      .academy-hero-stats {
        gap: 0.65rem;
      }

      .academy-hero-stat {
        min-width: calc(50% - 0.35rem);
        flex: 1 1 calc(50% - 0.35rem);
      }

      .logo img,
      .logo .logo-img {
        height: 80px;
      }

      .footer-brand .logo img {
        height: 96px;
      }
    }

    @@media (max-width: 768px) {
      :root {
        --header-logo-height: 5rem;
        --header-nav-padding-y: 1.7rem;
      }

      html {
        font-size: 93.75%;
      }

      nav {
        justify-content: space-between;
        padding: 0.85rem 4%;
      }

      nav.scrolled {
        padding: 0.65rem 4%;
      }

      .top-bar {
        justify-content: center;
        padding: 0.45rem 1.25rem 0.1rem;
      }

      .top-bar-inner {
        gap: 0.65rem;
      }

      section {
        padding: 2.75rem 1rem;
      }

      .contact-section {
        padding: 2.75rem 1rem;
      }

      .section-header {
        margin-bottom: 2rem;
      }

      .section-subtitle {
        font-size: 0.95rem;
      }

      .hero {
        min-height: auto;
        justify-content: center;
        padding: calc(6.5rem + var(--top-bar-height) + 1.5rem) 1rem 1.5rem;
      }

      .hero-badge {
        font-size: 0.72rem;
        padding: 0.35rem 0.75rem;
        margin-bottom: 0.65rem;
      }

      .hero h1 {
        font-size: clamp(1.55rem, 6.8vw, 1.95rem);
        margin-bottom: 0.65rem;
        line-height: 1.12;
      }

      .hero p {
        font-size: 0.9rem;
        line-height: 1.5;
        margin-bottom: 1rem;
      }

      .hero-buttons {
        flex-direction: column;
        width: 100%;
        max-width: 280px;
        margin-left: auto;
        margin-right: auto;
        gap: 0.55rem;
      }

      .hero-buttons .btn {
        width: 100%;
        justify-content: center;
        padding: 0.7rem 1.25rem;
        font-size: 0.9rem;
      }

      .hero-visual {
        display: block;
        position: relative;
        right: auto;
        top: auto;
        transform: none;
        width: 100%;
        max-width: 100%;
        margin: 1.35rem auto 0;
        opacity: 1;
        animation: none;
      }

      .hero-card {
        padding: 1.15rem;
        border-radius: 18px;
      }

      .hero-card-icon {
        width: 52px;
        height: 52px;
        font-size: 1.35rem;
        margin-bottom: 0.75rem;
      }

      .hero-card h3 {
        font-size: 1.05rem;
      }

      .hero-card p {
        font-size: 0.82rem;
        margin-bottom: 0.75rem;
      }

      .hero-stats {
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.85rem;
        padding-top: 0.75rem;
      }

      .stat-value {
        font-size: 1.35rem;
      }

      .slider-dots {
        bottom: 0.65rem;
      }

      .slider-dot {
        width: 9px;
        height: 9px;
      }

      .logo img,
      .logo .logo-img {
        height: 56px;
      }

      .footer-brand .logo img {
        height: 110px;
      }

      .services-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
      }

      .service-card {
        padding: 1.75rem 1.5rem;
      }

      .inner-body.services--page {
        padding: 2rem 0.65rem 3rem;
        justify-content: flex-start;
        overflow-x: clip;
      }

      .services-honeycomb {
        --hex-gap-x: clamp(0.4rem, 2.2vw, 1rem);
        --hex-size: calc((100% - 2 * var(--hex-gap-x)) / 3);
        --hex-overlap: calc(var(--hex-size) * 0.2);
        width: 100%;
        max-width: 100%;
        padding: 0.5rem 0 1.25rem;
      }

      .services-honeycomb__grid {
        width: 100%;
        max-width: 100%;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        column-gap: var(--hex-gap-x);
        row-gap: 0.35rem;
        padding: 2rem 0.25rem 2.25rem;
        overflow: visible;
      }

      .services-hex-item {
        width: 100%;
      }

      .services-hex-shell-wrap {
        width: 100%;
        margin: 0 auto;
      }

      .services-honeycomb__bg-hex--1,
      .services-honeycomb__bg-hex--2,
      .services-honeycomb__bg-hex--4,
      .services-honeycomb__bg-hex--5,
      .services-honeycomb__bg-hex--6 {
        opacity: 0.2;
      }

      .services-honeycomb__center-glow {
        width: min(195px, 66%);
      }

      .services-honeycomb__center-label {
        font-size: clamp(0.74rem, 3.8vw, 0.95rem);
        letter-spacing: 0.1em;
      }

      .services-hex-label {
        width: 62%;
        max-width: 62%;
        font-size: clamp(0.62rem, 2.8vw, 0.84rem);
        letter-spacing: 0.02em;
      }

      .services-hex-label.is-compact {
        width: 56%;
        max-width: 56%;
        font-size: clamp(0.54rem, 2.4vw, 0.72rem);
        line-height: 1.14;
      }

      .services-hex-placeholder {
        font-size: clamp(1.35rem, 7vw, 2rem);
      }

      .service-detail-body {
        padding: 0 1.25rem 3.5rem;
      }

      .service-detail-content {
        padding: 1.5rem 1.35rem 2rem;
      }

      .process-grid {
        grid-template-columns: 1fr;
        gap: 2rem;
      }

      .process-step::after {
        display: none !important;
      }

      .process--page .process-timeline-header {
        margin-bottom: 2rem;
      }

      .process--page .process-timeline-card {
        padding: 1.45rem 1.35rem 1.35rem;
        border-radius: 20px;
      }

      .process--page .process-timeline-card h3 {
        font-size: 1.2rem;
      }

      .gallery-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
      }

      .gallery-page .gallery-grid-pro {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.85rem;
      }

      .gallery-page .gallery-tile__visual {
        max-height: 155px;
        border-radius: 14px;
      }

      .gallery-showcase__head {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.85rem;
      }

      .gallery-showcase__hint {
        text-align: left;
        max-width: none;
      }

      .about-body {
        padding: 0 1.25rem 3.5rem;
        margin-top: -1rem;
      }

      .about-main__head {
        flex-direction: column;
        align-items: flex-start;
      }

      .about-main__meta {
        justify-content: flex-start;
      }

      .about-main__card {
        padding: 1.5rem 1.25rem 1.65rem;
        border-radius: 20px;
      }

      .about-main__content {
        padding-left: 1rem;
      }

      .laboratory-body {
        padding: 0 1.25rem 3.5rem;
      }

      .laboratory-grid {
        grid-template-columns: 1fr;
        gap: 0.85rem;
      }

      .laboratory-card {
        border-radius: 18px;
      }

      .about-sidebar__card {
        border-radius: 20px;
        padding: 1.1rem;
      }

      .about-sidebar__mosaic {
        max-height: none;
      }

      .about-modal {
        padding: 0.5rem;
      }

      .about-modal__close {
        top: 0.25rem;
        right: 0.25rem;
      }

      .gallery-item {
        aspect-ratio: 16/10;
      }

      .gallery-overlay {
        opacity: 1;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.75), transparent 55%);
      }

      .gallery-lightbox {
        padding: 0.5rem;
      }

      .gallery-lightbox-shell {
        width: 100%;
        height: 92dvh;
        gap: 0.65rem;
      }

      .gallery-lightbox-stage {
        position: relative;
        grid-template-columns: minmax(0, 1fr);
        grid-template-rows: minmax(0, 1fr);
        gap: 0.55rem;
      }

      .gallery-lightbox-media {
        max-height: calc(92dvh - 9rem);
        border-radius: 18px;
      }

      .gallery-lightbox-media img {
        max-height: min(calc(92dvh - 11rem), 66vh);
        border-radius: 12px;
      }

      .gallery-lightbox-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 42px;
        height: 42px;
        border-radius: 12px;
        z-index: 2;
      }

      .gallery-lightbox-prev {
        left: 0.45rem;
      }

      .gallery-lightbox-next {
        right: 0.45rem;
      }

      .gallery-lightbox-close {
        width: 40px;
        height: 40px;
        border-radius: 12px;
      }

      .gallery-lightbox-caption {
        padding: 0.8rem 1rem;
        border-radius: 14px;
      }

      .features-card {
        padding: 2rem 1.5rem;
      }

      .features-card-icon {
        width: 90px;
        height: 90px;
        font-size: 2.25rem;
      }

      .contact-row {
        grid-template-columns: minmax(0, 1fr) !important;
        gap: 1.5rem;
      }

      .contact-row--single {
        max-width: none;
      }

      .contact-card,
      .contact-form-wrapper {
        padding: 1.65rem 1.5rem;
        border-radius: 22px;
      }

      .contact-glass-item {
        padding: 0.85rem 0.95rem;
      }

      .contact-form-grid {
        grid-template-columns: 1fr;
      }

      .contact-actions {
        justify-content: stretch;
      }

      .footer-columns {
        grid-template-columns: 1fr;
        gap: 0;
        max-width: 26rem;
        margin-inline: auto;
      }

      .footer-brand {
        margin-bottom: 2rem;
        padding-bottom: 2rem;
      }

      .footer-column {
        min-width: 0;
        padding: 1.25rem 0;
      }

      .footer-column + .footer-column {
        border-top: 1px solid var(--border);
      }

      .footer-column h4 {
        margin-bottom: 0.85rem;
        font-size: 1rem;
      }

      .footer-column ul a {
        font-size: 0.9rem;
      }

      .footer-column ul a.footer-link-with-icon {
        white-space: normal;
        justify-content: flex-start;
        align-items: flex-start;
        text-align: left;
      }

      .footer-column ul a.footer-link-with-icon i {
        margin-top: 0.15rem;
      }

      .social-links {
        justify-content: center;
      }

      .footer-bottom {
        font-size: 0.85rem;
        line-height: 1.6;
      }

      .shape:nth-child(1) { width: 180px; height: 180px; }
      .shape:nth-child(2) { width: 120px; height: 120px; }
      .shape:nth-child(3) { width: 90px; height: 90px; }
    }

    @@media (max-width: 480px) {
      :root {
        --header-logo-height: 3.5rem;
      }

      html {
        font-size: 90%;
      }

      .hero {
        padding: calc(6rem + var(--top-bar-height) + 1.25rem) 0.85rem 1.25rem;
      }

      .hero h1 {
        font-size: clamp(1.45rem, 7.5vw, 1.8rem);
      }

      .hero p {
        font-size: 0.86rem;
      }

      .section-title {
        font-size: clamp(1.65rem, 7vw, 2rem);
      }

      .hero-card-icon {
        width: 64px;
        height: 64px;
        font-size: 1.6rem;
      }

      .process-number {
        width: 64px;
        height: 64px;
        font-size: 1.4rem;
      }

      .process--page .process-timeline-marker {
        width: 48px;
        height: 48px;
        box-shadow:
          0 0 0 6px var(--bg),
          0 8px 20px rgba(2, 132, 199, 0.16);
      }

      .process--page .process-timeline-number {
        font-size: 0.82rem;
      }

      .process--page .process-timeline::before {
        left: 23px;
      }

      .process--page .process-timeline-icon {
        width: 42px;
        height: 42px;
        font-size: 1rem;
        border-radius: 12px;
      }

      .inner-body.services--page {
        padding: 1.75rem 0.5rem 2.75rem;
      }

      .services-honeycomb {
        --hex-gap-x: clamp(0.25rem, 1.6vw, 0.55rem);
        --hex-size: calc((100% - 2 * var(--hex-gap-x)) / 3);
        --hex-overlap: calc(var(--hex-size) * 0.17);
        padding: 0.25rem 0 1rem;
      }

      .services-honeycomb__grid {
        row-gap: 0.2rem;
        padding: 1.75rem 0.15rem 2rem;
      }

      .services-honeycomb__center-glow {
        width: min(170px, 68%);
      }

      .services-honeycomb__center-label {
        font-size: clamp(0.68rem, 4vw, 0.88rem);
        letter-spacing: 0.08em;
        padding: 0 6%;
      }

      .services-hex-label {
        width: 66%;
        max-width: 66%;
        font-size: clamp(0.56rem, 3.2vw, 0.78rem);
      }

      .services-hex-label.is-compact {
        width: 60%;
        max-width: 60%;
        font-size: clamp(0.5rem, 2.8vw, 0.66rem);
        line-height: 1.12;
      }

      .services-hex-placeholder {
        font-size: clamp(1.1rem, 8vw, 1.65rem);
      }

      .faq-body {
        padding: 0 1rem 3.5rem;
      }

      .faq-card__trigger {
        grid-template-columns: auto 1fr auto;
        gap: 0.75rem 1rem;
        padding: 1.15rem 1.1rem;
      }

      .faq-card__index {
        font-size: 0.72rem;
        padding: 0.35rem 0.5rem;
        min-width: 2.1rem;
      }

      .faq-card__answer {
        margin: 0 0.65rem 0.65rem;
        padding: 1.1rem 1.15rem;
      }

      .faq-cta {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
        padding: 1.5rem 1.25rem;
      }

      .faq-cta__btn {
        justify-content: center;
      }

      .contact-item {
        align-items: flex-start;
      }

      .nav-espace-client-mobile {
        font-size: 0.8rem;
        max-width: 5.5rem;
        overflow: hidden;
        text-overflow: ellipsis;
      }
    }

    /* Effets hover uniquement sur desktop (évite les bugs tactile) */
    @@media (hover: hover) and (pointer: fine) {
      .service-card:hover {
        transform: translateY(-12px) scale(1.05) rotate3d(1, 1, 0, 6deg);
      }

      .services .service-card.reveal.active:hover {
        transform: translateY(-12px) scale(1.05) rotate3d(1, 1, 0, 6deg);
      }

      .process--page .process-timeline-item:hover .process-timeline-card {
        transform: translateY(-6px);
        box-shadow: 0 26px 52px rgba(15, 23, 42, 0.12);
        border-color: rgba(14, 165, 233, 0.28);
      }

      .process--page .process-timeline-item:hover .process-timeline-marker {
        transform: scale(1.08);
        border-color: var(--secondary);
        box-shadow:
          0 0 0 8px var(--bg),
          0 12px 32px rgba(2, 132, 199, 0.28);
      }

      .process--page .process-timeline-item:hover .process-timeline-icon {
        background: var(--gradient-1);
        color: #fff;
      }

      .faq-card:hover {
        transform: translateY(-2px);
      }

      .faq-card__trigger:hover .faq-card__question {
        color: #e0f2fe;
      }

      .services-hex-item:has(.services-hex-link:hover) {
        z-index: 6;
      }

      .services-hex-link:hover {
        transform: translateY(-10px);
      }

      .services-hex-link:hover .services-hex-shell-wrap {
        filter:
          drop-shadow(0 0 0 2px rgba(255, 255, 255, 0.98))
          drop-shadow(0 0 22px rgba(56, 189, 248, 0.6))
          drop-shadow(0 14px 32px rgba(0, 40, 70, 0.42));
        transform: scale(1.07);
      }

      .services-hex-link:hover .services-hex-shell-wrap:has(.services-hex-shell.has-image) {
        filter:
          drop-shadow(0 0 22px rgba(56, 189, 248, 0.55))
          drop-shadow(0 14px 32px rgba(0, 40, 70, 0.32));
      }

      .services-hex-link:hover .services-hex-shell.has-image {
        background-size: 115%;
        filter: brightness(1.12) saturate(1.08);
      }

      .services-hex-link:hover .services-hex-shell.is-placeholder {
        background-image: linear-gradient(135deg, #0284c7 0%, #0369a1 55%, #0c4a6e 100%);
      }

      .services-hex-link:hover .services-hex-overlay {
        background: linear-gradient(
          180deg,
          rgba(2, 132, 199, 0.35) 0%,
          rgba(0, 18, 32, 0.58) 42%,
          rgba(0, 18, 32, 0.58) 58%,
          rgba(2, 132, 199, 0.35) 100%
        );
      }

      .services-hex-link:hover .services-hex-shell.has-image .services-hex-overlay {
        background: linear-gradient(
          180deg,
          rgba(2, 132, 199, 0.12) 0%,
          rgba(0, 18, 32, 0.22) 42%,
          rgba(0, 18, 32, 0.22) 58%,
          rgba(2, 132, 199, 0.12) 100%
        );
      }

      .services-hex-link:hover .services-hex-label {
        transform: translate(-50%, -54%);
        letter-spacing: 0.09em;
        color: #e0f2fe;
        text-shadow:
          0 0 14px rgba(56, 189, 248, 0.65),
          0 2px 10px rgba(0, 0, 0, 0.55);
      }

      .services-hex-link:hover .services-hex-placeholder {
        transform: scale(1.12);
        color: rgba(255, 255, 255, 0.58);
      }
    }

    @@media (hover: none), (pointer: coarse) {
      .service-card:hover {
        transform: none;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
        filter: none;
      }

      .service-card:hover::before {
        opacity: 0;
        animation: none;
      }

      .gallery-item:hover img {
        transform: none;
      }
    }

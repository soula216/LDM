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

    @@media (min-width: 769px) {
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
      color: var(--text);
      border: 1px solid var(--primary);
    }

    .btn-secondary:hover {
      border-color: var(--primary);
      color: var(--primary);
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
      background: var(--gradient-2);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
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

    /* Pages internes (Services, Process, Academy) */
    .inner-page,
    .academy-page {
      min-height: 100vh;
    }

    .inner-hero {
      position: relative;
      min-height: 48vh;
      display: flex;
      align-items: flex-end;
      padding: calc(8rem + var(--top-bar-height)) 5% 4rem;
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
      --hex-size: 200px;
      --hex-overlap: calc(var(--hex-size) * 0.22);
      --hex-gap-x: 2rem;
      position: relative;
      width: 100%;
      max-width: 960px;
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
      width: min(280px, 58%);
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
      font-size: 2.5rem;
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
      font-size: clamp(0.76rem, 1.65vw, 0.95rem);
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
      width: 52%;
      max-width: 52%;
      font-size: clamp(0.64rem, 1.35vw, 0.8rem);
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
      padding-top: calc(10.5rem + var(--top-bar-height));
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

    .academy-hero {
      position: relative;
      min-height: 58vh;
      display: flex;
      align-items: flex-end;
      padding: calc(8rem + var(--top-bar-height)) 5% 4.5rem;
      overflow: hidden;
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

    /* Contact Section */
    .contact-section {
      padding: 6rem 5%;
      position: relative;
      overflow: hidden;
    }

    .contact-section::before {
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

    .contact-grid {
      max-width: 1200px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: minmax(0, 1.2fr) minmax(0, 1.3fr);
      gap: 3rem;
      align-items: stretch;
      position: relative;
      z-index: 1;
    }

    .contact-card {
      background: rgba(15, 23, 42, 0.9);
      border-radius: 24px;
      border: 1px solid rgba(148, 163, 184, 0.6);
      padding: 2.5rem;
      box-shadow: 0 30px 80px rgba(15, 23, 42, 0.85);
    }

    .contact-card h2 {
      font-family: 'Space Grotesk', sans-serif;
      font-size: clamp(2rem, 3vw, 2.6rem);
      margin-bottom: 1rem;
      color: #f9fafb;
    }

    .contact-card p {
      color: rgba(226, 232, 240, 0.9);
    }

    .contact-tag {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.35rem 1rem;
      border-radius: 999px;
      background: rgba(15, 23, 42, 0.9);
      border: 1px solid rgba(56, 189, 248, 0.4);
      color: #e0f2fe;
      font-size: 0.8rem;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      margin-bottom: 1.25rem;
    }

    .contact-items {
      margin-top: 2rem;
      display: grid;
      gap: 1.25rem;
    }

    .contact-item {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .contact-item-icon {
      width: 40px;
      height: 40px;
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: radial-gradient(circle at top left, #22c55e, #0ea5e9);
      color: #0b1120;
      box-shadow: 0 0 25px rgba(56, 189, 248, 0.5);
      flex-shrink: 0;
    }

    .contact-item-text h4 {
      font-size: 0.95rem;
      margin-bottom: 0.2rem;
      color: #e5e7eb;
    }

    .contact-item-text p {
      font-size: 0.9rem;
      color: #94a3b8;
      margin: 0;
    }

    .contact-item-text p + p {
      margin-top: 0.15rem;
    }

    .contact-form-wrapper {
      background: radial-gradient(circle at top, rgba(15, 23, 42, 0.9), rgba(15, 23, 42, 0.98));
      border-radius: 24px;
      border: 1px solid rgba(148, 163, 184, 0.6);
      padding: 2.5rem;
      box-shadow: 0 30px 80px rgba(15, 23, 42, 0.9);
    }

    .contact-form-title {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 1.3rem;
      margin-bottom: 1.5rem;
      color: #f9fafb;
    }

    .contact-form-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 1.25rem 1rem;
    }

    .contact-form-grid .full-row {
      grid-column: 1 / -1;
    }

    .contact-label {
      font-size: 0.85rem;
      color: #cbd5f5;
      margin-bottom: 0.35rem;
      display: block;
    }

    .contact-input,
    .contact-textarea,
    .contact-select {
      width: 100%;
      border-radius: 999px;
      border: 1px solid rgba(148, 163, 184, 0.6);
      background: rgba(15, 23, 42, 0.9);
      color: #e5e7eb;
      padding: 0.85rem 1rem;
      font-size: 0.9rem;
      outline: none;
      transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    .contact-textarea {
      border-radius: 18px;
      min-height: 120px;
      resize: vertical;
    }

    .contact-input::placeholder,
    .contact-textarea::placeholder {
      color: #64748b;
    }

    .contact-input:focus,
    .contact-textarea:focus,
    .contact-select:focus {
      border-color: #38bdf8;
      box-shadow: 0 0 0 1px rgba(56, 189, 248, 0.8);
      background: rgba(15, 23, 42, 0.95);
    }

    .contact-actions {
      margin-top: 1.75rem;
      display: flex;
      justify-content: flex-end;
    }

    .contact-submit {
      width: 100%;
      justify-content: center;
      background: var(--gradient-1);
      color: #f9fafb;
      border-radius: 999px;
      padding: 0.9rem 1.5rem;
      font-weight: 600;
      border: none;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      box-shadow: 0 18px 45px var(--primary-glow);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .contact-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 24px 60px var(--primary-glow);
    }

    .contact-submit i {
      font-size: 0.95rem;
    }

    /* Gallery - Nos Travaux */
    .gallery-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1.5rem;
      max-width: 1200px;
      margin: 0 auto;
    }

    .gallery-item {
      position: relative;
      border-radius: 20px;
      overflow: hidden;
      aspect-ratio: 4/3;
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
      display: grid;
      grid-template-columns: 2fr 1fr 1fr 1fr;
      gap: 3rem;
      max-width: 1200px;
      margin: 0 auto 3rem;
    }

    .footer-brand .logo {
      margin-bottom: 1rem;
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
      margin-bottom: 1.5rem;
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
      white-space: nowrap;
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

      .features-container {
        grid-template-columns: 1fr;
        gap: 2.5rem;
      }

      .gallery-grid {
        grid-template-columns: repeat(2, 1fr);
      }

      .inner-body.services--page {
        padding: 2.5rem 1.25rem 3.5rem;
      }

      .services-honeycomb {
        --hex-size: 172px;
        --hex-gap-x: 1.5rem;
      }

      .services-honeycomb__grid {
        padding: 3rem 2rem 3.25rem;
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
      .academy-hero {
        min-height: auto;
        padding: calc(6.5rem + var(--top-bar-height)) 1.25rem 3rem;
      }

      .service-detail-page .inner-hero.inner-hero--has-summary {
        padding-top: calc(8rem + var(--top-bar-height));
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

      .footer-content {
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
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

      nav.mobile-menu-open .nav-links {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        text-align: right;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100%;
        height: 100vh;
        height: 100dvh;
        padding: calc(6rem + var(--top-bar-height)) 1.25rem 2rem;
        gap: 1.5rem;
        background: rgba(15, 23, 42, 0.96);
        z-index: 999;
        list-style: none;
        overflow-y: auto;
      }

      nav.mobile-menu-open .nav-links a {
        color: #e2e8f0 !important;
        font-size: 1.1rem;
        font-weight: 500;
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
        z-index: 1001;
      }

      nav.mobile-menu-open .nav-espace-client-mobile {
        color: #e2e8f0;
      }

      body.mobile-menu-open {
        overflow: hidden;
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
        padding: 2rem 1rem 3rem;
      }

      .services-honeycomb {
        --hex-size: 148px;
        --hex-gap-x: 1.1rem;
        max-width: 100%;
      }

      .services-honeycomb__grid {
        row-gap: 0.75rem;
        padding: 2.5rem 1rem 2.75rem;
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
        width: 54%;
        max-width: 54%;
        font-size: clamp(0.68rem, 3vw, 0.84rem);
      }

      .services-hex-label.is-compact {
        width: 48%;
        max-width: 48%;
        font-size: clamp(0.58rem, 2.7vw, 0.72rem);
        line-height: 1.16;
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

      .gallery-item {
        aspect-ratio: 16/10;
      }

      .gallery-overlay {
        opacity: 1;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.75), transparent 55%);
      }

      .features-card {
        padding: 2rem 1.5rem;
      }

      .features-card-icon {
        width: 90px;
        height: 90px;
        font-size: 2.25rem;
      }

      .contact-grid {
        grid-template-columns: minmax(0, 1fr);
        gap: 1.5rem;
      }

      .contact-card,
      .contact-form-wrapper {
        padding: 1.5rem;
        border-radius: 18px;
      }

      .contact-form-grid {
        grid-template-columns: 1fr;
      }

      .contact-actions {
        justify-content: stretch;
      }

      .footer-content {
        grid-template-columns: 1fr;
        text-align: center;
        gap: 2rem;
      }

      .footer-brand .logo {
        display: flex;
        justify-content: center;
        margin-left: auto;
        margin-right: auto;
      }

      .footer-column ul a.footer-link-with-icon {
        white-space: normal;
        justify-content: center;
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

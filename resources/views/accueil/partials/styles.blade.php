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

    /* Navigation */
    nav {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      padding: 1.2rem 5%;
      display: flex;
      justify-content: space-between;
      align-items: center;
      z-index: 1000;
      transition: all 0.4s ease;
    }

    nav.scrolled {
      background: rgba(241, 245, 249, 0.95);
      backdrop-filter: blur(20px);
      padding: 0.8rem 5%;
      border-bottom: 1px solid var(--border);
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      text-decoration: none;
    }

    .logo img {
      height: 120px;
      width: auto;
      object-fit: contain;
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
      padding: 8rem 5% 4rem;
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
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 30px;
      padding: 2rem;
      position: relative;
      overflow: hidden;
    }

    .hero-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: var(--gradient-1);
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
    }

    .hero-card p {
      color: var(--text-muted);
      font-size: 0.95rem;
      margin-bottom: 1.5rem;
    }

    .hero-stats {
      display: flex;
      gap: 2rem;
      padding-top: 1.5rem;
      border-top: 1px solid rgba(255, 255, 255, 0.08);
    }

    .stat {
      text-align: center;
    }

    .stat-value {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 2rem;
      font-weight: 700;
      color: var(--primary);
    }

    .stat-label {
      font-size: 0.85rem;
      color: var(--text-muted);
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
      padding: 2.6rem 2.4rem;
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
      width: 72px;
      height: 72px;
      background: radial-gradient(circle at top left, rgba(14, 165, 233, 0.16), rgba(37, 99, 235, 0.08));
      border-radius: 22px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.9rem;
      color: var(--primary);
      margin-bottom: 1.6rem;
      position: relative;
      z-index: 1;
      transition: all 0.4s ease;
    }

    .service-card:hover .service-icon {
      background: var(--gradient-1);
      color: #fff;
      transform: translateY(-4px) scale(1.08);
      box-shadow: 0 12px 32px var(--primary-glow);
    }

    .service-card h3 {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 1.45rem;
      margin-bottom: 0.9rem;
      position: relative;
      z-index: 1;
      color: var(--dark);
      transition: transform 0.35s ease, color 0.35s ease;
    }

    .service-card h3::after {
      content: '';
      position: absolute;
      left: 0;
      bottom: -0.55rem;
      width: 0;
      height: 3px;
      border-radius: 999px;
      background: var(--gradient-1);
      transition: width 0.35s ease;
    }

    .service-card p {
      color: var(--text-muted);
      font-size: 0.96rem;
      position: relative;
      z-index: 1;
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

    .process-grid::before {
      content: '';
      position: absolute;
      top: 40px;
      left: 15%;
      width: 70%;
      height: 2px;
      background: linear-gradient(90deg, var(--primary), var(--secondary), var(--accent));
      opacity: 0.3;
    }

    .process-step {
      text-align: center;
      position: relative;
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

    @@media (max-width: 900px) {
      .contact-grid {
        grid-template-columns: minmax(0, 1fr);
      }

      .contact-form-wrapper {
        margin-top: 1.5rem;
      }
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

    @@media (max-width: 768px) {
      .gallery-grid {
        grid-template-columns: 1fr;
      }
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

    .social-links a:hover {
      background: var(--gradient-1);
      color: #fff;
      transform: translateY(-3px);
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
      color: var(--text-muted);
      text-decoration: none;
      font-size: 0.95rem;
      transition: color 0.3s ease;
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
      .hero-visual {
        display: none;
      }

      .hero {
        justify-content: center;
        text-align: center;
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
      }

      .process-grid::before {
        display: none;
      }

      .features-container {
        grid-template-columns: 1fr;
      }

      .footer-content {
        grid-template-columns: 1fr 1fr;
      }
    }

    @@media (max-width: 768px) {
      nav {
        justify-content: space-between;
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
        inset: 0;
        width: 100%;
        height: 100vh;
        padding: 7rem 5% 2rem;
        gap: 1.75rem;
        background: rgba(15, 23, 42, 0.96);
        z-index: 999;
        list-style: none;
      }

      nav.mobile-menu-open .nav-links a {
        color: #e2e8f0 !important;
        font-size: 1.15rem;
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

      .process-grid {
        grid-template-columns: 1fr;
      }

      .footer-content {
        grid-template-columns: 1fr;
        text-align: center;
      }

      .social-links {
        justify-content: center;
      }
    }

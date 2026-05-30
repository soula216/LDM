{{-- Footer --}}
<footer>
  <div class="footer-content">
    <div class="footer-brand">
      <a href="#accueil" class="logo">
        <img src="{{ asset('logo_ldm.png') }}" alt="LDM - Dentaire Moderne">
      </a>
      <p>Laboratoire de prothèse dentaire de référence en France. Excellence et innovation au service de votre sourire.</p>
      <div class="social-links">
        <a href="#"><i class="fab fa-facebook-f"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-linkedin-in"></i></a>
      </div>
    </div>
    <div class="footer-column">
      <h4>Services</h4>
      <ul>
        <li><a href="#">Couronnes</a></li>
        <li><a href="#">Bridges</a></li>
        <li><a href="#">Prothèses amovibles</a></li>
        <li><a href="#">Facettes</a></li>
      </ul>
    </div>
    <div class="footer-column">
      <h4>Entreprise</h4>
      <ul>
        <li><a href="#">À propos</a></li>
        <li><a href="#">Équipe</a></li>
        <li><a href="#">Carrières</a></li>
        <li><a href="#">Actualités</a></li>
      </ul>
    </div>
    <div class="footer-column">
      <h4>Contact</h4>
      <ul>
        <li><a href="#"><i class="fas fa-map-marker-alt"></i> Paris, France</a></li>
        <li><a href="#"><i class="fas fa-phone"></i> +33 1 23 45 67 89</a></li>
        <li><a href="#"><i class="fas fa-envelope"></i> contact@dentaltech.fr</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <p>© {{ date('Y') }} LDM. Tous droits réservés. | <a href="#" style="color: var(--primary);">Mentions légales</a></p>
  </div>
</footer>

<div id="galleryLightbox"
     class="gallery-lightbox"
     hidden
     aria-hidden="true"
     role="dialog"
     aria-modal="true"
     aria-labelledby="galleryLightboxTitle">
  <div class="gallery-lightbox-backdrop" data-gallery-lightbox-close></div>

  <div class="gallery-lightbox-shell">
    <header class="gallery-lightbox-topbar">
      <div class="gallery-lightbox-meta">
        <span class="gallery-lightbox-badge">Galerie</span>
        <span id="galleryLightboxCounter" class="gallery-lightbox-counter"></span>
      </div>
      <button type="button" class="gallery-lightbox-close" data-gallery-lightbox-close aria-label="Fermer">
        <i class="fas fa-times" aria-hidden="true"></i>
      </button>
    </header>

    <div class="gallery-lightbox-stage">
      <button type="button" class="gallery-lightbox-nav gallery-lightbox-prev" data-gallery-lightbox-prev aria-label="Image précédente">
        <i class="fas fa-chevron-left" aria-hidden="true"></i>
      </button>

      <div class="gallery-lightbox-media">
        <div class="gallery-lightbox-media-glow" aria-hidden="true"></div>
        <img id="galleryLightboxImage" src="" alt="">
      </div>

      <button type="button" class="gallery-lightbox-nav gallery-lightbox-next" data-gallery-lightbox-next aria-label="Image suivante">
        <i class="fas fa-chevron-right" aria-hidden="true"></i>
      </button>
    </div>

    <footer class="gallery-lightbox-caption">
      <h3 id="galleryLightboxTitle"></h3>
      <p id="galleryLightboxDesc"></p>
    </footer>
  </div>
</div>

@php
    $modern = $modern ?? false;
@endphp
<div id="galleryLightbox"
     @class(['gallery-lightbox', 'gallery-lightbox--premium' => $modern])
     hidden
     aria-hidden="true"
     role="dialog"
     aria-modal="true"
     aria-labelledby="galleryLightboxTitle">
  <div class="gallery-lightbox-backdrop" data-gallery-lightbox-close></div>

  <div class="gallery-lightbox-shell">
    @if($modern)
      <div class="gallery-lightbox-progress" aria-hidden="true">
        <span id="galleryLightboxProgress"></span>
      </div>
    @endif

    <header class="gallery-lightbox-topbar">
      <div class="gallery-lightbox-meta">
        <span class="gallery-lightbox-badge">Galerie</span>
        <span id="galleryLightboxCounter" class="gallery-lightbox-counter"></span>
        @if($modern)
          <span id="galleryLightboxTitleInline" class="gallery-lightbox-title-inline"></span>
        @endif
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

    @if($modern)
      <div id="galleryLightboxThumbs" class="gallery-lightbox-thumbs" role="tablist" aria-label="Miniatures galerie"></div>
    @endif

    <footer class="gallery-lightbox-caption">
      <h3 id="galleryLightboxTitle"></h3>
      <p id="galleryLightboxDesc"></p>
    </footer>
  </div>
</div>

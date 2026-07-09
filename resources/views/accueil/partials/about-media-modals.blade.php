<div id="aboutImageModal"
     class="about-modal"
     hidden
     aria-hidden="true"
     role="dialog"
     aria-modal="true"
     aria-labelledby="aboutImageModalTitle">
  <div class="about-modal__backdrop" data-about-modal-close></div>
  <div class="about-modal__shell">
    <button type="button" class="about-modal__close" data-about-modal-close aria-label="Fermer">
      <i class="fas fa-times" aria-hidden="true"></i>
    </button>
    <figure class="about-modal__figure">
      <img id="aboutImageModalImg" src="" alt="">
      <figcaption>
        <strong id="aboutImageModalTitle"></strong>
        <span id="aboutImageModalCaption"></span>
      </figcaption>
    </figure>
  </div>
</div>

<div id="aboutVideoModal"
     class="about-modal about-modal--video"
     hidden
     aria-hidden="true"
     role="dialog"
     aria-modal="true"
     aria-labelledby="aboutVideoModalTitle">
  <div class="about-modal__backdrop" data-about-modal-close></div>
  <div class="about-modal__shell">
    <button type="button" class="about-modal__close" data-about-modal-close aria-label="Fermer">
      <i class="fas fa-times" aria-hidden="true"></i>
    </button>
    <div class="about-modal__video-wrap">
      <div id="aboutVideoModalPlayer" class="about-modal__player"></div>
      <p id="aboutVideoModalTitle" class="about-modal__video-title"></p>
    </div>
  </div>
</div>

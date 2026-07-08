<div id="academyImageModal"
     class="academy-media-modal"
     hidden
     aria-hidden="true"
     role="dialog"
     aria-modal="true"
     aria-labelledby="academyImageModalCaption">
  <div class="academy-media-modal__backdrop" data-academy-modal-close></div>
  <div class="academy-media-modal__shell">
    <button type="button" class="academy-media-modal__close" data-academy-modal-close aria-label="Fermer">
      <i class="fas fa-times" aria-hidden="true"></i>
    </button>
    <figure class="academy-media-modal__figure">
      <img id="academyImageModalImg" src="" alt="">
      <figcaption id="academyImageModalCaption"></figcaption>
    </figure>
  </div>
</div>

<div id="academyVideoModal"
     class="academy-media-modal academy-media-modal--video"
     hidden
     aria-hidden="true"
     role="dialog"
     aria-modal="true"
     aria-labelledby="academyVideoModalCaption">
  <div class="academy-media-modal__backdrop" data-academy-modal-close></div>
  <div class="academy-media-modal__shell">
    <button type="button" class="academy-media-modal__close" data-academy-modal-close aria-label="Fermer">
      <i class="fas fa-times" aria-hidden="true"></i>
    </button>
    <div class="academy-media-modal__video-wrap">
      <div id="academyVideoModalPlayer" class="academy-media-modal__player"></div>
      <p id="academyVideoModalCaption" class="academy-media-modal__video-title"></p>
    </div>
  </div>
</div>

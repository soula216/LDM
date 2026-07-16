@php
    use App\Models\VitrineBlock;

    $mediaPage = $mediaPage ?? VitrineBlock::aboutMediaPage($about ?? []);
    $mediaPhotos = $mediaPhotos ?? VitrineBlock::aboutMediaPagePhotos($about ?? []);
    $hasDescription = filled($mediaPage['description'] ?? null);
@endphp

<div class="about-media-page">
  <article class="about-article reveal">
    <div class="about-article__card">
      <header class="about-article__head">
        <span class="about-article__eyebrow">{{ $mediaPage['section_label'] ?: 'Certifications' }}</span>
        <h2 class="about-article__title">{{ $mediaPage['title'] ?: 'Certifications' }}</h2>
      </header>

      @if($hasDescription)
        <div class="about-article__content">
          @foreach(preg_split('/\R{2,}/', trim($mediaPage['description'])) as $paragraph)
            @if(filled($paragraph))
              <p>{{ $paragraph }}</p>
            @endif
          @endforeach
        </div>
      @endif
    </div>
  </article>

  @if($mediaPhotos->isNotEmpty())
    <div class="about-media-gallery reveal" style="--about-delay: 0.1s">
      <div class="about-media-gallery__grid">
        @foreach($mediaPhotos as $index => $photo)
          @php
            $photoTitle = $photo['title'] !== '' ? $photo['title'] : 'Photo ' . ($index + 1);
          @endphp
          <button type="button"
                  class="about-media-gallery__item"
                  data-about-image
                  data-src="{{ e($photo['image_url']) }}"
                  data-title="{{ e($photo['title']) }}"
                  data-caption="{{ e($photo['description']) }}"
                  aria-label="Agrandir : {{ $photoTitle }}"
                  style="--about-media-delay: {{ min($index * 0.05, 0.4) }}s">
            <span class="about-media-gallery__visual">
              <img src="{{ $photo['image_url'] }}"
                   alt="{{ $photoTitle }}"
                   loading="lazy"
                   decoding="async">
              <span class="about-media-gallery__overlay" aria-hidden="true">
                <i class="fas fa-expand"></i>
              </span>
            </span>
            @if($photo['title'] !== '' || $photo['description'] !== '')
              <span class="about-media-gallery__meta">
                @if($photo['title'] !== '')
                  <strong>{{ $photo['title'] }}</strong>
                @endif
                @if($photo['description'] !== '')
                  <span>{{ $photo['description'] }}</span>
                @endif
              </span>
            @endif
          </button>
        @endforeach
      </div>
    </div>
  @elseif(!$hasDescription)
    <div class="about-empty reveal">
      <div class="about-empty__icon"><i class="fas fa-images" aria-hidden="true"></i></div>
      <h2>Certifications à venir</h2>
      <p>Les certifications du laboratoire seront bientôt disponibles ici.</p>
    </div>
  @endif
</div>

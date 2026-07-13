@extends('layouts.accueil')

@php
    use App\Models\VitrineBlock;
    $imageUrl = VitrineBlock::serviceItemImageUrl($service);
    $title = $service['title'] ?? 'Service';
    $hasSummary = filled($service['description'] ?? null);
    $sections = VitrineBlock::serviceSections($service);
    $hasContentHtml = filled($service['content_html'] ?? null);
    $hasMainCard = $imageUrl !== '' || $hasContentHtml || ($sections->isEmpty() && $hasSummary);
@endphp

@section('title', $title . ' | LDM - Digital Max')

@section('header')
  @include('accueil.header')
@endsection

@section('content')
<main class="inner-page service-detail-page">
  <section @class(['inner-hero', 'inner-hero--has-summary' => $hasSummary])>
    <div class="inner-hero-bg" aria-hidden="true"></div>
    <div class="inner-hero-content">
      <div class="inner-hero-badge">
        <i class="fas fa-tooth" aria-hidden="true"></i>
        <span>{{ $services['section_label'] ?? 'Nos Services' }}</span>
      </div>
      <h1>{{ $title }}</h1>
      @if($hasSummary)
        <p>{{ $service['description'] }}</p>
      @endif
      <a href="{{ route('vitrine.services') }}" class="service-detail-back">
        <i class="fas fa-arrow-left" aria-hidden="true"></i>
        <span>Retour aux services</span>
      </a>
    </div>
  </section>

  <section class="service-detail-body">
    @if($hasMainCard)
      <div class="service-detail-container">
        @if($imageUrl !== '')
          <figure class="service-detail-figure">
            <img src="{{ $imageUrl }}" alt="{{ $title }}">
          </figure>
        @endif

        @if($hasContentHtml)
          <article class="service-detail-content prose-vitrine">
            {!! $service['content_html'] !!}
          </article>
        @elseif($sections->isEmpty() && $hasSummary)
          <article class="service-detail-content prose-vitrine">
            <p>{{ $service['description'] }}</p>
          </article>
        @endif
      </div>
    @endif

    @if($sections->isNotEmpty())
      <div class="service-showcase" id="serviceSectionsGallery" data-gallery-scope>
        <div class="service-showcase__intro reveal">
          <span class="service-showcase__kicker">En détail</span>
          <h2 class="service-showcase__heading">Découvrez ce service</h2>
          <p class="service-showcase__lead">Explorez chaque aspect de notre prestation, illustré par nos réalisations.</p>
        </div>

        <div class="service-showcase__track">
          @foreach($sections as $index => $section)
            @php
              $sectionPhotos = VitrineBlock::serviceSectionPhotos($section);
              $sectionParagraphs = preg_split('/\R{2,}/', trim((string) ($section['description'] ?? ''))) ?: [];
              $sectionParagraphs = array_values(array_filter(array_map('trim', $sectionParagraphs)));
              $photoCount = $sectionPhotos->count();
              $galleryGroup = 'service-section-' . $index;
              $mosaicClass = match (true) {
                $photoCount <= 1 => 'service-showcase__mosaic--1',
                $photoCount === 2 => 'service-showcase__mosaic--2',
                default => 'service-showcase__mosaic--bento',
              };
            @endphp
            <article @class([
              'service-showcase__block',
              'reveal',
              'service-showcase__block--alt' => $index % 2 === 1,
              'service-showcase__block--has-photos' => $photoCount > 0,
            ]) style="--showcase-delay: {{ number_format(min($index * 0.08, 0.48), 2, '.', '') }}s">
              <div class="service-showcase__block-inner">
                <div class="service-showcase__copy">
                  <div class="service-showcase__index" aria-hidden="true">
                    <span>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                  </div>

                  @if(filled($section['title'] ?? null))
                    <h3 class="service-showcase__title">{{ $section['title'] }}</h3>
                  @endif

                  @if($sectionParagraphs !== [])
                    <div class="service-showcase__text">
                      @foreach($sectionParagraphs as $paragraph)
                        <p>{{ $paragraph }}</p>
                      @endforeach
                    </div>
                  @endif
                </div>

                @if($photoCount > 0)
                  <div @class(['service-showcase__mosaic', $mosaicClass])>
                    @foreach($sectionPhotos as $photoIndex => $photo)
                      @php
                        $photoTitle = $photo['title'] !== ''
                          ? $photo['title']
                          : ($section['title'] ?? $title);
                      @endphp
                      <button type="button"
                              class="service-showcase__tile"
                              data-gallery-item
                              data-gallery-group="{{ $galleryGroup }}"
                              data-gallery-index="{{ $photoIndex }}"
                              data-gallery-src="{{ $photo['image_url'] }}"
                              data-gallery-title="{{ $photoTitle }}"
                              data-gallery-description="{{ $section['title'] ?? '' }}"
                              aria-label="Agrandir : {{ $photoTitle }}">
                        <span class="service-showcase__tile-visual">
                          <img src="{{ $photo['image_url'] }}"
                               alt="{{ $photoTitle }}"
                               loading="lazy"
                               decoding="async">
                          <span class="service-showcase__tile-overlay" aria-hidden="true">
                            <span class="service-showcase__tile-expand">
                              <i class="fas fa-expand"></i>
                            </span>
                          </span>
                        </span>
                        @if($photo['title'] !== '')
                          <span class="service-showcase__tile-caption">{{ $photo['title'] }}</span>
                        @endif
                      </button>
                    @endforeach
                  </div>
                @endif
              </div>
              <div class="service-showcase__glow" aria-hidden="true"></div>
            </article>
          @endforeach
        </div>
      </div>

      @include('accueil.partials.gallery-lightbox', ['modern' => true])
    @endif
  </section>
</main>
@endsection

@section('footer')
  @include('accueil.footer')
@endsection

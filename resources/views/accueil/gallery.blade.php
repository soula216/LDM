@extends('layouts.accueil')

@section('title', ($gallery['section_title'] ?? 'Galerie') . ' | LDM - Digital Max')

@section('header')
  @include('accueil.header')
@endsection

@section('content')
@php
    use App\Models\VitrineBlock;

    $items = VitrineBlock::pageGalleryItems($gallery);
@endphp

<main class="inner-page gallery-page">
  <section class="inner-hero gallery-page-hero">
    <div class="inner-hero-bg gallery-page-hero__bg" aria-hidden="true"></div>
    <div class="inner-hero-content">
      <div class="inner-hero-badge">
        <i class="fas fa-images" aria-hidden="true"></i>
        <span>{{ $gallery['section_label'] ?? 'Nos Travaux' }}</span>
      </div>
      <h1>{{ $gallery['section_title'] ?? 'Découvrez Nos Réalisations' }}</h1>
      @if(filled($gallery['section_subtitle'] ?? null))
        <p>{{ $gallery['section_subtitle'] }}</p>
      @endif
      @if($items->isNotEmpty())
        <div class="gallery-page-meta">
          <strong>{{ $items->count() }}</strong>
          <span>réalisation{{ $items->count() > 1 ? 's' : '' }}</span>
        </div>
      @endif
    </div>
  </section>

  <section class="inner-body gallery gallery--page" id="travaux">
    @if($items->isEmpty())
      <div class="inner-empty">
        <div class="inner-empty-icon"><i class="fas fa-images" aria-hidden="true"></i></div>
        <h2>Galerie à venir</h2>
        <p>Nos réalisations seront bientôt présentées ici.</p>
      </div>
    @else
      <div class="gallery-showcase">
        <div class="gallery-showcase__head reveal">
          <div class="gallery-showcase__head-left">
            <span class="gallery-showcase__pill">{{ $items->count() }} visuel{{ $items->count() > 1 ? 's' : '' }}</span>
          </div>
          <p class="gallery-showcase__hint">
            <i class="fas fa-up-right-and-down-left-from-center" aria-hidden="true"></i>
            Cliquez sur une image pour l’agrandir
          </p>
        </div>

        <div class="gallery-grid-pro">
          @foreach($items as $index => $item)
            <article class="gallery-tile reveal"
                     style="--gallery-delay: {{ number_format(min($index * 0.05, 0.45), 2, '.', '') }}s">
              <button type="button"
                      class="gallery-tile__btn"
                      data-gallery-item
                      data-gallery-index="{{ $index }}"
                      data-gallery-src="{{ $item['image_url'] ?? '' }}"
                      data-gallery-title="{{ $item['title'] ?? '' }}"
                      data-gallery-description="{{ $item['description'] ?? '' }}"
                      aria-label="Agrandir : {{ $item['title'] ?? 'Image galerie' }}">
                <div class="gallery-tile__visual">
                  <img src="{{ $item['image_url'] ?? '' }}"
                       alt="{{ $item['title'] ?? '' }}"
                       loading="lazy"
                       decoding="async">
                  <div class="gallery-tile__overlay" aria-hidden="true">
                    <span class="gallery-tile__expand">
                      <i class="fas fa-expand"></i>
                    </span>
                  </div>
                </div>
                <div class="gallery-tile__meta">
                  <span class="gallery-tile__index">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                  <div class="gallery-tile__copy">
                    @if(filled($item['title'] ?? null))
                      <h3>{{ $item['title'] }}</h3>
                    @else
                      <h3>Réalisation {{ $index + 1 }}</h3>
                    @endif
                    @if(filled($item['description'] ?? null))
                      <p>{{ $item['description'] }}</p>
                    @endif
                  </div>
                </div>
              </button>
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

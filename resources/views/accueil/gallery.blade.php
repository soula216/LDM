@extends('layouts.accueil')

@section('title', ($gallery['section_title'] ?? 'Galerie') . ' | LDM - Digital Max')

@section('header')
  @include('accueil.header')
@endsection

@section('content')
@php
    use App\Models\VitrineBlock;

    $items = VitrineBlock::pageGalleryItems($gallery);
    $categoryDefinitions = collect($gallery['categories'] ?? [])
        ->filter(fn ($category): bool => is_array($category)
            && filled($category['key'] ?? null)
            && filled($category['label'] ?? null))
        ->map(fn (array $category): array => [
            'key' => trim((string) $category['key']),
            'label' => trim((string) $category['label']),
        ])
        ->keyBy('key');
    $categoryCounts = $items
        ->groupBy(fn (array $item): string => trim((string) ($item['category'] ?? '')))
        ->map->count();
    $visibleCategories = $categoryDefinitions;
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
        @if($visibleCategories->isNotEmpty())
          <div class="gallery-category-filters reveal" role="group" aria-label="Filtrer les réalisations par catégorie">
            <button type="button"
                    class="gallery-category-filter is-active"
                    data-gallery-filter="all"
                    aria-pressed="true">
              <span>Toutes</span>
              <strong>{{ $items->count() }}</strong>
            </button>
            @foreach($visibleCategories as $categoryKey => $category)
              <button type="button"
                      class="gallery-category-filter"
                      data-gallery-filter="{{ $categoryKey }}"
                      aria-pressed="false">
                <span>{{ $category['label'] }}</span>
                <strong>{{ $categoryCounts[$categoryKey] ?? 0 }}</strong>
              </button>
            @endforeach
          </div>
        @endif

        <div class="gallery-showcase__head reveal">
          <div class="gallery-showcase__head-left">
            <span class="gallery-showcase__pill">
              <span data-gallery-visible-count>{{ $items->count() }}</span>
              <span data-gallery-visible-label>visuel{{ $items->count() > 1 ? 's' : '' }}</span>
            </span>
          </div>
          <p class="gallery-showcase__hint">
            <i class="fas fa-up-right-and-down-left-from-center" aria-hidden="true"></i>
            Cliquez sur une image pour l’agrandir
          </p>
        </div>

        <div class="gallery-grid-pro">
          @foreach($items as $index => $item)
            @php
              $categoryKey = trim((string) ($item['category'] ?? ''));
              $categoryLabel = $categoryDefinitions[$categoryKey]['label'] ?? '';
            @endphp
            <article class="gallery-tile reveal"
                     data-gallery-tile
                     data-gallery-category="{{ $categoryKey }}"
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
                    @if(filled($categoryLabel))
                      <span class="gallery-tile__category">{{ $categoryLabel }}</span>
                    @endif
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

        <div class="gallery-filter-empty" data-gallery-filter-empty hidden>
          <i class="fas fa-images" aria-hidden="true"></i>
          <h2>Aucune réalisation dans cette catégorie</h2>
          <p>Sélectionnez une autre catégorie pour découvrir nos travaux.</p>
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

@push('scripts')
<script>
  (function initGalleryCategoryFilters() {
    const filters = Array.from(document.querySelectorAll('[data-gallery-filter]'));
    const tiles = Array.from(document.querySelectorAll('[data-gallery-tile]'));
    if (filters.length === 0 || tiles.length === 0) return;

    const countEl = document.querySelector('[data-gallery-visible-count]');
    const labelEl = document.querySelector('[data-gallery-visible-label]');
    const emptyEl = document.querySelector('[data-gallery-filter-empty]');

    function applyFilter(category, updateUrl = true) {
      let visibleCount = 0;

      tiles.forEach((tile) => {
        const visible = category === 'all' || tile.dataset.galleryCategory === category;
        tile.hidden = !visible;
        tile.querySelectorAll('[data-gallery-item]').forEach((item) => {
          item.hidden = !visible;
        });
        if (visible) visibleCount++;
      });

      filters.forEach((button) => {
        const active = button.dataset.galleryFilter === category;
        button.classList.toggle('is-active', active);
        button.setAttribute('aria-pressed', active ? 'true' : 'false');
      });

      if (countEl) countEl.textContent = String(visibleCount);
      if (labelEl) labelEl.textContent = visibleCount > 1 ? 'visuels' : 'visuel';
      if (emptyEl) emptyEl.hidden = visibleCount !== 0;

      if (updateUrl) {
        const url = new URL(window.location.href);
        if (category === 'all') url.searchParams.delete('categorie');
        else url.searchParams.set('categorie', category);
        window.history.replaceState({}, '', url);
      }
    }

    filters.forEach((button) => {
      button.addEventListener('click', () => applyFilter(button.dataset.galleryFilter || 'all'));
    });

    const requestedCategory = new URL(window.location.href).searchParams.get('categorie');
    const initialCategory = filters.some((button) => button.dataset.galleryFilter === requestedCategory)
      ? requestedCategory
      : 'all';
    applyFilter(initialCategory, false);
  })();
</script>
@endpush

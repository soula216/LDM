@extends('layouts.accueil')

@section('title', 'LDM Academy | LDM - Digital Max')

@section('header')
  @include('accueil.header')
@endsection

@section('content')
@php
    $academyCategories = $academyCategories ?? [
        'catalogue' => ['label' => 'Catalogues', 'icon' => 'fas fa-book-open'],
        'guide' => ['label' => 'Guides techniques', 'icon' => 'fas fa-drafting-compass'],
        'protocole' => ['label' => 'Protocoles', 'icon' => 'fas fa-clipboard-list'],
        'notice' => ['label' => 'Notices', 'icon' => 'fas fa-file-alt'],
    ];
    $academyCategoryCounts = $academyCategoryCounts ?? [];
    $academyTotal = $academyTotal ?? 0;
    $academyHasMore = $academyHasMore ?? false;
    $academyPage = $academyPage ?? 1;
@endphp

<main class="academy-page">
  <section class="academy-hero">
    <div class="academy-hero-bg" aria-hidden="true"></div>
    <div class="academy-hero-content">
      <div class="academy-hero-badge">
        <i class="fas fa-graduation-cap" aria-hidden="true"></i>
        <span>{{ $academy['section_label'] ?? 'LDM Academy' }}</span>
      </div>
      <h1>{{ $academy['section_title'] ?? 'Ressources pour les praticiens' }}</h1>
      @if(filled($academy['section_subtitle'] ?? null))
        <p>{{ $academy['section_subtitle'] }}</p>
      @endif
      @if($academyTotal > 0)
        <div class="academy-hero-stats">
          <div class="academy-hero-stat">
            <strong>{{ $academyTotal }}</strong>
            <span>document{{ $academyTotal > 1 ? 's' : '' }}</span>
          </div>
          <div class="academy-hero-stat">
            <strong>{{ count($academyCategoryCounts) }}</strong>
            <span>catégorie{{ count($academyCategoryCounts) > 1 ? 's' : '' }}</span>
          </div>
          <div class="academy-hero-stat">
            <strong>PDF</strong>
            <span>téléchargeables</span>
          </div>
        </div>
      @endif
    </div>
  </section>

  <section class="academy-body" id="academy">
    @if($academyTotal === 0)
      <div class="academy-empty">
        <div class="academy-empty-icon">
          <i class="fas fa-folder-open" aria-hidden="true"></i>
        </div>
        <h2>Aucun document pour le moment</h2>
        <p>Les catalogues, guides techniques, protocoles et notices seront bientôt disponibles au téléchargement.</p>
      </div>
    @else
      <div class="academy-toolbar"
           data-documents-url="{{ route('vitrine.academy.documents') }}"
           data-page="{{ $academyPage }}"
           data-has-more="{{ $academyHasMore ? '1' : '0' }}"
           data-filter="all">
        <div class="academy-filters" role="tablist" aria-label="Filtrer par catégorie">
          <button type="button"
                  class="academy-filter is-active"
                  data-filter="all"
                  role="tab"
                  aria-selected="true">
            Tous
            <span>{{ $academyTotal }}</span>
          </button>
          @foreach($academyCategoryCounts as $categoryKey => $count)
            @php $meta = $academyCategories[$categoryKey] ?? ['label' => ucfirst($categoryKey), 'icon' => 'fas fa-file-pdf']; @endphp
            <button type="button"
                    class="academy-filter"
                    data-filter="{{ $categoryKey }}"
                    role="tab"
                    aria-selected="false">
              <i class="{{ $meta['icon'] }}" aria-hidden="true"></i>
              {{ $meta['label'] }}
              <span>{{ $count }}</span>
            </button>
          @endforeach
        </div>

        <div class="academy-grid" id="academyGrid">
          @include('accueil.partials.academy-cards', [
              'documents' => $academyDocuments,
              'academyCategories' => $academyCategories,
          ])
        </div>

        <div class="academy-empty academy-empty--filter" id="academyFilterEmpty" hidden>
          <div class="academy-empty-icon">
            <i class="fas fa-search" aria-hidden="true"></i>
          </div>
          <h2>Aucun document dans cette catégorie</h2>
          <p>Sélectionnez une autre catégorie pour afficher les ressources disponibles.</p>
        </div>

        <div class="academy-load-more" id="academyLoadMore" @if(! $academyHasMore) hidden @endif>
          <div class="academy-load-more-spinner" aria-hidden="true"></div>
          <span>Chargement des documents…</span>
        </div>
        <div id="academySentinel" class="academy-sentinel" aria-hidden="true"></div>
      </div>
    @endif
  </section>
</main>

<script>
  (function () {
    const toolbar = document.querySelector('.academy-toolbar');
    if (!toolbar) return;

    const grid = document.getElementById('academyGrid');
    const filters = toolbar.querySelectorAll('.academy-filter');
    const emptyState = document.getElementById('academyFilterEmpty');
    const loadMore = document.getElementById('academyLoadMore');
    const sentinel = document.getElementById('academySentinel');
    const documentsUrl = toolbar.dataset.documentsUrl;

    let page = parseInt(toolbar.dataset.page || '1', 10);
    let hasMore = toolbar.dataset.hasMore === '1';
    let filter = toolbar.dataset.filter || 'all';
    let loading = false;
    let observer = null;

    function setLoading(isLoading) {
      loading = isLoading;
      if (loadMore) {
        loadMore.hidden = !isLoading;
      }
    }

    function updateEmptyState() {
      if (!emptyState || !grid) return;
      emptyState.hidden = grid.children.length > 0;
    }

    function initObserver() {
      if (!sentinel) return;
      if (observer) observer.disconnect();
      if (!hasMore) return;

      observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting) {
          loadNextPage();
        }
      }, { rootMargin: '240px' });

      observer.observe(sentinel);
    }

    async function fetchDocuments(nextPage, replace) {
      if (loading) return;
      setLoading(true);

      try {
        const params = new URLSearchParams({
          page: String(nextPage),
          category: filter,
        });
        const response = await fetch(`${documentsUrl}?${params.toString()}`, {
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
          },
        });

        if (!response.ok) return;

        const data = await response.json();
        if (!grid) return;

        if (replace) {
          grid.innerHTML = data.html || '';
        } else if (data.html) {
          grid.insertAdjacentHTML('beforeend', data.html);
        }

        page = data.page || nextPage;
        hasMore = !!data.has_more;
        toolbar.dataset.page = String(page);
        toolbar.dataset.hasMore = hasMore ? '1' : '0';
        updateEmptyState();
      } catch (error) {
        console.error('Erreur lazyload academy:', error);
      } finally {
        setLoading(false);
        if (hasMore) {
          initObserver();
        } else if (observer) {
          observer.disconnect();
        }
      }
    }

    function loadNextPage() {
      if (loading || !hasMore) return;
      fetchDocuments(page + 1, false);
    }

    function resetAndLoad(newFilter) {
      filter = newFilter;
      toolbar.dataset.filter = filter;
      page = 0;
      hasMore = true;
      toolbar.dataset.page = '0';
      toolbar.dataset.hasMore = '1';
      if (observer) observer.disconnect();
      fetchDocuments(1, true);
    }

    filters.forEach((button) => {
      button.addEventListener('click', () => {
        const nextFilter = button.dataset.filter || 'all';
        if (nextFilter === filter && grid && grid.children.length > 0) return;

        filters.forEach((item) => {
          const active = item === button;
          item.classList.toggle('is-active', active);
          item.setAttribute('aria-selected', active ? 'true' : 'false');
        });

        resetAndLoad(nextFilter);
      });
    });

    updateEmptyState();
    initObserver();
  })();
</script>
@endsection

@section('footer')
  @include('accueil.footer')
@endsection

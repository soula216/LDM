@extends('layouts.accueil')

@section('title', ($services['section_title'] ?? 'Services') . ' | LDM - Digital Max')

@section('header')
  @include('accueil.header')
@endsection

@section('content')
@php
    use App\Models\VitrineBlock;
    $items = VitrineBlock::activeServiceItems($services['items'] ?? []);
    $displayItems = $items->values();

    $honeycombPosition = static function (int $index, int $total): string {
        if ($total >= 6) {
            return match ($index) {
                0 => 'top',
                1 => 'left-upper',
                2 => 'right-upper',
                3 => 'left-lower',
                4 => 'right-lower',
                5 => 'bottom',
                default => 'top',
            };
        }

        if ($total >= 5) {
            return match ($index) {
                0 => 'top',
                1 => 'left-upper',
                2 => 'right-upper',
                3 => 'left-lower',
                4 => 'right-lower',
                default => 'top',
            };
        }

        if ($total === 4) {
            return match ($index) {
                0 => 'top',
                1 => 'left-upper',
                2 => 'right-upper',
                3 => 'bottom',
                default => 'top',
            };
        }

        if ($total === 3) {
            return match ($index) {
                0 => 'top',
                1 => 'left-upper',
                2 => 'right-upper',
                default => 'top',
            };
        }

        if ($total === 2) {
            return match ($index) {
                0 => 'left-upper',
                1 => 'right-upper',
                default => 'top',
            };
        }

        return 'top';
    };
@endphp

<main class="inner-page inner-page--services">
  <section class="inner-hero">
    <div class="inner-hero-bg" aria-hidden="true"></div>
    <div class="inner-hero-content">
      <div class="inner-hero-badge">
        <i class="fas fa-tooth" aria-hidden="true"></i>
        <span>{{ $services['section_label'] ?? 'Nos Services' }}</span>
      </div>
      <h1>{{ $services['section_title'] ?? 'Solutions Complètes' }}</h1>
      @if(filled($services['section_subtitle'] ?? null))
        <p>{{ $services['section_subtitle'] }}</p>
      @endif
    </div>
  </section>

  <section class="inner-body services--page" id="services">
    @if($items->isEmpty())
      <div class="inner-empty">
        <div class="inner-empty-icon"><i class="fas fa-layer-group" aria-hidden="true"></i></div>
        <h2>Services à venir</h2>
        <p>Nos prestations seront bientôt détaillées sur cette page.</p>
      </div>
    @else
      <div class="services-honeycomb">
        <div class="services-honeycomb__grid">
          <div class="services-honeycomb__bg" aria-hidden="true">
            <div class="services-honeycomb__bg-base"></div>
            <span class="services-honeycomb__bg-hex services-honeycomb__bg-hex--1"></span>
            <span class="services-honeycomb__bg-hex services-honeycomb__bg-hex--2"></span>
            <span class="services-honeycomb__bg-hex services-honeycomb__bg-hex--3"></span>
            <span class="services-honeycomb__bg-hex services-honeycomb__bg-hex--4"></span>
            <span class="services-honeycomb__bg-hex services-honeycomb__bg-hex--5"></span>
            <span class="services-honeycomb__bg-hex services-honeycomb__bg-hex--6"></span>
          </div>
          <div class="services-honeycomb__center-glow">
            <span class="services-honeycomb__center-glow-bloom" aria-hidden="true"></span>
            <span class="services-honeycomb__center-glow-core" aria-hidden="true"></span>
            <p class="services-honeycomb__center-label">LES SERVICES</p>
          </div>
          @foreach($displayItems as $index => $item)
            @php
              $imageUrl = VitrineBlock::serviceItemImageUrl($item);
              $slug = VitrineBlock::serviceItemSlug($item);
              $position = $honeycombPosition($index, $displayItems->count());
              $title = mb_strtoupper($item['title'] ?? '', 'UTF-8');
              $labelClass = mb_strlen($title) > 22 || substr_count($title, ' ') >= 2 ? ' is-compact' : '';
            @endphp
            <article class="services-hex-item reveal active" data-pos="{{ $position }}">
              <a href="{{ route('vitrine.services.show', $slug) }}" class="services-hex-link" aria-label="{{ $item['title'] ?? 'Service' }}">
                <div class="services-hex-shell-wrap">
                  <div class="services-hex-shell{{ $imageUrl !== '' ? ' has-image' : ' is-placeholder' }}"
                       @if($imageUrl !== '') style="background-image: url('{{ e($imageUrl) }}')" @endif>
                    @if($imageUrl === '')
                      <div class="services-hex-placeholder" aria-hidden="true">
                        <i class="fas fa-tooth"></i>
                      </div>
                    @endif
                    <div class="services-hex-overlay" aria-hidden="true"></div>
                  </div>
                  <h2 class="services-hex-label{{ $labelClass }}">{{ $title }}</h2>
                </div>
              </a>
            </article>
          @endforeach
        </div>
      </div>
    @endif
  </section>
</main>
@endsection

@section('footer')
  @include('accueil.footer')
@endsection

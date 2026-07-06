@extends('layouts.accueil')

@section('title', ($process['section_title'] ?? 'Processus') . ' | LDM - Digital Max')

@section('header')
  @include('accueil.header')
@endsection

@section('content')
@php
    use App\Models\VitrineBlock;

    $steps = VitrineBlock::activeProcessSteps($process['steps'] ?? []);
    $defaultIcons = ['fa-comments', 'fa-cube', 'fa-cogs', 'fa-truck-fast'];
@endphp

<main class="inner-page">
  <section class="inner-hero">
    <div class="inner-hero-bg" aria-hidden="true"></div>
    <div class="inner-hero-content">
      <div class="inner-hero-badge">
        <i class="fas fa-project-diagram" aria-hidden="true"></i>
        <span>{{ $process['section_label'] ?? 'Notre Process' }}</span>
      </div>
      <h1>{{ $process['section_title'] ?? 'Comment Nous Travaillons' }}</h1>
      @if(filled($process['section_subtitle'] ?? null))
        <p>{{ $process['section_subtitle'] }}</p>
      @endif
    </div>
  </section>

  <section class="inner-body process process--page" id="process">
    @if($steps->isEmpty())
      <div class="inner-empty">
        <div class="inner-empty-icon"><i class="fas fa-stream" aria-hidden="true"></i></div>
        <h2>Processus à venir</h2>
        <p>Les étapes de notre méthode de travail seront bientôt présentées ici.</p>
      </div>
    @else
      <div class="process-timeline-wrap">
        <div class="process-timeline-header">
          <span class="process-timeline-count">{{ $steps->count() }} étape{{ $steps->count() > 1 ? 's' : '' }}</span>
          <p>Un parcours structuré, de la prise de contact à la livraison finale.</p>
        </div>

        <div class="process-timeline">
          @foreach($steps as $step)
            @php
              $icon = filled($step['icon'] ?? null)
                ? $step['icon']
                : ($defaultIcons[$loop->index % count($defaultIcons)] ?? 'fa-circle-check');
            @endphp
            <article class="process-timeline-item reveal active">
              <div class="process-timeline-marker" aria-hidden="true">
                <span class="process-timeline-number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
              </div>
              <div class="process-timeline-card">
                <div class="process-timeline-card-top">
                  <div class="process-timeline-icon">
                    <i class="fas {{ $icon }}" aria-hidden="true"></i>
                  </div>
                  <span class="process-timeline-label">Étape {{ $loop->iteration }}</span>
                </div>
                <h3>{{ $step['title'] ?? '' }}</h3>
                <p>{{ $step['description'] ?? '' }}</p>
              </div>
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

@extends('layouts.accueil')

@php
    use App\Models\VitrineBlock;
    $imageUrl = VitrineBlock::serviceItemImageUrl($service);
    $title = $service['title'] ?? 'Service';
    $hasSummary = filled($service['description'] ?? null);
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
    <div class="service-detail-container">
      @if($imageUrl !== '')
        <figure class="service-detail-figure">
          <img src="{{ $imageUrl }}" alt="{{ $title }}">
        </figure>
      @endif

      @if(filled($service['content_html'] ?? null))
        <article class="service-detail-content prose-vitrine">
          {!! $service['content_html'] !!}
        </article>
      @elseif(filled($service['description'] ?? null))
        <article class="service-detail-content prose-vitrine">
          <p>{{ $service['description'] }}</p>
        </article>
      @endif
    </div>
  </section>
</main>
@endsection

@section('footer')
  @include('accueil.footer')
@endsection

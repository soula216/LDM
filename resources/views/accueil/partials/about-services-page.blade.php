@php
    use App\Models\VitrineBlock;

    $items = VitrineBlock::activeServiceItems($services['items'] ?? []);
    $displayItems = $items->values();
    $serviceCount = $displayItems->count();
    $gridRows = VitrineBlock::serviceHoneycombRowCount($serviceCount);
@endphp

<div class="services services--page" id="services">
  @if($items->isEmpty())
    <div class="inner-empty">
      <div class="inner-empty-icon"><i class="fas fa-layer-group" aria-hidden="true"></i></div>
      <h2>Services à venir</h2>
      <p>Nos prestations seront bientôt détaillées sur cette page.</p>
    </div>
  @else
    <div class="services-honeycomb">
      <div class="services-honeycomb__grid" style="--honeycomb-rows: {{ $gridRows }}; grid-template-rows: repeat({{ $gridRows }}, auto);">
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
          @php($placement = VitrineBlock::serviceHoneycombPlacement($index, $serviceCount))
          @include('accueil.partials.service-hex-card', [
            'item' => $item,
            'gridRow' => $placement['row'],
            'gridCol' => $placement['col'],
            'role' => $placement['role'],
          ])
        @endforeach
      </div>
    </div>
  @endif
</div>

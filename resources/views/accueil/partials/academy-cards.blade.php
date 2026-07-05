@php
    use App\Models\VitrineBlock;

    $academyCategories = $academyCategories ?? [
        'catalogue' => ['label' => 'Catalogues', 'icon' => 'fas fa-book-open'],
        'guide' => ['label' => 'Guides techniques', 'icon' => 'fas fa-drafting-compass'],
        'protocole' => ['label' => 'Protocoles', 'icon' => 'fas fa-clipboard-list'],
        'notice' => ['label' => 'Notices', 'icon' => 'fas fa-file-alt'],
    ];
@endphp

@foreach($documents as $index => $doc)
  @php
    $categoryKey = $doc['category'] ?? 'catalogue';
    $meta = $academyCategories[$categoryKey] ?? ['label' => 'Document', 'icon' => 'fas fa-file-pdf'];
    $coverUrl = VitrineBlock::academyDocumentBackgroundUrl($doc);
    $hasCover = $coverUrl !== '';
  @endphp
  <article @class(['academy-card', 'academy-card--has-cover' => $hasCover])
           data-category="{{ $categoryKey }}"
           @if($hasCover) style="--academy-cover: url('{{ e($coverUrl) }}'); --delay: {{ min($index, 8) * 40 }}ms" @else style="--delay: {{ min($index, 8) * 40 }}ms" @endif>
    @if($hasCover)
      <div class="academy-card-bg" aria-hidden="true"></div>
    @endif
    <div class="academy-card-inner">
    <div class="academy-card-top">
      <div class="academy-card-icon">
        <i class="{{ $meta['icon'] }}" aria-hidden="true"></i>
      </div>
      <span class="academy-card-category">{{ $meta['label'] }}</span>
    </div>
    <h2>{{ $doc['title'] ?? 'Document PDF' }}</h2>
    @if(filled($doc['description'] ?? null))
      <p>{{ $doc['description'] }}</p>
    @else
      <p class="academy-card-muted">Document PDF destiné aux chirurgiens-dentistes.</p>
    @endif
    <div class="academy-card-footer">
      <span class="academy-card-format">
        <i class="fas fa-file-pdf" aria-hidden="true"></i>
        PDF
      </span>
      <a href="{{ $doc['file_url'] }}"
         class="academy-download"
         download="{{ $doc['file_name'] ?? '' }}"
         target="_blank"
         rel="noopener noreferrer">
        <span>Télécharger</span>
        <i class="fas fa-arrow-down" aria-hidden="true"></i>
      </a>
    </div>
    </div>
  </article>
@endforeach

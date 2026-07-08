@php
    use App\Models\VitrineBlock;

    $academyCategories = $academyCategories ?? VitrineBlock::defaultAcademyCategories();
@endphp

@foreach($documents as $index => $doc)
  @php
    $categoryKey = $doc['category'] ?? 'catalogue';
    $meta = $academyCategories[$categoryKey] ?? ['label' => 'Document', 'icon' => ''];
    $fileMeta = VitrineBlock::academyFileTypeMeta($doc);
    $fileType = VitrineBlock::normalizeAcademyFileType($doc['file_type'] ?? 'pdf');
    $coverUrl = VitrineBlock::academyDocumentBackgroundUrl($doc);
    $hasCover = $coverUrl !== '';
    $fileUrl = VitrineBlock::resolveImageAbsoluteUrl($doc['file_url'] ?? '');
    $videoConfig = $fileType === 'video' ? VitrineBlock::academyVideoPlayerConfig($doc['file_url'] ?? '') : null;
    $defaultDescription = match ($fileType) {
        'image' => 'Image destinée aux chirurgiens-dentistes.',
        'video' => 'Vidéo destinée aux chirurgiens-dentistes.',
        'word' => 'Document Word destiné aux chirurgiens-dentistes.',
        default => 'Document PDF destiné aux chirurgiens-dentistes.',
    };
  @endphp
  <article @class(['academy-card', 'academy-card--has-cover' => $hasCover])
           data-category="{{ $categoryKey }}"
           @if($hasCover) style="--academy-cover: url('{{ e($coverUrl) }}'); --delay: {{ min($index, 8) * 40 }}ms" @else style="--delay: {{ min($index, 8) * 40 }}ms" @endif>
    @if($hasCover)
      <div class="academy-card-bg" aria-hidden="true"></div>
    @endif
    <div class="academy-card-inner">
    <div class="academy-card-top">
      @if(filled($meta['icon'] ?? null))
        <div class="academy-card-icon">
          <i class="{{ $meta['icon'] }}" aria-hidden="true"></i>
        </div>
      @endif
      <span class="academy-card-category">{{ $meta['label'] }}</span>
    </div>
    <h2>{{ $doc['title'] ?? $fileMeta['label'] }}</h2>
    @if(filled($doc['description'] ?? null))
      <p>{{ $doc['description'] }}</p>
    @else
      <p class="academy-card-muted">{{ $defaultDescription }}</p>
    @endif
    <div class="academy-card-footer">
      <span class="academy-card-format">
        <i class="{{ $fileMeta['icon'] }}" aria-hidden="true"></i>
        {{ $fileMeta['label'] }}
      </span>
      @if(in_array($fileType, ['pdf', 'word'], true))
        <a href="{{ $fileUrl }}"
           class="academy-download"
           download="{{ $doc['file_name'] ?? '' }}"
           target="_blank"
           rel="noopener noreferrer">
          <span>Télécharger</span>
          <i class="fas fa-arrow-down" aria-hidden="true"></i>
        </a>
      @elseif($fileType === 'image')
        <button type="button"
                class="academy-download"
                data-academy-image
                data-src="{{ e($fileUrl) }}"
                data-title="{{ e($doc['title'] ?? 'Image') }}">
          <span>Voir</span>
          <i class="fas fa-expand" aria-hidden="true"></i>
        </button>
      @elseif($fileType === 'video' && $videoConfig)
        <button type="button"
                class="academy-download"
                data-academy-video
                data-video-mode="{{ e($videoConfig['mode']) }}"
                data-src="{{ e($videoConfig['src']) }}"
                data-title="{{ e($doc['title'] ?? 'Vidéo') }}">
          <span>Regarder</span>
          <i class="fas fa-play" aria-hidden="true"></i>
        </button>
      @endif
    </div>
    </div>
  </article>
@endforeach

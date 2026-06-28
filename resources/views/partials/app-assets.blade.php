@php
    $viteEntries = ['resources/css/app.css', 'resources/js/app.js'];
    $manifestPath = public_path('build/manifest.json');
    $useManifest = file_exists($manifestPath) && (app()->environment('production') || ! file_exists(public_path('hot')));

    $assetVersion = function (string $relativePath): string {
        $fullPath = public_path('build/'.$relativePath);
        if (is_file($fullPath)) {
            return '?v=' . filemtime($fullPath);
        }

        return '';
    };
@endphp

@if ($useManifest)
    @php
        $manifest = json_decode(file_get_contents($manifestPath), true);
        $loadedStyles = [];
    @endphp
    @if (is_array($manifest))
        @foreach ($viteEntries as $entry)
            @if (! isset($manifest[$entry]))
                @continue
            @endif
            @php $chunk = $manifest[$entry]; @endphp
            @if (! empty($chunk['css']))
                @foreach ($chunk['css'] as $cssFile)
                    @if (! in_array($cssFile, $loadedStyles, true))
                        @php $loadedStyles[] = $cssFile; @endphp
                        <link rel="stylesheet" href="{{ asset('build/'.$cssFile).$assetVersion($cssFile) }}">
                    @endif
                @endforeach
            @endif
            @if (! empty($chunk['file']) && str_ends_with($chunk['file'], '.css') && ! in_array($chunk['file'], $loadedStyles, true))
                @php $loadedStyles[] = $chunk['file']; @endphp
                <link rel="stylesheet" href="{{ asset('build/'.$chunk['file']).$assetVersion($chunk['file']) }}">
            @endif
        @endforeach
        @foreach ($viteEntries as $entry)
            @if (isset($manifest[$entry]['file']) && str_ends_with($manifest[$entry]['file'], '.js'))
                <script type="module" src="{{ asset('build/'.$manifest[$entry]['file']).$assetVersion($manifest[$entry]['file']) }}" defer></script>
            @endif
        @endforeach
    @else
        @vite($viteEntries)
    @endif
@else
    @vite($viteEntries)
@endif

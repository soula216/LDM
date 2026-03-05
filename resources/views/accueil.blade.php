@php
    $html = file_get_contents(base_path('accueil.html'));
    // Remplacer le chemin statique du logo par le vrai chemin public Laravel
    $html = str_replace('assets/logo_ldm.png', asset('logo_ldm.png'), $html);
    echo $html;
@endphp


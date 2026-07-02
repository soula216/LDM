<?php

namespace App\Http\Controllers;

use App\Models\VitrineBlock;
use Illuminate\View\View;

class VitrineController extends Controller
{
    public function show(): View
    {
        try {
            $blocks = VitrineBlock::allKeyed();
        } catch (\Throwable) {
            $blocks = [];
        }

        if (empty($blocks)) {
            $blocks = [
                'header' => ['logo_alt' => 'LDM', 'nav_links' => [], 'client_space_label' => 'Espace client'],
                'hero' => ['slides' => [], 'title_line1' => 'Prothèses Dentaires', 'title_highlight' => 'de Précision', 'buttons' => [], 'card' => ['stats' => []]],
                'services' => ['section_title' => 'Services', 'items' => []],
                'process' => ['section_title' => 'Process', 'steps' => []],
                'gallery' => ['section_title' => 'Galerie', 'items' => []],
                'features' => ['title_before' => 'Pourquoi', 'title_highlight' => 'LDM', 'list' => [], 'card' => []],
                'contact' => ['title' => 'Contact', 'items' => []],
                'footer' => ['brand_description' => '', 'social_links' => [], 'columns' => [], 'copyright' => 'LDM', 'legal_link' => ['label' => 'Mentions légales', 'href' => '#']],
            ];
        }

        return view('accueil', compact('blocks'));
    }
}

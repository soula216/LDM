<?php

use App\Models\VitrineBlock;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $header = VitrineBlock::query()->where('key', 'header')->first();
        if (! $header) {
            return;
        }

        $content = $header->content ?? [];
        $content['nav_links'] = $this->orderedNavLinks($content['nav_links'] ?? []);
        $header->update(['content' => $content]);
    }

    public function down(): void
    {
        // Pas de retour arrière : l'ordre précédent variait selon les environnements.
    }

    /**
     * @param  array<int, array<string, mixed>>  $navLinks
     * @return array<int, array<string, mixed>>
     */
    private function orderedNavLinks(array $navLinks): array
    {
        $home = null;
        $about = null;
        $services = null;
        $others = [];

        foreach ($navLinks as $link) {
            $href = strtolower(trim((string) ($link['href'] ?? '')));
            $label = strtolower(trim((string) ($link['label'] ?? '')));

            if ($href === '/about' || str_contains($label, 'propos')) {
                $about = [
                    'label' => $link['label'] ?? 'À propos',
                    'href' => '/about',
                ];

                continue;
            }

            if (in_array($href, ['/', '#accueil', '#home'], true) || $label === 'accueil') {
                $home = $link;

                continue;
            }

            if (in_array($href, ['/services', '#services'], true) || $label === 'services') {
                $services = $link;

                continue;
            }

            $others[] = $link;
        }

        if ($about === null) {
            $about = ['label' => 'À propos', 'href' => '/about'];
        }

        $ordered = [];

        if ($home !== null) {
            $ordered[] = $home;
        }

        $ordered[] = $about;

        if ($services !== null) {
            $ordered[] = $services;
        }

        return array_values(array_merge($ordered, $others));
    }
};

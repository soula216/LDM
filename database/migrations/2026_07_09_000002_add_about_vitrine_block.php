<?php

use App\Models\VitrineBlock;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        VitrineBlock::query()->where('key', 'partners')->update(['sort_order' => 8]);
        VitrineBlock::query()->where('key', 'faq')->update(['sort_order' => 9]);
        VitrineBlock::query()->where('key', 'academy')->update(['sort_order' => 10]);
        VitrineBlock::query()->where('key', 'contact')->update(['sort_order' => 11]);
        VitrineBlock::query()->where('key', 'footer')->update(['sort_order' => 12]);

        VitrineBlock::updateOrCreate(
            ['key' => 'about'],
            [
                'label' => 'Page À propos',
                'sort_order' => 7,
                'is_active' => true,
                'content' => [
                    'section_label' => 'À propos',
                    'title' => 'Notre laboratoire, notre engagement',
                    'description' => 'Depuis plus de 15 ans, LDM accompagne les chirurgiens-dentistes avec des prothèses de précision, une expertise technique reconnue et un service client réactif. Découvrez notre univers, notre équipe et notre savoir-faire à travers images et vidéos.',
                    'photos' => [],
                    'videos' => [],
                ],
            ]
        );

        $header = VitrineBlock::query()->where('key', 'header')->first();
        if (! $header) {
            return;
        }

        $content = $header->content ?? [];
        $navLinks = $content['nav_links'] ?? [];

        $aboutIndex = collect($navLinks)->search(function ($link) {
            $label = strtolower(trim((string) ($link['label'] ?? '')));
            $href = trim((string) ($link['href'] ?? ''));

            return str_contains($label, 'propos') || in_array($href, ['/about', '#about', 'about'], true);
        });

        if ($aboutIndex !== false) {
            $navLinks[(int) $aboutIndex]['label'] = 'À propos';
            $navLinks[(int) $aboutIndex]['href'] = '/about';
        } else {
            $servicesIndex = collect($navLinks)->search(fn ($link) => ($link['href'] ?? '') === '/services');
            $aboutLink = ['label' => 'À propos', 'href' => '/about'];

            if ($servicesIndex !== false) {
                array_splice($navLinks, (int) $servicesIndex + 1, 0, [$aboutLink]);
            } else {
                $navLinks[] = $aboutLink;
            }
        }

        $content['nav_links'] = array_values($navLinks);
        $header->update(['content' => $content]);
    }

    public function down(): void
    {
        VitrineBlock::query()->where('key', 'about')->delete();
        VitrineBlock::query()->where('key', 'partners')->update(['sort_order' => 7]);
        VitrineBlock::query()->where('key', 'faq')->update(['sort_order' => 8]);
        VitrineBlock::query()->where('key', 'academy')->update(['sort_order' => 9]);
        VitrineBlock::query()->where('key', 'contact')->update(['sort_order' => 10]);
        VitrineBlock::query()->where('key', 'footer')->update(['sort_order' => 11]);

        $header = VitrineBlock::query()->where('key', 'header')->first();
        if (! $header) {
            return;
        }

        $content = $header->content ?? [];
        $content['nav_links'] = collect($content['nav_links'] ?? [])
            ->reject(fn ($link) => ($link['href'] ?? '') === '/about')
            ->values()
            ->all();
        $header->update(['content' => $content]);
    }
};

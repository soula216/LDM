<?php

use App\Models\VitrineBlock;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        VitrineBlock::query()->where('key', 'partners')->update(['sort_order' => 9]);
        VitrineBlock::query()->where('key', 'faq')->update(['sort_order' => 10]);
        VitrineBlock::query()->where('key', 'academy')->update(['sort_order' => 11]);
        VitrineBlock::query()->where('key', 'contact')->update(['sort_order' => 12]);
        VitrineBlock::query()->where('key', 'footer')->update(['sort_order' => 13]);

        VitrineBlock::updateOrCreate(
            ['key' => 'laboratory'],
            [
                'label' => 'Laboratoire / Équipe',
                'sort_order' => 8,
                'is_active' => true,
                'content' => [
                    'section_label' => 'Laboratoire / Équipe',
                    'title' => 'Notre équipe & nos installations',
                    'description' => 'Découvrez les professionnels qui composent LDM, nos espaces de travail et les équipements de pointe qui garantissent la précision de nos prothèses.',
                    'photos' => [],
                ],
            ]
        );

        $header = VitrineBlock::query()->where('key', 'header')->first();
        if (! $header) {
            return;
        }

        $content = $header->content ?? [];
        $navLinks = $content['nav_links'] ?? [];

        $hasLink = collect($navLinks)->contains(function ($link) {
            $href = strtolower(trim((string) ($link['href'] ?? '')));
            $label = strtolower(trim((string) ($link['label'] ?? '')));

            return in_array($href, ['/laboratoire', '#laboratoire', 'laboratoire'], true)
                || str_contains($label, 'laboratoire')
                || str_contains($label, 'équipe');
        });

        if ($hasLink) {
            $navLinks = collect($navLinks)->map(function ($link) {
                $href = strtolower(trim((string) ($link['href'] ?? '')));
                $label = strtolower(trim((string) ($link['label'] ?? '')));

                if (in_array($href, ['/laboratoire', '#laboratoire', 'laboratoire'], true)
                    || str_contains($label, 'laboratoire')
                    || (str_contains($label, 'équipe') && ! str_contains($label, 'propos'))) {
                    $link['label'] = 'Laboratoire / Équipe';
                    $link['href'] = '/laboratoire';
                }

                return $link;
            })->values()->all();
        } else {
            $aboutIndex = collect($navLinks)->search(function ($link) {
                $href = strtolower(trim((string) ($link['href'] ?? '')));
                $label = strtolower(trim((string) ($link['label'] ?? '')));

                return $href === '/about' || str_contains($label, 'propos');
            });

            $labLink = ['label' => 'Laboratoire / Équipe', 'href' => '/laboratoire'];

            if ($aboutIndex !== false) {
                array_splice($navLinks, (int) $aboutIndex + 1, 0, [$labLink]);
            } else {
                $servicesIndex = collect($navLinks)->search(function ($link) {
                    $href = strtolower(trim((string) ($link['href'] ?? '')));
                    $label = strtolower(trim((string) ($link['label'] ?? '')));

                    return in_array($href, ['/services', '#services'], true) || $label === 'services';
                });

                if ($servicesIndex !== false) {
                    array_splice($navLinks, (int) $servicesIndex, 0, [$labLink]);
                } else {
                    $navLinks[] = $labLink;
                }
            }
        }

        $content['nav_links'] = array_values($navLinks);
        $header->update(['content' => $content]);
    }

    public function down(): void
    {
        VitrineBlock::query()->where('key', 'laboratory')->delete();
        VitrineBlock::query()->where('key', 'partners')->update(['sort_order' => 8]);
        VitrineBlock::query()->where('key', 'faq')->update(['sort_order' => 9]);
        VitrineBlock::query()->where('key', 'academy')->update(['sort_order' => 10]);
        VitrineBlock::query()->where('key', 'contact')->update(['sort_order' => 11]);
        VitrineBlock::query()->where('key', 'footer')->update(['sort_order' => 12]);

        $header = VitrineBlock::query()->where('key', 'header')->first();
        if (! $header) {
            return;
        }

        $content = $header->content ?? [];
        $content['nav_links'] = collect($content['nav_links'] ?? [])
            ->reject(fn ($link) => ($link['href'] ?? '') === '/laboratoire')
            ->values()
            ->all();
        $header->update(['content' => $content]);
    }
};

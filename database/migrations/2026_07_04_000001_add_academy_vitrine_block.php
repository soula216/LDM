<?php

use App\Models\VitrineBlock;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        VitrineBlock::query()->where('key', 'contact')->update(['sort_order' => 8]);
        VitrineBlock::query()->where('key', 'footer')->update(['sort_order' => 9]);

        VitrineBlock::updateOrCreate(
            ['key' => 'academy'],
            [
                'label' => 'LDM Academy',
                'sort_order' => 7,
                'is_active' => true,
                'content' => [
                    'section_label' => 'LDM Academy',
                    'section_title' => 'Ressources pour les praticiens',
                    'section_subtitle' => 'Catalogues, guides techniques, protocoles et notices à télécharger',
                    'documents' => [],
                ],
            ]
        );

        $header = VitrineBlock::query()->where('key', 'header')->first();
        if (! $header) {
            return;
        }

        $content = $header->content ?? [];
        $navLinks = $content['nav_links'] ?? [];
        $hasAcademyLink = collect($navLinks)->contains(function ($link) {
            $href = $link['href'] ?? '';
            $label = strtolower($link['label'] ?? '');

            return $href === '#academy' || str_contains($label, 'academy');
        });

        if (! $hasAcademyLink) {
            $contactIndex = collect($navLinks)->search(fn ($link) => ($link['href'] ?? '') === '#contact');
            $academyLink = ['label' => 'LDM Academy', 'href' => '#academy'];

            if ($contactIndex !== false) {
                array_splice($navLinks, (int) $contactIndex, 0, [$academyLink]);
            } else {
                $navLinks[] = $academyLink;
            }

            $content['nav_links'] = array_values($navLinks);
            $header->update(['content' => $content]);
        }
    }

    public function down(): void
    {
        VitrineBlock::query()->where('key', 'academy')->delete();
        VitrineBlock::query()->where('key', 'contact')->update(['sort_order' => 7]);
        VitrineBlock::query()->where('key', 'footer')->update(['sort_order' => 8]);

        $header = VitrineBlock::query()->where('key', 'header')->first();
        if (! $header) {
            return;
        }

        $content = $header->content ?? [];
        $content['nav_links'] = collect($content['nav_links'] ?? [])
            ->reject(fn ($link) => ($link['href'] ?? '') === '#academy')
            ->values()
            ->all();
        $header->update(['content' => $content]);
    }
};

<?php

use App\Models\VitrineBlock;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        if (VitrineBlock::query()->where('key', 'vos-patients')->exists()) {
            return;
        }

        $faq = VitrineBlock::query()->where('key', 'faq')->first();
        $sortOrder = $faq ? ((int) $faq->sort_order) + 1 : 12;

        VitrineBlock::query()
            ->where('sort_order', '>=', $sortOrder)
            ->increment('sort_order');

        VitrineBlock::query()->create([
            'key' => 'vos-patients',
            'label' => 'Vos patients',
            'sort_order' => $sortOrder,
            'is_active' => true,
            'content' => [
                'section_label' => 'Vos patients',
                'section_title' => 'Des sourires qui parlent',
                'section_subtitle' => 'Découvrez des cas cliniques et des témoignages vidéo pour illustrer le résultat de nos collaborations.',
                'categories' => [
                    ['key' => 'esthetique', 'label' => 'Esthétique', 'icon' => 'fas fa-smile'],
                    ['key' => 'implantologie', 'label' => 'Implantologie', 'icon' => 'fas fa-tooth'],
                    ['key' => 'prothese', 'label' => 'Prothèse', 'icon' => 'fas fa-teeth'],
                ],
                'videos' => [],
            ],
        ]);

        $header = VitrineBlock::query()->where('key', 'header')->first();
        if (! $header) {
            return;
        }

        $content = $header->content ?? [];
        $navLinks = is_array($content['nav_links'] ?? null) ? $content['nav_links'] : [];

        $alreadyPresent = collect($navLinks)->contains(function ($link) {
            $href = strtolower(trim((string) ($link['href'] ?? '')));

            return in_array($href, ['/vos-patients', 'vos-patients', '#vos-patients'], true)
                || str_contains($href, 'vos-patients');
        });

        if ($alreadyPresent) {
            return;
        }

        $inserted = false;
        $newLinks = [];

        foreach ($navLinks as $link) {
            $newLinks[] = $link;
            $href = strtolower(trim((string) ($link['href'] ?? '')));

            if (! $inserted && (in_array($href, ['/faq', 'faq', '#faq'], true) || str_contains($href, '/faq'))) {
                $newLinks[] = [
                    'label' => 'Vos patients',
                    'href' => '/vos-patients',
                    'is_active' => true,
                ];
                $inserted = true;
            }
        }

        if (! $inserted) {
            $newLinks[] = [
                'label' => 'Vos patients',
                'href' => '/vos-patients',
                'is_active' => true,
            ];
        }

        $content['nav_links'] = $newLinks;
        $header->update(['content' => $content]);
    }

    public function down(): void
    {
        $block = VitrineBlock::query()->where('key', 'vos-patients')->first();

        if ($block) {
            $sortOrder = (int) $block->sort_order;
            $block->delete();

            VitrineBlock::query()
                ->where('sort_order', '>', $sortOrder)
                ->decrement('sort_order');
        }

        $header = VitrineBlock::query()->where('key', 'header')->first();
        if (! $header) {
            return;
        }

        $content = $header->content ?? [];
        $navLinks = is_array($content['nav_links'] ?? null) ? $content['nav_links'] : [];

        $content['nav_links'] = collect($navLinks)
            ->reject(function ($link) {
                $href = strtolower(trim((string) ($link['href'] ?? '')));

                return in_array($href, ['/vos-patients', 'vos-patients', '#vos-patients'], true)
                    || str_contains($href, 'vos-patients');
            })
            ->values()
            ->all();

        $header->update(['content' => $content]);
    }
};

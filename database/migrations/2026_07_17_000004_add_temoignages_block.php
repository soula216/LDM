<?php

use App\Models\VitrineBlock;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        if (VitrineBlock::query()->where('key', 'temoignages')->exists()) {
            return;
        }

        $partners = VitrineBlock::query()->where('key', 'partners')->first();
        $sortOrder = $partners ? ((int) $partners->sort_order) + 1 : 10;

        VitrineBlock::query()
            ->where('sort_order', '>=', $sortOrder)
            ->increment('sort_order');

        VitrineBlock::query()->create([
            'key' => 'temoignages',
            'label' => 'Témoignages',
            'sort_order' => $sortOrder,
            'is_active' => true,
            'content' => [
                'section_label' => 'Témoignages',
                'section_title' => 'Ils Nous Recommandent',
                'section_subtitle' => 'Découvrez les avis des chirurgiens-dentistes qui nous font confiance au quotidien.',
                'items' => [],
            ],
        ]);
    }

    public function down(): void
    {
        $block = VitrineBlock::query()->where('key', 'temoignages')->first();

        if (! $block) {
            return;
        }

        $sortOrder = (int) $block->sort_order;
        $block->delete();

        VitrineBlock::query()
            ->where('sort_order', '>', $sortOrder)
            ->decrement('sort_order');
    }
};

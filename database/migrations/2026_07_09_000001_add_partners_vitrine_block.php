<?php

use App\Models\VitrineBlock;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private function defaultPartnersContent(): array
    {
        return [
            'section_label' => 'Nos Partenaires',
            'section_title' => 'Ils Nous Font Confiance',
            'section_subtitle' => 'Des collaborations solides avec les leaders du secteur dentaire pour vous offrir excellence et innovation.',
            'items' => [
                [
                    'name' => 'Straumann',
                    'url' => '',
                    'image_url' => 'https://logo.clearbit.com/straumann.com',
                    'source_type' => 'url',
                    'is_active' => true,
                ],
                [
                    'name' => 'Ivoclar',
                    'url' => '',
                    'image_url' => 'https://logo.clearbit.com/ivoclar.com',
                    'source_type' => 'url',
                    'is_active' => true,
                ],
                [
                    'name' => '3Shape',
                    'url' => '',
                    'image_url' => 'https://logo.clearbit.com/3shape.com',
                    'source_type' => 'url',
                    'is_active' => true,
                ],
                [
                    'name' => 'Dentsply Sirona',
                    'url' => '',
                    'image_url' => 'https://logo.clearbit.com/dentsplysirona.com',
                    'source_type' => 'url',
                    'is_active' => true,
                ],
                [
                    'name' => 'Nobel Biocare',
                    'url' => '',
                    'image_url' => 'https://logo.clearbit.com/nobelbiocare.com',
                    'source_type' => 'url',
                    'is_active' => true,
                ],
                [
                    'name' => 'VITA Zahnfabrik',
                    'url' => '',
                    'image_url' => 'https://logo.clearbit.com/vita-zahnfabrik.com',
                    'source_type' => 'url',
                    'is_active' => true,
                ],
            ],
        ];
    }

    public function up(): void
    {
        VitrineBlock::query()->where('key', 'faq')->update(['sort_order' => 8]);
        VitrineBlock::query()->where('key', 'academy')->update(['sort_order' => 9]);
        VitrineBlock::query()->where('key', 'contact')->update(['sort_order' => 10]);
        VitrineBlock::query()->where('key', 'footer')->update(['sort_order' => 11]);

        VitrineBlock::updateOrCreate(
            ['key' => 'partners'],
            [
                'label' => 'Partenaires',
                'sort_order' => 7,
                'is_active' => true,
                'content' => $this->defaultPartnersContent(),
            ]
        );
    }

    public function down(): void
    {
        VitrineBlock::query()->where('key', 'partners')->delete();
        VitrineBlock::query()->where('key', 'faq')->update(['sort_order' => 7]);
        VitrineBlock::query()->where('key', 'academy')->update(['sort_order' => 8]);
        VitrineBlock::query()->where('key', 'contact')->update(['sort_order' => 9]);
        VitrineBlock::query()->where('key', 'footer')->update(['sort_order' => 10]);
    }
};

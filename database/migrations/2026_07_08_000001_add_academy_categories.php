<?php

use App\Models\VitrineBlock;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $block = VitrineBlock::query()->where('key', 'academy')->first();

        if (! $block) {
            return;
        }

        $content = $block->content ?? [];

        if (! empty($content['categories']) && is_array($content['categories'])) {
            return;
        }

        $content['categories'] = [
            ['key' => 'catalogue', 'label' => 'Catalogues', 'icon' => 'fas fa-book-open'],
            ['key' => 'guide', 'label' => 'Guides techniques', 'icon' => 'fas fa-drafting-compass'],
            ['key' => 'protocole', 'label' => 'Protocoles', 'icon' => 'fas fa-clipboard-list'],
            ['key' => 'notice', 'label' => 'Notices', 'icon' => 'fas fa-file-alt'],
        ];

        $block->update(['content' => $content]);
    }

    public function down(): void
    {
        $block = VitrineBlock::query()->where('key', 'academy')->first();

        if (! $block) {
            return;
        }

        $content = $block->content ?? [];
        unset($content['categories']);
        $block->update(['content' => $content]);
    }
};

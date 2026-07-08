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
        $categories = $content['categories'] ?? [];

        if (! is_array($categories) || $categories === []) {
            return;
        }

        $content['categories'] = array_map(function (array $category) {
            $category['icon'] = VitrineBlock::normalizeAcademyCategoryIcon($category['icon'] ?? '');

            return $category;
        }, $categories);

        $block->update(['content' => $content]);
    }

    public function down(): void
    {
        // Non réversible : les icônes placeholder ne sont pas restaurées.
    }
};

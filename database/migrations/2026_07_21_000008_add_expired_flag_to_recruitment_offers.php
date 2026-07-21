<?php

use App\Models\VitrineBlock;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $block = VitrineBlock::query()->where('key', 'recrutement')->first();

        if (! $block) {
            return;
        }

        $content = $block->content ?? [];
        $content['items'] = collect($content['items'] ?? [])
            ->map(function (mixed $item): mixed {
                if (is_array($item) && ! array_key_exists('is_expired', $item)) {
                    $item['is_expired'] = false;
                }

                return $item;
            })
            ->values()
            ->all();

        $block->update(['content' => $content]);
    }

    public function down(): void
    {
        $block = VitrineBlock::query()->where('key', 'recrutement')->first();

        if (! $block) {
            return;
        }

        $content = $block->content ?? [];
        $content['items'] = collect($content['items'] ?? [])
            ->map(function (mixed $item): mixed {
                if (is_array($item)) {
                    unset($item['is_expired']);
                }

                return $item;
            })
            ->values()
            ->all();

        $block->update(['content' => $content]);
    }
};

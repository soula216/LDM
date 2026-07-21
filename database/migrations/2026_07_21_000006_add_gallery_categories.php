<?php

use App\Models\VitrineBlock;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $gallery = VitrineBlock::query()->where('key', 'gallery')->first();

        if (! $gallery) {
            return;
        }

        $content = $gallery->content ?? [];
        $content['categories'] = is_array($content['categories'] ?? null) && $content['categories'] !== []
            ? $content['categories']
            : [
                ['key' => 'prothese-fixe', 'label' => 'Prothèse fixe'],
                ['key' => 'prothese-amovible', 'label' => 'Prothèse amovible'],
                ['key' => 'implantologie', 'label' => 'Implantologie'],
                ['key' => 'esthetique-dentaire', 'label' => 'Esthétique dentaire'],
            ];

        $content['items'] = collect($content['items'] ?? [])
            ->map(function (mixed $item): mixed {
                if (! is_array($item) || filled($item['category'] ?? null)) {
                    return $item;
                }

                $title = mb_strtolower((string) ($item['title'] ?? ''));
                $item['category'] = match (true) {
                    str_contains($title, 'couronne') => 'prothese-fixe',
                    str_contains($title, 'partielle'), str_contains($title, 'amovible') => 'prothese-amovible',
                    str_contains($title, 'facette'), str_contains($title, 'blanchiment') => 'esthetique-dentaire',
                    str_contains($title, 'implant'), str_contains($title, 'complète') => 'implantologie',
                    default => '',
                };

                return $item;
            })
            ->values()
            ->all();

        $gallery->update(['content' => $content]);
    }

    public function down(): void
    {
        $gallery = VitrineBlock::query()->where('key', 'gallery')->first();

        if (! $gallery) {
            return;
        }

        $content = $gallery->content ?? [];
        unset($content['categories']);

        $content['items'] = collect($content['items'] ?? [])
            ->map(function (mixed $item): mixed {
                if (is_array($item)) {
                    unset($item['category']);
                }

                return $item;
            })
            ->values()
            ->all();

        $gallery->update(['content' => $content]);
    }
};

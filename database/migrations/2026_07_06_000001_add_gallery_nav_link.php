<?php

use App\Models\VitrineBlock;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $header = VitrineBlock::query()->where('key', 'header')->first();
        if (! $header) {
            return;
        }

        $content = $header->content ?? [];
        $navLinks = $content['nav_links'] ?? [];

        $hasGalleryLink = collect($navLinks)->contains(function ($link) {
            $href = strtolower(trim((string) ($link['href'] ?? '')));
            $label = strtolower(trim((string) ($link['label'] ?? '')));

            return in_array($href, ['/gallery', '#travaux', '#gallery', 'gallery'], true)
                || str_contains($label, 'galerie');
        });

        if ($hasGalleryLink) {
            $navLinks = collect($navLinks)
                ->map(function ($link) {
                    $href = strtolower(trim((string) ($link['href'] ?? '')));
                    $label = strtolower(trim((string) ($link['label'] ?? '')));

                    if (in_array($href, ['#travaux', '#gallery'], true) || str_contains($label, 'galerie')) {
                        $link['label'] = 'Galerie';
                        $link['href'] = '/gallery';
                    }

                    return $link;
                })
                ->values()
                ->all();

            $content['nav_links'] = $navLinks;
            $header->update(['content' => $content]);

            return;
        }

        $processIndex = collect($navLinks)->search(function ($link) {
            $href = strtolower(trim((string) ($link['href'] ?? '')));
            $label = strtolower(trim((string) ($link['label'] ?? '')));

            return $href === '/process' || str_contains($label, 'process');
        });

        $galleryLink = ['label' => 'Galerie', 'href' => '/gallery'];

        if ($processIndex !== false) {
            array_splice($navLinks, (int) $processIndex + 1, 0, [$galleryLink]);
        } else {
            $navLinks[] = $galleryLink;
        }

        $content['nav_links'] = array_values($navLinks);
        $header->update(['content' => $content]);
    }

    public function down(): void
    {
        $header = VitrineBlock::query()->where('key', 'header')->first();
        if (! $header) {
            return;
        }

        $content = $header->content ?? [];
        $content['nav_links'] = collect($content['nav_links'] ?? [])
            ->reject(function ($link) {
                $href = strtolower(trim((string) ($link['href'] ?? '')));
                $label = strtolower(trim((string) ($link['label'] ?? '')));

                return $href === '/gallery' || str_contains($label, 'galerie');
            })
            ->values()
            ->all();

        $header->update(['content' => $content]);
    }
};

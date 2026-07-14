<?php

use App\Models\VitrineBlock;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $aboutBlock = VitrineBlock::query()->where('key', 'about')->first();

        if ($aboutBlock) {
            $content = $aboutBlock->content ?? [];

            if (($content['section_label'] ?? '') === 'À propos') {
                $content['section_label'] = 'Le Laboratoire';
            }

            $aboutBlock->update([
                'label' => 'Le Laboratoire',
                'content' => $content,
            ]);
        }

        foreach (['header', 'footer'] as $blockKey) {
            $block = VitrineBlock::query()->where('key', $blockKey)->first();

            if (! $block) {
                continue;
            }

            $content = $block->content ?? [];
            $changed = false;

            if ($blockKey === 'header' && isset($content['nav_links']) && is_array($content['nav_links'])) {
                $content['nav_links'] = $this->renameAboutLinks($content['nav_links'], $changed);
            }

            if ($blockKey === 'footer' && isset($content['columns']) && is_array($content['columns'])) {
                foreach ($content['columns'] as $columnIndex => $column) {
                    if (! is_array($column) || ! isset($column['links']) || ! is_array($column['links'])) {
                        continue;
                    }

                    $content['columns'][$columnIndex]['links'] = $this->renameAboutLinks($column['links'], $changed);
                }
            }

            if ($changed) {
                $block->update(['content' => $content]);
            }
        }
    }

    public function down(): void
    {
        $aboutBlock = VitrineBlock::query()->where('key', 'about')->first();

        if ($aboutBlock) {
            $content = $aboutBlock->content ?? [];

            if (($content['section_label'] ?? '') === 'Le Laboratoire') {
                $content['section_label'] = 'À propos';
            }

            $aboutBlock->update([
                'label' => 'Page À propos',
                'content' => $content,
            ]);
        }

        foreach (['header', 'footer'] as $blockKey) {
            $block = VitrineBlock::query()->where('key', $blockKey)->first();

            if (! $block) {
                continue;
            }

            $content = $block->content ?? [];
            $changed = false;

            if ($blockKey === 'header' && isset($content['nav_links']) && is_array($content['nav_links'])) {
                $content['nav_links'] = $this->revertAboutLinks($content['nav_links'], $changed);
            }

            if ($blockKey === 'footer' && isset($content['columns']) && is_array($content['columns'])) {
                foreach ($content['columns'] as $columnIndex => $column) {
                    if (! is_array($column) || ! isset($column['links']) || ! is_array($column['links'])) {
                        continue;
                    }

                    $content['columns'][$columnIndex]['links'] = $this->revertAboutLinks($column['links'], $changed);
                }
            }

            if ($changed) {
                $block->update(['content' => $content]);
            }
        }
    }

    /**
     * @param  array<int, mixed>  $links
     * @return array<int, mixed>
     */
    private function renameAboutLinks(array $links, bool &$changed): array
    {
        foreach ($links as $index => $link) {
            if (! is_array($link)) {
                continue;
            }

            $href = trim((string) ($link['href'] ?? ''));
            $label = trim((string) ($link['label'] ?? ''));
            $normalized = rtrim(strtolower($href), '/');

            $isAboutLink = in_array($normalized, ['/about', 'about', '#about'], true)
                || str_ends_with($normalized, '/about')
                || str_contains(mb_strtolower($label), 'propos');

            if (! $isAboutLink) {
                continue;
            }

            $links[$index]['href'] = '/le-laboratoire';

            if ($label === '' || str_contains(mb_strtolower($label), 'propos')) {
                $links[$index]['label'] = 'Le Laboratoire';
            }

            $changed = true;
        }

        return $links;
    }

    /**
     * @param  array<int, mixed>  $links
     * @return array<int, mixed>
     */
    private function revertAboutLinks(array $links, bool &$changed): array
    {
        foreach ($links as $index => $link) {
            if (! is_array($link)) {
                continue;
            }

            $href = trim((string) ($link['href'] ?? ''));
            $normalized = rtrim(strtolower($href), '/');

            if (! in_array($normalized, ['/le-laboratoire', 'le-laboratoire', '#le-laboratoire'], true)
                && ! str_ends_with($normalized, '/le-laboratoire')) {
                continue;
            }

            $links[$index]['href'] = '/about';
            $links[$index]['label'] = 'À propos';
            $changed = true;
        }

        return $links;
    }
};

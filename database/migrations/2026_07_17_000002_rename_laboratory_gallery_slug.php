<?php

use App\Models\VitrineBlock;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $label = VitrineBlock::aboutLaboratoryPageLabel();
        $newPath = '/le-laboratoire/' . VitrineBlock::aboutLaboratoryPageSlug();

        VitrineBlock::query()
            ->where('key', 'laboratory')
            ->update(['label' => $label]);

        $laboratory = VitrineBlock::query()->where('key', 'laboratory')->first();
        if ($laboratory) {
            $content = $laboratory->content ?? [];
            $sectionLabel = trim((string) ($content['section_label'] ?? ''));

            if ($sectionLabel === '' || mb_strtolower($sectionLabel) === 'galerie') {
                $content['section_label'] = $label;
                $laboratory->update(['content' => $content]);
            }
        }

        foreach (['header', 'footer'] as $blockKey) {
            $block = VitrineBlock::query()->where('key', $blockKey)->first();
            if (! $block) {
                continue;
            }

            $content = $block->content ?? [];
            $changed = false;

            if ($blockKey === 'header' && isset($content['nav_links']) && is_array($content['nav_links'])) {
                $content['nav_links'] = $this->rewriteLaboratoryLinks($content['nav_links'], $newPath, $changed);
            }

            if ($blockKey === 'footer' && isset($content['columns']) && is_array($content['columns'])) {
                foreach ($content['columns'] as $columnIndex => $column) {
                    if (! is_array($column) || ! isset($column['links']) || ! is_array($column['links'])) {
                        continue;
                    }

                    $content['columns'][$columnIndex]['links'] = $this->rewriteLaboratoryLinks(
                        $column['links'],
                        $newPath,
                        $changed
                    );
                }
            }

            if ($changed) {
                $block->update(['content' => $content]);
            }
        }
    }

    public function down(): void
    {
        $oldPath = '/le-laboratoire/gallery';

        VitrineBlock::query()
            ->where('key', 'laboratory')
            ->update(['label' => 'Galerie']);

        $laboratory = VitrineBlock::query()->where('key', 'laboratory')->first();
        if ($laboratory) {
            $content = $laboratory->content ?? [];
            if (($content['section_label'] ?? '') === VitrineBlock::aboutLaboratoryPageLabel()) {
                $content['section_label'] = 'Galerie';
                $laboratory->update(['content' => $content]);
            }
        }

        foreach (['header', 'footer'] as $blockKey) {
            $block = VitrineBlock::query()->where('key', $blockKey)->first();
            if (! $block) {
                continue;
            }

            $content = $block->content ?? [];
            $changed = false;

            if ($blockKey === 'header' && isset($content['nav_links']) && is_array($content['nav_links'])) {
                $content['nav_links'] = $this->rewriteLaboratoryLinks($content['nav_links'], $oldPath, $changed, true);
            }

            if ($blockKey === 'footer' && isset($content['columns']) && is_array($content['columns'])) {
                foreach ($content['columns'] as $columnIndex => $column) {
                    if (! is_array($column) || ! isset($column['links']) || ! is_array($column['links'])) {
                        continue;
                    }

                    $content['columns'][$columnIndex]['links'] = $this->rewriteLaboratoryLinks(
                        $column['links'],
                        $oldPath,
                        $changed,
                        true
                    );
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
    private function rewriteLaboratoryLinks(array $links, string $targetPath, bool &$changed, bool $revert = false): array
    {
        $from = $revert
            ? ['/le-laboratoire/galerie-equipe-equipement', 'le-laboratoire/galerie-equipe-equipement']
            : ['/le-laboratoire/gallery', 'le-laboratoire/gallery', '/laboratoire', 'laboratoire'];

        return collect($links)
            ->map(function ($link) use ($from, $targetPath, &$changed) {
                if (! is_array($link)) {
                    return $link;
                }

                $href = strtolower(rtrim(trim((string) ($link['href'] ?? '')), '/'));

                if (in_array($href, $from, true) || str_ends_with($href, '/gallery') && str_contains($href, 'le-laboratoire')) {
                    $link['href'] = $targetPath;
                    $changed = true;
                }

                return $link;
            })
            ->values()
            ->all();
    }
};

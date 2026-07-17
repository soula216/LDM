<?php

use App\Models\VitrineBlock;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        VitrineBlock::query()
            ->where('key', 'process')
            ->update(['label' => 'Process de travail']);

        $header = VitrineBlock::query()->where('key', 'header')->first();
        if (! $header) {
            return;
        }

        $content = $header->content ?? [];
        $content['nav_links'] = collect($content['nav_links'] ?? [])
            ->reject(fn ($link) => $this->isProcessNavLink($link))
            ->values()
            ->all();

        $header->update(['content' => $content]);
    }

    public function down(): void
    {
        VitrineBlock::query()
            ->where('key', 'process')
            ->update(['label' => 'Processus']);

        $header = VitrineBlock::query()->where('key', 'header')->first();
        if (! $header) {
            return;
        }

        $content = $header->content ?? [];
        $navLinks = collect($content['nav_links'] ?? []);

        if ($navLinks->contains(fn ($link) => $this->isProcessNavLink($link))) {
            return;
        }

        $processLink = ['label' => 'Process', 'href' => '/process', 'is_active' => true];

        $servicesIndex = $navLinks->search(function ($link) {
            $href = strtolower(trim((string) ($link['href'] ?? '')));

            return in_array($href, ['/services', '#services', 'services'], true);
        });

        $navLinks = $navLinks->values()->all();

        if ($servicesIndex !== false) {
            array_splice($navLinks, (int) $servicesIndex + 1, 0, [$processLink]);
        } else {
            $navLinks[] = $processLink;
        }

        $content['nav_links'] = array_values($navLinks);
        $header->update(['content' => $content]);
    }

    private function isProcessNavLink(mixed $link): bool
    {
        if (! is_array($link)) {
            return false;
        }

        $href = strtolower(rtrim(trim((string) ($link['href'] ?? '')), '/'));
        $label = mb_strtolower(trim((string) ($link['label'] ?? '')));

        return in_array($href, ['/process', '#process', 'process'], true)
            || $label === 'process';
    }
};

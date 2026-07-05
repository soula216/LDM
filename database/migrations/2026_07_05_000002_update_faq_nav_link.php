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
        $navLinks = collect($content['nav_links'] ?? [])
            ->map(function ($link) {
                if (($link['href'] ?? '') === '#faq') {
                    $link['href'] = '/faq';
                }

                return $link;
            })
            ->values()
            ->all();

        $content['nav_links'] = $navLinks;
        $header->update(['content' => $content]);
    }

    public function down(): void
    {
        $header = VitrineBlock::query()->where('key', 'header')->first();
        if (! $header) {
            return;
        }

        $content = $header->content ?? [];
        $navLinks = collect($content['nav_links'] ?? [])
            ->map(function ($link) {
                if (($link['href'] ?? '') === '/faq') {
                    $link['href'] = '#faq';
                }

                return $link;
            })
            ->values()
            ->all();

        $content['nav_links'] = $navLinks;
        $header->update(['content' => $content]);
    }
};

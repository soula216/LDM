<?php

use App\Models\VitrineBlock;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $header = VitrineBlock::query()->where('key', 'header')->first();

        if ($header) {
            $content = $header->content ?? [];
            $links = is_array($content['nav_links'] ?? null) ? $content['nav_links'] : [];

            $content['nav_links'] = collect($links)
                ->reject(fn ($link) => $this->isFaqLink($link))
                ->values()
                ->all();

            $header->update(['content' => $content]);
        }

        $footer = VitrineBlock::query()->where('key', 'footer')->first();

        if ($footer) {
            $content = $footer->content ?? [];
            $content['faq_link'] = [
                'label' => 'FAQ',
                'href' => '/faq',
            ];

            $footer->update(['content' => $content]);
        }
    }

    public function down(): void
    {
        $header = VitrineBlock::query()->where('key', 'header')->first();

        if ($header) {
            $content = $header->content ?? [];
            $links = is_array($content['nav_links'] ?? null) ? $content['nav_links'] : [];

            if (! collect($links)->contains(fn ($link) => $this->isFaqLink($link))) {
                $links[] = [
                    'label' => 'FAQ',
                    'href' => '/faq',
                    'is_active' => true,
                ];
            }

            $content['nav_links'] = $links;
            $header->update(['content' => $content]);
        }

        $footer = VitrineBlock::query()->where('key', 'footer')->first();

        if ($footer) {
            $content = $footer->content ?? [];
            unset($content['faq_link']);
            $footer->update(['content' => $content]);
        }
    }

    private function isFaqLink(mixed $link): bool
    {
        if (! is_array($link)) {
            return false;
        }

        $href = strtolower(trim((string) ($link['href'] ?? '')));

        return in_array($href, ['/faq', 'faq', '#faq'], true);
    }
};

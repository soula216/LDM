<?php

use App\Models\VitrineBlock;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $this->reorderAboutLink(beforeServices: true);
    }

    public function down(): void
    {
        $this->reorderAboutLink(beforeServices: false);
    }

    private function reorderAboutLink(bool $beforeServices): void
    {
        $header = VitrineBlock::query()->where('key', 'header')->first();
        if (! $header) {
            return;
        }

        $content = $header->content ?? [];
        $navLinks = $content['nav_links'] ?? [];
        $aboutLink = null;
        $filtered = [];

        foreach ($navLinks as $link) {
            $href = trim((string) ($link['href'] ?? ''));
            $label = strtolower(trim((string) ($link['label'] ?? '')));

            if ($href === '/about' || str_contains($label, 'propos')) {
                $aboutLink = [
                    'label' => $link['label'] ?? 'À propos',
                    'href' => '/about',
                ];

                continue;
            }

            $filtered[] = $link;
        }

        if ($aboutLink === null) {
            $aboutLink = ['label' => 'À propos', 'href' => '/about'];
        }

        $servicesIndex = collect($filtered)->search(function ($link) {
            $href = strtolower(trim((string) ($link['href'] ?? '')));
            $label = strtolower(trim((string) ($link['label'] ?? '')));

            return in_array($href, ['/services', '#services'], true) || $label === 'services';
        });

        if ($servicesIndex !== false) {
            $insertAt = $beforeServices ? (int) $servicesIndex : (int) $servicesIndex + 1;
            array_splice($filtered, $insertAt, 0, [$aboutLink]);
        } else {
            $homeIndex = collect($filtered)->search(function ($link) {
                $href = strtolower(trim((string) ($link['href'] ?? '')));
                $label = strtolower(trim((string) ($link['label'] ?? '')));

                return in_array($href, ['/', '#accueil', '#home'], true) || $label === 'accueil';
            });

            if ($homeIndex !== false) {
                array_splice($filtered, (int) $homeIndex + 1, 0, [$aboutLink]);
            } else {
                array_unshift($filtered, $aboutLink);
            }
        }

        $content['nav_links'] = array_values($filtered);
        $header->update(['content' => $content]);
    }
};

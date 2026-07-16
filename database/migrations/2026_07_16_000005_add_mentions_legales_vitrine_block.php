<?php

use App\Models\VitrineBlock;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * @return array<string, mixed>
     */
    private function defaultContent(): array
    {
        return [
            'section_label' => 'Mentions légales',
            'section_title' => 'Mentions légales',
            'content_html' => '<p>Les présentes mentions légales régissent l’utilisation du site LDM.</p><h2>Éditeur du site</h2><p>LDM – Laboratoire de prothèse dentaire.</p><h2>Hébergement</h2><p>Les informations d’hébergement seront précisées ici.</p><h2>Propriété intellectuelle</h2><p>L’ensemble des contenus présents sur ce site est protégé par le droit de la propriété intellectuelle.</p>',
        ];
    }

    public function up(): void
    {
        $footer = VitrineBlock::query()->where('key', 'footer')->first();
        $footerSortOrder = (int) ($footer?->sort_order ?? 14);

        VitrineBlock::updateOrCreate(
            ['key' => 'mentions-legales'],
            [
                'label' => 'Mentions légales',
                'sort_order' => $footerSortOrder + 1,
                'is_active' => true,
                'content' => $this->defaultContent(),
            ]
        );

        if ($footer) {
            $content = $footer->content ?? [];
            $legalLink = is_array($content['legal_link'] ?? null) ? $content['legal_link'] : [];
            $content['legal_link'] = [
                'label' => trim((string) ($legalLink['label'] ?? 'Mentions légales')) ?: 'Mentions légales',
                'href' => '/mentions-legales',
            ];
            $footer->update(['content' => $content]);
        }
    }

    public function down(): void
    {
        VitrineBlock::query()->where('key', 'mentions-legales')->delete();

        $footer = VitrineBlock::query()->where('key', 'footer')->first();
        if (! $footer) {
            return;
        }

        $content = $footer->content ?? [];
        $legalLink = is_array($content['legal_link'] ?? null) ? $content['legal_link'] : [];
        $href = strtolower(trim((string) ($legalLink['href'] ?? '')));

        if (in_array($href, ['/mentions-legales', 'mentions-legales', '#mentions-legales'], true)) {
            $content['legal_link'] = [
                'label' => trim((string) ($legalLink['label'] ?? 'Mentions légales')) ?: 'Mentions légales',
                'href' => '#',
            ];
            $footer->update(['content' => $content]);
        }
    }
};

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
            'section_label' => 'Recrutement',
            'section_title' => 'Rejoindre LDM',
            'section_subtitle' => 'Découvrez nos offres d’emploi et rejoignez une équipe passionnée par l’excellence prothétique.',
            'items' => [
                [
                    'title' => 'Technicien prothésiste dentaire',
                    'vacancies' => 2,
                    'employment_types' => ['CDI', 'CDD'],
                    'experience' => '0 à 1 an',
                    'education_level' => 'Licence, Bac + 3',
                    'languages' => 'Français, Anglais, Arabe',
                    'gender' => 'Indifférent',
                    'description_html' => '<p>Nous recherchons un(e) technicien(ne) prothésiste motivé(e) pour renforcer notre équipe de fabrication numérique.</p><ul><li>Maîtrise des workflow CAD/CAM</li><li>Esprit d’équipe et rigueur</li><li>Sens du détail esthétique</li></ul>',
                    'is_active' => true,
                ],
                [
                    'title' => 'Responsable qualité & traçabilité',
                    'vacancies' => 1,
                    'employment_types' => ['CDI', 'SIVP'],
                    'experience' => '2 à 5 ans',
                    'education_level' => 'Bac + 3 / Bac + 5',
                    'languages' => 'Français, Anglais',
                    'gender' => 'Indifférent',
                    'description_html' => '<p>Vous pilotez la démarche qualité du laboratoire et garantissez la conformité des dispositifs prothétiques.</p><ul><li>Suivi des non-conformités et actions correctives</li><li>Traçabilité des matériaux et des lots</li><li>Rédaction et mise à jour des procédures internes</li><li>Coordination avec les équipes de production</li></ul><p>Profil organisé, rigoureux et à l’aise avec la documentation qualité.</p>',
                    'is_active' => true,
                ],
            ],
        ];
    }

    public function up(): void
    {
        VitrineBlock::query()->where('key', 'academy')->update(['sort_order' => 12]);
        VitrineBlock::query()->where('key', 'contact')->update(['sort_order' => 13]);
        VitrineBlock::query()->where('key', 'footer')->update(['sort_order' => 14]);

        VitrineBlock::updateOrCreate(
            ['key' => 'recrutement'],
            [
                'label' => 'Recrutement',
                'sort_order' => 11,
                'is_active' => true,
                'content' => $this->defaultContent(),
            ]
        );

        $header = VitrineBlock::query()->where('key', 'header')->first();
        if (! $header) {
            return;
        }

        $content = $header->content ?? [];
        $navLinks = collect($content['nav_links'] ?? [])->values()->all();

        $alreadyExists = collect($navLinks)->contains(function ($link) {
            $href = strtolower(trim((string) ($link['href'] ?? '')));

            return in_array($href, ['/recrutement', '#recrutement', 'recrutement'], true)
                || str_contains(mb_strtolower((string) ($link['label'] ?? '')), 'recrutement');
        });

        if ($alreadyExists) {
            return;
        }

        $recruitmentLink = ['label' => 'Recrutement', 'href' => '/recrutement'];
        $contactIndex = collect($navLinks)->search(function ($link) {
            $href = strtolower(trim((string) ($link['href'] ?? '')));

            return in_array($href, ['#contact', '/contact', 'contact'], true)
                || str_contains(mb_strtolower((string) ($link['label'] ?? '')), 'contact');
        });

        if ($contactIndex !== false) {
            array_splice($navLinks, (int) $contactIndex, 0, [$recruitmentLink]);
        } else {
            $faqIndex = collect($navLinks)->search(function ($link) {
                $href = strtolower(trim((string) ($link['href'] ?? '')));

                return in_array($href, ['/faq', '#faq', 'faq'], true);
            });

            if ($faqIndex !== false) {
                array_splice($navLinks, (int) $faqIndex + 1, 0, [$recruitmentLink]);
            } else {
                $navLinks[] = $recruitmentLink;
            }
        }

        $content['nav_links'] = array_values($navLinks);
        $header->update(['content' => $content]);
    }

    public function down(): void
    {
        VitrineBlock::query()->where('key', 'recrutement')->delete();

        $header = VitrineBlock::query()->where('key', 'header')->first();
        if ($header) {
            $content = $header->content ?? [];
            $content['nav_links'] = collect($content['nav_links'] ?? [])
                ->reject(function ($link) {
                    $href = strtolower(trim((string) ($link['href'] ?? '')));

                    return in_array($href, ['/recrutement', '#recrutement', 'recrutement'], true);
                })
                ->values()
                ->all();
            $header->update(['content' => $content]);
        }

        VitrineBlock::query()->where('key', 'academy')->update(['sort_order' => 11]);
        VitrineBlock::query()->where('key', 'contact')->update(['sort_order' => 12]);
        VitrineBlock::query()->where('key', 'footer')->update(['sort_order' => 13]);
    }
};

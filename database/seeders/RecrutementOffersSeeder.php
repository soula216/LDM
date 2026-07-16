<?php

namespace Database\Seeders;

use App\Models\VitrineBlock;
use Illuminate\Database\Seeder;

class RecrutementOffersSeeder extends Seeder
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function offers(): array
    {
        return [
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
        ];
    }

    public function run(): void
    {
        $block = VitrineBlock::query()->where('key', 'recrutement')->first();

        if (! $block) {
            $this->command?->warn('Bloc « recrutement » introuvable. Lancez d\'abord la migration ou VitrineBlockSeeder.');

            return;
        }

        $content = $block->content ?? [];
        $content['section_label'] = $content['section_label'] ?? 'Recrutement';
        $content['section_title'] = $content['section_title'] ?? 'Rejoindre LDM';
        $content['section_subtitle'] = $content['section_subtitle']
            ?? 'Découvrez nos offres d’emploi et rejoignez une équipe passionnée par l’excellence prothétique.';
        $content['items'] = static::offers();

        $block->update(['content' => $content]);

        $this->command?->info('Offres de recrutement renseignées (' . count(static::offers()) . ' offres).');
    }
}

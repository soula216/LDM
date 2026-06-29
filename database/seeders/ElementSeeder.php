<?php

namespace Database\Seeders;

use App\Models\Element;
use App\Models\Stock;
use Illuminate\Database\Seeder;

class ElementSeeder extends Seeder
{
    public function run(): void
    {
        $elements = [
            'Résine composite A2',
            'Résine composite B1',
            'Céramique dentaire',
            'Plâtre dentaire',
            'Gants latex',
            'Masques chirurgicaux',
            'Gaze stérile',
            'Alcool isopropylique',
            'Eau distillée',
            'Silicone addition',
            'Cire modelage',
            'Articulateurs',
            'Fil de ligature',
            'Pâte à polir',
            'Billes de sablage',
            'Disques diamantés',
            'Fraises carbure',
            'Colle adhésive',
            'Ciment verre ionomère',
            'Acide phosphorique',
            'Etching gel',
            'Emballages stérilisés',
            'Alginates',
            'Silicone condensation',
            'Résine impression 3D',
            'Poudre métallique CoCr',
            'Zirconia blanks',
            'Pivots fibre de verre',
            'Gouttières thermoformées',
            'Fil de retrait',
            'Cire de occlusion',
            'Pince modelage',
            'Spatule cire',
            'Brosses polissage',
            'Pâte prophylactique',
        ];

        foreach ($elements as $nom) {
            Element::firstOrCreate(['nom' => $nom]);
        }

        $stockQuantities = [
            'Résine composite A2' => 24,
            'Résine composite B1' => 18,
            'Céramique dentaire' => 32,
            'Plâtre dentaire' => 0,
            'Gants latex' => 150,
            'Masques chirurgicaux' => 200,
            'Gaze stérile' => 85,
            'Alcool isopropylique' => 12,
            'Eau distillée' => 40,
            'Silicone addition' => 16,
            'Cire modelage' => 28,
            'Articulateurs' => 6,
            'Fil de ligature' => 0,
            'Pâte à polir' => 22,
            'Billes de sablage' => 35,
            'Disques diamantés' => 45,
            'Fraises carbure' => 58,
            'Colle adhésive' => 14,
            'Ciment verre ionomère' => 9,
            'Acide phosphorique' => 11,
            'Etching gel' => 7,
            'Emballages stérilisés' => 120,
            'Alginates' => 19,
            'Silicone condensation' => 13,
            'Résine impression 3D' => 8,
            'Poudre métallique CoCr' => 25,
            'Zirconia blanks' => 12,
            'Pivots fibre de verre' => 30,
            'Gouttières thermoformées' => 5,
            'Fil de retrait' => 42,
            'Cire de occlusion' => 17,
            'Pince modelage' => 10,
            'Spatule cire' => 15,
            'Brosses polissage' => 0,
            'Pâte prophylactique' => 21,
        ];

        $stockCount = 0;
        foreach ($stockQuantities as $nom => $qte) {
            $element = Element::where('nom', $nom)->first();
            if ($element) {
                Stock::updateOrCreate(
                    ['element_id' => $element->id],
                    ['qte' => $qte]
                );
                $stockCount++;
            }
        }

        $this->command->info('✅ ' . count($elements) . ' éléments créés');
        $this->command->info('✅ ' . $stockCount . ' stocks créés');
    }
}

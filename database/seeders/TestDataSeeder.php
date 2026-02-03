<?php

namespace Database\Seeders;

use App\Models\Groupe;
use App\Models\Service;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer des groupes de test
        $groupes = [
            ['nom' => 'Groupe A', 'description' => 'Groupe de travail A'],
            ['nom' => 'Groupe B', 'description' => 'Groupe de travail B'],
        ];

        foreach ($groupes as $groupeData) {
            Groupe::firstOrCreate(
                ['nom' => $groupeData['nom']],
                $groupeData
            );
        }

        $this->command->info('✅ Groupes créés');

        // Créer des services de test avec prix
        $services = [
            ['nom' => 'Couronne Céramo-Métallique', 'description' => 'Couronne CCM', 'prix_unitaire_ttc' => 150.00],
            ['nom' => 'Bridge 3 éléments', 'description' => 'Bridge 3 éléments', 'prix_unitaire_ttc' => 450.00],
            ['nom' => 'Prothèse Complète', 'description' => 'Prothèse complète', 'prix_unitaire_ttc' => 300.00],
            ['nom' => 'Prothèse Partielle', 'description' => 'Prothèse partielle', 'prix_unitaire_ttc' => 250.00],
            ['nom' => 'Inlay Core', 'description' => 'Inlay Core', 'prix_unitaire_ttc' => 80.00],
            ['nom' => 'Facette Céramique', 'description' => 'Facette céramique', 'prix_unitaire_ttc' => 200.00],
        ];

        foreach ($services as $serviceData) {
            Service::firstOrCreate(
                ['nom' => $serviceData['nom']],
                $serviceData
            );
        }

        $this->command->info('✅ Services créés avec prix par défaut');
    }
}

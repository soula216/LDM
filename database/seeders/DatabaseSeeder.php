<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. D'abord créer les rôles et permissions
        $this->call([
            RolePermissionSeeder::class,
        ]);

        // 2. Créer les données de test (groupes, services)
        $this->call([
            TestDataSeeder::class,
        ]);

        // 3. Ensuite créer les utilisateurs de test
        $this->call([
            AdminUserSeeder::class,
        ]);
    }
}

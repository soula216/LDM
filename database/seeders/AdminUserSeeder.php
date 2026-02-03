<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier si l'admin existe déjà
        $adminEmail = 'admin@labo.tn';
        
        $admin = User::where('email', $adminEmail)->first();

        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin',
                'nom' => 'Admin',
                'prénom' => 'Système',
                'email' => $adminEmail,
                'password' => Hash::make('password'), // Mot de passe par défaut : password
                'gouvernorat' => 'Tunis',
                'ville' => 'Tunis',
                'adresse' => 'Adresse admin',
                'tél' => '+216 12345678',
                'email_verified_at' => now(),
            ]);

            // Assigner le rôle admin
            $adminRole = Role::firstOrCreate(['name' => 'admin']);
            $admin->assignRole($adminRole);

            $this->command->info('✅ Utilisateur admin créé avec succès !');
            $this->command->info('   Email: ' . $adminEmail);
            $this->command->info('   Mot de passe: password');
        } else {
            // S'assurer que l'utilisateur a le rôle admin
            if (!$admin->hasRole('admin')) {
                $adminRole = Role::firstOrCreate(['name' => 'admin']);
                $admin->assignRole($adminRole);
                $this->command->info('✅ Rôle admin assigné à l\'utilisateur existant.');
            } else {
                $this->command->info('ℹ️  L\'utilisateur admin existe déjà.');
            }
        }

        // Créer aussi quelques utilisateurs de test pour les autres rôles
        $this->createTestUsers();
    }

    /**
     * Créer des utilisateurs de test pour les autres rôles
     */
    private function createTestUsers(): void
    {
        // Dentist de test
        $dentist = User::firstOrCreate(
            ['email' => 'dentist@labo.tn'],
            [
                'name' => 'Dentiste Test',
                'nom' => 'Dentiste',
                'prénom' => 'Test',
                'password' => Hash::make('password'),
                'gouvernorat' => 'Sousse',
                'ville' => 'Sousse',
                'email_verified_at' => now(),
            ]
        );
        if (!$dentist->hasRole('dentist')) {
            $dentist->assignRole('dentist');
        }

        // Employer de test (nécessite un groupe)
        $groupe = \App\Models\Groupe::first();
        $employer = User::firstOrCreate(
            ['email' => 'employer@labo.tn'],
            [
                'name' => 'Employer Test',
                'nom' => 'Employer',
                'prénom' => 'Test',
                'password' => Hash::make('password'),
                'gouvernorat' => 'Tunis',
                'ville' => 'Tunis',
                'groupe_id' => $groupe?->id,
                'email_verified_at' => now(),
            ]
        );
        if (!$employer->hasRole('employer')) {
            $employer->assignRole('employer');
        }
        // S'assurer que l'employer a un groupe
        if ($groupe && !$employer->groupe_id) {
            $employer->update(['groupe_id' => $groupe->id]);
        }

        // Responsable de test
        $responsable = User::firstOrCreate(
            ['email' => 'responsable@labo.tn'],
            [
                'name' => 'Responsable Test',
                'nom' => 'Responsable',
                'prénom' => 'Test',
                'password' => Hash::make('password'),
                'gouvernorat' => 'Tunis',
                'ville' => 'Tunis',
                'email_verified_at' => now(),
            ]
        );
        if (!$responsable->hasRole('responsable')) {
            $responsable->assignRole('responsable');
        }

        $this->command->info('✅ Utilisateurs de test créés (dentist, employer, responsable)');
        $this->command->info('   Tous les mots de passe: password');
    }
}

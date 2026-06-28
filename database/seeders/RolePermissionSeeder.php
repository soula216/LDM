<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vider ancien cache
        cache()->forget('spatie.permission.cache');

        // Permissions V2 (Commandes)
        $commandesPermissions = [
            'view_commandes',
            'create_commandes',
            'edit_commandes',
            'delete_commandes',
            'upload_commande_files',
            'delete_commande_files',
            'change_commande_status',
        ];

        // Permissions V3 (Calendrier, détails, pricing, BL)
        $v3Permissions = [
            'view_commandes_calendar',
            'view_commande_details',
            'manage_service_pricing',
            'view_bons_livraison',
            'print_bons_livraison',
        ];

        // Permissions Admin (V2)
        $adminPermissions = [
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'view_roles',
            'manage_permissions',
            'view_dashboard',
            'view_statistics',
            'manage_config',
        ];

        // Permissions Factures
        $facturesPermissions = [
            'view_factures',
            'create_factures',
            'edit_factures',
            'delete_factures',
        ];

        // Permissions Fiche de contrôle qualité
        $ficheControleQualityPermissions = [
            'view_fiche_controle_quality',
            'create_fiche_controle_quality',
            'edit_fiche_controle_quality',
            'delete_fiche_controle_quality',
        ];

        // Permissions Dépenses
        $depensesPermissions = [
            'view_depenses',
            'manage_depenses',
        ];

        // Toutes les permissions
        $allPermissions = array_merge($commandesPermissions, $v3Permissions, $adminPermissions, $facturesPermissions, $ficheControleQualityPermissions, $depensesPermissions);

        foreach ($allPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Admin = toutes les permissions
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        // Responsable : peut voir les utilisateurs et gérer les commandes
        $responsable = Role::firstOrCreate(['name' => 'responsable']);
        $responsable->syncPermissions([
            'view_users',
            'view_commandes',
            'create_commandes',
            'edit_commandes',
            'upload_commande_files',
            'view_commandes_calendar',
            'view_commande_details',
            'change_commande_status',
            'manage_service_pricing',
            'view_bons_livraison',
            'print_bons_livraison',
            'view_statistics',
            'view_factures',
            'create_factures',
            'edit_factures',
            'delete_factures',
            'manage_config',
            'view_fiche_controle_quality',
            'create_fiche_controle_quality',
            'edit_fiche_controle_quality',
            'delete_fiche_controle_quality',
        ]);

        // Employer
        $employer = Role::firstOrCreate(['name' => 'employer']);
        $employer->syncPermissions([
            'view_commandes',
            'create_commandes',
            'edit_commandes',
            'upload_commande_files',
            'view_commandes_calendar',
            'view_commande_details',
            'view_bons_livraison',
            'view_fiche_controle_quality',
            'create_fiche_controle_quality',
            'edit_fiche_controle_quality',
        ]);

        // Dentist
        $dentist = Role::firstOrCreate(['name' => 'dentist']);
        $dentist->syncPermissions([
            'view_commandes_calendar',
            'view_commande_details',
            'view_bons_livraison',
            'view_factures',
            'view_fiche_controle_quality',
            'edit_fiche_controle_quality',
        ]);

        // Clear cache
        cache()->forget('spatie.permission.cache');
    }
}

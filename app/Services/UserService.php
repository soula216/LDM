<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;

class UserService
{
    /**
     * Créer utilisateur avec rôle assigné
     */
    public function createUser(array $data): User
    {
        $user = User::create([
            'name' => $data['name'] ?? ($data['nom'] . ' ' . $data['prénom']),
            'nom' => $data['nom'] ?? null,
            'prénom' => $data['prénom'] ?? null,
            'email' => !empty($data['email']) ? $data['email'] : null,
            'password' => Hash::make($data['password']),
            'gouvernorat' => $data['gouvernorat'] ?? null,
            'ville' => $data['ville'] ?? null,
            'adresse' => $data['adresse'] ?? null,
            'num_dentist' => $data['num_dentist'] ?? null,
            'order' => $data['order'] ?? null,
            'tél' => $data['tél'] ?? null,
            'num_ordinaire' => $data['num_ordinaire'] ?? null,
            'groupe_id' => $data['groupe_id'] ?? null,
        ]);

        // Assigner rôle (pas admin)
        $roleName = $data['role'] ?? null;
        if ($roleName && $roleName !== 'admin') {
            $user->assignRole($roleName);
        }

        // Invalidate caches
        $this->invalidateUserCaches();

        return $user;
    }

    /**
     * Mettre à jour utilisateur
     */
    public function updateUser(User $user, array $data): User
    {
        $updateData = [
            'name' => $data['name'] ?? $user->name,
            'nom' => $data['nom'] ?? $user->nom,
            'prénom' => $data['prénom'] ?? $user->prénom,
            'email' => isset($data['email']) ? (!empty($data['email']) ? $data['email'] : null) : $user->email,
            'gouvernorat' => $data['gouvernorat'] ?? $user->gouvernorat,
            'ville' => $data['ville'] ?? $user->ville,
            'adresse' => $data['adresse'] ?? $user->adresse,
            'num_dentist' => $data['num_dentist'] ?? $user->num_dentist,
            'order' => $data['order'] ?? $user->order,
            'tél' => $data['tél'] ?? $user->tél,
            'num_ordinaire' => $data['num_ordinaire'] ?? $user->num_ordinaire,
            'groupe_id' => $data['groupe_id'] ?? $user->groupe_id,
        ];

        // Mettre à jour le mot de passe seulement si fourni
        if (isset($data['password']) && !empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        // Changer rôle si fourni (pas en admin)
        if (isset($data['role']) && $data['role'] !== 'admin') {
            $user->syncRoles($data['role']);
        }

        // Invalidate caches
        $this->invalidateUserCaches($user->id);

        return $user->fresh();
    }

    /**
     * Supprimer utilisateur (soft delete)
     */
    public function deleteUser(User $user): void
    {
        $user->delete(); // Soft delete

        $this->invalidateUserCaches($user->id);
    }

    /**
     * Invalider les caches liés à utilisateur
     */
    private function invalidateUserCaches(?int $userId = null): void
    {
        // Caches globaux
        Cache::forget('admin.users.list');
        Cache::forget('admin.users.table.*');
        Cache::forget('admin.dentists.list');
        Cache::forget('admin.teams.list');
        Cache::forget('admin.dashboard.stats');
        cache()->forget('spatie.permission.cache');

        // Cache user spécifique si applicable
        if ($userId) {
            cache()->forget("user.{$userId}.dashboard");
            cache()->forget("admin.nav.{$userId}");
        }

        // Tous les users
        cache()->forget('admin.nav.*');
    }
}

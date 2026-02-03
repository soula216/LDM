<?php

namespace App\Policies;

use App\Models\Commande;
use App\Models\User;

class CommandePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_commandes');
    }

    /**
     * Determine whether the user can view the model.
     * Admin/Responsable/Secrétaire/Prothésiste : toutes
     * Employer : seulement si au moins une tâche de son groupe
     * Dentist : seulement ses commandes
     */
    public function view(User $user, Commande $commande): bool
    {
        if ($user->hasRole('admin') || $user->hasAnyRole(['responsable', 'secretaire', 'prothesiste'])) {
            return true;
        }

        if ($user->hasRole('employer')) {
            return $commande->taches()->where('groupe_id', $user->groupe_id)->exists();
        }

        if ($user->hasRole('dentist')) {
            return $commande->dentiste_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_commandes');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Commande $commande): bool
    {
        return $user->can('edit_commandes');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Commande $commande): bool
    {
        return $user->can('delete_commandes');
    }

    /**
     * Determine whether the user can change commande status.
     * Admin et rôles internes : oui
     * Dentist : non
     */
    public function changeCommandeStatus(User $user, Commande $commande): bool
    {
        if ($user->hasRole('dentist')) {
            return false;
        }

        return $user->can('change_commande_status');
    }
}

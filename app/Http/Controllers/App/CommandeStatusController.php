<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Services\BonLivraisonService;
use App\Events\CommandeUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CommandeStatusController extends Controller
{
    public function update(Request $request, Commande $commande)
    {
        $this->authorize('changeCommandeStatus', $commande);

        $oldStatus = $commande->status;
        $newStatus = $request->input('status');

        // Vérifier si le statut a réellement changé
        if ($oldStatus === $newStatus) {
            return redirect()->back()->with('info', 'Aucune modification détectée');
        }

        $updateData = ['status' => $newStatus];
        
        // Si le statut passe à "Terminée", enregistrer l'utilisateur qui fait ce changement
        if ($newStatus === 'Terminée' && $oldStatus !== 'Terminée') {
            $updateData['finished_by'] = auth()->id();
        }
        
        // Si le statut change de "Terminée" à autre chose, réinitialiser finished_by
        if ($oldStatus === 'Terminée' && $newStatus !== 'Terminée') {
            $updateData['finished_by'] = null;
        }

        $commande->update($updateData);
        
        // Forcer la mise à jour du timestamp updated_at
        $commande->touch();
        
        // Recharger la commande pour avoir le bon updated_at
        $commande->refresh();

        // Générer BL si passage à Terminée
        if ($newStatus === 'Terminée' && $oldStatus !== 'Terminée') {
            app(BonLivraisonService::class)->generateFromCommande($commande);
        }

        // Invalider caches
        Cache::forget("admin.commandes.show.{$commande->id}");
        Cache::forget("app.commandes.modal.{$commande->id}." . auth()->id());
        
        // Invalider tous les caches du calendrier en incrémentant la version
        $version = Cache::get('app.commandes.calendar.version', 0);
        Cache::put('app.commandes.calendar.version', $version + 1, now()->addDays(30));
        
        // Déclencher l'événement de mise à jour seulement si le statut a changé
        event(new CommandeUpdated($commande));

        return redirect()->back()->with('success', 'Statut mis à jour');
    }
}

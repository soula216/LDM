<?php

namespace App\Services;

use App\Models\BonLivraison;
use App\Models\BonLivraisonLigne;
use App\Models\Commande;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class BonLivraisonService
{
    /**
     * Génère un bon de livraison à partir d'une commande (idempotent)
     */
    public function generateFromCommande(Commande $commande): BonLivraison
    {
        // Idempotent : si BL existe, ne pas recréer
        if ($commande->bonLivraison) {
            return $commande->bonLivraison;
        }

        $bl = BonLivraison::create([
            'commande_id' => $commande->id,
            'numero_bl' => $this->generateNumberBl(),
            'created_by' => Auth::id(),
        ]);

        $totalTtc = 0;
        foreach ($commande->taches as $tache) {
            $lineTotalTtc = $tache->prix_unitaire_ttc_snapshot * $tache->nb_elem;
            $totalTtc += $lineTotalTtc;

            BonLivraisonLigne::create([
                'bon_livraison_id' => $bl->id,
                'service_id' => $tache->service_id,
                'service_name_snapshot' => $tache->service->nom,
                'prix_unitaire_ttc_snapshot' => $tache->prix_unitaire_ttc_snapshot,
                'quantite' => $tache->nb_elem,
                'total_ligne_ttc' => $lineTotalTtc,
            ]);
        }

        $bl->update(['total_ttc' => $totalTtc]);

        Cache::forget("bl.commande.{$commande->id}");

        return $bl;
    }

    /**
     * Génère un numéro de BL unique (format: BL-YYYY-XXXXX)
     */
    private function generateNumberBl(): string
    {
        $year = now()->year;
        $count = BonLivraison::whereYear('created_at', $year)->count() + 1;
        return "BL-{$year}-" . str_pad($count, 5, '0', STR_PAD_LEFT);
    }
}

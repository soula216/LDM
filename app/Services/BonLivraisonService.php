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
        // Idempotent : si BL existe, ne pas recréer (vérification en base pour éviter les doublons)
        $existing = $commande->bonLivraison()->first();
        if ($existing) {
            return $existing;
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
                'service_name_snapshot' => $tache->service_nom,
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
        $prefix = "BL-{$year}-";
        $last = BonLivraison::where('numero_bl', 'like', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(numero_bl, 10) AS UNSIGNED) DESC')
            ->value('numero_bl');
        $next = 1;
        if ($last && preg_match('/^BL-\d{4}-(\d+)$/', $last, $m)) {
            $next = (int) $m[1] + 1;
        }
        $numero = $prefix . str_pad($next, 5, '0', STR_PAD_LEFT);
        // En cas de concurrence, réessayer avec un numéro plus grand jusqu'à trouver un libre
        while (BonLivraison::where('numero_bl', $numero)->exists()) {
            $next++;
            $numero = $prefix . str_pad($next, 5, '0', STR_PAD_LEFT);
        }
        return $numero;
    }
}

<?php

namespace App\Services;

use App\Models\BonLivraison;
use App\Models\BonLivraisonLigne;
use App\Models\Commande;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BonLivraisonService
{
    /**
     * Génère un bon de livraison à partir d'une commande (idempotent)
     */
    public function generateFromCommande(Commande $commande): BonLivraison
    {
        $commande->loadMissing('taches');

        $existing = $commande->bonLivraison()->first();
        if ($existing) {
            return $this->syncFromCommande($commande) ?? $existing;
        }

        return DB::transaction(function () use ($commande) {
            $bl = BonLivraison::create([
                'commande_id' => $commande->id,
                'numero_bl' => $this->generateNumberBl(),
                'created_by' => Auth::id(),
            ]);

            $totalTtc = $this->createLignesFromTaches($bl, $commande);
            $bl->update(['total_ttc' => $totalTtc]);

            Cache::forget("bl.commande.{$commande->id}");

            return $bl;
        });
    }

    /**
     * Met à jour le BL existant d'une commande (lignes + total) et recalcule les factures liées.
     */
    public function syncFromCommande(Commande $commande): ?BonLivraison
    {
        $commande->loadMissing(['taches', 'bonLivraison']);
        $bl = $commande->bonLivraison;

        if (!$bl) {
            return null;
        }

        DB::transaction(function () use ($commande, $bl) {
            $bl->lignes()->delete();
            $totalTtc = $this->createLignesFromTaches($bl, $commande);
            $bl->update(['total_ttc' => $totalTtc]);
        });

        Cache::forget("bl.commande.{$commande->id}");

        app(FactureService::class)->recalculateForBonLivraison($bl->fresh());

        return $bl->fresh(['lignes']);
    }

    /**
     * Crée les lignes BL à partir des tâches de la commande.
     */
    private function createLignesFromTaches(BonLivraison $bl, Commande $commande): float
    {
        $totalTtc = 0;

        foreach ($commande->taches as $tache) {
            $lineTotalTtc = (float) $tache->prix_unitaire_ttc_snapshot * (int) $tache->nb_elem;
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

        return $totalTtc;
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
        while (BonLivraison::where('numero_bl', $numero)->exists()) {
            $next++;
            $numero = $prefix . str_pad($next, 5, '0', STR_PAD_LEFT);
        }

        return $numero;
    }
}

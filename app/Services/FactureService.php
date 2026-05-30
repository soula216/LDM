<?php

namespace App\Services;

use App\Models\BonLivraison;
use App\Models\Facture;

class FactureService
{
    /**
     * Recalcule montant, montant_paye et montant_restant à partir des BL liés.
     */
    public function recalculateMontants(Facture $facture): Facture
    {
        $facture->load(['bonsLivraison', 'echeances']);

        $montant = (float) $facture->bonsLivraison->sum('total_ttc');
        $ancienSolde = (float) ($facture->ancien_solde ?? 0);
        $avance = (float) ($facture->avance ?? 0);
        $maxPaye = max(0, $montant + $ancienSolde - $avance);

        if ($facture->echeances->isNotEmpty()) {
            $montantPaye = (float) $facture->echeances()
                ->where('statut_paiement', 'Payé')
                ->sum('montant');
            $montantRestant = $montant + $ancienSolde - $avance - $montantPaye;
        } elseif ($facture->status === 'paid') {
            $montantPaye = $montant;
            $montantRestant = $montant + $ancienSolde - $avance - $montantPaye;
        } elseif ($facture->status === 'partially_paid') {
            $montantPaye = min((float) ($facture->montant_paye ?? 0), $maxPaye);
            $montantRestant = $montant + $ancienSolde - $avance - $montantPaye;
        } else {
            $montantPaye = 0;
            $montantRestant = $montant + $ancienSolde - $avance;
        }

        $facture->update([
            'montant' => $montant,
            'montant_paye' => $montantPaye,
            'montant_restant' => $montantRestant,
        ]);

        return $facture->fresh();
    }

    /**
     * Recalcule toutes les factures liées à un bon de livraison.
     */
    public function recalculateForBonLivraison(BonLivraison $bl): void
    {
        $bl->load('factures');

        foreach ($bl->factures as $facture) {
            $this->recalculateMontants($facture);
        }
    }
}

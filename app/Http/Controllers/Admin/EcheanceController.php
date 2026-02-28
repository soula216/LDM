<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Echeance;
use App\Models\Facture;
use Illuminate\Http\Request;

class EcheanceController extends Controller
{
    /**
     * Convertir la valeur affichée en valeur enum pour la base de données
     */
    private function convertModeReglementToEnum($value)
    {
        $mapping = [
            'Espèces' => 'especes',
            'Virement bancaire' => 'virement_bancaire',
            'Lettre de change (كمبيالة)' => 'lettre_change',
        ];
        return $mapping[$value] ?? $value;
    }

    /**
     * Recalculer montant_paye et montant_restant pour une facture
     */
    private function recalculateFactureMontants(Facture $facture)
    {
        // Calculer le montant payé (somme des échéances avec statut "Payé")
        $montantPaye = $facture->echeances()
            ->where('statut_paiement', 'Payé')
            ->sum('montant');

        // Montant restant = Montant Facture + Ancien Solde - Avance - Montant payé
        $ancienSolde = (float) ($facture->ancien_solde ?? 0);
        $avance = (float) ($facture->avance ?? 0);
        $montantRestant = $facture->montant + $ancienSolde - $avance - $montantPaye;

        // Mettre à jour la facture
        $facture->update([
            'montant_paye' => $montantPaye,
            'montant_restant' => $montantRestant,
        ]);

        return [
            'montant_paye' => $montantPaye,
            'montant_restant' => $montantRestant,
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Facture $facture)
    {
        try {
            $validated = $request->validate([
                'montant' => 'required|numeric|min:0',
                'mode_reglement' => 'required|in:Espèces,Virement bancaire,Lettre de change (كمبيالة)',
                'date' => 'required|date',
                'statut_paiement' => 'nullable|in:Payé,A encaisser,Encaissé,A recevoir',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreurs de validation',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        // Convertir la valeur affichée en valeur enum pour la base de données
        $validated['mode_reglement'] = $this->convertModeReglementToEnum($validated['mode_reglement']);

        // Déterminer automatiquement le statut selon le mode de règlement
        if (in_array($validated['mode_reglement'], ['especes', 'virement_bancaire'])) {
            $validated['statut_paiement'] = 'Payé';
        } elseif ($validated['mode_reglement'] === 'lettre_change') {
            $validated['statut_paiement'] = 'A encaisser';
        }

        $facture->echeances()->create($validated);

        // Recalculer les montants (toujours, car le statut peut être "Payé")
        $this->recalculateFactureMontants($facture);

        // Recharger la facture pour avoir les valeurs à jour
        $facture->refresh();
        
        // Retourner JSON pour les requêtes AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Échéance ajoutée avec succès',
                'montant_paye' => $facture->montant_paye ?? 0,
                'montant_restant' => $facture->montant_restant ?? 0,
            ]);
        }

        return redirect()->route('admin.factures.show', $facture)
            ->with('success', 'Échéance ajoutée avec succès');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Facture $facture, Echeance $echeance)
    {
        // Debug: logger les données reçues
        \Log::info('Données reçues pour update échéance:', $request->all());
        
        try {
            $validated = $request->validate([
                'montant' => 'required|numeric|min:0',
                'mode_reglement' => 'required|in:Espèces,Virement bancaire,Lettre de change (كمبيالة)',
                'date' => 'required|date',
                'statut_paiement' => 'nullable|in:Payé,A encaisser,Encaissé,A recevoir',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Erreurs de validation:', $e->errors());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreurs de validation',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        // Convertir la valeur affichée en valeur enum pour la base de données
        $validated['mode_reglement'] = $this->convertModeReglementToEnum($validated['mode_reglement']);

        // Lors de la mise à jour, respecter le statut choisi par l'utilisateur
        // Ne définir automatiquement le statut que s'il n'est pas fourni
        if (empty($validated['statut_paiement'])) {
            // Déterminer automatiquement le statut selon le mode de règlement seulement si non fourni
            if (in_array($validated['mode_reglement'], ['especes', 'virement_bancaire'])) {
                $validated['statut_paiement'] = 'Payé';
            } elseif ($validated['mode_reglement'] === 'lettre_change') {
                $validated['statut_paiement'] = 'A encaisser';
            }
        }

        $echeance->update($validated);

        // Recalculer les montants (même si le statut change, il faut recalculer)
        $this->recalculateFactureMontants($facture);

        // Recharger la facture pour avoir les valeurs à jour
        $facture->refresh();

        // Retourner JSON pour les requêtes AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Échéance modifiée avec succès',
                'montant_paye' => $facture->montant_paye ?? 0,
                'montant_restant' => $facture->montant_restant ?? 0,
            ]);
        }

        return redirect()->route('admin.factures.show', $facture)
            ->with('success', 'Échéance modifiée avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Facture $facture, Echeance $echeance)
    {
        $echeance->delete();

        // Recalculer les montants (toujours, pour prendre en compte toutes les échéances restantes)
        $this->recalculateFactureMontants($facture);

        // Recharger la facture pour avoir les valeurs à jour
        $facture->refresh();

        // Retourner JSON pour les requêtes AJAX
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Échéance supprimée avec succès',
                'montant_paye' => $facture->montant_paye ?? 0,
                'montant_restant' => $facture->montant_restant ?? 0,
            ]);
        }

        return redirect()->route('admin.factures.show', $facture)
            ->with('success', 'Échéance supprimée avec succès');
    }
}

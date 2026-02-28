<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facture;
use App\Models\User;
use App\Models\BonLivraison;
use App\Models\Commande;
use App\Events\CommandeUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class FactureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view_factures');
        
        $user = auth()->user();
        $query = Facture::with(['dentist', 'bonsLivraison']);

        // Filtrer par dentiste si l'utilisateur est un dentiste
        if ($user->hasRole('dentist')) {
            $query->where('dentist_id', $user->id);
        }

        $factures = $query->latest()->paginate(10);
        $dentists = User::role('dentist')->orderBy('order')->get();
        return view('admin.factures.index', compact('factures', 'dentists'));
    }

    /**
     * Get bons de livraison for a dentist (AJAX)
     */
    public function getBonsLivraison(Request $request)
    {
        $request->validate([
            'dentist_id' => 'required|exists:users,id',
            'facture_id' => 'nullable|exists:factures,id',
        ]);

        $dentistId = $request->dentist_id;
        $factureId = $request->facture_id;

        // Récupérer les BL du dentiste qui ne sont pas déjà facturés
        // ou qui sont facturés dans la facture en cours d'édition
        $query = BonLivraison::whereHas('commande', function ($query) use ($dentistId) {
            $query->where('dentiste_id', $dentistId);
        });

        if ($factureId) {
            // Exclure les BL facturés dans d'autres factures, mais inclure ceux de la facture en cours
            $query->where(function ($q) use ($factureId) {
                $q->whereDoesntHave('factures')
                  ->orWhereHas('factures', function ($q2) use ($factureId) {
                      $q2->where('factures.id', $factureId);
                  });
            });
        } else {
            // Exclure tous les BL déjà facturés
            $query->whereDoesntHave('factures');
        }

        $bonsLivraison = $query->with(['commande.dentiste', 'lignes', 'factures'])->get();

        return response()->json([
            'success' => true,
            'bonsLivraison' => $bonsLivraison->map(function ($bl) use ($factureId) {
                return [
                    'id' => $bl->id,
                    'numero_bl' => $bl->numero_bl,
                    'total_ttc' => $bl->total_ttc,
                    'date' => $bl->created_at->format('Y-m-d'),
                    'commande_num' => $bl->commande->num_cmd ?? '-',
                    'is_selected' => $factureId ? $bl->factures->contains('id', $factureId) : false,
                ];
            })
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'num_facture' => 'required|string|max:255|unique:factures,num_facture',
            'date' => 'required|date',
            'dentist_id' => 'required|exists:users,id',
            'titre_document' => 'required|in:facture,bon_livraison',
            'ancien_solde' => 'nullable|numeric|min:0',
            'avance' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,delivered,paid,partially_paid,rejected',
            'bon_livraison_ids' => 'required|array|min:1',
            'bon_livraison_ids.*' => 'exists:bons_livraison,id',
        ];

        // Calculer le montant total pour la validation
        $montant = BonLivraison::whereIn('id', $request->bon_livraison_ids ?? [])
            ->sum('total_ttc');
        $maxPaye = $montant + (float) ($request->input('ancien_solde', 0)) - (float) ($request->input('avance', 0));

        // Si le statut est "partially_paid", montant_paye est requis et ne doit pas dépasser (Montant Facture + Ancien Solde - Avance)
        if ($request->status === 'partially_paid') {
            $rules['montant_paye'] = ['required', 'numeric', 'min:0', 'max:' . max(0, $maxPaye)];
        } else {
            $rules['montant_paye'] = 'nullable|numeric|min:0';
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            // Calculer montant_paye et montant_restant
            // Montant restant = Montant Facture + Ancien Solde - Avance - Montant payé
            // Si statut = Payée : Montant payé = Montant Facture
            // Si statut = En attente, Envoyé, Rejetée : Montant payé = 0.00
            $ancienSolde = (float) ($validated['ancien_solde'] ?? 0);
            $avance = (float) ($validated['avance'] ?? 0);
            if ($validated['status'] === 'paid') {
                $montantPaye = $montant;
                $montantRestant = $montant + $ancienSolde - $avance - $montantPaye;
            } elseif ($validated['status'] === 'partially_paid') {
                $montantPaye = $validated['montant_paye'];
                $montantRestant = $montant + $ancienSolde - $avance - $montantPaye;
            } else {
                // En attente, Envoyé, Rejetée
                $montantPaye = 0;
                $montantRestant = $montant + $ancienSolde - $avance;
            }

            // Créer la facture
            $facture = Facture::create([
                'num_facture' => $validated['num_facture'],
                'date' => $validated['date'],
                'dentist_id' => $validated['dentist_id'],
                'titre_document' => $validated['titre_document'],
                'montant' => $montant,
                'ancien_solde' => $validated['ancien_solde'] ?? 0,
                'avance' => $validated['avance'] ?? 0,
                'status' => $validated['status'],
                'montant_paye' => $montantPaye,
                'montant_restant' => $montantRestant,
            ]);

            // Associer les BL à la facture
            foreach ($validated['bon_livraison_ids'] as $blId) {
                DB::table('bl_factures')->insert([
                    'facture_id' => $facture->id,
                    'bon_livraison_id' => $blId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Récupérer toutes les commandes liées aux BL et changer leur statut en "Livrée"
            $commandesIds = BonLivraison::whereIn('id', $validated['bon_livraison_ids'])
                ->pluck('commande_id')
                ->unique();

            Commande::whereIn('id', $commandesIds)->update(['status' => 'Livrée']);

            // Invalider les caches des commandes et déclencher les événements
            foreach ($commandesIds as $commandeId) {
                Cache::forget("admin.commandes.show.{$commandeId}");
                Cache::forget("app.commandes.modal.{$commandeId}." . auth()->id());
                // Déclencher l'événement pour chaque commande
                $commande = Commande::find($commandeId);
                if ($commande) {
                    event(new \App\Events\CommandeUpdated($commande));
                }
            }

            // Invalider le cache du calendrier
            $version = Cache::get('app.commandes.calendar.version', 0);
            Cache::put('app.commandes.calendar.version', $version + 1, now()->addDays(30));

            DB::commit();

            return redirect()->route('admin.factures.index')
                ->with('success', 'Facture créée avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la création de la facture: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Facture $facture)
    {
        $user = auth()->user();

        // Vérifier si l'utilisateur est un dentiste et si la facture lui appartient
        if ($user->hasRole('dentist') && $facture->dentist_id !== $user->id) {
            abort(403, 'Vous n\'avez pas accès à cette facture.');
        }

        $facture->load(['dentist', 'bonsLivraison.commande', 'bonsLivraison.lignes', 'echeances']);
        return view('admin.factures.show', compact('facture'));
    }

    /**
     * Print the specified facture.
     */
    public function print(Facture $facture)
    {
        $this->authorize('view_factures');
        
        $user = auth()->user();

        // Vérifier si l'utilisateur est un dentiste et si la facture lui appartient
        if ($user->hasRole('dentist') && $facture->dentist_id !== $user->id) {
            abort(403, 'Vous n\'avez pas accès à cette facture.');
        }
        
        $facture->load([
            'dentist', 
            'bonsLivraison.commande.dentiste', 
            'bonsLivraison.commande.taches',
            'bonsLivraison.lignes.service',
            'echeances'
        ]);
        
        return view('admin.factures.print', compact('facture'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Facture $facture)
    {
        $this->authorize('edit_factures');
        
        $user = auth()->user();

        // Vérifier si l'utilisateur est un dentiste et si la facture lui appartient
        if ($user->hasRole('dentist') && $facture->dentist_id !== $user->id) {
            abort(403, 'Vous n\'avez pas accès à cette facture.');
        }
        
        $facture->load(['dentist', 'bonsLivraison.commande', 'bonsLivraison.lignes']);
        $dentists = User::role('dentist')->orderBy('order')->get();
        return view('admin.factures.edit', compact('facture', 'dentists'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Facture $facture)
    {
        $this->authorize('edit_factures');

        $user = auth()->user();

        // Vérifier si l'utilisateur est un dentiste et si la facture lui appartient
        if ($user->hasRole('dentist') && $facture->dentist_id !== $user->id) {
            abort(403, 'Vous n\'avez pas accès à cette facture.');
        }

        $rules = [
            'num_facture' => 'required|string|max:255|unique:factures,num_facture,' . $facture->id,
            'date' => 'required|date',
            'dentist_id' => 'required|exists:users,id',
            'titre_document' => 'required|in:facture,bon_livraison',
            'ancien_solde' => 'nullable|numeric|min:0',
            'avance' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,delivered,paid,partially_paid,rejected',
            'bon_livraison_ids' => 'required|array|min:1',
            'bon_livraison_ids.*' => 'exists:bons_livraison,id',
        ];

        // Calculer le montant total pour la validation
        $montant = BonLivraison::whereIn('id', $request->bon_livraison_ids ?? [])
            ->sum('total_ttc');
        $maxPaye = $montant + (float) ($request->input('ancien_solde', 0)) - (float) ($request->input('avance', 0));

        // Pour l'édition, montant_paye est optionnel ; max = Montant Facture + Ancien Solde - Avance
        $rules['montant_paye'] = 'nullable|numeric|min:0|max:' . max(0, $maxPaye);

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            // Charger la relation bonsLivraison avant la synchronisation
            $facture->load('bonsLivraison');
            
            // Calculer montant_paye et montant_restant
            // Montant restant = Montant Facture + Ancien Solde - Avance - Montant payé
            // Si statut = Payée : Montant payé = Montant Facture
            // Si statut = En attente, Envoyé, Rejetée : Montant payé = 0.00
            $ancienSolde = (float) ($validated['ancien_solde'] ?? 0);
            $avance = (float) ($validated['avance'] ?? 0);
            if ($validated['status'] === 'paid') {
                $montantPaye = $montant;
                $montantRestant = $montant + $ancienSolde - $avance - $montantPaye;
            } elseif ($validated['status'] === 'partially_paid') {
                if (isset($validated['montant_paye']) && $validated['montant_paye'] !== null) {
                    $montantPaye = $validated['montant_paye'];
                    $montantRestant = $montant + $ancienSolde - $avance - $montantPaye;
                } elseif ($facture->montant_paye !== null) {
                    $montantPaye = $facture->montant_paye;
                    $montantRestant = $montant + $ancienSolde - $avance - $montantPaye;
                } else {
                    $montantPaye = null;
                    $montantRestant = null;
                }
            } else {
                // En attente, Envoyé, Rejetée
                $montantPaye = 0;
                $montantRestant = $montant + $ancienSolde - $avance;
            }

            // Mettre à jour la facture
            $updateData = [
                'num_facture' => $validated['num_facture'],
                'date' => $validated['date'],
                'dentist_id' => $validated['dentist_id'],
                'titre_document' => $validated['titre_document'],
                'montant' => $montant,
                'ancien_solde' => $validated['ancien_solde'] ?? 0,
                'avance' => $validated['avance'] ?? 0,
                'status' => $validated['status'],
            ];

            if ($validated['status'] === 'partially_paid' && $montantPaye === null) {
                $updateData['montant_paye'] = null;
                $updateData['montant_restant'] = null;
            } else {
                $updateData['montant_paye'] = $montantPaye;
                $updateData['montant_restant'] = $montantRestant;
            }

            $facture->update($updateData);

            // Récupérer les anciens BL avant la synchronisation
            $oldBlIds = $facture->bonsLivraison->pluck('id')->toArray();
            
            // Synchroniser les BL (supprimer les anciens et ajouter les nouveaux)
            $facture->bonsLivraison()->sync($validated['bon_livraison_ids']);

            // Récupérer les nouveaux BL ajoutés (ceux qui n'étaient pas dans l'ancienne liste)
            $newBlIds = array_diff($validated['bon_livraison_ids'], $oldBlIds);
            
            // Récupérer les BL retirés (ceux qui étaient dans l'ancienne liste mais pas dans la nouvelle)
            $removedBlIds = array_diff($oldBlIds, $validated['bon_livraison_ids']);
            
            // Récupérer toutes les commandes liées aux nouveaux BL et changer leur statut en "Livrée"
            if (!empty($newBlIds)) {
                $commandesIds = BonLivraison::whereIn('id', $newBlIds)
                    ->pluck('commande_id')
                    ->unique();

                Commande::whereIn('id', $commandesIds)->update(['status' => 'Livrée']);

                // Invalider les caches des commandes et déclencher les événements
                foreach ($commandesIds as $commandeId) {
                    Cache::forget("admin.commandes.show.{$commandeId}");
                    Cache::forget("app.commandes.modal.{$commandeId}." . auth()->id());
                    // Déclencher l'événement pour chaque commande
                    $commande = Commande::find($commandeId);
                    if ($commande) {
                        event(new CommandeUpdated($commande));
                    }
                }
            }

            // Récupérer toutes les commandes liées aux BL retirés et changer leur statut en "Terminée"
            if (!empty($removedBlIds)) {
                $commandesIds = BonLivraison::whereIn('id', $removedBlIds)
                    ->pluck('commande_id')
                    ->unique();

                Commande::whereIn('id', $commandesIds)->update(['status' => 'Terminée']);

                // Invalider les caches des commandes et déclencher les événements
                foreach ($commandesIds as $commandeId) {
                    Cache::forget("admin.commandes.show.{$commandeId}");
                    Cache::forget("app.commandes.modal.{$commandeId}." . auth()->id());
                    // Déclencher l'événement pour chaque commande
                    $commande = Commande::find($commandeId);
                    if ($commande) {
                        event(new CommandeUpdated($commande));
                    }
                }
            }

            // Invalider le cache du calendrier si des changements ont été effectués
            if (!empty($newBlIds) || !empty($removedBlIds)) {
                $version = Cache::get('app.commandes.calendar.version', 0);
                Cache::put('app.commandes.calendar.version', $version + 1, now()->addDays(30));
            }

            DB::commit();

            return redirect()->route('admin.factures.show', $facture)
                ->with('success', 'Facture modifiée avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la modification de la facture: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Facture $facture)
    {
        $this->authorize('delete_factures');

        $user = auth()->user();

        // Vérifier si l'utilisateur est un dentiste et si la facture lui appartient
        if ($user->hasRole('dentist') && $facture->dentist_id !== $user->id) {
            abort(403, 'Vous n\'avez pas accès à cette facture.');
        }

        DB::beginTransaction();
        try {
            // Charger les BLs liés à la facture avant de les détacher
            $facture->load('bonsLivraison');
            
            // Récupérer les IDs des BLs liés à la facture
            $blIds = $facture->bonsLivraison->pluck('id')->toArray();
            
            // Récupérer toutes les commandes liées aux BLs et changer leur statut en "Terminée"
            if (!empty($blIds)) {
                $commandesIds = BonLivraison::whereIn('id', $blIds)
                    ->pluck('commande_id')
                    ->unique();

                Commande::whereIn('id', $commandesIds)->update(['status' => 'Terminée']);

                // Invalider les caches des commandes et déclencher les événements
                foreach ($commandesIds as $commandeId) {
                    Cache::forget("admin.commandes.show.{$commandeId}");
                    Cache::forget("app.commandes.modal.{$commandeId}." . auth()->id());
                    // Déclencher l'événement pour chaque commande
                    $commande = Commande::find($commandeId);
                    if ($commande) {
                        event(new CommandeUpdated($commande));
                    }
                }

                // Invalider le cache du calendrier
                $version = Cache::get('app.commandes.calendar.version', 0);
                Cache::put('app.commandes.calendar.version', $version + 1, now()->addDays(30));
            }
            
            // Détacher tous les BL de la facture
            $facture->bonsLivraison()->detach();
            
            // Supprimer la facture
            $facture->delete();

            DB::commit();

            return redirect()->route('admin.factures.index')
                ->with('success', 'Facture supprimée avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la suppression de la facture: ' . $e->getMessage());
        }
    }
}

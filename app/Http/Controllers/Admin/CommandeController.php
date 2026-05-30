<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\CommandeTache;
use App\Models\User;
use App\Models\Groupe;
use App\Models\Service;
use App\Models\CritereQuality;
use App\Models\FicheControleQuality;
use App\Enums\CritereQualityType;
use App\Services\ServicePricingResolver;
use App\Services\BonLivraisonService;
use App\Events\CommandeUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CommandeController extends Controller
{
    private const COMMANDES_PER_PAGE = 20;

    /**
     * Requête de base pour la liste globale des commandes (admin/commandes).
     */
    private function buildCommandesIndexQuery(Request $request)
    {
        $user = auth()->user();
        $query = Commande::with(['dentiste', 'taches', 'createdBy', 'finishedBy']);

        if ($user->hasRole('employer')) {
            $query->whereHas('taches.service', function ($q) use ($user) {
                $q->where('groupe_id', $user->groupe_id);
            });
        } elseif ($user->hasRole('dentist')) {
            $query->where('dentiste_id', $user->id);
        }

        $search = $request->filled('search') ? trim($request->input('search')) : null;
        if ($search !== null && $search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nom_patient', 'like', '%' . $search . '%')
                    ->orWhereHas('dentiste', function ($q2) use ($search) {
                        $q2->where('nom', 'like', '%' . $search . '%')
                            ->orWhere('prénom', 'like', '%' . $search . '%')
                            ->orWhere('name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->input('date_debut'));
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->input('date_fin'));
        }

        if ($request->filled('status') && in_array($request->input('status'), \App\Enums\CommandeStatus::values(), true)) {
            $query->where('status', $request->input('status'));
        }

        return $query;
    }

    public function index(Request $request)
    {
        $commandes = $this->buildCommandesIndexQuery($request)
            ->latest()
            ->paginate(self::COMMANDES_PER_PAGE)
            ->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.commandes.partials.rows', compact('commandes'))->render(),
                'has_more' => $commandes->hasMorePages(),
            ]);
        }

        return view('admin.commandes.index', compact('commandes'));
    }

    /**
     * Liste des commandes d'un dentiste (page dédiée, identique à index mais filtrée).
     */
    public function dentistCommandes(Request $request, User $user)
    {
        $authUser = auth()->user();
        $query = Commande::with(['dentiste', 'taches', 'createdBy', 'finishedBy'])
            ->where('dentiste_id', $user->id);

        if ($authUser->hasRole('employer')) {
            $query->whereHas('taches.service', function ($q) use ($authUser) {
                $q->where('groupe_id', $authUser->groupe_id);
            });
        }

        $search = $request->filled('search') ? trim($request->input('search')) : null;
        if ($search !== null && $search !== '') {
            $query->where('nom_patient', 'like', '%' . $search . '%');
        }
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->input('date_debut'));
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->input('date_fin'));
        }
        if ($request->filled('status') && in_array($request->input('status'), \App\Enums\CommandeStatus::values(), true)) {
            $query->where('status', $request->input('status'));
        }

        $commandes = $query->latest()->get();
        $dentist = $user;

        return view('admin.commandes.index', compact('commandes', 'dentist'));
    }

    public function create()
    {
        $dentistes = User::role('dentist')->get();
        $services = Service::with('groupe')->get();

        return view('admin.commandes.create', compact('dentistes', 'services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'dentiste_id' => 'required|exists:users,id',
            'num_cmd' => 'required|string|max:50',
            'nom_patient' => 'nullable|string|max:255',
            'urgent' => 'boolean',
            'commentaire' => 'nullable|string',
            'taches' => 'required|array|min:1',
            'taches.*.service_id' => 'required|exists:services,id',
            'taches.*.nb_elem' => 'required|integer|min:1',
            'taches.*.dents' => 'nullable|string|max:255',
            'taches.*.teinte' => 'nullable|string|max:100',
            'taches.*.date_livraison' => 'required|date',
        ]);

        // Ajouter l'utilisateur qui crée la commande
        $validated['created_by'] = auth()->id();

        $commande = Commande::create($validated);

        $resolver = app(ServicePricingResolver::class);

        foreach ($request->input('taches', []) as $tacheData) {
            // Utiliser directement la date de livraison du formulaire avec l'heure (déjà calculée par JavaScript)
            // Le JavaScript gère la logique de calcul selon le checkbox urgent
            // Le contrôleur utilise simplement la date avec l'heure envoyée depuis le formulaire
            $tacheData['date_livraison'] = Carbon::parse($tacheData['date_livraison']);

            // Retirer groupe_id des données car il n'est plus stocké dans commande_taches
            unset($tacheData['groupe_id']);

            // Résoudre prix + snapshot
            $prix = $resolver->resolvePriceTtc(
                $commande->dentiste_id,
                $tacheData['service_id']
            );

            $tacheData['prix_unitaire_ttc_snapshot'] = $prix;
            $tacheData['total_ligne_ttc'] = $prix * $tacheData['nb_elem'];

            $commande->taches()->create($tacheData);
        }

        Cache::forget('admin.commandes.list');
        
        // Invalider tous les caches du calendrier
        $this->invalidateCalendarCaches();
        
        // Déclencher l'événement de mise à jour
        event(new CommandeUpdated($commande));

        return redirect()->route('admin.commandes.show', $commande)
            ->with('success', 'Commande créée avec succès');
    }

    public function show(Commande $commande)
    {
        $user = auth()->user();

        // Vérifier si l'utilisateur est un dentiste et si la commande lui appartient
        if ($user->hasRole('dentist') && $commande->dentiste_id !== $user->id) {
            abort(403, 'Vous n\'avez pas accès à cette commande.');
        }

        // Vérifier si l'utilisateur est un employé et si la commande contient des tâches de son groupe
        if ($user->hasRole('employer')) {
            $hasAccess = $commande->taches()->whereHas('service', function ($q) use ($user) {
                $q->where('groupe_id', $user->groupe_id);
            })->exists();
            
            if (!$hasAccess) {
                abort(403, 'Vous n\'avez pas accès à cette commande.');
            }
        }

        $commande = Cache::remember("admin.commandes.show.{$commande->id}", 300, function () use ($commande) {
            return $commande->load(['dentiste', 'taches.service.groupe', 'taches.ficheControleQuality.createdBy', 'taches.ficheControleQuality.updatedBy', 'files', 'bonLivraison']);
        });

        return view('admin.commandes.show', compact('commande'));
    }

    /**
     * Show commande for app (with role filtering)
     */
    public function showApp(Commande $commande)
    {
        $user = auth()->user();

        // Vérifier si l'utilisateur est un dentiste et si la commande lui appartient
        if ($user->hasRole('dentist') && $commande->dentiste_id !== $user->id) {
            abort(403, 'Vous n\'avez pas accès à cette commande.');
        }

        // Vérifier si l'utilisateur est un employé et si la commande contient des tâches de son groupe
        if ($user->hasRole('employer')) {
            $hasAccess = $commande->taches()->whereHas('service', function ($q) use ($user) {
                $q->where('groupe_id', $user->groupe_id);
            })->exists();
            
            if (!$hasAccess) {
                abort(403, 'Vous n\'avez pas accès à cette commande.');
            }
        }

        $cacheKey = "app.commandes.modal.{$commande->id}.{$user->id}";

        $commande = Cache::remember($cacheKey, 120, function () use ($commande, $user) {
            $commande->load(['dentiste', 'taches.service.groupe', 'bonLivraison']);

            // Filtrer tâches si employer (via le service)
            if ($user->hasRole('employer')) {
                $commande->setRelation('taches', $commande->taches->filter(function($tache) use ($user) {
                    return $tache->service && $tache->service->groupe_id == $user->groupe_id;
                }));
            }

            return $commande;
        });

        return view('app.commandes.show', compact('commande'));
    }

    public function edit(Commande $commande)
    {
        $user = auth()->user();

        // Vérifier si l'utilisateur est un dentiste et si la commande lui appartient
        if ($user->hasRole('dentist') && $commande->dentiste_id !== $user->id) {
            abort(403, 'Vous n\'avez pas accès à cette commande.');
        }

        // Vérifier si l'utilisateur est un employé et si la commande contient des tâches de son groupe
        if ($user->hasRole('employer')) {
            $hasAccess = $commande->taches()->whereHas('service', function ($q) use ($user) {
                $q->where('groupe_id', $user->groupe_id);
            })->exists();
            
            if (!$hasAccess) {
                abort(403, 'Vous n\'avez pas accès à cette commande.');
            }
        }

        $dentistes = User::role('dentist')->get();
        $services = Service::with('groupe')->get();
        $commande->load('files');

        return view('admin.commandes.edit', compact('commande', 'dentistes', 'services'));
    }

    public function update(Request $request, Commande $commande)
    {
        $user = auth()->user();

        // Vérifier si l'utilisateur est un dentiste et si la commande lui appartient
        if ($user->hasRole('dentist') && $commande->dentiste_id !== $user->id) {
            abort(403, 'Vous n\'avez pas accès à cette commande.');
        }

        // Vérifier si l'utilisateur est un employé et si la commande contient des tâches de son groupe
        if ($user->hasRole('employer')) {
            $hasAccess = $commande->taches()->whereHas('service', function ($q) use ($user) {
                $q->where('groupe_id', $user->groupe_id);
            })->exists();
            
            if (!$hasAccess) {
                abort(403, 'Vous n\'avez pas accès à cette commande.');
            }
        }

        // Sauvegarder l'état initial pour comparer
        $commande->load('taches');
        $originalCommande = [
            'dentiste_id' => $commande->dentiste_id,
            'num_cmd' => $commande->num_cmd,
            'nom_patient' => $commande->nom_patient,
            'status' => $commande->status,
            'urgent' => $commande->urgent,
            'commentaire' => $commande->commentaire,
        ];
        
        $originalTaches = $commande->taches->map(function($tache) {
            return [
                'service_id' => (int)$tache->service_id,
                'nb_elem' => (int)$tache->nb_elem,
                'dents' => $tache->dents ?? null,
                'teinte' => $tache->teinte ?? null,
                'date_livraison' => $tache->date_livraison ? $tache->date_livraison->format('Y-m-d H:i:s') : null,
            ];
        })->sortBy(['service_id', 'date_livraison'])->values()->toArray();

        $validated = $request->validate([
            'dentiste_id' => 'required|exists:users,id',
            'num_cmd' => 'required|string|max:50',
            'nom_patient' => 'nullable|string|max:255',
            'status' => 'required|in:Reçue,En cours,Terminée,Livrée',
            'urgent' => 'nullable|boolean',
            'commentaire' => 'nullable|string',
            'taches' => 'required|array|min:1',
            'taches.*.service_id' => 'required|exists:services,id',
            'taches.*.nb_elem' => 'required|integer|min:1',
            'taches.*.dents' => 'nullable|string|max:255',
            'taches.*.teinte' => 'nullable|string|max:100',
            'taches.*.date_livraison' => 'required|date',
        ]);

        // Gérer le champ urgent (checkbox non cochée = non envoyée dans la requête)
        $validated['urgent'] = $request->has('urgent') && $request->input('urgent') == '1';

        // Préparer les nouvelles tâches pour comparaison
        $newTaches = collect($request->input('taches', []))->map(function($tacheData) {
            return [
                'service_id' => (int)$tacheData['service_id'],
                'nb_elem' => (int)$tacheData['nb_elem'],
                'dents' => $tacheData['dents'] ?? null,
                'teinte' => $tacheData['teinte'] ?? null,
                'date_livraison' => Carbon::parse($tacheData['date_livraison'])->format('Y-m-d H:i:s'),
            ];
        })->sortBy(['service_id', 'date_livraison'])->values()->toArray();

        // Normaliser les valeurs null pour la comparaison
        $normalize = function($value) {
            return $value === null || $value === '' ? null : $value;
        };

        // Fonction pour normaliser un tableau en triant récursivement les clés
        $normalizeArray = function($array) use (&$normalizeArray) {
            if (!is_array($array)) {
                return $array;
            }
            ksort($array);
            return array_map(function($item) use (&$normalizeArray) {
                return is_array($item) ? $normalizeArray($item) : $item;
            }, $array);
        };

        // Vérifier si des changements ont été faits sur la commande
        $commandeChanged = 
            $originalCommande['dentiste_id'] != $validated['dentiste_id'] ||
            $normalize($originalCommande['num_cmd']) != $normalize($validated['num_cmd'] ?? null) ||
            $normalize($originalCommande['nom_patient']) != $normalize($validated['nom_patient'] ?? null) ||
            $originalCommande['status'] != $validated['status'] ||
            (bool)$originalCommande['urgent'] != (bool)$validated['urgent'] ||
            $normalize($originalCommande['commentaire']) != $normalize($validated['commentaire'] ?? null);

        // Vérifier si les tâches ont changé (comparaison JSON normalisée)
        $normalizedOriginalTaches = $normalizeArray($originalTaches);
        $normalizedNewTaches = $normalizeArray($newTaches);
        $tachesChanged = json_encode($normalizedOriginalTaches) !== json_encode($normalizedNewTaches);

        // Si aucun changement, ne pas mettre à jour et ne pas notifier
        if (!$commandeChanged && !$tachesChanged) {
            return redirect()->route('admin.commandes.show', $commande)
                ->with('info', 'Aucune modification détectée');
        }

        // Gérer finished_by si le statut passe à "Terminée"
        $oldStatus = $originalCommande['status'];
        $newStatus = $validated['status'];
        
        if ($newStatus === 'Terminée' && $oldStatus !== 'Terminée') {
            // Si le statut passe à "Terminée", enregistrer l'utilisateur qui fait ce changement
            $validated['finished_by'] = auth()->id();
        } elseif ($oldStatus === 'Terminée' && $newStatus !== 'Terminée') {
            // Si le statut change de "Terminée" à autre chose, réinitialiser finished_by
            $validated['finished_by'] = null;
        }
        // Si le statut reste "Terminée", ne pas modifier finished_by (garder celui qui l'a terminée)
        
        // Mettre à jour la commande avec tous les champs validés
        $commande->update($validated);
        
        // Forcer la mise à jour du timestamp updated_at pour garantir la détection
        DB::table('commandes')
            ->where('id', $commande->id)
            ->update(['updated_at' => now()]);

        $resolver = app(ServicePricingResolver::class);

        // Mettre à jour les tâches seulement si elles ont changé
        if ($tachesChanged) {
            $commande->taches()->delete();
            foreach ($request->input('taches', []) as $tacheData) {
                $tacheData['date_livraison'] = Carbon::parse($tacheData['date_livraison']);

                unset($tacheData['groupe_id']);

                // Résoudre prix + snapshot
                $prix = $resolver->resolvePriceTtc(
                    $commande->dentiste_id,
                    $tacheData['service_id']
                );

                $tacheData['prix_unitaire_ttc_snapshot'] = $prix;
                $tacheData['total_ligne_ttc'] = $prix * $tacheData['nb_elem'];

                $commande->taches()->create($tacheData);
            }
        }

        // Forcer la mise à jour du timestamp updated_at pour garantir la détection des modifications
        // Important : même si seulement les tâches changent, on doit mettre à jour updated_at de la commande
        // Utiliser DB::table() pour forcer la mise à jour directement en base de données
        // Cela garantit que updated_at est toujours mis à jour, même si aucun autre champ n'a changé
        DB::table('commandes')
            ->where('id', $commande->id)
            ->update(['updated_at' => now()]);
        
        // Recharger la commande depuis la base de données pour avoir les valeurs à jour
        $commande->refresh();
        
        // Générer BL automatiquement si passage à Terminée et qu'il n'existe pas déjà
        if ($newStatus === 'Terminée' && $oldStatus !== 'Terminée' && !$commande->bonLivraison) {
            app(BonLivraisonService::class)->generateFromCommande($commande);
            // Recharger la commande pour avoir le BL
            $commande->refresh();
        }
        
        // Stocker temporairement l'ID de la commande modifiée par cet utilisateur pour éviter les notifications
        // Clé: "user_modified_commandes.{user_id}" avec expiration de 60 secondes
        $userId = auth()->id();
        $modifiedCommandes = Cache::get("user_modified_commandes.{$userId}", []);
        
        // Ajouter la nouvelle modification
        $modifiedCommandes[] = [
            'commande_id' => $commande->id,
            'updated_at' => $commande->updated_at->toIso8601String(),
            'timestamp' => now()->toIso8601String()
        ];
        
        // Garder seulement les 20 dernières modifications et supprimer les anciennes (> 60 secondes)
        $now = now();
        $modifiedCommandes = collect($modifiedCommandes)->filter(function ($mod) use ($now) {
            $modTime = \Carbon\Carbon::parse($mod['timestamp']);
            return $now->diffInSeconds($modTime) < 60;
        })->values()->toArray();
        
        // Garder seulement les 20 dernières
        $modifiedCommandes = array_slice($modifiedCommandes, -20);
        
        // Stocker dans le cache avec expiration de 60 secondes
        Cache::put("user_modified_commandes.{$userId}", $modifiedCommandes, now()->addSeconds(60));
        
        \Log::info('Commande modifiée - Cache mis à jour', [
            'user_id' => $userId,
            'commande_id' => $commande->id,
            'updated_at' => $commande->updated_at->toIso8601String(),
            'cache_count' => count($modifiedCommandes),
            'cache_content' => $modifiedCommandes
        ]);
        
        // Log pour déboguer (à retirer en production)
        \Log::info('Commande modifiée', [
            'commande_id' => $commande->id,
            'user_id' => $userId,
            'updated_at' => $commande->updated_at->toIso8601String(),
            'commande_changed' => $commandeChanged,
            'taches_changed' => $tachesChanged,
            'now' => now()->toIso8601String()
        ]);

        Cache::forget('admin.commandes.list');
        Cache::forget("admin.commandes.show.{$commande->id}");
        
        // Invalider tous les caches du calendrier
        $this->invalidateCalendarCaches();
        
        // Déclencher l'événement de mise à jour seulement si des changements ont été faits
        event(new CommandeUpdated($commande));

        return redirect()->route('admin.commandes.show', $commande)
            ->with('success', 'Commande mise à jour');
    }

    public function destroy(Commande $commande)
    {
        $user = auth()->user();

        // Vérifier si l'utilisateur est un dentiste et si la commande lui appartient
        if ($user->hasRole('dentist') && $commande->dentiste_id !== $user->id) {
            abort(403, 'Vous n\'avez pas accès à cette commande.');
        }

        // Vérifier si l'utilisateur est un employé et si la commande contient des tâches de son groupe
        if ($user->hasRole('employer')) {
            $hasAccess = $commande->taches()->whereHas('service', function ($q) use ($user) {
                $q->where('groupe_id', $user->groupe_id);
            })->exists();
            
            if (!$hasAccess) {
                abort(403, 'Vous n\'avez pas accès à cette commande.');
            }
        }

        $commande->delete();
        Cache::forget('admin.commandes.list');
        Cache::forget("admin.commandes.show.{$commande->id}");
        
        // Invalider tous les caches du calendrier
        $this->invalidateCalendarCaches();
        
        // Déclencher l'événement de mise à jour (même pour suppression)
        event(new CommandeUpdated($commande));

        return redirect()->route('admin.commandes.index')
            ->with('success', 'Commande supprimée');
    }

    /**
     * Invalider tous les caches du calendrier
     * Utilise un système de versioning pour forcer le rechargement
     */
    private function invalidateCalendarCaches(): void
    {
        // Incrémenter la version du cache du calendrier
        // Cela forcera tous les caches du calendrier à être régénérés
        $version = Cache::get('app.commandes.calendar.version', 0);
        Cache::put('app.commandes.calendar.version', $version + 1, now()->addDays(30));
    }

    public function generateBonLivraison(Commande $commande)
    {
        $this->authorize('view_bons_livraison');

        // Générer le BL même si le statut est "Terminée"
        $bl = app(BonLivraisonService::class)->generateFromCommande($commande);

        Cache::forget("admin.commandes.show.{$commande->id}");

        return redirect()->route('admin.commandes.show', $commande)
            ->with('success', 'Bon de livraison généré avec succès');
    }

    private function calculateDeliveryDate(?string $baseDate, bool $urgent): string
    {
        if ($urgent) {
            return Carbon::now()->addDay()->toDateString();
        }

        if ($baseDate) {
            return Carbon::parse($baseDate)->toDateString();
        }

        return Carbon::now()->addDays(3)->toDateString();
    }

    /**
     * Récupérer les critères de qualité pour une tâche
     */
    public function getTacheCriteres(CommandeTache $tache)
    {
        $this->authorize('view_fiche_controle_quality');
        
        // Vérifier que la tâche a un service avec un groupe
        if (!$tache->service || !$tache->service->groupe) {
            return response()->json([
                'success' => false,
                'message' => 'La tâche n\'a pas de groupe associé'
            ], 404);
        }

        $groupe = $tache->service->groupe;

        // Vérifier si le groupe est "Conjointe" ou "Mobile"
        if (!in_array($groupe->nom, ['Conjointe', 'Mobile'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cette fonctionnalité est uniquement disponible pour les groupes "Conjointe" et "Mobile"'
            ], 400);
        }

        // Récupérer les critères du groupe, groupés par type
        $criteres = CritereQuality::where('groupe_id', $groupe->id)
            ->orderByRaw("FIELD(type, 'Empreinte', 'Contrôle visuel', 'Occlusion', 'Livraison', 'Marque des Matériaux')")
            ->orderBy('id')
            ->get();

        // Grouper par type
        $criteresGroupes = $criteres->groupBy(function ($critere) {
            return $critere->type->value;
        });

        // Récupérer la fiche existante si elle existe
        $fiche = $tache->ficheControleQuality;
        $ficheData = [];
        if ($fiche && $fiche->data) {
            foreach ($fiche->data as $item) {
                $ficheData[$item['critere_id']] = [
                    'validation' => $item['validation'],
                    'remarque' => $item['remarque'] ?? null
                ];
            }
        }

        // Convertir en format JSON
        $result = [];
        $order = ['Empreinte', 'Contrôle visuel', 'Occlusion', 'Livraison', 'Marque des Matériaux'];
        
        foreach ($order as $type) {
            if ($criteresGroupes->has($type)) {
                $result[$type] = $criteresGroupes[$type]->map(function ($critere) use ($ficheData) {
                    $data = [
                        'id' => $critere->id,
                        'nom' => $critere->nom,
                    ];
                    
                    // Ajouter les données de la fiche si elles existent
                    if (isset($ficheData[$critere->id])) {
                        $data['validation'] = $ficheData[$critere->id]['validation'];
                        $data['remarque'] = $ficheData[$critere->id]['remarque'];
                    }
                    
                    return $data;
                })->values();
            }
        }

        return response()->json([
            'success' => true,
            'tache_id' => $tache->id,
            'groupe' => $groupe->nom,
            'criteres' => $result
        ]);
    }

    public function storeFicheControleQuality(Request $request, CommandeTache $tache)
    {
        // Vérifier que la tâche appartient à une commande
        if (!$tache->commande) {
            return response()->json([
                'success' => false,
                'message' => 'Tâche non trouvée'
            ], 404);
        }

        // Vérifier si la fiche existe déjà pour déterminer la permission
        $fiche = FicheControleQuality::where('commande_id', $tache->commande_id)
            ->where('tache_id', $tache->id)
            ->first();
        
        if ($fiche) {
            // Fiche existe : vérifier la permission d'édition
            $this->authorize('edit_fiche_controle_quality');
        } else {
            // Fiche n'existe pas : vérifier la permission de création
            $this->authorize('create_fiche_controle_quality');
        }

        // Valider les données
        $validated = $request->validate([
            'data' => 'required|array',
            'data.*.critere_id' => 'required|exists:critere_quality,id',
            'data.*.validation' => 'required|in:0,1',
            'data.*.remarque' => 'nullable|string|max:1000',
        ]);

        // Vérifier que tous les critères ont une validation
        $criteres = CritereQuality::where('groupe_id', $tache->service->groupe_id)->pluck('id');
        $submittedCriteres = collect($validated['data'])->pluck('critere_id');
        
        if ($criteres->diff($submittedCriteres)->isNotEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tous les critères doivent être validés'
            ], 422);
        }

        // Préparer les données pour le JSON
        $dataToStore = collect($validated['data'])->map(function ($item) {
            return [
                'critere_id' => $item['critere_id'],
                'validation' => (int)$item['validation'],
                'remarque' => $item['remarque'] ?? null,
            ];
        })->values()->toArray();

        // Calculer le nombre de failed
        $nbFailed = collect($dataToStore)->where('validation', 0)->count();
        
        if ($fiche) {
            // Mise à jour : ne modifier que updated_by (created_by reste inchangé)
            $fiche->update([
                'data' => $dataToStore,
                'updated_by' => auth()->id(),
            ]);
        } else {
            // Création : définir created_by, updated_by reste null
            $fiche = FicheControleQuality::create([
                'commande_id' => $tache->commande_id,
                'tache_id' => $tache->id,
                'data' => $dataToStore,
                'created_by' => auth()->id(),
                'updated_by' => null,
            ]);
        }

        // Oublier le cache de la commande pour forcer le rechargement des données
        Cache::forget("admin.commandes.show.{$tache->commande_id}");

        // Charger les relations createdBy et updatedBy pour retourner les noms
        $fiche->load('createdBy', 'updatedBy');
        
        return response()->json([
            'success' => true,
            'message' => 'Fiche de contrôle qualité enregistrée avec succès',
            'fiche_id' => $fiche->id,
            'nb_failed' => $nbFailed,
            'created_by_name' => $fiche->createdBy ? ($fiche->createdBy->full_name ?? ($fiche->createdBy->nom . ' ' . $fiche->createdBy->prénom ?? $fiche->createdBy->name ?? 'N/A')) : null,
            'updated_by_name' => $fiche->updatedBy ? ($fiche->updatedBy->full_name ?? ($fiche->updatedBy->nom . ' ' . $fiche->updatedBy->prénom ?? $fiche->updatedBy->name ?? 'N/A')) : null,
        ]);
    }
}

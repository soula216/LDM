<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\CommandeTache;
use App\Exports\TachesExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class CommandeCalendarController extends Controller
{
    public function index()
    {
        return view('app.commandes.calendar');
    }

    private function bumpCalendarCache(): void
    {
        $version = Cache::get('app.commandes.calendar.version', 0);
        Cache::put('app.commandes.calendar.version', $version + 1, now()->addDays(30));
    }

    private function applyTacheVisibilityFilter($query, $user): void
    {
        if ($user->hasRole('employer')) {
            $groupeId = $user->groupe_id;
            $query->where(function ($q) use ($groupeId) {
                $q->where('groupe_id', $groupeId)
                    ->orWhereHas('service', fn ($q2) => $q2->where('groupe_id', $groupeId));
            });
        } elseif ($user->hasRole('dentist')) {
            $query->whereHas('commande', function ($q) use ($user) {
                $q->where('dentiste_id', $user->id);
            });
        }
    }

    /**
     * Tâches visibles dans le calendrier pour une date (mêmes filtres que la vue).
     */
    private function visibleTachesForCalendarDay(string $date, $user)
    {
        $dateCarbon = Carbon::parse($date)->startOfDay();
        $endDate = $dateCarbon->copy()->endOfDay();

        $query = CommandeTache::query()
            ->whereDate('date_livraison', '>=', $dateCarbon)
            ->whereDate('date_livraison', '<=', $endDate)
            ->whereHas('commande', fn ($q) => $q->where('status', '!=', 'Livrée'));

        $this->applyTacheVisibilityFilter($query, $user);

        return $query
            ->orderByRaw('calendar_sort_order IS NULL')
            ->orderBy('calendar_sort_order')
            ->orderBy('id')
            ->get();
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'tache_ids' => 'required|array|min:1',
            'tache_ids.*' => 'integer|exists:commande_taches,id',
        ]);

        $user = auth()->user();
        $visibleTaches = $this->visibleTachesForCalendarDay($validated['date'], $user);

        $allowedIds = $visibleTaches->pluck('id')->all();
        $requestedIds = array_values(array_map('intval', $validated['tache_ids']));

        if (count($allowedIds) !== count($requestedIds)) {
            return response()->json([
                'message' => 'Liste de tâches invalide pour cette date.',
                'expected' => count($allowedIds),
                'received' => count($requestedIds),
            ], 422);
        }

        $allowedSet = collect($allowedIds)->sort()->values()->all();
        $requestedSet = collect($requestedIds)->unique()->sort()->values()->all();

        if ($allowedSet !== $requestedSet) {
            return response()->json(['message' => 'Liste de tâches invalide pour cette date.'], 422);
        }

        DB::transaction(function () use ($requestedIds) {
            foreach ($requestedIds as $index => $tacheId) {
                CommandeTache::where('id', $tacheId)->update([
                    'calendar_sort_order' => $index + 1,
                ]);
            }
        });

        $this->bumpCalendarCache();

        return response()->json([
            'success' => true,
            'version' => Cache::get('app.commandes.calendar.version', 0),
        ]);
    }

    public function events(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');
        $user = auth()->user();

        // Extraire seulement la partie date (YYYY-MM-DD) du format ISO avec fuseau horaire
        // FullCalendar envoie: "2025-12-29T00:00:00+01:00" ou "2025-12-29T00:00:00-05:00"
        $startDateStr = substr($start, 0, 10); // Prendre les 10 premiers caractères (YYYY-MM-DD)
        $endDateStr = substr($end, 0, 10);

        // Récupérer la version du cache pour forcer l'invalidation
        $cacheVersion = Cache::get('app.commandes.calendar.version', 0);
        $cacheKey = "app.commandes.calendar.events.{$user->id}.{$startDateStr}.{$endDateStr}.v{$cacheVersion}";

        $events = Cache::remember($cacheKey, 120, function () use ($user) {
            // Charger toutes les commandes avec leurs tâches (sans filtrer par date dans la requête)
            $query = Commande::with(['taches.service.groupe', 'taches.groupe', 'dentiste'])
                ->where('status', '!=', 'Livrée');

            // Filtrage rôle selon le cahier des charges
            if ($user->hasRole('employer')) {
                $query->whereHas('taches', function ($q) use ($user) {
                    $q->where(function ($q2) use ($user) {
                        $q2->where('groupe_id', $user->groupe_id)
                            ->orWhereHas('service', fn ($q3) => $q3->where('groupe_id', $user->groupe_id));
                    });
                });
            } elseif ($user->hasRole('dentist')) {
                $query->where('dentiste_id', $user->id);
            }
            // admin, responsable, secretaire, prothesiste voient toutes les commandes

            return $query->get();
        });

        // Créer un événement pour chaque tâche de chaque commande
        $calendarEvents = [];
        
        // Convertir les dates en Carbon pour la comparaison
        $startDate = Carbon::parse($startDateStr)->startOfDay();
        $endDate = Carbon::parse($endDateStr)->endOfDay();
        
        foreach ($events as $commande) {
            foreach ($commande->taches as $tache) {
                // Filtrer les tâches par groupe pour les employés (via le service)
                if ($user->hasRole('employer')) {
                    $matchesGroupe = $tache->groupe_id == $user->groupe_id
                        || ($tache->service && $tache->service->groupe_id == $user->groupe_id);
                    if (!$matchesGroupe) {
                        continue;
                    }
                }
                
                // Vérifier que la date de livraison existe
                if (!$tache->date_livraison) {
                    continue;
                }
                
                // Convertir en Carbon pour la comparaison (la date_livraison est déjà un Carbon instance grâce au cast)
                $tacheDate = $tache->date_livraison instanceof Carbon 
                    ? $tache->date_livraison->copy()->startOfDay()
                    : Carbon::parse($tache->date_livraison)->startOfDay();
                
                // Vérifier que la date est dans la plage (inclusive)
                if ($tacheDate->gte($startDate) && $tacheDate->lte($endDate)) {
                    
                    // Obtenir la date et l'heure complète de la tâche
                    $tacheDateTime = $tache->date_livraison instanceof Carbon 
                        ? $tache->date_livraison->copy()
                        : Carbon::parse($tache->date_livraison);
                    
                    $now = Carbon::now();
                    
                    // Calculer la différence en heures
                    $hoursUntilDelivery = $now->diffInHours($tacheDateTime, false);
                    
                    // Vérifier si la date/heure est passée ou s'il reste moins de 2 heures
                    $isUrgentOrPast = $tacheDateTime->isPast() || $hoursUntilDelivery < 2;
                    
                    // Vérifier si le statut est "Reçue" ou "En cours"
                    $isStatusRelevant = in_array($commande->status, ['Reçue', 'En cours']);
                    
                    // Couleur selon le statut, mais rouge si passé ou moins de 2h ET statut est "Reçue" ou "En cours"
                    if ($isUrgentOrPast && $isStatusRelevant) {
                        $color = '#EF4444'; // Rouge pour urgent/passé uniquement si statut Reçue ou En cours
                    } else {
                        $color = match($commande->status) {
                            'Reçue' => '#6B7280',      // Gris
                            'En cours' => '#F59E0B',   // Orange/Warning
                            'Terminée' => '#22C55E',   // Vert/Success
                            'Livrée' => '#0EA5E9',     // Bleu/Primary
                            default => '#6B7280',
                        };
                    }

                    // Couleur de bordure (même couleur que le background)
                    $borderColor = $color;

                    // Format de l'heure
                    $heure = $tacheDateTime->format('H:i');
                    
                    // Déterminer si on affiche le dentiste ou le patient
                    // Si l'utilisateur est dentiste, afficher le patient, sinon afficher le dentiste
                    $displayName = $user->hasRole('dentist') 
                        ? $commande->nom_patient 
                        : ($commande->dentiste->full_name ?? $commande->dentiste->name ?? 'N/A');
                    
                    // Construire le titre avec le nom du service
                    $urgentPrefix = $commande->urgent ? '⚡ ' : '';
                    $serviceName = $tache->service_nom !== '-' ? $tache->service_nom : 'N/A';
                    
                    $calendarEvents[] = [
                        'id' => "commande-{$commande->id}-tache-{$tache->id}",
                        'title' => $urgentPrefix . $serviceName, // Titre principal avec le nom du service
                        'start' => $tache->date_livraison->toDateString(),
                        'backgroundColor' => $color,
                        'borderColor' => $borderColor,
                        'textColor' => '#FFFFFF',
                        'extendedProps' => [
                            'commande_id' => $commande->id,
                            'tache_id' => $tache->id,
                            'num_cmd' => $commande->num_cmd,
                            'heure' => $heure,
                            'date_livraison_formatted' => $tacheDateTime->format('d/m/Y H:i'),
                            'display_name' => $displayName,
                            'nom_patient' => $commande->nom_patient,
                            'status' => $commande->status,
                            'urgent' => $commande->urgent,
                            'dentiste' => $commande->dentiste->full_name ?? $commande->dentiste->name,
                            'service' => $serviceName,
                            'groupe' => $tache->groupe?->nom ?? $tache->service?->groupe?->nom ?? '',
                            'nb_elem' => $tache->nb_elem,
                            'teinte' => $tache->teinte,
                            'commentaire' => $commande->commentaire,
                            'prix_unitaire_ttc' => $tache->prix_unitaire_ttc_snapshot,
                            'total_ligne_ttc' => $tache->total_ligne_ttc,
                            'displayOrder' => $tache->calendar_sort_order ?? 999999,
                        ],
                        'url' => route('app.commandes.show', $commande->id),
                    ];
                }
            }
        }

        usort($calendarEvents, function ($a, $b) {
            $orderA = $a['extendedProps']['displayOrder'] ?? 999999;
            $orderB = $b['extendedProps']['displayOrder'] ?? 999999;
            if ($orderA !== $orderB) {
                return $orderA <=> $orderB;
            }

            return ($a['extendedProps']['tache_id'] ?? 0) <=> ($b['extendedProps']['tache_id'] ?? 0);
        });

        return response()->json($calendarEvents);
    }

    /**
     * Vérifier la version du cache pour détecter les mises à jour
     */
    public function checkVersion(Request $request)
    {
        $currentVersion = Cache::get('app.commandes.calendar.version', 0);
        
        return response()->json([
            'version' => $currentVersion,
            'timestamp' => now()->toIso8601String()
        ]);
    }

    /**
     * Vérifier les nouvelles commandes créées et les modifications
     */
    public function checkNew(Request $request)
    {
        $user = auth()->user();
        $lastCommandeId = (int) $request->input('last_id', 0);
        $lastCheckTime = $request->input('last_check_time');

        // Convertir le timestamp en Carbon si fourni
        $lastCheckCarbon = null;
        if ($lastCheckTime) {
            try {
                $lastCheckCarbon = Carbon::parse($lastCheckTime);
            } catch (\Exception $e) {
                // Si le format est invalide, ignorer
            }
        }

        // Récupérer l'ID de la dernière commande créée (toutes commandes confondues)
        $globalLastCommande = Commande::orderBy('id', 'desc')->first();
        $globalLastId = $globalLastCommande ? $globalLastCommande->id : 0;

        // Construire la requête avec filtrage selon les permissions de l'utilisateur
        $query = Commande::query();

        // Déterminer si l'utilisateur voit toutes les commandes
        $seesAllCommandes = $user->hasRole('admin') || 
                           $user->hasAnyRole(['responsable', 'secretaire', 'prothesiste']);

        // Filtrage selon les permissions de l'utilisateur
        if ($user->hasRole('employer')) {
            $query->whereHas('taches', function ($q) use ($user) {
                $q->where(function ($q2) use ($user) {
                    $q2->where('groupe_id', $user->groupe_id)
                        ->orWhereHas('service', fn ($q3) => $q3->where('groupe_id', $user->groupe_id));
                });
            });
        } elseif ($user->hasRole('dentist')) {
            $query->where('dentiste_id', $user->id);
        }
        // admin, responsable, secretaire, prothesiste voient toutes les commandes (pas de filtre)

        // Pour les utilisateurs qui voient toutes les commandes, utiliser le dernier ID global
        // Pour les autres, utiliser le dernier ID filtré
        if ($seesAllCommandes) {
            $userLastId = $globalLastId;
        } else {
            $userLastCommande = $query->orderBy('id', 'desc')->first();
            $userLastId = $userLastCommande ? $userLastCommande->id : 0;
        }

        $newCommandes = collect();
        $updatedCommandes = collect();

        // Récupérer les nouvelles commandes créées depuis le dernier ID connu
        if ($lastCommandeId > 0) {
            $newCommandes = $query->where('id', '>', $lastCommandeId)
                ->orderBy('id', 'asc')
                ->get(['id', 'num_cmd', 'nom_patient', 'created_at', 'updated_at', 'status']);
        } else {
            // Première vérification : ne pas retourner de nouvelles commandes
            $newCommandes = collect();
        }

        // Récupérer les commandes modifiées depuis la dernière vérification
        // On cherche les commandes dont updated_at est plus récent que lastCheckCarbon
        // IMPORTANT: Ne pas utiliser id > lastCommandeId pour les modifications, car cela exclut les commandes existantes modifiées
        if ($lastCheckCarbon) {
            // Soustraire 20 secondes pour éviter les problèmes de timing et garantir la détection
            $checkTime = $lastCheckCarbon->copy()->subSeconds(20);
            
            // Créer une nouvelle requête pour les modifications (sans le filtre id > lastCommandeId)
            $updatedQuery = Commande::query();
            
            // Appliquer les filtres de permissions selon l'utilisateur
            if ($user->hasRole('employer')) {
                $updatedQuery->whereHas('taches', function ($q) use ($user) {
                    $q->where(function ($q2) use ($user) {
                        $q2->where('groupe_id', $user->groupe_id)
                            ->orWhereHas('service', fn ($q3) => $q3->where('groupe_id', $user->groupe_id));
                    });
                });
            } elseif ($user->hasRole('dentist')) {
                $updatedQuery->where('dentiste_id', $user->id);
            }
            // admin, responsable, secretaire, prothesiste voient toutes les commandes (pas de filtre)
            
            // Chercher les commandes modifiées depuis le checkTime
            // IMPORTANT: Ne pas filtrer par id > lastCommandeId car on veut aussi les commandes existantes modifiées
            $updatedQuery->where('updated_at', '>', $checkTime);
            
            // Exclure les nouvelles commandes créées récemment (pour éviter les doublons)
            if ($lastCommandeId > 0 && $newCommandes->count() > 0) {
                $newIds = $newCommandes->pluck('id')->toArray();
                $updatedQuery->whereNotIn('id', $newIds);
            }
            
            // Exclure les commandes créées très récemment (dans les 30 dernières secondes)
            // car elles sont déjà gérées comme nouvelles commandes
            $recentCreatedTime = now()->subSeconds(30);
            $updatedQuery->where('created_at', '<', $recentCreatedTime);
            
            $updatedCommandes = $updatedQuery->orderBy('updated_at', 'asc')
                ->get(['id', 'num_cmd', 'nom_patient', 'updated_at', 'status', 'created_at']);
            
            // Log pour déboguer (à retirer en production)
            \Log::info('Vérification des commandes modifiées', [
                'user_id' => $user->id,
                'user_roles' => $user->getRoleNames()->toArray(),
                'last_check_time' => $lastCheckCarbon->toIso8601String(),
                'check_time' => $checkTime->toIso8601String(),
                'now' => now()->toIso8601String(),
                'updated_count' => $updatedCommandes->count(),
                'updated_ids' => $updatedCommandes->pluck('id')->toArray(),
                'updated_at_values' => $updatedCommandes->map(function($c) {
                    return ['id' => $c->id, 'updated_at' => $c->updated_at->toIso8601String()];
                })->toArray(),
                'sql' => $updatedQuery->toSql(),
                'bindings' => $updatedQuery->getBindings()
            ]);
        } else {
            $updatedCommandes = collect();
        }

        $result = [
            'last_commande_id' => $userLastId,
            'last_check_time' => now()->toIso8601String(),
            'new_commandes' => [],
            'updated_commandes' => [],
            'finished_commandes' => [],
            'has_new' => false,
            'has_updates' => false,
            'has_finished' => false
        ];

        // Traiter les nouvelles commandes
        if ($newCommandes->count() > 0) {
            $result['new_commandes'] = $newCommandes->map(function ($commande) {
                return [
                    'id' => $commande->id,
                    'num_cmd' => $commande->num_cmd,
                    'nom_patient' => $commande->nom_patient,
                    'created_at' => $commande->created_at->toIso8601String(),
                    'type' => 'created'
                ];
            })->toArray();
            $result['has_new'] = true;
        }

        // Détecter les commandes terminées récemment (pour notifier admin et responsable)
        $finishedCommandes = collect();
        if ($lastCheckCarbon) {
            $checkTime = $lastCheckCarbon->copy()->subSeconds(20);
            
            $finishedQuery = Commande::query()
                ->where('status', 'Terminée')
                ->whereNotNull('finished_by')
                ->where('updated_at', '>', $checkTime)
                ->with('finishedBy');
            
            // Appliquer les filtres de permissions
            if ($user->hasRole('employer')) {
                $finishedQuery->whereHas('taches', function ($q) use ($user) {
                    $q->where(function ($q2) use ($user) {
                        $q2->where('groupe_id', $user->groupe_id)
                            ->orWhereHas('service', fn ($q3) => $q3->where('groupe_id', $user->groupe_id));
                    });
                });
            } elseif ($user->hasRole('dentist')) {
                $finishedQuery->where('dentiste_id', $user->id);
            }
            
            $finishedCommandes = $finishedQuery->get(['id', 'num_cmd', 'nom_patient', 'finished_by', 'updated_at']);
        }
        
        // Traiter les commandes modifiées
        // Exclure les commandes modifiées par l'utilisateur actuel
        $userModifiedCommandes = Cache::get("user_modified_commandes.{$user->id}", []);
        $userModifiedIds = collect($userModifiedCommandes)->pluck('commande_id')->unique()->toArray();
        
        if ($updatedCommandes->count() > 0) {
            // Filtrer les commandes modifiées par l'utilisateur actuel
            $filteredUpdatedCommandes = $updatedCommandes->filter(function ($commande) use ($userModifiedIds, $userModifiedCommandes, $user) {
                // Si cette commande est dans la liste des commandes modifiées par l'utilisateur
                if (in_array($commande->id, $userModifiedIds)) {
                    // Vérifier le timestamp pour être sûr que c'est la même modification
                    $userModification = collect($userModifiedCommandes)->firstWhere('commande_id', $commande->id);
                    if ($userModification) {
                        $commandeUpdatedAt = $commande->updated_at->toIso8601String();
                        $userModificationUpdatedAt = $userModification['updated_at'];
                        
                        // Comparer les timestamps (avec une tolérance de 2 secondes pour les problèmes de timing)
                        $commandeTime = \Carbon\Carbon::parse($commandeUpdatedAt);
                        $userModTime = \Carbon\Carbon::parse($userModificationUpdatedAt);
                        
                        // Si la différence est inférieure à 5 secondes, c'est probablement la même modification
                        if ($commandeTime->diffInSeconds($userModTime) < 5) {
                            \Log::info('Commande exclue (modifiée par utilisateur)', [
                                'user_id' => $user->id,
                                'commande_id' => $commande->id,
                                'commande_updated_at' => $commandeUpdatedAt,
                                'user_modification_updated_at' => $userModificationUpdatedAt,
                                'diff_seconds' => $commandeTime->diffInSeconds($userModTime)
                            ]);
                            return false; // Exclure cette commande
                        }
                    }
                }
                return true; // Garder cette commande
            });
            
            $result['updated_commandes'] = $filteredUpdatedCommandes->map(function ($commande) {
                return [
                    'id' => $commande->id,
                    'num_cmd' => $commande->num_cmd,
                    'nom_patient' => $commande->nom_patient,
                    'updated_at' => $commande->updated_at->toIso8601String(),
                    'status' => $commande->status,
                    'type' => 'updated'
                ];
            })->toArray();
            
            if ($filteredUpdatedCommandes->count() > 0) {
                $result['has_updates'] = true;
            }
            
            // Log pour déboguer
            \Log::info('Filtrage des commandes modifiées', [
                'user_id' => $user->id,
                'total_updated' => $updatedCommandes->count(),
                'filtered_count' => $filteredUpdatedCommandes->count(),
                'user_modified_ids' => $userModifiedIds,
                'user_modified_commandes' => $userModifiedCommandes
            ]);
        }
        
        // Traiter les commandes terminées (pour notifier admin et responsable uniquement)
        if ($finishedCommandes->count() > 0 && ($user->hasRole('admin') || $user->hasRole('responsable'))) {
            // Exclure les commandes terminées par l'utilisateur actuel
            $filteredFinishedCommandes = $finishedCommandes->filter(function ($commande) use ($user) {
                return $commande->finished_by !== $user->id;
            });
            
            if ($filteredFinishedCommandes->count() > 0) {
                $result['finished_commandes'] = $filteredFinishedCommandes->map(function ($commande) {
                    return [
                        'id' => $commande->id,
                        'num_cmd' => $commande->num_cmd,
                        'nom_patient' => $commande->nom_patient,
                        'finished_by' => [
                            'id' => $commande->finishedBy->id,
                            'name' => $commande->finishedBy->full_name ?? $commande->finishedBy->name,
                        ],
                        'updated_at' => $commande->updated_at->toIso8601String(),
                        'type' => 'finished'
                    ];
                })->toArray();
                $result['has_finished'] = true;
            }
        }

        return response()->json($result);
    }

    /**
     * Exporter les tâches du jour vers Excel
     */
    public function exportExcel(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        $user = auth()->user();

        // Formater la date au format jj-mm-aaaa pour le nom du fichier
        $dateFormatted = \Carbon\Carbon::parse($date)->format('d-m-Y');
        $fileName = 'taches_' . $dateFormatted . '.xlsx';

        return Excel::download(new TachesExport($date, $user), $fileName);
    }
}

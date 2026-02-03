<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Commande : ') . $commande->num_cmd }}
            </h2>
            <a href="{{ route('app.commandes.calendar') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Retour au calendrier
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Informations Commande -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-semibold">Informations Générales</h3>
                        @can('change_commande_status')
                        <form action="{{ route('app.commandes.status.update', $commande) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="rounded-md border-gray-300 shadow-sm" onchange="this.form.submit()">
                                <option value="Reçue" {{ $commande->status === 'Reçue' ? 'selected' : '' }}>Reçue</option>
                                <option value="En cours" {{ $commande->status === 'En cours' ? 'selected' : '' }}>En cours</option>
                                <option value="Terminée" {{ $commande->status === 'Terminée' ? 'selected' : '' }}>Terminée</option>
                                <option value="Livrée" {{ $commande->status === 'Livrée' ? 'selected' : '' }}>Livrée</option>
                            </select>
                        </form>
                        @endcan
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Numéro Commande</p>
                            <p class="font-semibold">{{ $commande->num_cmd }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Patient</p>
                            <p class="font-semibold">{{ $commande->nom_patient }}</p>
                        </div>
                        @unless(auth()->user()->hasRole('dentist'))
                        <div>
                            <p class="text-sm text-gray-500">Dentiste</p>
                            <p class="font-semibold">{{ $commande->dentiste->full_name ?? $commande->dentiste->name }}</p>
                        </div>
                        @endunless
                        <div>
                            <p class="text-sm text-gray-500">Statut</p>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($commande->status === 'Terminée') bg-green-100 text-green-800
                                @elseif($commande->status === 'En cours') bg-yellow-100 text-yellow-800
                                @elseif($commande->status === 'Livrée') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $commande->status }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Urgent</p>
                            <p class="font-semibold">{{ $commande->urgent ? 'Oui' : 'Non' }}</p>
                        </div>
                        @if($commande->bonLivraison && !auth()->user()->hasRole('employer'))
                        <div>
                            <p class="text-sm text-gray-500">Bon de Livraison</p>
                            <a href="{{ route('app.bl.show', $commande->bonLivraison) }}" class="text-blue-600 hover:text-blue-900 font-semibold">
                                {{ $commande->bonLivraison->numero_bl }}
                            </a>
                        </div>
                        @endif
                        @if($commande->commentaire)
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-500">Commentaire</p>
                            <p class="font-semibold">{{ $commande->commentaire }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tâches -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Tâches</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    @unless(auth()->user()->hasRole('dentist'))
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Groupe</th>
                                    @endunless
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nb Éléments</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teinte</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Livraison</th>
                                    @unless(auth()->user()->hasRole('employer'))
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix Unitaire</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                    @endunless
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php
                                    $taches = $commande->taches;
                                    // Filtrer par groupe si employer (via le service)
                                    if (auth()->user()->hasRole('employer')) {
                                        $taches = $taches->filter(function($tache) {
                                            return $tache->service && $tache->service->groupe_id == auth()->user()->groupe_id;
                                        });
                                    }
                                @endphp
                                @forelse($taches as $tache)
                                    <tr>
                                        @unless(auth()->user()->hasRole('dentist'))
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $tache->service->groupe->nom ?? '-' }}</td>
                                        @endunless
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $tache->service->nom ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $tache->nb_elem }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $tache->teinte ?? '-' }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            <div class="font-medium">{{ $tache->date_livraison->format('d/m/Y') }}</div>
                                            <div class="text-gray-500 text-xs mt-1">{{ $tache->date_livraison->format('H:i') }}</div>
                                        </td>
                                        @unless(auth()->user()->hasRole('employer'))
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($tache->prix_unitaire_ttc_snapshot, 2) }} TND</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">{{ number_format($tache->total_ligne_ttc, 2) }} TND</td>
                                        @endunless
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="@php
                                            $colspan = 5; // Service, Nb Éléments, Teinte, Date Livraison
                                            if (!auth()->user()->hasRole('dentist')) $colspan++; // Groupe
                                            if (!auth()->user()->hasRole('employer')) $colspan += 2; // Prix Unitaire, Total
                                            echo $colspan;
                                        @endphp" class="px-6 py-4 text-center text-gray-500">
                                            Aucune tâche trouvée
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($commande->bonLivraison && !auth()->user()->hasRole('employer'))
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Bon de Livraison</h3>
                        @can('print_bons_livraison')
                        <a href="{{ route('app.bl.print', $commande->bonLivraison) }}" target="_blank" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Imprimer BL
                        </a>
                        @endcan
                    </div>
                    <p class="text-sm text-gray-600">Numéro : <span class="font-semibold">{{ $commande->bonLivraison->numero_bl }}</span></p>
                    <p class="text-sm text-gray-600">Total TTC : <span class="font-semibold">{{ number_format($commande->bonLivraison->total_ttc, 2) }} TND</span></p>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>

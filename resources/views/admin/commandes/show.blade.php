<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-primary rounded-lg">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h2 class="text-xl sm:text-2xl font-semibold text-primary">
                    {{ __('Commande : ') . $commande->num_cmd }}
                </h2>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                @can('edit_commandes')
                <a href="{{ route('admin.commandes.edit', $commande) }}" class="btn-primary inline-flex items-center justify-center w-full sm:w-auto" style="background-color: #F59E0B; border-color: #F59E0B;">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Modifier
                </a>
                @endcan
                <a href="{{ route('admin.commandes.index') }}" class="btn-secondary inline-flex items-center justify-center w-full sm:w-auto">
                    Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-8 bg-app min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Informations Commande -->
            <div class="card mb-6">
                <div class="p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-primary mb-4">Informations Générales</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-secondary mb-1">Numéro Commande</p>
                            <p class="font-medium text-primary">{{ $commande->num_cmd }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-secondary mb-1">Patient</p>
                            <p class="font-medium text-primary">{{ $commande->nom_patient ?? '-' }}</p>
                        </div>
                        @unless(auth()->user()->hasRole('dentist'))
                        <div>
                            <p class="text-sm text-secondary mb-1">Dentiste</p>
                            <p class="font-medium text-primary">{{ $commande->dentiste->full_name ?? $commande->dentiste->name }}</p>
                        </div>
                        @endunless
                        <div>
                            <p class="text-sm text-secondary mb-1">Statut</p>
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($commande->status === 'Terminée') bg-green-100 text-green-800
                                @elseif($commande->status === 'En cours') bg-yellow-100 text-yellow-800
                                @elseif($commande->status === 'Livrée') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $commande->status }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-secondary mb-1">Urgent</p>
                            <p class="font-medium text-primary">{{ $commande->urgent ? 'Oui' : 'Non' }}</p>
                        </div>
                        @if($commande->commentaire)
                        <div class="sm:col-span-2">
                            <p class="text-sm text-secondary mb-1">Commentaire</p>
                            <p class="font-medium text-primary">{{ $commande->commentaire }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tâches -->
            <div class="card mb-6">
                <div class="p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-primary mb-4">Tâches</h3>
                    <div class="overflow-x-auto -mx-4 sm:mx-0">
                        <div class="inline-block min-w-full align-middle">
                            <table class="min-w-full divide-y divide-border">
                                <thead class="bg-neutral-50">
                                    <tr>
                                        @unless(auth()->user()->hasRole('dentist'))
                                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Groupe</th>
                                        @endunless
                                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Service</th>
                                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Nb Éléments</th>
                                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider hidden sm:table-cell">Teinte</th>
                                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Date Livraison</th>
                                        @unless(auth()->user()->hasRole('employer'))
                                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider hidden md:table-cell">Prix Unitaire</th>
                                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Total</th>
                                        @endunless
                                        @if(auth()->user()->can('view_fiche_controle_quality') && (($commande->status === 'Terminée' || $commande->status === 'Livrée') && (!auth()->user()->hasRole('dentist') || $commande->status === 'Livrée')))
                                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider" style="min-width: 200px; max-width: 250px;">Fiche de contrôle qualité</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-border">
                                    @foreach($commande->taches as $tache)
                                        <tr class="hover:bg-neutral-50">
                                            @unless(auth()->user()->hasRole('dentist'))
                                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm text-primary">{{ $tache->service->groupe->nom ?? '-' }}</td>
                                            @endunless
                                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm text-primary">{{ $tache->service->nom ?? '-' }}</td>
                                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm text-primary">{{ $tache->nb_elem }}</td>
                                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm text-primary hidden sm:table-cell">{{ $tache->teinte ?? '-' }}</td>
                                            <td class="px-3 sm:px-6 py-4 text-sm">
                                                <div class="font-medium text-primary">{{ $tache->date_livraison->format('d/m/Y') }}</div>
                                                <div class="text-secondary text-xs mt-1">{{ $tache->date_livraison->format('H:i') }}</div>
                                            </td>
                                            @unless(auth()->user()->hasRole('employer'))
                                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm text-primary hidden md:table-cell">{{ number_format($tache->prix_unitaire_ttc_snapshot, 2) }} TND</td>
                                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm font-semibold text-primary">{{ number_format($tache->total_ligne_ttc, 2) }} TND</td>
                                            @endunless
                                            @if(auth()->user()->can('view_fiche_controle_quality') && (($commande->status === 'Terminée' || $commande->status === 'Livrée') && (!auth()->user()->hasRole('dentist') || $commande->status === 'Livrée')))
                                            @php
                                                $fiche = $tache->ficheControleQuality;
                                                $ficheExists = $fiche !== null;
                                                $canCreate = auth()->user()->can('create_fiche_controle_quality');
                                                $canEdit = auth()->user()->can('edit_fiche_controle_quality');
                                            @endphp
                                            <td class="px-3 sm:px-6 py-4 text-sm" style="min-width: 200px; max-width: 250px;">
                                                <div class="flex flex-col gap-1">
                                                    <button type="button" 
                                                            onclick="openFicheModal({{ $tache->id }}, '{{ addslashes($tache->service->nom ?? '') }}', {{ $ficheExists ? 'true' : 'false' }}, {{ $canCreate ? 'true' : 'false' }}, {{ $canEdit ? 'true' : 'false' }})" 
                                                            class="font-medium inline-flex items-center transition-colors duration-200 text-blue-600">
                                                        <svg class="w-4 h-4 mr-1 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                        <span class="whitespace-nowrap">Voir Fiche</span>
                                                    </button>
                                                    @php
                                                        $nbFailed = 0;
                                                        if ($fiche && $fiche->data) {
                                                            $nbFailed = collect($fiche->data)->where('validation', 0)->count();
                                                        }
                                                    @endphp
                                                    <div id="fiche-status-{{ $tache->id }}" class="break-words">
                                                        @if($fiche)
                                                            @if($nbFailed === 0)
                                                                <span class="text-xs font-medium" style="color: #10b981;">Validé</span>
                                                            @else
                                                                <span class="text-xs font-medium" style="color: #ef4444;">{{ $nbFailed }} non validé</span>
                                                            @endif
                                                            @unless(auth()->user()->hasRole('dentist'))
                                                                @if($fiche->createdBy)
                                                                    <div class="text-xs text-secondary mt-1 break-words">
                                                                        Créé par: {{ $fiche->createdBy->full_name ?? ($fiche->createdBy->nom . ' ' . $fiche->createdBy->prénom ?? $fiche->createdBy->name ?? 'N/A') }}
                                                                    </div>
                                                                @endif
                                                                @if($fiche->updatedBy)
                                                                    <div class="text-xs text-secondary mt-1 break-words">
                                                                        Modifié par: {{ $fiche->updatedBy->full_name ?? ($fiche->updatedBy->nom . ' ' . $fiche->updatedBy->prénom ?? $fiche->updatedBy->name ?? 'N/A') }}
                                                                    </div>
                                                                @endif
                                                            @endunless
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bon de Livraison -->
            @can('view_bons_livraison')
            @unless(auth()->user()->hasRole('employer'))
            @php
                $shouldShowBl = true;
                // Ne pas afficher si statut est "Reçue" ou "En cours"
                if ($commande->status === 'Reçue' || $commande->status === 'En cours') {
                    $shouldShowBl = false;
                }
                // Pour les dentistes : ne pas afficher si statut est "Terminée" et qu'il n'y a pas de BL
                if (auth()->user()->hasRole('dentist') && $commande->status === 'Terminée' && !$commande->bonLivraison) {
                    $shouldShowBl = false;
                }
            @endphp
            @if($shouldShowBl)
            <div class="card mb-6">
                <div class="p-4 sm:p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                        <h3 class="text-lg font-semibold text-primary">Bon de Livraison</h3>
                        @if($commande->bonLivraison)
                            <div class="flex items-center gap-3">
                                <a href="{{ route('app.bl.show', $commande->bonLivraison) }}" class="btn-primary inline-flex items-center justify-center">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Voir le BL
                                </a>
                                @can('print_bons_livraison')
                                <a href="{{ route('app.bl.print', $commande->bonLivraison) }}" target="_blank" class="btn-primary inline-flex items-center justify-center" style="background-color: #3B82F6; border-color: #3B82F6;">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                    </svg>
                                    Imprimer
                                </a>
                                @endcan
                            </div>
                        @else
                            <form action="{{ route('admin.commandes.generate-bl', $commande) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-primary inline-flex items-center justify-center">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Générer le Bon de Livraison
                                </button>
                            </form>
                        @endif
                    </div>
                    @if($commande->bonLivraison)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-secondary mb-1">Numéro BL</p>
                                <p class="font-medium text-primary">{{ $commande->bonLivraison->numero_bl }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-secondary mb-1">Total TTC</p>
                                <p class="font-medium text-primary">{{ number_format($commande->bonLivraison->total_ttc, 2, ',', ' ') }} TND</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @endif
            @endunless
            @endcan

            <!-- Fichiers -->
            <div class="card" x-data="{ showImageModal: false, imageUrl: '', imageName: '', showStlModal: false, stlUrl: '', stlName: '', empreinteError: '' }" x-cloak>
                <div class="p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-primary mb-4">Fichiers</h3>
                    @can('upload_commande_files')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <!-- Formulaire Empreinte -->
                        <form action="{{ route('admin.commandes.files.store', $commande) }}" method="POST" enctype="multipart/form-data" class="border border-border rounded-lg p-4" @submit.prevent="empreinteError = ''; const files = $el.querySelector('input[type=file]').files; const allowedExtensions = ['stl', 'jpy']; let hasError = false; for(let i = 0; i < files.length; i++) { const ext = files[i].name.split('.').pop().toLowerCase(); if(!allowedExtensions.includes(ext)) { empreinteError = 'Extension non autorisée. Seuls les fichiers STL et JPY sont acceptés.'; hasError = true; break; } } if(!hasError) { $el.submit(); }">
                            @csrf
                            <input type="hidden" name="type" value="empreinte">
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-primary mb-1">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Empreinte
                                </label>
                                <p class="text-xs text-secondary mb-2">Extensions acceptées : <span class="font-medium">STL, JPY</span></p>
                                <input type="file" name="files[]" multiple accept=".stl,.jpy" class="block w-full text-sm text-secondary file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20" @change="empreinteError = ''">
                                <template x-if="empreinteError">
                                    <p class="mt-2 text-sm text-danger flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span x-text="empreinteError"></span>
                                    </p>
                                </template>
                            </div>
                            <button type="submit" class="btn-primary w-full">Uploader</button>
                        </form>
                        <!-- Formulaire Image -->
                        <form action="{{ route('admin.commandes.files.store', $commande) }}" method="POST" enctype="multipart/form-data" class="border border-border rounded-lg p-4">
                            @csrf
                            <input type="hidden" name="type" value="image">
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-primary mb-2">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Image
                                </label>
                                <input type="file" name="files[]" multiple accept="image/*" class="block w-full text-sm text-secondary file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                            </div>
                            <button type="submit" class="btn-primary w-full">Uploader</button>
                        </form>
                    </div>
                    @endcan

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Bloc Empreinte -->
                        <div>
                            <h4 class="text-base font-semibold text-primary mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Empreinte
                            </h4>
                            @php
                                $empreintes = $commande->files->where('type', 'empreinte');
                            @endphp
                            @if($empreintes->count() > 0)
                                <div class="space-y-3">
                                    @foreach($empreintes as $file)
                                        @php
                                            $isStl = strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION)) === 'stl';
                                        @endphp
                                        <div class="border border-border rounded-lg p-4 hover:shadow-md transition-shadow">
                                            <p class="font-semibold text-primary mb-1 break-words">{{ $file->original_name }}</p>
                                            <p class="text-sm text-secondary mb-2">{{ number_format($file->size / 1024, 2) }} KB</p>
                                            <div class="flex items-center gap-3">
                                                @if($isStl)
                                                <button type="button" @click="showStlModal = true; stlUrl = {{ json_encode(asset('storage/' . $file->path)) }}; stlName = {{ json_encode($file->original_name) }}" class="text-primary hover:text-primary/80 text-sm font-medium inline-flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                    Prévisualiser
                                                </button>
                                                @endif
                                                <a href="{{ asset('storage/' . $file->path) }}" target="_blank" class="text-primary hover:text-primary/80 text-sm font-medium inline-flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                    </svg>
                                                    Télécharger
                                                </a>
                                                @can('delete_commande_files')
                                                <form action="{{ route('admin.commandes.files.destroy', [$commande, $file]) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-danger hover:text-danger/80 text-sm font-medium">Supprimer</button>
                                                </form>
                                                @endcan
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-secondary">Aucune empreinte</p>
                            @endif
                        </div>

                        <!-- Bloc Image -->
                        <div>
                            <h4 class="text-base font-semibold text-primary mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Image
                            </h4>
                            @php
                                $images = $commande->files->where('type', 'image');
                            @endphp
                            @if($images->count() > 0)
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    @foreach($images as $file)
                                        <div class="border border-border rounded-lg overflow-hidden hover:shadow-md transition-shadow group relative">
                                            <div class="aspect-square bg-neutral-100 flex items-center justify-center cursor-pointer" @click="showImageModal = true; imageUrl = {{ json_encode(asset('storage/' . $file->path)) }}; imageName = {{ json_encode($file->original_name) }}">
                                                <img src="{{ asset('storage/' . $file->path) }}" alt="{{ $file->original_name }}" class="w-full h-full object-cover">
                                            </div>
                                            <div class="p-2">
                                                <p class="text-xs font-medium text-primary mb-1 truncate" title="{{ $file->original_name }}">{{ $file->original_name }}</p>
                                                <p class="text-xs text-secondary mb-2">{{ number_format($file->size / 1024, 2) }} KB</p>
                                                @can('delete_commande_files')
                                                <form action="{{ route('admin.commandes.files.destroy', [$commande, $file]) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-danger hover:text-danger/80 text-xs font-medium">Supprimer</button>
                                                </form>
                                                @endcan
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-secondary">Aucune image</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Modal Preview Image -->
                <div x-show="showImageModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-50 flex items-center justify-center p-4"
                     style="display: none;"
                     @click.self="showImageModal = false">
                    <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm" x-on:click="showImageModal = false"></div>
                    <div class="bg-white rounded-2xl w-full max-w-4xl shadow-xl border border-gray-100 relative z-10 overflow-hidden"
                         style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);"
                         @click.stop>
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-primary" x-text="imageName"></h3>
                                <button type="button" @click="showImageModal = false" class="p-2 text-secondary hover:text-primary hover:bg-neutral-100 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                            <div class="flex items-center justify-center bg-neutral-50 rounded-lg p-4">
                                <img :src="imageUrl" :alt="imageName" class="max-w-full max-h-[70vh] object-contain rounded-lg">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Preview STL -->
                <div x-show="showStlModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-50 flex items-center justify-center p-4"
                     style="display: none;"
                     @click.self="showStlModal = false"
                     x-init="$watch('showStlModal', value => { if(value) { setTimeout(() => initStlViewer(stlUrl), 100); } else { cleanupStlViewer(); } })">
                    <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm" x-on:click="showStlModal = false"></div>
                    <div class="bg-white rounded-2xl w-full max-w-6xl shadow-xl border border-gray-100 relative z-10 overflow-hidden"
                         style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);"
                         @click.stop>
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-primary" x-text="stlName"></h3>
                                <button type="button" @click="showStlModal = false" class="p-2 text-secondary hover:text-primary hover:bg-neutral-100 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                            <div class="bg-neutral-50 rounded-lg p-4 relative" style="height: 70vh; min-height: 500px;">
                                <div id="stl-viewer-container" class="w-full h-full rounded-lg"></div>
                                <div class="absolute top-4 right-4 flex flex-col gap-2 z-10">
                                    <button type="button" id="stl-zoom-in" class="p-2 bg-white rounded-lg shadow-md hover:bg-neutral-100 transition-colors" title="Zoom avant">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"></path>
                                        </svg>
                                    </button>
                                    <button type="button" id="stl-zoom-out" class="p-2 bg-white rounded-lg shadow-md hover:bg-neutral-100 transition-colors" title="Zoom arrière">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"></path>
                                        </svg>
                                    </button>
                                    <button type="button" id="stl-reset-view" class="p-2 bg-white rounded-lg shadow-md hover:bg-neutral-100 transition-colors" title="Réinitialiser la vue">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Fiche de contrôle qualité -->
    <div id="ficheModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm" onclick="closeFicheModal()"></div>
        <div class="bg-white rounded-2xl w-full max-w-4xl shadow-xl border border-gray-100 relative z-10 overflow-hidden"
             style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);"
             onclick="event.stopPropagation()">
            <div class="p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-primary" id="ficheModalTitle">Fiche de contrôle qualité</h3>
                    <button type="button" onclick="closeFicheModal()" class="p-2 text-secondary hover:text-primary hover:bg-neutral-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="bg-neutral-50 rounded-lg p-2 max-h-[70vh] overflow-y-auto">
                    <!-- Loading state -->
                    <div id="ficheModalLoading" class="text-center py-8" style="display: none;">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                        <p class="text-secondary mt-2">Chargement des critères...</p>
                    </div>

                    <!-- Error state -->
                    <div id="ficheModalError" class="text-center py-8" style="display: none;">
                        <p class="text-danger" id="ficheModalErrorMessage"></p>
                    </div>

                    <!-- Content -->
                    <div id="ficheModalContent" class="space-y-6" style="display: none;"></div>

                    <!-- Empty state -->
                    <div id="ficheModalEmpty" class="text-center py-8" style="display: none;">
                        <p class="text-secondary">Aucun critère trouvé pour cette tâche.</p>
                    </div>
                </div>
                
                <!-- Footer avec bouton Enregistrer -->
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeFicheModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Annuler
                    </button>
                    <button type="button" onclick="saveFiche()" id="ficheModalSaveBtn" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" style="display: none;">
                        <span id="ficheModalSaveText">Enregistrer</span>
                        <span id="ficheModalSaveLoading" class="flex items-center gap-2" style="display: none;">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Enregistrement...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Variables globales pour la modal
        let currentTacheId = null;
        let currentServiceNom = '';
        let currentCriteres = {};
        let canCreate = false;
        let canEdit = false;
        let ficheExists = false;
        
        // Fonction pour ouvrir la modal
        function openFicheModal(tacheId, serviceNom, ficheExistsParam, canCreateParam, canEditParam) {
            currentTacheId = tacheId;
            currentServiceNom = serviceNom || '';
            currentCriteres = {};
            ficheExists = ficheExistsParam;
            canCreate = canCreateParam;
            canEdit = canEditParam;
            
            const modal = document.getElementById('ficheModal');
            const title = document.getElementById('ficheModalTitle');
            const saveBtn = document.getElementById('ficheModalSaveBtn');
            
            if (currentServiceNom) {
                title.textContent = 'Fiche de contrôle qualité pour ' + currentServiceNom;
            } else {
                title.textContent = 'Fiche de contrôle qualité';
            }
            
            // Afficher ou masquer le bouton Enregistrer selon les permissions
            if ((ficheExists && canEdit) || (!ficheExists && canCreate)) {
                saveBtn.style.display = 'inline-flex';
            } else {
                saveBtn.style.display = 'none';
            }
            
            modal.style.display = 'flex';
            loadCriteres(tacheId);
        }
        
        // Fonction pour fermer la modal
        function closeFicheModal() {
            const modal = document.getElementById('ficheModal');
            modal.style.display = 'none';
            currentTacheId = null;
            currentServiceNom = '';
            currentCriteres = {};
            
            // Réinitialiser le contenu
            document.getElementById('ficheModalContent').innerHTML = '';
            document.getElementById('ficheModalLoading').style.display = 'none';
            document.getElementById('ficheModalError').style.display = 'none';
            document.getElementById('ficheModalEmpty').style.display = 'none';
        }
        
        // Fonction pour charger les critères
        async function loadCriteres(tacheId) {
            const loading = document.getElementById('ficheModalLoading');
            const error = document.getElementById('ficheModalError');
            const errorMessage = document.getElementById('ficheModalErrorMessage');
            const content = document.getElementById('ficheModalContent');
            const empty = document.getElementById('ficheModalEmpty');
            
            // Vérifier que tous les éléments existent
            if (!loading || !error || !errorMessage || !content || !empty) {
                console.error('Éléments de la modal non trouvés');
                return;
            }
            
            // Afficher le loading
            loading.style.display = 'block';
            error.style.display = 'none';
            content.style.display = 'none';
            empty.style.display = 'none';
            
            try {
                const response = await fetch('/admin/commandes/taches/' + tacheId + '/criteres');
                const data = await response.json();
                
                if (data.success) {
                    currentCriteres = data.criteres;
                    displayCriteres(data.criteres);
                } else {
                    errorMessage.textContent = data.message || 'Erreur lors du chargement des critères';
                    loading.style.display = 'none';
                    error.style.display = 'block';
                }
            } catch (err) {
                console.error('Erreur lors du chargement des critères:', err);
                errorMessage.textContent = 'Erreur lors du chargement des critères';
                loading.style.display = 'none';
                error.style.display = 'block';
            }
        }
        
        // Fonction pour afficher les critères
        function displayCriteres(criteres) {
            const loading = document.getElementById('ficheModalLoading');
            const error = document.getElementById('ficheModalError');
            const content = document.getElementById('ficheModalContent');
            const empty = document.getElementById('ficheModalEmpty');
            
            // Vérifier que tous les éléments existent
            if (!loading || !error || !content || !empty) {
                console.error('Éléments de la modal non trouvés');
                return;
            }
            
            loading.style.display = 'none';
            error.style.display = 'none';
            
            if (!criteres || Object.keys(criteres).length === 0) {
                empty.style.display = 'block';
                content.style.display = 'none';
                return;
            }
            
            empty.style.display = 'none';
            content.style.display = 'block';
            
            // Ordre des types
            const typeOrder = ['Empreinte', 'Contrôle visuel', 'Occlusion', 'Livraison', 'Marque des Matériaux'];
            
            let html = '';
            
            typeOrder.forEach(type => {
                if (!criteres[type] || criteres[type].length === 0) return;
                
                html += '<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">';
                html += '<div class="bg-gradient-to-r from-primary/10 to-primary/5 px-4 py-3 border-b border-gray-200">';
                html += '<h4 class="text-base font-semibold text-primary">' + type + '</h4>';
                html += '</div>';
                html += '<div class="overflow-x-auto">';
                html += '<table class="w-full">';
                html += '<thead class="bg-gray-50 border-b border-gray-200">';
                html += '<tr>';
                html += '<th class="text-left text-xs font-semibold text-gray-700 uppercase tracking-wider px-4 py-3">Critère</th>';
                html += '<th class="text-center text-xs font-semibold text-gray-700 uppercase tracking-wider px-4 py-3" style="width: 80px;">';
                html += '<div class="flex justify-center items-center"><img src="{{ asset('success.png') }}" alt="Success" class="w-6 h-6 object-contain"></div>';
                html += '</th>';
                html += '<th class="text-center text-xs font-semibold text-gray-700 uppercase tracking-wider px-4 py-3" style="width: 80px;">';
                html += '<div class="flex justify-center items-center"><img src="{{ asset('failed.png') }}" alt="Failed" class="w-6 h-6 object-contain"></div>';
                html += '</th>';
                html += '<th class="text-left text-xs font-semibold text-gray-700 uppercase tracking-wider px-4 py-3">Remarques</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody class="bg-white divide-y divide-gray-200">';
                
                criteres[type].forEach(critere => {
                    const validation = critere.validation !== undefined ? critere.validation : null;
                    const remarque = critere.remarque || '';
                    const checked1 = validation === 1 ? ' checked' : '';
                    const checked0 = validation === 0 ? ' checked' : '';
                    
                    // Déterminer si les inputs doivent être désactivés
                    const canModify = (ficheExists && canEdit) || (!ficheExists && canCreate);
                    const disabledAttr = canModify ? '' : ' disabled';
                    const disabledClass = canModify ? '' : ' opacity-50 cursor-not-allowed';
                    
                    html += '<tr class="hover:bg-gray-50 transition-colors duration-150">';
                    html += '<td class="px-4 py-4 whitespace-nowrap"><div class="text-sm font-medium text-gray-900">' + critere.nom + '</div></td>';
                    html += '<td class="px-4 py-4 whitespace-nowrap"><div class="flex justify-center items-center h-full">';
                    html += '<input type="radio" name="critere_' + critere.id + '" value="1"' + checked1 + disabledAttr + ' class="w-5 h-5 text-primary focus:ring-2 focus:ring-primary focus:ring-offset-2 border-gray-300 cursor-pointer transition-all' + disabledClass + '">';
                    html += '</div></td>';
                    html += '<td class="px-4 py-4 whitespace-nowrap"><div class="flex justify-center items-center h-full">';
                    html += '<input type="radio" name="critere_' + critere.id + '" value="0"' + checked0 + disabledAttr + ' class="w-5 h-5 text-primary focus:ring-2 focus:ring-primary focus:ring-offset-2 border-gray-300 cursor-pointer transition-all' + disabledClass + '">';
                    html += '</div></td>';
                    html += '<td class="px-4 py-4"><div class="flex items-center h-full">';
                    html += '<textarea name="remarque_' + critere.id + '" rows="2"' + disabledAttr + ' class="w-full text-sm rounded-lg border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all resize-none' + disabledClass + '" style="min-height: 60px;">' + (remarque ? remarque.replace(/"/g, '&quot;').replace(/'/g, '&#39;') : '') + '</textarea>';
                    html += '</div></td>';
                    html += '</tr>';
                });
                
                html += '</tbody>';
                html += '</table>';
                html += '</div>';
                html += '</div>';
            });
            
            content.innerHTML = html;
        }
        
        // Fonction pour sauvegarder la fiche
        async function saveFiche() {
            if (!currentTacheId) return;
            
            // Valider que tous les critères ont une validation
            let allValidated = true;
            const dataToSend = [];
            
            for (const [type, criteresList] of Object.entries(currentCriteres)) {
                for (const critere of criteresList) {
                    const radioName = 'critere_' + critere.id;
                    const radios = document.querySelectorAll('input[name="' + radioName + '"]:checked');
                    
                    if (radios.length === 0) {
                        allValidated = false;
                        break;
                    }
                    
                    const validation = radios[0].value;
                    const textarea = document.querySelector('textarea[name="remarque_' + critere.id + '"]');
                    const remarque = textarea ? textarea.value.trim() : null;
                    
                    dataToSend.push({
                        critere_id: critere.id,
                        validation: validation,
                        remarque: remarque || null
                    });
                }
                if (!allValidated) break;
            }
            
            if (!allValidated) {
                showToast('Veuillez valider tous les critères (succès ou échec)', 'error');
                return;
            }
            
            const saveBtn = document.getElementById('ficheModalSaveBtn');
            const saveText = document.getElementById('ficheModalSaveText');
            const saveLoading = document.getElementById('ficheModalSaveLoading');
            
            saveBtn.disabled = true;
            saveText.style.display = 'none';
            saveLoading.style.display = 'flex';
            
            try {
                const response = await fetch('/admin/commandes/taches/' + currentTacheId + '/fiche-controle-quality', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ data: dataToSend })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showToast('Fiche de contrôle qualité enregistrée avec succès', 'success');
                    
                    // Mettre à jour le statut en temps réel AVANT de fermer la modal
                    updateFicheStatus(currentTacheId, data.nb_failed || 0, data.created_by_name || null, data.updated_by_name || null);
                    
                    closeFicheModal();
                } else {
                    showToast(data.message || 'Erreur lors de l\'enregistrement', 'error');
                }
            } catch (err) {
                showToast('Erreur lors de l\'enregistrement', 'error');
            } finally {
                saveBtn.disabled = false;
                saveText.style.display = 'inline';
                saveLoading.style.display = 'none';
            }
        }
        
        // Fonction pour mettre à jour le statut de la fiche
        function updateFicheStatus(tacheId, nbFailed, createdByName = null, updatedByName = null) {
            const statusContainer = document.getElementById('fiche-status-' + tacheId);
            if (!statusContainer) {
                console.error('Conteneur de statut non trouvé pour la tâche:', tacheId);
                return;
            }
            
            // Vérifier si l'utilisateur est un dentiste
            const isDentist = document.body.getAttribute('data-user-role') === 'dentist';
            
            // Vider le conteneur d'abord
            statusContainer.innerHTML = '';
            
            if (nbFailed === 0) {
                const span = document.createElement('span');
                span.className = 'text-xs font-medium';
                span.style.color = '#10b981';
                span.textContent = 'Validé';
                statusContainer.appendChild(span);
            } else {
                const span = document.createElement('span');
                span.className = 'text-xs font-medium';
                span.style.color = '#ef4444';
                span.textContent = nbFailed + ' non validé';
                statusContainer.appendChild(span);
            }
            
            // Ajouter le nom du créateur et modificateur seulement si l'utilisateur n'est pas dentiste
            if (!isDentist) {
                // Ajouter le nom du créateur si disponible
                if (createdByName) {
                    const creatorDiv = document.createElement('div');
                    creatorDiv.className = 'text-xs text-secondary mt-1 break-words';
                    creatorDiv.textContent = 'Créé par: ' + createdByName;
                    statusContainer.appendChild(creatorDiv);
                }
                
                // Ajouter le nom du modificateur si disponible
                if (updatedByName) {
                    const updaterDiv = document.createElement('div');
                    updaterDiv.className = 'text-xs text-secondary mt-1 break-words';
                    updaterDiv.textContent = 'Modifié par: ' + updatedByName;
                    statusContainer.appendChild(updaterDiv);
                }
            }
        }
        
        // Fonction pour afficher les toasts
        function showToast(message, type = 'success') {
            let container = document.getElementById('toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container';
                container.className = 'fixed top-4 right-4 z-[9999] space-y-3';
                container.style.cssText = 'max-width: 400px; pointer-events: none; position: fixed !important; top: 1rem !important; right: 1rem !important; z-index: 9999 !important;';
                document.body.appendChild(container);
            }
            
            const toast = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-50 border-green-500' : 'bg-red-50 border-red-500';
            const textColor = type === 'success' ? 'text-green-800' : 'text-red-800';
            const iconColor = type === 'success' ? 'text-green-500' : 'text-red-500';
            
            toast.className = 'bg-white rounded-lg shadow-lg border-l-4 p-4 flex items-start gap-3 ' + bgColor;
            toast.style.cssText = 'animation: slideInRight 0.3s ease-out; min-width: 300px; max-width: 400px; pointer-events: auto; position: relative; z-index: 10000; display: flex !important; visibility: visible !important; opacity: 1 !important; margin-bottom: 0.75rem;';
            
            const iconSvg = type === 'success' 
                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
            
            toast.innerHTML = '<div class="flex-shrink-0 ' + iconColor + '"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">' + iconSvg + '</svg></div><div class="flex-1 min-w-0"><p class="text-sm font-semibold ' + textColor + ' mb-1">' + (type === 'success' ? 'Succès' : 'Erreur') + '</p><p class="text-sm ' + textColor + '">' + message + '</p></div><button type="button" onclick="this.parentElement.remove()" class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>';
            
            container.appendChild(toast);
            
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 5000);
        }
    </script>
    <script type="importmap">
    {
        "imports": {
            "three": "https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.module.js",
            "three/addons/": "https://cdn.jsdelivr.net/npm/three@0.160.0/examples/jsm/"
        }
    }
    </script>
    <script type="module">
        import * as THREE from 'three';
        import { STLLoader } from 'three/addons/loaders/STLLoader.js';
        import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

        let stlScene, stlCamera, stlRenderer, stlControls, stlMesh;

        function onStlWindowResize() {
            const container = document.getElementById('stl-viewer-container');
            if (!container || !stlCamera || !stlRenderer) return;

            const width = container.clientWidth;
            const height = container.clientHeight;

            stlCamera.aspect = width / height;
            stlCamera.updateProjectionMatrix();
            stlRenderer.setSize(width, height);
        }

        window.initStlViewer = function(url) {
            const container = document.getElementById('stl-viewer-container');
            if (!container) return;

            // Nettoyer le contenu précédent
            while (container.firstChild) {
                container.removeChild(container.firstChild);
            }

            // Créer la scène
            stlScene = new THREE.Scene();
            stlScene.background = new THREE.Color(0xf5f5f5);

            // Créer la caméra avec un zoom élevé pour une meilleure visibilité
            const width = container.clientWidth;
            const height = container.clientHeight;
            stlCamera = new THREE.PerspectiveCamera(45, width / height, 0.1, 1000);
            stlCamera.position.set(0, 0, 100);

            // Créer le renderer
            stlRenderer = new THREE.WebGLRenderer({ antialias: true });
            stlRenderer.setSize(width, height);
            stlRenderer.setPixelRatio(window.devicePixelRatio);
            container.appendChild(stlRenderer.domElement);

            // Ajouter des lumières pour une meilleure visibilité
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
            stlScene.add(ambientLight);

            const directionalLight1 = new THREE.DirectionalLight(0xffffff, 0.8);
            directionalLight1.position.set(1, 1, 1);
            stlScene.add(directionalLight1);

            const directionalLight2 = new THREE.DirectionalLight(0xffffff, 0.4);
            directionalLight2.position.set(-1, -1, -1);
            stlScene.add(directionalLight2);

            // Contrôles de zoom - fonction pour configurer les boutons
            const setupZoomControls = () => {
                const zoomIn = document.getElementById('stl-zoom-in');
                const zoomOut = document.getElementById('stl-zoom-out');
                const resetView = document.getElementById('stl-reset-view');
                
                if (zoomIn) {
                    zoomIn.onclick = () => {
                        if (stlControls) {
                            stlCamera.position.multiplyScalar(0.8);
                            stlControls.update();
                        }
                    };
                }
                
                if (zoomOut) {
                    zoomOut.onclick = () => {
                        if (stlControls) {
                            stlCamera.position.multiplyScalar(1.25);
                            stlControls.update();
                        }
                    };
                }
                
                if (resetView) {
                    resetView.onclick = () => {
                        if (stlMesh && stlCamera && stlControls) {
                            const box = new THREE.Box3().setFromObject(stlMesh);
                            const size = box.getSize(new THREE.Vector3());
                            const maxDim = Math.max(size.x, size.y, size.z);
                            const fov = stlCamera.fov * (Math.PI / 180);
                            let cameraZ = Math.abs(maxDim / 2 / Math.tan(fov / 2));
                            cameraZ *= 1.5;
                            stlCamera.position.set(0, 0, cameraZ);
                            stlCamera.lookAt(0, 0, 0);
                            stlControls.target.set(0, 0, 0);
                            stlControls.update();
                        }
                    };
                }
            };

            // Charger le fichier STL
            const loader = new STLLoader();
            loader.load(url, function(geometry) {
                // Calculer le centre de la géométrie
                geometry.computeBoundingBox();
                const center = geometry.boundingBox.getCenter(new THREE.Vector3());
                geometry.translate(-center.x, -center.y, -center.z);

                // Créer le matériau avec une couleur claire et bien visible
                const material = new THREE.MeshPhongMaterial({
                    color: 0x00d4aa,
                    specular: 0x111111,
                    shininess: 200,
                    flatShading: false
                });

                // Créer le mesh
                stlMesh = new THREE.Mesh(geometry, material);
                stlScene.add(stlMesh);

                // Calculer la distance de la caméra pour voir tout le modèle
                const box = new THREE.Box3().setFromObject(stlMesh);
                const size = box.getSize(new THREE.Vector3());
                const maxDim = Math.max(size.x, size.y, size.z);
                const fov = stlCamera.fov * (Math.PI / 180);
                let cameraZ = Math.abs(maxDim / 2 / Math.tan(fov / 2));
                cameraZ *= 1.5; // Ajouter un peu de marge
                stlCamera.position.z = cameraZ;
                stlCamera.lookAt(0, 0, 0);

                // Créer les contrôles OrbitControls pour rotation/zoom
                stlControls = new OrbitControls(stlCamera, stlRenderer.domElement);
                stlControls.enableDamping = true;
                stlControls.dampingFactor = 0.05;
                stlControls.minDistance = maxDim * 0.5;
                stlControls.maxDistance = maxDim * 5;
                stlControls.target.set(0, 0, 0);
                stlControls.update();

                // Gérer le redimensionnement
                window.addEventListener('resize', onStlWindowResize);
                
                // Configurer les contrôles de zoom après le chargement
                setupZoomControls();
            });

            // Animation loop
            function animate() {
                requestAnimationFrame(animate);
                if (stlControls) {
                    stlControls.update();
                }
                if (stlRenderer && stlScene && stlCamera) {
                    stlRenderer.render(stlScene, stlCamera);
                }
            }
            animate();
        };

        window.cleanupStlViewer = function() {
            window.removeEventListener('resize', onStlWindowResize);
            if (stlRenderer && stlRenderer.domElement && stlRenderer.domElement.parentNode) {
                stlRenderer.domElement.parentNode.removeChild(stlRenderer.domElement);
            }
            if (stlControls) {
                stlControls.dispose();
            }
            stlScene = null;
            stlCamera = null;
            stlRenderer = null;
            stlControls = null;
            stlMesh = null;
        };
    </script>
    @endpush
    </div>
</x-app-layout>
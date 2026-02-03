<div x-data="{ showWarningModal: false, warningMessage: '' }" x-cloak>
<x-app-layout>
    <style>
        @media (min-width: 1024px) {
            .lg\:w-\[50\%\] {
                width: 50% !important;
            }
        }
    </style>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3 space-y-2 sm:space-y-0">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-primary rounded-lg">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <h2 class="text-xl sm:text-2xl font-semibold text-primary">
                    {{ __('Modifier Commande : ') . $commande->num_cmd }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-8 bg-app min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card">
                <form action="{{ route('admin.commandes.update', $commande) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        @unless(auth()->user()->hasRole('dentist'))
                        <div>
                            <x-label for="dentiste_id" value="{{ __('Dentiste') }}" class="text-primary font-medium mb-2" />
                            <select name="dentiste_id" id="dentiste_id" class="block w-full input-field" required>
                                <option value="">Sélectionner un dentiste</option>
                                @foreach($dentistes as $dentiste)
                                    <option value="{{ $dentiste->id }}" {{ $commande->dentiste_id == $dentiste->id ? 'selected' : '' }}>{{ $dentiste->full_name ?: $dentiste->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error for="dentiste_id" class="mt-2" />
                        </div>
                        @else
                        <input type="hidden" name="dentiste_id" value="{{ auth()->id() }}">
                        @endunless

                        <div>
                            <x-label for="num_cmd" value="{{ __('Numéro Commande') }}" class="text-primary font-medium mb-2" />
                            <x-input id="num_cmd" name="num_cmd" type="text" class="block w-full input-field" value="{{ $commande->num_cmd }}" />
                            <x-input-error for="num_cmd" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="nom_patient" value="{{ __('Nom Patient') }}" class="text-primary font-medium mb-2" />
                            <x-input id="nom_patient" name="nom_patient" type="text" class="block w-full input-field" value="{{ $commande->nom_patient }}" />
                            <x-input-error for="nom_patient" class="mt-2" />
                        </div>

                        @unless(auth()->user()->hasRole('dentist'))
                        <div>
                            <x-label for="status" value="{{ __('Statut') }}" class="text-primary font-medium mb-2" />
                            <select name="status" id="status" class="block w-full input-field" required>
                                <option value="Reçue" {{ $commande->status === 'Reçue' ? 'selected' : '' }}>Reçue</option>
                                <option value="En cours" {{ $commande->status === 'En cours' ? 'selected' : '' }}>En cours</option>
                                <option value="Terminée" {{ $commande->status === 'Terminée' ? 'selected' : '' }}>Terminée</option>
                                <option value="Livrée" {{ $commande->status === 'Livrée' ? 'selected' : '' }}>Livrée</option>
                            </select>
                        </div>
                        @else
                        <input type="hidden" name="status" value="{{ $commande->status }}">
                        @endunless

                        <div class="flex items-center pt-8">
                            <x-checkbox id="urgent" name="urgent" value="1" :checked="$commande->urgent" class="rounded border-border text-primary focus:ring-primary" />
                            <x-label for="urgent" value="{{ __('Commande Urgente') }}" class="ml-2 text-primary font-medium" />
                        </div>
                    </div>

                    <!-- Tâches -->
                    <div id="taches-container" class="space-y-4">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-primary">Tâches</h3>
                        </div>

                        @foreach($commande->taches as $index => $tache)
                            <div class="tache-item card border-l-4 border-primary relative overflow-hidden">
                                <button type="button" class="delete-tache absolute top-0 right-0 p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-bl-lg transition-colors duration-200 z-10" title="Supprimer cette tâche">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                                <div class="flex flex-row gap-4 items-end">
                                    <div class="flex-1 min-w-0">
                                        <x-label for="taches[{{ $index }}][service_id]" value="{{ __('Service') }}" class="text-primary font-medium mb-2" />
                                        <select name="taches[{{ $index }}][service_id]" id="taches[{{ $index }}][service_id]" class="block w-full input-field tache-service-select" required>
                                            <option value="">Sélectionner un service</option>
                                            @foreach($services as $service)
                                                <option value="{{ $service->id }}" {{ $tache->service_id == $service->id ? 'selected' : '' }}>{{ $service->nom }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="flex gap-4" style="width: 30%;">
                                        <div class="flex-1 min-w-0">
                                            <x-label for="taches[{{ $index }}][nb_elem]" value="Nombre d'éléments" class="text-primary font-medium mb-2" />
                                            <x-input id="taches[{{ $index }}][nb_elem]" name="taches[{{ $index }}][nb_elem]" type="number" min="1" class="block w-full input-field" value="{{ $tache->nb_elem }}" required />
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <x-label for="taches[{{ $index }}][teinte]" value="{{ __('Teinte') }}" class="text-primary font-medium mb-2" />
                                            <x-input id="taches[{{ $index }}][teinte]" name="taches[{{ $index }}][teinte]" type="text" class="block w-full input-field" value="{{ $tache->teinte }}" />
                                        </div>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <x-label for="taches[{{ $index }}][date_livraison]" value="{{ __('Date de livraison') }}" class="text-primary font-medium mb-2" />
                                        <x-input id="taches[{{ $index }}][date_livraison]" name="taches[{{ $index }}][date_livraison]" type="text" class="block w-full input-field tache-date-livraison" value="{{ $tache->date_livraison->format('Y-m-d H:i') }}" placeholder="Sélectionner date et heure" />
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="mt-4">
                            <button type="button" id="add-tache" class="btn-secondary text-sm">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Ajouter une tâche
                            </button>
                        </div>
                    </div>

                    <div>
                        <x-label for="commentaire" value="{{ __('Commentaire') }}" class="text-primary font-medium mb-2" />
                        <textarea name="commentaire" id="commentaire" rows="3" class="block w-full input-field">{{ $commande->commentaire }}</textarea>
                        <x-input-error for="commentaire" class="mt-2" />
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 sm:gap-4 pt-4 border-t border-border">
                        <a href="{{ route('admin.commandes.show', $commande) }}" class="btn-secondary text-center sm:w-auto">
                            Annuler
                        </a>
                        <button type="submit" class="btn-primary w-full sm:w-auto">
                            Mettre à jour
                        </button>
                    </div>
                </form>
            </div>

            <!-- Fichiers -->
            <div class="card mt-6" x-data="{ showImageModal: false, imageUrl: '', imageName: '', showStlModal: false, stlUrl: '', stlName: '', empreinteError: '' }" x-cloak>
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
    </div>

    <!-- Modal d'avertissement moderne -->
    <div x-show="showWarningModal" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0 scale-90" 
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100 scale-100" 
         x-transition:leave-end="opacity-0 scale-90"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0"
         style="display: none;"
         @click.away="showWarningModal = false">
        <div x-on:click.stop class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm"></div>
        <div class="bg-white rounded-2xl max-w-sm sm:max-w-md w-full relative z-10 overflow-hidden border border-gray-100" style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);">
            <div class="px-4 py-5 sm:px-6 sm:py-6">
                <div class="flex items-center mb-4 gap-3 sm:gap-3">
                    <div class="flex-shrink-0 bg-orange-500/10 rounded-full p-2">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #f97316;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl font-semibold text-primary">Attention</h3>
                </div>
                <p class="text-secondary text-sm sm:text-base mb-6" x-text="warningMessage">
                    Vous devez garder au moins une tâche.
                </p>
                <div class="border-t border-border pt-4 flex justify-end">
                    <button 
                        type="button" 
                        @click="showWarningModal = false" 
                        class="px-4 py-2 text-sm sm:text-base font-medium text-white rounded-lg transition-colors duration-200 shadow-sm hover:opacity-90"
                        style="background-color: #f97316;"
                    >
                        Compris
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let tacheIndex = {{ $commande->taches->count() }};
            const addTacheBtn = document.getElementById('add-tache');
            const tachesContainer = document.getElementById('taches-container');
            const urgentCheckbox = document.getElementById('urgent');

            // Fonction pour calculer la date de livraison avec heure
            function getDeliveryDate(isUrgent = false) {
                const today = new Date();
                const currentHour = today.getHours();
                const isAfter15 = currentHour >= 15;
                
                let daysToAdd;
                if (isUrgent) {
                    // Si Commande EST urgente
                    daysToAdd = isAfter15 ? 2 : 1; // >= 15:00 → +2 jours, < 15:00 → +1 jour
                } else {
                    // Si Commande n'est PAS urgente
                    daysToAdd = isAfter15 ? 4 : 3; // >= 15:00 → +4 jours, < 15:00 → +3 jours
                }
                
                // Calculer la date de livraison en excluant les dimanches
                const deliveryDate = new Date(today);
                let daysAdded = 0;
                let daysToSkip = daysToAdd;
                
                // Ajouter les jours un par un en sautant les dimanches
                while (daysToSkip > 0) {
                    daysAdded++;
                    const checkDate = new Date(today);
                    checkDate.setDate(today.getDate() + daysAdded);
                    
                    // Si ce n'est pas un dimanche, compter ce jour
                    if (checkDate.getDay() !== 0) { // 0 = dimanche
                        daysToSkip--;
                    }
                    // Si c'est un dimanche, on ne le compte pas et on continue
                }
                
                // La date finale est aujourd'hui + le nombre de jours ajoutés (en excluant les dimanches)
                deliveryDate.setDate(today.getDate() + daysAdded);
                
                deliveryDate.setHours(12, 0, 0, 0); // 12h00 par défaut
                
                // Formater la date au format YYYY-MM-DD HH:mm
                const year = deliveryDate.getFullYear();
                const month = String(deliveryDate.getMonth() + 1).padStart(2, '0');
                const day = String(deliveryDate.getDate()).padStart(2, '0');
                const hours = String(deliveryDate.getHours()).padStart(2, '0');
                const minutes = String(deliveryDate.getMinutes()).padStart(2, '0');
                return `${year}-${month}-${day} ${hours}:${minutes}`;
            }

            // Fonction pour initialiser Flatpickr sur un input
            function initFlatpickr(input) {
                if (input._flatpickr) {
                    input._flatpickr.destroy();
                }
                
                flatpickr(input, {
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                    time_24hr: true,
                    clickOpens: true,
                    allowInput: true,
                    locale: "fr"
                });
            }

            // Fonction pour mettre à jour toutes les dates de livraison
            function updateAllDeliveryDates() {
                const isUrgent = urgentCheckbox && urgentCheckbox.checked;
                const dateValue = getDeliveryDate(isUrgent);
                
                document.querySelectorAll('.tache-date-livraison').forEach(input => {
                    if (input._flatpickr) {
                        input._flatpickr.setDate(dateValue, false);
                    } else {
                        input.value = dateValue;
                    }
                });
            }

            // Initialiser Flatpickr pour tous les inputs de date existants
            document.querySelectorAll('.tache-date-livraison').forEach(input => {
                initFlatpickr(input);
            });

            // Écouter les changements sur le checkbox "urgent"
            if (urgentCheckbox) {
                urgentCheckbox.addEventListener('change', function() {
                    updateAllDeliveryDates();
                });
            }

            // Fonction pour afficher la modal d'alerte
            function showAlert(message) {
                // Utiliser Alpine.js pour afficher la modale
                const alpineElement = document.querySelector('[x-data*="showWarningModal"]');
                if (alpineElement && window.Alpine) {
                    const alpineData = Alpine.$data(alpineElement);
                    if (alpineData) {
                        alpineData.warningMessage = message || 'Vous devez garder au moins une tâche.';
                        alpineData.showWarningModal = true;
                    }
                } else if (alpineElement && alpineElement._x_dataStack) {
                    // Fallback pour accéder aux données Alpine
                    const alpineData = alpineElement._x_dataStack[0];
                    alpineData.warningMessage = message || 'Vous devez garder au moins une tâche.';
                    alpineData.showWarningModal = true;
                } else {
                    // Fallback si Alpine.js n'est pas encore chargé
                    setTimeout(() => showAlert(message), 100);
                }
            }

            // Fonction pour supprimer une tâche
            function setupDeleteButtons() {
                document.querySelectorAll('.delete-tache').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const tacheItem = this.closest('.tache-item');
                        const allTaches = tachesContainer.querySelectorAll('.tache-item');
                        
                        // Ne pas permettre de supprimer s'il n'y a qu'une seule tâche
                        if (allTaches.length <= 1) {
                            showAlert('Vous devez garder au moins une tâche.');
                            return;
                        }
                        
                        // Détruire l'instance Flatpickr si elle existe
                        const dateInput = tacheItem.querySelector('.tache-date-livraison');
                        if (dateInput && dateInput._flatpickr) {
                            dateInput._flatpickr.destroy();
                        }
                        
                        // Supprimer le bloc tâche
                        tacheItem.remove();
                    });
                });
            }

            // Initialiser les boutons de suppression existants
            setupDeleteButtons();

            // Fonction pour ajouter une nouvelle tâche
            if (addTacheBtn && tachesContainer) {
                addTacheBtn.addEventListener('click', function() {
                    const isUrgent = urgentCheckbox && urgentCheckbox.checked;
                    const deliveryDate = getDeliveryDate(isUrgent);
                    
                    const firstTache = document.querySelector('.tache-item');
                    if (!firstTache) return;
                    
                    const newTache = firstTache.cloneNode(true);
                    newTache.innerHTML = newTache.innerHTML.replace(/\[(\d+)\]/g, '[' + tacheIndex + ']');
                    
                    // Réinitialiser les valeurs des inputs et selects
                    newTache.querySelectorAll('input, select').forEach(input => {
                        if (input.classList.contains('tache-date-livraison')) {
                            input.value = deliveryDate;
                            // Détruire toute instance Flatpickr existante
                            if (input._flatpickr) {
                                input._flatpickr.destroy();
                            }
                        } else if (input.type !== 'checkbox' && !input.classList.contains('tache-date-livraison')) {
                            input.value = '';
                        }
                    });
                    
                    // Mettre à jour les IDs des labels et inputs
                    newTache.querySelectorAll('label, input, select').forEach(element => {
                        if (element.getAttribute('for')) {
                            element.setAttribute('for', element.getAttribute('for').replace(/\[(\d+)\]/g, '[' + tacheIndex + ']'));
                        }
                        if (element.id) {
                            element.id = element.id.replace(/\[(\d+)\]/g, '[' + tacheIndex + ']');
                        }
                        if (element.name) {
                            element.name = element.name.replace(/\[(\d+)\]/g, '[' + tacheIndex + ']');
                        }
                    });
                    
                    // Trouver le dernier bloc tâche
                    const allTaches = tachesContainer.querySelectorAll('.tache-item');
                    const lastTache = allTaches[allTaches.length - 1];
                    
                    // Insérer la nouvelle tâche après le dernier bloc tâche
                    if (lastTache && lastTache.nextSibling) {
                        lastTache.parentNode.insertBefore(newTache, lastTache.nextSibling);
                    } else if (lastTache) {
                        lastTache.parentNode.appendChild(newTache);
                    } else {
                        // Si aucune tâche n'existe, insérer avant le bouton
                        const addButtonContainer = addTacheBtn.parentElement;
                        addButtonContainer.insertBefore(newTache, addTacheBtn);
                    }
                    
                    // Initialiser Flatpickr pour le nouvel input de date
                    const newDateInput = newTache.querySelector('.tache-date-livraison');
                    if (newDateInput) {
                        initFlatpickr(newDateInput);
                        if (newDateInput._flatpickr) {
                            newDateInput._flatpickr.setDate(deliveryDate, false);
                        }
                    }

                    // Initialiser le bouton de suppression pour la nouvelle tâche
                    const deleteBtn = newTache.querySelector('.delete-tache');
                    if (deleteBtn) {
                        deleteBtn.addEventListener('click', function() {
                            const tacheItem = this.closest('.tache-item');
                            const allTaches = tachesContainer.querySelectorAll('.tache-item');
                            
                            // Ne pas permettre de supprimer s'il n'y a qu'une seule tâche
                            if (allTaches.length <= 1) {
                                showAlert('Vous devez garder au moins une tâche.');
                                return;
                            }
                            
                            // Détruire l'instance Flatpickr si elle existe
                            const dateInput = tacheItem.querySelector('.tache-date-livraison');
                            if (dateInput && dateInput._flatpickr) {
                                dateInput._flatpickr.destroy();
                            }
                            
                            // Supprimer le bloc tâche
                            tacheItem.remove();
                        });
                    }
                    
                    tacheIndex++;
                });
            }
        });
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
</x-app-layout>
</div>
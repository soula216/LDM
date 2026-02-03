<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3 space-y-2 sm:space-y-0">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-accent-secondary rounded-lg">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="text-xl sm:text-2xl font-semibold text-primary">
                    {{ __('Gestion des Prix par Dentiste') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-8 bg-app min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-accent-secondary/10 border-l-4 border-accent-secondary rounded-lg flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-accent-secondary mr-2 sm:mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-accent-secondary font-medium text-sm sm:text-base">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Add/Edit Form -->
            <div class="card mb-6">
                <div class="mb-4 sm:mb-6">
                    <h3 class="text-base sm:text-lg font-semibold text-primary flex items-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Ajouter/Modifier un Prix
                    </h3>
                </div>
                <form action="{{ route('admin.pricing.dentists.store') }}" method="POST" class="space-y-4 sm:space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                        <div class="sm:col-span-1">
                            <x-label for="dentist_id" value="{{ __('Dentiste') }}" class="text-primary font-medium mb-2" />
                            <select name="dentist_id" id="dentist_id" class="input-field" required>
                                <option value="">Sélectionner un dentiste</option>
                                @foreach($dentists as $dentist)
                                    <option value="{{ $dentist->id }}">{{ $dentist->full_name ?: $dentist->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error for="dentist_id" class="mt-2" />
                        </div>
                        <div class="sm:col-span-1">
                            <x-label for="service_id" value="{{ __('Service') }}" class="text-primary font-medium mb-2" />
                            <select name="service_id" id="service_id" class="input-field" required>
                                <option value="">Sélectionner un service</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->nom }} ({{ number_format($service->prix_unitaire_ttc, 2) }} TND)</option>
                                @endforeach
                            </select>
                            <x-input-error for="service_id" class="mt-2" />
                        </div>
                        <div class="sm:col-span-1">
                            <x-label for="prix_unitaire_ttc" value="{{ __('Prix TTC') }}" class="text-primary font-medium mb-2" />
                            <x-input name="prix_unitaire_ttc" type="number" step="0.01" min="0" class="input-field" required />
                            <x-input-error for="prix_unitaire_ttc" class="mt-2" />
                        </div>
                        <div class="sm:col-span-1 flex items-end">
                            <button type="submit" class="btn-primary w-full sm:w-auto">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Enregistrer
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Prices Table -->
            <div class="card">
                <div class="mb-4 sm:mb-6">
                    <h3 class="text-base sm:text-lg font-semibold text-primary flex items-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Prix Personnalisés
                    </h3>
                </div>
                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <table class="min-w-full divide-y divide-border">
                        <thead class="bg-neutral-100">
                            <tr>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Dentiste</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Service</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider hidden md:table-cell">Prix Défaut</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Prix Override</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-card divide-y divide-border">
                            @forelse($prices as $price)
                                <tr class="hover:bg-neutral-100/50 transition-colors">
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-primary">{{ $price->dentist->full_name ?? $price->dentist->name }}</div>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-secondary">{{ $price->service->nom }}</div>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                        <div class="text-sm text-secondary">{{ number_format($price->service->prix_unitaire_ttc, 2) }} TND</div>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-primary">{{ number_format($price->prix_unitaire_ttc, 2) }} TND</div>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <form action="{{ route('admin.pricing.dentists.destroy', $price) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce prix ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-danger hover:text-danger/80 transition-colors" title="Supprimer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-16 h-16 text-secondary mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <p class="text-secondary text-base font-medium">Aucun prix personnalisé</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($prices->hasPages())
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 px-4 sm:px-6 py-4 border-t border-border">
                    <p class="text-sm text-secondary">Affichage de {{ $prices->firstItem() }} à {{ $prices->lastItem() }} sur {{ $prices->total() }} résultats</p>
                    {{ $prices->onEachSide(2)->links() }}
                </div>
                @endif
            </div>

            <!-- Link to Services -->
            <div class="mt-6">
                <a href="{{ route('admin.services.index') }}" class="inline-flex items-center text-primary hover:text-primary/80 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    Gestion des Services
                </a>
            </div>
        </div>
    </div>
</x-app-layout>

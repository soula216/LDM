@foreach($services as $service)
    <tr class="hover:bg-neutral-100/50 transition-colors">
        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
            <span class="text-primary font-medium">{{ $service->nom }}</span>
        </td>
        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
            <span class="text-primary font-medium">{{ number_format($service->prix_unitaire_ttc, 2) }} TND</span>
        </td>
        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
            <span class="text-secondary">{{ $service->groupe ? $service->groupe->nom : '-' }}</span>
        </td>
        <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.pricing.dentists.index') }}?service_id={{ $service->id }}" class="text-primary hover:text-primary/80 transition-colors" title="Voir les prix par dentiste">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </a>
                <button
                    type="button"
                    data-service-id="{{ $service->id }}"
                    data-service-nom="{{ $service->nom }}"
                    data-service-prix="{{ $service->prix_unitaire_ttc }}"
                    data-service-groupe="{{ $service->groupe_id ?? '' }}"
                    @click="showEditModal = true; editingService = $el.dataset.serviceId; editForm.nom = $el.dataset.serviceNom; editForm.prix_unitaire_ttc = $el.dataset.servicePrix; editForm.groupe_id = $el.dataset.serviceGroupe || ''"
                    class="text-warning hover:text-warning/80 transition-colors"
                    title="Modifier le service"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </button>
                <button
                    type="button"
                    @click="showDeleteModal = true; serviceToDelete = '{{ $service->nom }}'; deleteFormAction = '{{ route('admin.services.destroy', $service) }}'"
                    class="text-danger hover:text-danger/80 transition-colors"
                    title="Supprimer le service"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        </td>
    </tr>
@endforeach

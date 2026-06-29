@foreach($elements as $element)
    <tr class="hover:bg-neutral-100/50 transition-colors">
        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
            <span class="text-primary font-medium">{{ $element->nom }}</span>
        </td>
        @can('manage_stock')
        <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
            <div class="flex items-center space-x-3">
                <button
                    type="button"
                    data-element-id="{{ $element->id }}"
                    data-element-nom="{{ $element->nom }}"
                    @click="showEditElementModal = true; editingElement = $el.dataset.elementId; editElementForm.nom = $el.dataset.elementNom"
                    class="text-warning hover:text-warning/80 transition-colors"
                    title="Modifier"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </button>
                <button
                    type="button"
                    @click="showDeleteModal = true; deleteLabel = '{{ addslashes($element->nom) }}'; deleteFormAction = '{{ route('admin.stock.elements.destroy', $element) }}'"
                    class="text-danger hover:text-danger/80 transition-colors"
                    title="Supprimer"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        </td>
        @endcan
    </tr>
@endforeach

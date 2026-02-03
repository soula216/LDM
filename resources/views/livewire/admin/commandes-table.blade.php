<div>
    <!-- Filtres -->
    <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <input type="text" 
                wire:model.live.debounce.500ms="search" 
                placeholder="Rechercher (numéro, patient, dentiste)..."
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
            <select wire:model.live="statusFilter" class="block w-full rounded-md border-gray-300 shadow-sm">
                <option value="">Tous les statuts</option>
                <option value="Reçue">Reçue</option>
                <option value="En cours">En cours</option>
                <option value="Terminée">Terminée</option>
                <option value="Livrée">Livrée</option>
            </select>
        </div>
        <div>
            <select wire:model.live="urgentFilter" class="block w-full rounded-md border-gray-300 shadow-sm">
                <option value="">Toutes les commandes</option>
                <option value="1">Urgentes uniquement</option>
                <option value="0">Non urgentes</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Numéro</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dentiste</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Urgent</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($commandes as $commande)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $commande->num_cmd }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $commande->nom_patient }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $commande->dentiste->full_name ?? $commande->dentiste->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($commande->status === 'Terminée') bg-green-100 text-green-800
                                @elseif($commande->status === 'En cours') bg-yellow-100 text-yellow-800
                                @elseif($commande->status === 'Livrée') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $commande->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($commande->urgent)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Urgent
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.commandes.show', $commande) }}" class="text-blue-600 hover:text-blue-900 mr-3">Voir</a>
                            @can('edit_commandes')
                            <a href="{{ route('admin.commandes.edit', $commande) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Modifier</a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Aucune commande trouvée
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $commandes->links() }}
    </div>
</div>

@foreach($commandes as $commande)
    <tr class="hover:bg-neutral-100/50 transition-colors" data-commande-id="{{ $commande->id }}">
        @if(!empty($bulkSelect) && auth()->user()->can('change_commande_status'))
        <td class="px-3 sm:px-6 py-4 w-10">
            <input
                type="checkbox"
                value="{{ $commande->id }}"
                class="commande-row-checkbox rounded border-border text-primary focus:ring-primary"
                @change="toggleCommandeSelection({{ $commande->id }}, $event.target.checked)"
                :checked="selectedCommandeIds.includes({{ $commande->id }})"
            />
        </td>
        @endif
        <td class="px-3 sm:px-6 py-4">
            <span class="text-sm font-semibold text-primary">{{ $commande->num_cmd }}</span>
        </td>
        <td class="px-3 sm:px-6 py-4">
            <div class="text-sm font-medium text-primary">{{ $commande->nom_patient }}</div>
        </td>
        @unless(auth()->user()->hasRole('dentist') || isset($dentist))
        <td class="px-3 sm:px-6 py-4 hidden md:table-cell">
            <div class="text-sm text-secondary">{{ $commande->dentiste->full_name ?? $commande->dentiste->name }}</div>
        </td>
        @endunless
        <td class="px-3 sm:px-6 py-4">
            @php
                $statusConfig = [
                    'Reçue' => ['bg' => 'bg-neutral-100', 'text' => 'text-secondary', 'border' => 'border-border'],
                    'En cours' => ['bg' => 'bg-warning/10', 'text' => 'text-warning', 'border' => 'border-warning/30'],
                    'Terminée' => ['bg' => 'bg-accent-secondary/10', 'text' => 'text-accent-secondary', 'border' => 'border-accent-secondary/30'],
                    'Livrée' => ['bg' => 'bg-primary/10', 'text' => 'text-primary', 'border' => 'border-primary/30'],
                ];
                $config = $statusConfig[$commande->status] ?? $statusConfig['Reçue'];
            @endphp
            <div class="flex flex-col gap-1">
                <span class="px-2 sm:px-3 py-1 inline-flex text-xs font-medium rounded-md {{ $config['bg'] }} {{ $config['text'] }} border {{ $config['border'] }}">
                    {{ $commande->status }}
                </span>
                @if($commande->status === 'Terminée' && $commande->finishedBy)
                    <span class="text-xs text-secondary mt-1">
                        Par: {{ $commande->finishedBy->full_name ?? $commande->finishedBy->name }}
                    </span>
                @endif
            </div>
        </td>
        <td class="px-3 sm:px-6 py-4 hidden lg:table-cell">
            @if($commande->urgent)
                <span class="inline-flex items-center px-2 sm:px-3 py-1 text-xs font-medium rounded-md bg-danger/10 text-danger border border-danger/30">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    Urgent
                </span>
            @else
                <span class="text-secondary">-</span>
            @endif
        </td>
        <td class="px-3 sm:px-6 py-4 hidden lg:table-cell">
            <div class="text-sm text-secondary">
                @if($commande->createdBy)
                    {{ $commande->createdBy->full_name ?? $commande->createdBy->name }}
                @else
                    <span class="text-secondary">-</span>
                @endif
            </div>
        </td>
        <td class="px-3 sm:px-6 py-4 hidden md:table-cell">
            <div class="flex flex-col">
                <span class="text-sm text-secondary">{{ $commande->created_at->format('d/m/Y') }}</span>
                <span class="text-xs text-secondary/70 mt-0.5">{{ $commande->created_at->format('H:i') }}</span>
            </div>
        </td>
        <td class="px-3 sm:px-6 py-4 text-sm font-medium">
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.commandes.show', $commande) }}" class="text-primary hover:text-primary/80 transition-colors" title="Voir">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </a>
                @can('edit_commandes')
                    @unless(auth()->user()->hasRole('dentist') && $commande->status === 'Livrée')
                    <a href="{{ route('admin.commandes.edit', $commande) }}" class="text-warning hover:text-warning/80 transition-colors" title="Modifier">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </a>
                    @endunless
                @endcan
                @if(auth()->user()->hasRole('dentist') && $commande->status === 'Reçue')
                    @can('delete_commandes')
                    <button
                        type="button"
                        @click="showDeleteModal = true; commandeToDelete = '{{ $commande->num_cmd }}'; deleteFormAction = '{{ route('admin.commandes.destroy', $commande) }}'"
                        class="text-danger hover:text-danger/80 transition-colors"
                        title="Supprimer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                    @endcan
                @elseif(!auth()->user()->hasRole('dentist'))
                    @can('delete_commandes')
                    <button
                        type="button"
                        @click="showDeleteModal = true; commandeToDelete = '{{ $commande->num_cmd }}'; deleteFormAction = '{{ route('admin.commandes.destroy', $commande) }}'"
                        class="text-danger hover:text-danger/80 transition-colors"
                        title="Supprimer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                    @endcan
                @endif
            </div>
        </td>
    </tr>
@endforeach

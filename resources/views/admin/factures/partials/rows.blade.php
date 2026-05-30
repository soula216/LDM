@foreach($factures as $facture)
    <tr class="hover:bg-neutral-100/50 transition-colors">
        <td class="px-3 sm:px-6 py-4">
            <span class="text-sm font-semibold text-primary">{{ $facture->num_facture }}</span>
        </td>
        @unless(isset($dentist))
        <td class="px-3 sm:px-6 py-4">
            <div class="text-sm text-secondary">{{ $facture->dentist->full_name ?? $facture->dentist->name }}</div>
        </td>
        @endunless
        <td class="px-3 sm:px-6 py-4">
            <div class="text-sm text-secondary">{{ $facture->date->format('d/m/Y') }}</div>
        </td>
        <td class="px-3 sm:px-6 py-4">
            <div class="text-sm font-medium text-primary">{{ number_format($facture->montant, 2, ',', ' ') }} TND</div>
        </td>
        <td class="px-3 sm:px-6 py-4">
            <div class="text-sm font-medium text-primary">
                @if($facture->montant_paye)
                    {{ number_format($facture->montant_paye, 2, ',', ' ') }} TND
                @else
                    <span class="text-secondary">-</span>
                @endif
            </div>
        </td>
        <td class="px-3 sm:px-6 py-4">
            <div class="text-sm font-medium text-primary">
                @if($facture->montant_restant !== null)
                    {{ number_format($facture->montant_restant, 2, ',', ' ') }} TND
                @else
                    <span class="text-secondary">-</span>
                @endif
            </div>
        </td>
        <td class="px-3 sm:px-6 py-4">
            <div class="text-sm font-medium text-primary">
                {{ $facture->bonsLivraison->count() }}
            </div>
        </td>
        <td class="px-3 sm:px-6 py-4">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                @if($facture->status === 'pending') bg-yellow-100 text-yellow-800
                @elseif($facture->status === 'delivered') bg-blue-100 text-blue-800
                @elseif($facture->status === 'paid') bg-green-100 text-green-800
                @elseif($facture->status === 'partially_paid') bg-orange-100 text-orange-800
                @elseif($facture->status === 'rejected') bg-red-100 text-red-800
                @else bg-gray-100 text-gray-800
                @endif">
                {{ $facture->status_label }}
            </span>
        </td>
        <td class="px-3 sm:px-6 py-4 text-sm font-medium">
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.factures.show', $facture) }}" class="text-primary hover:text-primary/80 transition-colors" title="Voir">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </a>
                @can('edit_factures')
                <button @click="openEditModal({
                    id: {{ $facture->id }},
                    dentist_id: {{ $facture->dentist_id }},
                    titre_document: '{{ $facture->titre_document ?? 'bon_livraison' }}',
                    date: '{{ $facture->date->format('Y-m-d') }}',
                    num_facture: '{{ addslashes($facture->num_facture) }}',
                    ancien_solde: {{ $facture->ancien_solde ?? 0 }},
                    avance: {{ $facture->avance ?? 0 }},
                    status: '{{ $facture->status }}',
                    montant_paye: {{ $facture->montant_paye ?? 'null' }},
                    bons_livraison_ids: [{{ $facture->bonsLivraison->pluck('id')->implode(',') }}]
                })" class="text-warning hover:text-warning/80 transition-colors" title="Modifier">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </button>
                @endcan
                @can('delete_factures')
                <button @click="openDeleteModal({{ $facture->id }}, '{{ $facture->num_facture }}')" class="text-danger hover:text-danger/80 transition-colors" title="Supprimer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
                @endcan
            </div>
        </td>
    </tr>
@endforeach

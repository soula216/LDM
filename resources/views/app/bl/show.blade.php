<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Bon de Livraison : ') . $bl->numero_bl }}
            </h2>
            @can('print_bons_livraison')
            <a href="{{ route('app.bl.print', $bl) }}" target="_blank" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Imprimer
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <!-- En-tête BL -->
                    <div class="mb-6 border-b pb-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h3 class="text-lg font-semibold">Bon de Livraison</h3>
                                <p class="text-sm text-gray-600">Numéro : <span class="font-semibold">{{ $bl->numero_bl }}</span></p>
                                <p class="text-sm text-gray-600">Date : <span class="font-semibold">{{ $bl->created_at->format('d/m/Y') }}</span></p>
                            </div>
                            <div class="text-right">
                                <h3 class="text-lg font-semibold">Commande</h3>
                                <p class="text-sm text-gray-600">Numéro : <span class="font-semibold">{{ $bl->commande->num_cmd }}</span></p>
                                @if($bl->commande->nom_patient)
                                <p class="text-sm text-gray-600">Patient : <span class="font-semibold">{{ $bl->commande->nom_patient }}</span></p>
                                @endif
                                <p class="text-sm text-gray-600">Dentiste : <span class="font-semibold">{{ $bl->commande->dentiste->full_name ?? $bl->commande->dentiste->name }}</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Lignes BL -->
                    <div class="mb-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix Unitaire TTC</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qte</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total TTC</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($bl->lignes as $ligne)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $ligne->service_name_snapshot }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($ligne->prix_unitaire_ttc_snapshot, 2) }} TND</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $ligne->quantite }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">{{ number_format($ligne->total_ligne_ttc, 2) }} TND</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right text-sm font-semibold">Total TTC :</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-lg font-bold">{{ number_format($bl->total_ttc, 2) }} TND</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

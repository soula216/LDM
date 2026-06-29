<h3 class="text-base sm:text-lg font-semibold text-primary mb-4 flex items-center">
    <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
    Ajouter au stock
</h3>

@if($elementsForSelect->isEmpty())
    <div class="mb-6 p-4 bg-warning/10 border border-warning/20 rounded-lg text-sm text-primary">
        Aucun élément disponible. Créez d'abord des éléments dans l'onglet <strong>Éléments</strong>.
    </div>
@elseif(auth()->user()->can('manage_stock'))
    <form action="{{ route('admin.stock.lines.store') }}" method="POST" class="mb-6">
        @csrf
        <input type="hidden" name="tab" value="stock">
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[12rem]">
                <label for="element_id" class="block text-sm font-medium text-primary mb-2">Élément <span class="text-danger">*</span></label>
                <select name="element_id" id="element_id" class="input-field w-full" required>
                    <option value="">Sélectionner un élément</option>
                    @foreach($elementsForSelect as $element)
                        <option value="{{ $element->id }}" @selected(old('element_id') == $element->id)>{{ $element->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-32">
                <label for="qte" class="block text-sm font-medium text-primary mb-2">Qté <span class="text-danger">*</span></label>
                <input type="number" name="qte" id="qte" value="{{ old('qte') }}" min="1" step="1" class="input-field w-full" placeholder="0" required />
            </div>
            <div>
                <button type="submit" class="btn-primary whitespace-nowrap">Ajouter</button>
            </div>
        </div>
    </form>
@endif

<h3 class="text-base sm:text-lg font-semibold text-primary mb-4">Liste du stock</h3>
<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-border">
        <thead class="bg-neutral-100">
            <tr>
                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Élément</th>
                <th class="px-3 sm:px-6 py-3 text-center text-xs font-medium text-primary uppercase tracking-wider">Qté</th>
                @can('manage_stock')
                <th class="px-3 sm:px-6 py-3 text-right text-xs font-medium text-primary uppercase tracking-wider">Actions</th>
                @endcan
            </tr>
        </thead>
        <tbody id="stocks-tbody" class="bg-card divide-y divide-border">
            @if($stocks->isEmpty())
                <tr id="stocks-empty-row">
                    <td colspan="{{ auth()->user()->can('manage_stock') ? 3 : 2 }}" class="px-6 py-12 text-center text-secondary">Aucun stock enregistré</td>
                </tr>
            @else
                @include('admin.stock.partials.stocks-rows', compact('stocks'))
            @endif
        </tbody>
    </table>
</div>
<div class="px-4 sm:px-6 py-4 border-t border-border">
    <p class="text-sm text-secondary text-center" x-show="!easyloadStockHasMore && !easyloadStockLoading">
        {{ $stocks->total() }} stock(s) au total — affichage par 20
    </p>
    <div x-ref="stockEasyloadSentinel" class="py-4 flex justify-center min-h-[3rem]">
        <div x-show="easyloadStockLoading" class="flex items-center gap-2 text-secondary text-sm">
            <svg class="animate-spin h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Chargement...</span>
        </div>
    </div>
</div>

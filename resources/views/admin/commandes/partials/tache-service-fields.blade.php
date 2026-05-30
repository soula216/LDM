@php
    $isCustom = $tache && filled($tache->custom_service);
    $serviceType = old("taches.{$index}.service_type", $isCustom ? 'custom' : 'catalog');
@endphp
<div class="tache-service-block w-full">
    <x-label value="{{ __('Service') }}" class="text-primary font-medium mb-2" />
    <div class="flex flex-wrap gap-4 mb-3">
        <label class="inline-flex items-center gap-2 text-sm text-primary cursor-pointer">
            <input
                type="radio"
                name="taches[{{ $index }}][service_type]"
                value="catalog"
                class="tache-service-type-radio rounded border-border text-primary focus:ring-primary"
                {{ $serviceType === 'catalog' ? 'checked' : '' }}
            />
            Service du catalogue
        </label>
        <label class="inline-flex items-center gap-2 text-sm text-primary cursor-pointer">
            <input
                type="radio"
                name="taches[{{ $index }}][service_type]"
                value="custom"
                class="tache-service-type-radio rounded border-border text-primary focus:ring-primary"
                {{ $serviceType === 'custom' ? 'checked' : '' }}
            />
            Service personnalisé
        </label>
    </div>

    <div class="tache-service-catalog {{ $serviceType === 'custom' ? 'hidden' : '' }}">
        <div class="w-full service-combobox-wrapper">
            <select name="taches[{{ $index }}][service_id]" id="taches[{{ $index }}][service_id]" class="tache-service-select sr-only" aria-hidden="true" tabindex="-1">
                <option value="">Sélectionner un service</option>
                @foreach($services as $service)
                    <option value="{{ $service->id }}" {{ (string) old("taches.{$index}.service_id", $tache->service_id ?? '') === (string) $service->id ? 'selected' : '' }}>{{ $service->nom }}</option>
                @endforeach
            </select>
            <div class="service-combobox relative">
                <button type="button" class="service-combobox-trigger w-full input-field text-left flex items-center justify-between" aria-haspopup="listbox" aria-expanded="false">
                    <span class="service-combobox-trigger-text truncate">
                        @if($serviceType === 'custom')
                            Sélectionner un service
                        @elseif($tache && $tache->service)
                            {{ $tache->service->nom }}
                        @else
                            Sélectionner un service
                        @endif
                    </span>
                    <svg class="w-4 h-4 flex-shrink-0 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div class="service-combobox-dropdown absolute left-0 right-0 top-full mt-1 z-50 bg-card border border-border rounded-lg shadow-lg overflow-hidden flex-col max-h-72" data-open="false">
                    <input type="text" class="service-combobox-filter px-3 py-2 text-sm flex-shrink-0" placeholder="Filtrer les services..." autocomplete="off">
                    <ul class="service-combobox-list overflow-y-auto py-1 text-sm text-primary flex-1 min-h-0" role="listbox"></ul>
                </div>
            </div>
        </div>
    </div>

    <div class="tache-service-custom {{ $serviceType === 'catalog' ? 'hidden' : '' }}">
        <label class="block text-sm text-primary font-medium mb-2">Groupe</label>
        <select
            name="taches[{{ $index }}][groupe_id]"
            id="taches[{{ $index }}][groupe_id]"
            class="block w-full input-field tache-custom-groupe-select"
        >
            <option value="">Sélectionner un groupe</option>
            @foreach($groupes as $groupe)
                <option value="{{ $groupe->id }}" {{ (string) old("taches.{$index}.groupe_id", $tache?->groupe_id ?? '') === (string) $groupe->id ? 'selected' : '' }}>{{ $groupe->nom }}</option>
            @endforeach
        </select>
        <input
            type="text"
            id="taches[{{ $index }}][custom_service]"
            name="taches[{{ $index }}][custom_service]"
            class="block w-full input-field tache-custom-service-input mt-3"
            value="{{ old("taches.{$index}.custom_service", $tache?->custom_service ?? '') }}"
            placeholder="Saisir le nom du service"
        />
    </div>
</div>

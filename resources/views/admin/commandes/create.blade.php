<div x-data="{ showWarningModal: false, warningMessage: '' }" x-cloak>
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3 space-y-2 sm:space-y-0">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-primary rounded-lg">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <h2 class="text-xl sm:text-2xl font-semibold text-primary">
                    {{ __('Nouvelle Commande') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-8 bg-app min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card">
                <form action="{{ route('admin.commandes.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        @unless(auth()->user()->hasRole('dentist'))
                        <div>
                            <x-label for="dentiste_id" value="{{ __('Dentiste') }}" class="text-primary font-medium mb-2" />
                            <select name="dentiste_id" id="dentiste_id" class="block w-full input-field" required>
                                <option value="">Sélectionner un dentiste</option>
                                @foreach($dentistes as $dentiste)
                                    <option value="{{ $dentiste->id }}">{{ $dentiste->full_name ?: $dentiste->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error for="dentiste_id" class="mt-2" />
                        </div>
                        @else
                        <input type="hidden" name="dentiste_id" value="{{ auth()->id() }}">
                        @endunless

                        <div>
                            <x-label for="num_cmd" value="{{ __('Numéro Commande') }}" class="text-primary font-medium mb-2" />
                            <x-input id="num_cmd" name="num_cmd" type="text" class="block w-full input-field" required />
                            <x-input-error for="num_cmd" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="nom_patient" value="{{ __('Nom Patient') }}" class="text-primary font-medium mb-2" />
                            <x-input id="nom_patient" name="nom_patient" type="text" class="block w-full input-field" />
                            <x-input-error for="nom_patient" class="mt-2" />
                        </div>

                        <div class="flex items-center pt-8">
                            <x-checkbox id="urgent" name="urgent" value="1" class="rounded border-border text-primary focus:ring-primary" />
                            <x-label for="urgent" value="{{ __('Commande Urgente') }}" class="ml-2 text-primary font-medium" />
                        </div>
                    </div>

                    <!-- Tâches -->
                    <div id="taches-container" class="space-y-4">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-primary">Tâches</h3>
                        </div>

                        <div class="tache-item card border-l-4 border-primary relative">
                            <button type="button" class="delete-tache absolute top-0 right-0 p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-bl-lg transition-colors duration-200 z-10" title="Supprimer cette tâche">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                            <div class="tache-fields-row flex flex-col gap-4 pt-10 md:pt-0">
                                @include('admin.commandes.partials.tache-service-fields', ['index' => 0, 'services' => $services, 'groupes' => $groupes, 'tache' => null])

                                <div class="flex flex-col sm:flex-row gap-4 w-full tache-nb-teinte">
                                    <div class="w-full sm:flex-1 min-w-0">
                                        <x-label for="taches[0][nb_elem]" value="Nombre d'éléments" class="text-primary font-medium mb-2" />
                                        <x-input id="taches[0][nb_elem]" name="taches[0][nb_elem]" type="number" min="1" class="block w-full input-field" required />
                                    </div>

                                    <div class="w-full sm:flex-1 min-w-0">
                                        <x-label for="taches[0][dents]" value="{{ __('Dents') }}" class="text-primary font-medium mb-2" />
                                        <x-input id="taches[0][dents]" name="taches[0][dents]" type="text" class="block w-full input-field" />
                                    </div>

                                    <div class="w-full sm:flex-1 min-w-0">
                                            <x-label for="taches[0][teinte]" value="{{ __('Teinte') }}" class="text-primary font-medium mb-2" />
                                            <x-input id="taches[0][teinte]" name="taches[0][teinte]" type="text" class="block w-full input-field" />
                                    </div>
                                </div>

                                <div class="w-full">
                                    <x-label for="taches[0][date_livraison]" value="{{ __('Date de livraison') }}" class="text-primary font-medium mb-2" />
                                    <x-input id="taches[0][date_livraison]" name="taches[0][date_livraison]" type="text" class="block w-full input-field tache-date-livraison" placeholder="Sélectionner date et heure" />
                                </div>
                            </div>
                        </div>

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
                        <textarea name="commentaire" id="commentaire" rows="3" class="block w-full input-field"></textarea>
                        <x-input-error for="commentaire" class="mt-2" />
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 sm:gap-4 pt-4 border-t border-border">
                        <a href="{{ route('admin.commandes.index') }}" class="btn-secondary text-center sm:w-auto">
                            Annuler
                        </a>
                        <button type="submit" class="btn-primary w-full sm:w-auto">
                            Créer la commande
                        </button>
                    </div>
                </form>
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
        @include('admin.commandes.partials.tache-service-scripts')
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let tacheIndex = 1;
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
                // Initialiser la date de livraison pour la première tâche
                if (input.id === 'taches[0][date_livraison]') {
                    const dateValue = getDeliveryDate(false);
                    if (input._flatpickr) {
                        input._flatpickr.setDate(dateValue, false);
                    }
                }
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
                const alpineData = Alpine.$data(document.querySelector('[x-data*="showWarningModal"]'));
                if (alpineData) {
                    alpineData.warningMessage = message || 'Vous devez garder au moins une tâche.';
                    alpineData.showWarningModal = true;
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

            // Combobox Service avec filtre
            function initServiceCombobox(wrapperEl) {
                if (!wrapperEl) return;
                const select = wrapperEl.querySelector('select.tache-service-select');
                const trigger = wrapperEl.querySelector('.service-combobox-trigger');
                const triggerText = wrapperEl.querySelector('.service-combobox-trigger-text');
                const dropdown = wrapperEl.querySelector('.service-combobox-dropdown');
                const filterInput = wrapperEl.querySelector('.service-combobox-filter');
                const listEl = wrapperEl.querySelector('.service-combobox-list');
                if (!select || !trigger || !dropdown || !filterInput || !listEl) return;

                function buildList(filter) {
                    const term = (filter || '').toLowerCase().trim();
                    listEl.innerHTML = '';
                    for (let i = 0; i < select.options.length; i++) {
                        const opt = select.options[i];
                        const text = (opt.textContent || opt.innerText || '').trim();
                        if (term && text && !text.toLowerCase().includes(term)) continue;
                        const li = document.createElement('li');
                        li.className = 'service-combobox-option px-3 py-2 cursor-pointer hover:bg-neutral-100 rounded';
                        li.setAttribute('data-value', opt.value);
                        li.textContent = text;
                        li.setAttribute('role', 'option');
                        listEl.appendChild(li);
                    }
                }
                function updateTriggerText() {
                    const opt = select.options[select.selectedIndex];
                    triggerText.textContent = opt ? (opt.textContent || opt.innerText || '').trim() : 'Sélectionner un service';
                }
                function closeDropdown() {
                    dropdown.setAttribute('data-open', 'false');
                    trigger.setAttribute('aria-expanded', 'false');
                    document.removeEventListener('click', closeDropdown);
                }
                function openDropdown() {
                    dropdown.setAttribute('data-open', 'true');
                    trigger.setAttribute('aria-expanded', 'true');
                    filterInput.value = '';
                    buildList('');
                    filterInput.focus();
                }
                // Fermé par défaut : ne jamais ouvrir au chargement
                dropdown.setAttribute('data-open', 'false');
                updateTriggerText();

                trigger.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isOpen = dropdown.getAttribute('data-open') === 'true';
                    if (isOpen) {
                        closeDropdown();
                        return;
                    }
                    openDropdown();
                    setTimeout(() => document.addEventListener('click', closeDropdown), 0);
                });
                dropdown.addEventListener('click', e => e.stopPropagation());
                filterInput.addEventListener('input', function() {
                    buildList(this.value);
                });
                filterInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') closeDropdown();
                });
                listEl.addEventListener('click', function(e) {
                    const li = e.target.closest('.service-combobox-option');
                    if (!li) return;
                    const val = li.getAttribute('data-value');
                    select.value = val;
                    updateTriggerText();
                    closeDropdown();
                });
            }
            document.querySelectorAll('.service-combobox-wrapper').forEach(initServiceCombobox);
            if (tachesContainer) {
                initTacheServiceModeDelegation(tachesContainer);
                initTacheServiceModes(tachesContainer);
            }

            // Fonction pour ajouter une nouvelle tâche
            if (addTacheBtn && tachesContainer) {
                addTacheBtn.addEventListener('click', function() {
                    const isUrgent = urgentCheckbox && urgentCheckbox.checked;
                    const deliveryDate = getDeliveryDate(isUrgent);
                    
                    const firstTache = document.querySelector('.tache-item');
                    if (!firstTache) return;
                    
                    // Détruire Flatpickr sur la première tâche avant de cloner pour éviter de dupliquer les éléments injectés par Flatpickr
                    const firstDateInput = firstTache.querySelector('.tache-date-livraison');
                    if (firstDateInput && firstDateInput._flatpickr) {
                        firstDateInput._flatpickr.destroy();
                        firstDateInput._flatpickr = null;
                    }
                    
                    const newTache = firstTache.cloneNode(true);
                    newTache.innerHTML = newTache.innerHTML.replace(/\[(\d+)\]/g, '[' + tacheIndex + ']');
                    
                    // Supprimer les doublons d'input "date de livraison" (le clone peut contenir l'input + une copie laissée par Flatpickr)
                    const duplicateDateInputs = newTache.querySelectorAll('.tache-date-livraison');
                    for (let i = 1; i < duplicateDateInputs.length; i++) {
                        duplicateDateInputs[i].remove();
                    }
                    
                    // Réinitialiser les valeurs des inputs et selects
                    newTache.querySelectorAll('input, select').forEach(input => {
                        if (input.classList.contains('tache-date-livraison')) {
                            input.value = deliveryDate;
                            if (input._flatpickr) {
                                input._flatpickr.destroy();
                            }
                        } else if (input.type === 'radio' && input.classList.contains('tache-service-type-radio')) {
                            input.checked = input.value === 'catalog';
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
                    
                    // Ré-initialiser Flatpickr sur la première tâche
                    const firstInputAgain = document.querySelector('.tache-item .tache-date-livraison');
                    if (firstInputAgain && !firstInputAgain._flatpickr) {
                        initFlatpickr(firstInputAgain);
                    }
                    
                    // Initialiser Flatpickr pour le nouvel input de date
                    const newDateInput = newTache.querySelector('.tache-date-livraison');
                    if (newDateInput) {
                        initFlatpickr(newDateInput);
                        if (newDateInput._flatpickr) {
                            newDateInput._flatpickr.setDate(deliveryDate, false);
                        }
                    }

                    // Initialiser le combobox Service pour la nouvelle tâche
                    const newComboboxWrapper = newTache.querySelector('.service-combobox-wrapper');
                    if (newComboboxWrapper) initServiceCombobox(newComboboxWrapper);
                    applyTacheServiceMode(newTache, 'catalog');

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
    @endpush
</x-app-layout>
</div>
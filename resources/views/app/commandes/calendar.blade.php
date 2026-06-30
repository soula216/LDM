<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3 space-y-2 sm:space-y-0">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-gradient-to-br from-primary to-primary-dark rounded-lg">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h2 class="text-xl sm:text-2xl font-semibold text-primary">
                    {{ __('Calendrier des Commandes') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-8 bg-app min-h-screen" x-data="{ showTaskModal: false, taskData: {}, showDayEventsModal: false, dayEventsDate: '', dayEvents: [] }" x-on:open-task-modal.window="showTaskModal = true; taskData = $event.detail" x-on:open-day-events-modal.window="showDayEventsModal = true; dayEventsDate = $event.detail.dateFormatted; dayEvents = $event.detail.events" x-cloak>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Légende -->
            <div class="card mb-6 bg-gradient-to-br from-card via-neutral-50 to-card">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-base sm:text-lg font-semibold text-primary mb-3 flex items-center">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Légende des Statuts
                        </h3>
                        <div class="flex flex-wrap gap-3 sm:gap-4">
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 rounded" style="background-color: #6B7280;"></div>
                                <span class="text-xs sm:text-sm text-secondary">Reçue</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 rounded" style="background-color: #F59E0B;"></div>
                                <span class="text-xs sm:text-sm text-secondary">En cours</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 rounded" style="background-color: #22C55E;"></div>
                                <span class="text-xs sm:text-sm text-secondary">Terminée</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs sm:text-sm text-danger font-medium">⚡</span>
                                <span class="text-xs sm:text-sm text-secondary">Urgent</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 rounded" style="background-color: #EF4444;"></div>
                                <span class="text-xs sm:text-sm text-secondary">Passé / &lt; 2h</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendrier -->
            <div class="card bg-gradient-to-br from-card via-neutral-50 to-card overflow-hidden">
                <div class="p-4 sm:p-6">
                    <div id="calendar-day-reorder-hint" class="hidden mb-4 p-3 sm:p-4 bg-primary/10 border border-primary/20 rounded-lg flex items-start gap-3">
                        <svg class="w-5 h-5 text-primary flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                        <p class="text-sm text-primary">
                            <span class="font-semibold">Vue jour :</span> saisissez la poignée <strong>⋮⋮</strong> à gauche de chaque commande et glissez pour modifier l'ordre d'affichage et d'export Excel.
                        </p>
                    </div>
                    <div id="calendar"></div>
                </div>
            </div>
        </div>

        <!-- Modal Détail de la Tâche -->
        <div x-show="showTaskModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             style="display: none;"
             @click.self="showTaskModal = false">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" x-on:click="showTaskModal = false"></div>
            <div class="bg-white rounded-2xl w-full sm:max-w-md shadow-xl border border-gray-100 relative z-10 overflow-hidden"
                 style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);"
                 @click.stop>
                <div class="px-4 py-5 sm:px-6 sm:py-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3 sm:gap-4">
                            <div class="flex-shrink-0 bg-primary/10 rounded-full p-2">
                                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-primary" x-text="taskData.service || 'Détail de la tâche'"></h3>
                        </div>
                        <button type="button" @click="showTaskModal = false" class="p-2 text-secondary hover:text-primary hover:bg-neutral-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div class="space-y-3 text-sm px-0 sm:px-0">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3">
                            <div><span class="text-secondary block mb-0.5">N° Commande</span><span class="font-medium" x-text="taskData.num_cmd || '-'"></span></div>
                            <div><span class="text-secondary block mb-0.5">Date & Heure livraison</span><span class="font-medium" x-text="taskData.date_livraison_formatted || '-'"></span></div>
                            <div><span class="text-secondary block mb-0.5">Patient</span><span class="font-medium" x-text="taskData.nom_patient || '-'"></span></div>
                            @unless(auth()->user()->hasRole('dentist'))
                            <div><span class="text-secondary block mb-0.5">Dentiste</span><span class="font-medium" x-text="taskData.dentiste || '-'"></span></div>
                            @endunless
                            <div><span class="text-secondary block mb-0.5">Statut</span><span class="font-medium" x-text="taskData.status || '-'"></span></div>
                            <div><span class="text-secondary block mb-0.5">Urgent</span><span class="font-medium" x-text="taskData.urgent ? 'Oui' : 'Non'"></span></div>
                            <div><span class="text-secondary block mb-0.5">Service</span><span class="font-medium" x-text="taskData.service || '-'"></span></div>
                            @unless(auth()->user()->hasRole('dentist'))
                            <div><span class="text-secondary block mb-0.5">Groupe</span><span class="font-medium" x-text="taskData.groupe || '-'"></span></div>
                            @endunless
                            <div><span class="text-secondary block mb-0.5">Nb éléments</span><span class="font-medium" x-text="taskData.nb_elem ?? '-'"></span></div>
                            <div><span class="text-secondary block mb-0.5">Teinte</span><span class="font-medium" x-text="taskData.teinte || '-'"></span></div>
                            @unless(auth()->user()->hasRole('employer'))
                            <div><span class="text-secondary block mb-0.5">Prix unitaire TTC</span><span class="font-medium" x-text="taskData.prix_unitaire_ttc != null ? (parseFloat(taskData.prix_unitaire_ttc).toFixed(2) + ' TND') : '-'"></span></div>
                            <div><span class="text-secondary block mb-0.5">Total TTC</span><span class="font-medium" x-text="taskData.total_ligne_ttc != null ? (parseFloat(taskData.total_ligne_ttc).toFixed(2) + ' TND') : '-'"></span></div>
                            @endunless
                        </div>
                        <template x-if="taskData.commentaire">
                            <div><span class="text-secondary block mb-0.5">Commentaire</span><span class="font-medium" x-text="taskData.commentaire"></span></div>
                        </template>
                    </div>
                    <div class="mt-6 pt-4 border-t border-gray-200 flex flex-col sm:flex-row gap-3 justify-end">
                        <a x-bind:href="taskData.url" x-show="taskData.url" class="inline-flex justify-center items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary/90 transition-colors">
                            Voir la fiche commande
                        </a>
                        <button type="button" @click="showTaskModal = false" class="inline-flex justify-center items-center px-4 py-2 bg-neutral-100 text-secondary text-sm font-medium rounded-lg hover:bg-neutral-200 transition-colors">
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal "Tous les événements du jour" (clic sur "+X en plus") -->
        <div x-show="showDayEventsModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             style="display: none;"
             @click.self="showDayEventsModal = false">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" x-on:click="showDayEventsModal = false"></div>
            <div class="bg-white rounded-2xl w-full sm:max-w-lg max-h-[90vh] flex flex-col shadow-xl border border-gray-100 relative z-10 overflow-hidden"
                 style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);"
                 @click.stop>
                <div class="px-4 py-5 sm:px-6 sm:py-6 border-b border-gray-100 flex-shrink-0">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-primary" x-text="'Événements du ' + dayEventsDate"></h3>
                        <button type="button" @click="showDayEventsModal = false" class="p-2 text-secondary hover:text-primary hover:bg-neutral-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto px-4 pb-5 sm:px-6 sm:pb-6">
                    <template x-if="dayEvents.length === 0">
                        <p class="text-secondary text-sm py-4">Aucun événement.</p>
                    </template>
                    <ul class="space-y-2" x-show="dayEvents.length > 0">
                        <template x-for="(ev, index) in dayEvents" :key="index">
                            <li>
                                <button type="button"
                                        @click="showDayEventsModal = false; $dispatch('open-task-modal', ev.payload)"
                                        class="w-full text-left rounded-lg border border-gray-200 p-3 hover:border-primary hover:bg-primary/5 transition-colors"
                                        :style="'border-left: 4px solid ' + (ev.backgroundColor || '#0EA5E9')">
                                    <div class="font-semibold text-primary" x-text="ev.title"></div>
                                    <div class="text-sm text-secondary mt-1" x-text="ev.heure"></div>
                                    <div class="text-sm text-secondary" x-text="ev.display_name"></div>
                                </button>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css" rel="stylesheet" />
    <style>
        /* Personnalisation FullCalendar avec la charte LDM v2 */
        :root {
            --fc-border-color: #E5E7EB;
            --fc-daygrid-event-dot-width: 8px;
            --fc-event-border-radius: 6px;
            --fc-event-border-width: 2px;
        }

        #calendar {
            max-width: 100%;
            margin: 0 auto;
            min-height: 600px;
        }
        
        /* S'assurer que le calendrier est visible */
        .fc {
            width: 100%;
        }

        /* Header du calendrier */
        .fc-header-toolbar {
            margin-bottom: 1.5rem !important;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .fc-toolbar-title {
            font-size: 1.5rem !important;
            font-weight: 600 !important;
            color: #0F172A !important;
            font-family: 'Manrope', 'Inter', sans-serif !important;
        }

        /* Boutons de navigation */
        .fc-button {
            background: linear-gradient(to bottom right, #0EA5E9, #0F172A) !important;
            border: none !important;
            border-radius: 0.5rem !important;
            padding: 0.5rem 1rem !important;
            font-weight: 500 !important;
            text-transform: none !important;
            font-size: 0.875rem !important;
            transition: all 0.2s !important;
            margin: 0 0.25rem !important;
        }
        
        /* Bouton Export Excel - Masqué par défaut et forcé à être masqué */
        .fc-exportExcel-button {
            background: linear-gradient(to bottom right, #22C55E, #16A34A) !important;
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }
        
        /* Forcer le masquage si la classe hidden est présente */
        .fc-exportExcel-button.fc-button-hidden {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }
        
        .fc-exportExcel-button:hover {
            background: linear-gradient(to bottom right, #16A34A, #22C55E) !important;
        }
        
        /* Masquer le bouton dans toutes les vues sauf jour */
        .fc-dayGridMonth-view .fc-exportExcel-button,
        .fc-timeGridWeek-view .fc-exportExcel-button,
        .fc-dayGridMonth-view ~ .fc-exportExcel-button,
        .fc-timeGridWeek-view ~ .fc-exportExcel-button {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }
        
        /* Afficher le bouton uniquement dans la vue jour */
        .fc-timeGridDay-view .fc-exportExcel-button,
        body:has(.fc-timeGridDay-view) .fc-exportExcel-button {
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
            pointer-events: auto !important;
        }

        .fc-button:first-child {
            margin-left: 0 !important;
        }

        .fc-button:last-child {
            margin-right: 0 !important;
        }

        .fc-button:hover {
            background: linear-gradient(to bottom right, #0F172A, #0EA5E9) !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .fc-button:active {
            transform: translateY(0);
        }

        .fc-button-primary:not(:disabled):active,
        .fc-button-primary:not(:disabled).fc-button-active {
            background: linear-gradient(to bottom right, #0F172A, #0EA5E9) !important;
        }

        /* Espacement entre les groupes de boutons */
        .fc-toolbar-chunk {
            display: flex !important;
            gap: 0.5rem !important;
        }

        .fc-toolbar-chunk .fc-button-group {
            display: flex !important;
            gap: 0.5rem !important;
        }

        .fc-toolbar-chunk .fc-button-group .fc-button {
            margin: 0 !important;
        }

        /* Vue du jour actuel */
        .fc-day-today {
            background: linear-gradient(to bottom right, rgba(14, 165, 233, 0.1), rgba(15, 23, 42, 0.05)) !important;
        }

        /* Événements */
        .fc-event {
            border-radius: 6px !important;
            padding: 4px 8px !important;
            font-size: 0.75rem !important;
            font-weight: 500 !important;
            cursor: pointer !important;
            transition: all 0.2s !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
        }

        .fc-event:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15) !important;
            z-index: 10 !important;
        }

        /* Masquer le popover FullCalendar "+X en plus" (on utilise notre modale à la place) */
        .fc-popover {
            display: none !important;
        }

        /* Cellules du calendrier */
        .fc-daygrid-day {
            border-color: #E5E7EB !important;
        }

        .fc-daygrid-day-top {
            padding: 0.5rem !important;
        }

        .fc-daygrid-day-number {
            color: #0F172A !important;
            font-weight: 500 !important;
            padding: 0.5rem !important;
        }

        .fc-col-header-cell {
            background: linear-gradient(to bottom, #F3F4F6, #FFFFFF) !important;
            border-color: #E5E7EB !important;
            padding: 0.75rem !important;
        }

        .fc-col-header-cell-cushion {
            color: #0F172A !important;
            font-weight: 600 !important;
            font-size: 0.875rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
        }

        /* Vue semaine/jour */
        .fc-timeGridDay-view .fc-timegrid-slot,
        .fc-timeGridWeek-view .fc-timegrid-slot {
            border-color: #E5E7EB !important;
        }

        .fc-timegrid-event {
            border-radius: 6px !important;
            padding: 4px 8px !important;
        }

        /* Vue jour : empiler les commandes pour permettre le glisser-déposer */
        .fc-timeGridDay-view .fc-daygrid-day-events {
            min-height: auto !important;
            position: relative !important;
        }

        .fc-timeGridDay-view .fc-daygrid-event-harness,
        .fc-timeGridDay-view .fc-daygrid-event-harness-abs {
            position: relative !important;
            top: auto !important;
            left: auto !important;
            right: auto !important;
            bottom: auto !important;
            margin-bottom: 4px !important;
            width: 100% !important;
        }

        .fc-timeGridDay-view .fc-daygrid-event {
            position: relative !important;
            margin-bottom: 0 !important;
        }

        .fc-timeGridDay-view .fc-event-sortable-chosen {
            z-index: 20 !important;
        }

        .fc-timeGridDay-view .fc-event-sortable-ghost {
            opacity: 0.45;
        }

        .fc-day-reorder-handle {
            flex-shrink: 0;
            cursor: grab;
            color: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
            line-height: 1;
            padding: 2px 4px;
            user-select: none;
            touch-action: none;
            letter-spacing: -2px;
        }

        .fc-day-reorder-handle:active {
            cursor: grabbing;
        }

        .fc-event-sortable-chosen .fc-day-reorder-handle {
            cursor: grabbing;
        }

        /* Responsive */
        @media (max-width: 640px) {
            .fc-toolbar-title {
                font-size: 1.25rem !important;
            }

            .fc-button {
                padding: 0.375rem 0.75rem !important;
                font-size: 0.75rem !important;
            }

            .fc-event {
                font-size: 0.625rem !important;
                padding: 2px 4px !important;
            }
        }

        /* Tooltip personnalisé */
        .fc-event-title {
            font-weight: 600 !important;
        }

        /* Animation pour la notification de mise à jour */
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/locales/fr.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            
            if (!calendarEl) {
                console.error('Élément #calendar non trouvé');
                return;
            }
            
            // Vérifier si l'utilisateur est un dentiste
            const isDentist = {{ auth()->user()->hasRole('dentist') ? 'true' : 'false' }};
            
            console.log('Initialisation du calendrier FullCalendar...');

            let dayViewDragging = false;
            let sortableInitTimer = null;
            let activeTooltipEl = null;
            let activeTooltipOwnerEl = null;

            function hideActiveEventTooltip() {
                if (activeTooltipEl) {
                    activeTooltipEl.remove();
                    activeTooltipEl = null;
                    activeTooltipOwnerEl = null;
                }
            }

            function positionEventTooltip(tooltipEl, eventEl) {
                const tooltipWidth = tooltipEl.offsetWidth;
                const tooltipHeight = tooltipEl.offsetHeight;
                const rect = eventEl.getBoundingClientRect();
                const viewportHeight = window.innerHeight;
                const viewportWidth = window.innerWidth;
                const spacing = 8;

                let left = rect.left + rect.width / 2 - tooltipWidth / 2;

                if (left < 10) {
                    left = 10;
                } else if (left + tooltipWidth > viewportWidth - 10) {
                    left = viewportWidth - tooltipWidth - 10;
                }

                let top;
                const topThreshold = 200;

                if (rect.top < topThreshold) {
                    top = rect.bottom + spacing;
                    if (top + tooltipHeight > viewportHeight - 10) {
                        top = Math.max(10, viewportHeight - tooltipHeight - 10);
                    }
                } else {
                    top = rect.top - tooltipHeight - spacing;
                    if (top < 10) {
                        top = rect.bottom + spacing;
                        if (top + tooltipHeight > viewportHeight - 10) {
                            top = Math.max(10, viewportHeight - tooltipHeight - 10);
                        }
                    }
                }

                tooltipEl.style.left = left + 'px';
                tooltipEl.style.top = top + 'px';
                tooltipEl.style.visibility = 'visible';
            }

            function showEventTooltip(info) {
                if (dayViewDragging) {
                    return;
                }

                hideActiveEventTooltip();

                const props = info.event.extendedProps;
                const dentistInfo = isDentist ? '' : `<div><strong>Dentiste:</strong> ${props.dentiste || 'N/A'}</div>`;
                const groupeInfo = isDentist ? '' : `<div><strong>Groupe:</strong> ${props.groupe || 'N/A'}</div>`;
                const tooltipHtml = `
                    <div class="p-2 bg-white rounded-lg shadow-lg border border-gray-200 text-sm">
                        <div class="font-semibold text-primary mb-1">${info.event.title}</div>
                        <div class="text-secondary space-y-1">
                            <div><strong>Patient:</strong> ${props.nom_patient || 'N/A'}</div>
                            ${dentistInfo}
                            <div><strong>Service:</strong> ${props.service || 'N/A'}</div>
                            ${groupeInfo}
                            <div><strong>Éléments:</strong> ${props.nb_elem || 'N/A'}</div>
                            <div><strong>Statut:</strong> ${props.status || 'N/A'}</div>
                            ${props.urgent ? '<div class="text-danger font-medium">⚡ Commande Urgente</div>' : ''}
                        </div>
                    </div>
                `;

                const tooltipEl = document.createElement('div');
                tooltipEl.className = 'fc-event-tooltip';
                tooltipEl.innerHTML = tooltipHtml;
                tooltipEl.style.position = 'fixed';
                tooltipEl.style.zIndex = '9999';
                tooltipEl.style.pointerEvents = 'none';
                tooltipEl.style.visibility = 'hidden';
                tooltipEl.style.maxWidth = '300px';
                document.body.appendChild(tooltipEl);

                positionEventTooltip(tooltipEl, info.el);

                activeTooltipEl = tooltipEl;
                activeTooltipOwnerEl = info.el;
            }

            function bindEventTooltipListeners(info) {
                if (info.el._tooltipBound) {
                    return;
                }

                info.el._tooltipBound = true;

                info.el.addEventListener('mouseenter', function() {
                    showEventTooltip(info);
                });

                info.el.addEventListener('mouseleave', function(e) {
                    const related = e.relatedTarget;
                    if (!related || !info.el.contains(related)) {
                        if (activeTooltipOwnerEl === info.el) {
                            hideActiveEventTooltip();
                        }
                    }
                });
            }

            document.addEventListener('mousemove', function(e) {
                if (!activeTooltipOwnerEl || dayViewDragging) {
                    return;
                }

                const target = e.target;
                if (target instanceof Node && !activeTooltipOwnerEl.contains(target)) {
                    hideActiveEventTooltip();
                }
            });

            calendarEl.addEventListener('scroll', hideActiveEventTooltip, true);
            
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                firstDay: 1, // Lundi
                editable: false,
                headerToolbar: {
                    left: 'prev,next today exportExcel',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                customButtons: {
                    exportExcel: {
                        text: 'Export Excel',
                        click: function() {
                            // Vérifier strictement que la vue actuelle est la vue jour
                            const currentView = calendar.view;
                            if (!currentView || currentView.type !== 'timeGridDay') {
                                alert('L\'export Excel est disponible uniquement en vue jour. Veuillez passer en vue jour pour exporter.');
                                return;
                            }
                            
                            // Obtenir la date actuelle de la vue (premier jour affiché)
                            const currentDate = currentView.activeStart;
                            if (!currentDate) {
                                alert('Impossible de déterminer la date. Veuillez réessayer.');
                                return;
                            }
                            
                            // Formater la date au format YYYY-MM-DD
                            const year = currentDate.getFullYear();
                            const month = String(currentDate.getMonth() + 1).padStart(2, '0');
                            const day = String(currentDate.getDate()).padStart(2, '0');
                            const dateStr = `${year}-${month}-${day}`;
                            
                            console.log('Export Excel pour la date:', dateStr);
                            
                            // Construire l'URL d'export
                            const exportUrl = '{{ route("app.commandes.calendar.export-excel") }}?date=' + dateStr;
                            
                            // Ouvrir dans une nouvelle fenêtre pour télécharger
                            window.location.href = exportUrl;
                        }
                    }
                },
                // S'assurer que le bouton est toujours créé
                buttonIcons: false,
                buttonText: {
                    today: 'Aujourd\'hui',
                    month: 'Mois',
                    week: 'Semaine',
                    day: 'Jour'
                },
                views: {
                    dayGridMonth: {
                        dayMaxEvents: 3,
                    },
                    timeGridDay: {
                        dayMaxEvents: false,
                        dayMaxEventRows: false,
                    },
                },
                height: 'auto',
                contentHeight: 'auto',
                aspectRatio: 1.8,
                eventContent: function(arg) {
                    const props = arg.event.extendedProps;
                    const urgentPrefix = props.urgent ? '⚡ ' : '';
                    const serviceName = props.service || 'N/A';
                    const heure = props.heure || '';
                    const displayName = props.display_name || '';
                    const isDayView = arg.view.type === 'timeGridDay';

                    const wrapper = document.createElement('div');
                    wrapper.style.padding = '3px 6px';
                    wrapper.style.lineHeight = '1.3';

                    if (isDayView) {
                        wrapper.style.display = 'flex';
                        wrapper.style.alignItems = 'flex-start';
                        wrapper.style.gap = '8px';

                        const handle = document.createElement('span');
                        handle.className = 'fc-day-reorder-handle';
                        handle.textContent = '⋮⋮';
                        handle.title = 'Glisser pour réorganiser';
                        wrapper.appendChild(handle);
                    }

                    const content = document.createElement('div');
                    content.style.flex = '1';
                    content.style.minWidth = '0';

                    const titleDiv = document.createElement('div');
                    titleDiv.style.fontWeight = '600';
                    titleDiv.style.fontSize = '0.8rem';
                    titleDiv.style.wordWrap = 'break-word';
                    titleDiv.style.overflowWrap = 'break-word';
                    titleDiv.style.whiteSpace = 'normal';
                    titleDiv.style.lineHeight = '1.2';
                    titleDiv.textContent = urgentPrefix + serviceName;
                    content.appendChild(titleDiv);

                    if (heure) {
                        const heureDiv = document.createElement('div');
                        heureDiv.style.fontSize = '0.8rem';
                        heureDiv.style.opacity = '0.95';
                        heureDiv.style.marginTop = '2px';
                        heureDiv.textContent = heure;
                        content.appendChild(heureDiv);
                    }

                    if (displayName) {
                        const nameDiv = document.createElement('div');
                        nameDiv.style.fontSize = '0.8rem';
                        nameDiv.style.opacity = '0.95';
                        nameDiv.style.marginTop = '2px';
                        nameDiv.textContent = displayName;
                        content.appendChild(nameDiv);
                    }

                    wrapper.appendChild(content);

                    return { domNodes: [wrapper] };
                },
                events: function(fetchInfo, successCallback, failureCallback) {
                    const url = '{{ route("app.commandes.calendar.events") }}?start=' + fetchInfo.startStr + '&end=' + fetchInfo.endStr;
                    console.log('Chargement des événements:', url);
                    
                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erreur HTTP: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Événements reçus:', data);
                        successCallback(data);
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des événements:', error);
                        failureCallback(error);
                    });
                },
                eventClick: function(info) {
                    if (dayViewDragging) {
                        info.jsEvent.preventDefault();
                        return;
                    }
                    info.jsEvent.preventDefault();
                    var payload = Object.assign({}, info.event.extendedProps, { url: info.event.url });
                    window.dispatchEvent(new CustomEvent('open-task-modal', { detail: payload }));
                },
                eventDidMount: function(info) {
                    bindEventTooltipListeners(info);

                    if (info.view.type !== 'timeGridDay') {
                        return;
                    }

                    const harness = info.el.closest('.fc-daygrid-event-harness');
                    if (harness && info.event.extendedProps.tache_id) {
                        harness.setAttribute('data-tache-id', info.event.extendedProps.tache_id);
                        harness.style.position = 'relative';
                        harness.style.top = 'auto';
                        harness.style.left = 'auto';
                        harness.style.right = 'auto';
                        harness.style.width = '100%';
                    }

                    scheduleDayViewSortableInit();
                },
                eventWillUnmount: function(info) {
                    if (activeTooltipOwnerEl === info.el) {
                        hideActiveEventTooltip();
                    }
                },
                eventOrder: function(a, b) {
                    const orderA = a.extendedProps.displayOrder ?? 999999;
                    const orderB = b.extendedProps.displayOrder ?? 999999;
                    if (orderA !== orderB) {
                        return orderA - orderB;
                    }

                    return (a.extendedProps.tache_id || 0) - (b.extendedProps.tache_id || 0);
                },
                moreLinkClick: function(info) {
                    info.jsEvent.preventDefault();
                    var dateFormatted = info.date.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                    dateFormatted = dateFormatted.charAt(0).toUpperCase() + dateFormatted.slice(1);
                    var events = (info.allSegs || []).map(function(seg) {
                        var ev = seg.event;
                        var props = ev.extendedProps || {};
                        return {
                            title: (props.urgent ? '⚡ ' : '') + (props.service || ev.title || 'N/A'),
                            heure: props.heure || '',
                            display_name: props.display_name || '',
                            backgroundColor: ev.backgroundColor || ev.backgroundColor,
                            payload: Object.assign({}, props, { url: ev.url })
                        };
                    });
                    window.dispatchEvent(new CustomEvent('open-day-events-modal', { detail: { dateFormatted: dateFormatted, events: events } }));
                },
                eventDisplay: 'block',
                displayEventTime: false,
            });

            const reorderHintEl = document.getElementById('calendar-day-reorder-hint');
            let daySortable = null;
            let isSavingOrder = false;
            let lastKnownVersion = null;
            let suppressCalendarRefetchUntil = 0;
            let localReorderInProgress = false;
            let preDragTacheOrder = [];

            function getContainerTacheIds(container) {
                return [...container.querySelectorAll('.fc-daygrid-event-harness[data-tache-id]')]
                    .map(function(el) {
                        return parseInt(el.getAttribute('data-tache-id'), 10);
                    })
                    .filter(function(id) {
                        return !Number.isNaN(id);
                    });
            }

            function applyDomTacheOrder(container, tacheIds) {
                const harnessById = {};
                container.querySelectorAll('.fc-daygrid-event-harness[data-tache-id]').forEach(function(harness) {
                    harnessById[harness.getAttribute('data-tache-id')] = harness;
                });

                tacheIds.forEach(function(tacheId) {
                    const harness = harnessById[String(tacheId)];
                    if (harness) {
                        container.appendChild(harness);
                    }
                });
            }

            function updateEventsDisplayOrderSilent(tacheIds) {
                calendar.getEvents().forEach(function(event) {
                    const tacheId = Number(event.extendedProps.tache_id);
                    const index = tacheIds.indexOf(tacheId);
                    if (index >= 0) {
                        event.extendedProps.displayOrder = index + 1;
                    }
                });
            }

            function scheduleDayViewSortableInit() {
                if (isSavingOrder || localReorderInProgress || dayViewDragging) {
                    return;
                }
                clearTimeout(sortableInitTimer);
                sortableInitTimer = setTimeout(initDayViewSortable, 150);
            }

            function getDayViewEventsContainer() {
                return document.querySelector('.fc-timeGridDay-view .fc-daygrid-day-events');
            }

            function getCurrentDayDateStr() {
                if (!calendar.view || !calendar.view.currentStart) {
                    return null;
                }

                const d = calendar.view.currentStart;
                const year = d.getFullYear();
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');

                return `${year}-${month}-${day}`;
            }

            function toggleDayReorderHint() {
                if (!reorderHintEl) {
                    return;
                }

                const isDayView = calendar.view && calendar.view.type === 'timeGridDay';
                reorderHintEl.classList.toggle('hidden', !isDayView);
            }

            function destroyDayViewSortable() {
                if (daySortable) {
                    daySortable.destroy();
                    daySortable = null;
                }
            }

            async function saveDayViewOrder(container) {
                if (isSavingOrder) {
                    return;
                }

                const dateStr = getCurrentDayDateStr();
                if (!dateStr) {
                    return;
                }

                const tacheIds = getContainerTacheIds(container);

                if (tacheIds.length < 2) {
                    localReorderInProgress = false;
                    return;
                }

                isSavingOrder = true;
                localReorderInProgress = true;

                try {
                    const response = await fetch('{{ route('app.commandes.calendar.reorder') }}', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            date: dateStr,
                            tache_ids: tacheIds,
                        }),
                    });

                    const data = await response.json().catch(function() {
                        return {};
                    });

                    if (!response.ok) {
                        console.error('Réorganisation refusée:', response.status, data);
                        throw new Error(data.message || ('Erreur HTTP: ' + response.status));
                    }

                    if (typeof data.version === 'number') {
                        lastKnownVersion = data.version;
                    }

                    suppressCalendarRefetchUntil = Date.now() + 30000;
                    updateEventsDisplayOrderSilent(tacheIds);
                } catch (error) {
                    console.error('Erreur lors de la réorganisation:', error);
                    if (preDragTacheOrder.length > 0) {
                        applyDomTacheOrder(container, preDragTacheOrder);
                        updateEventsDisplayOrderSilent(preDragTacheOrder);
                    } else {
                        calendar.refetchEvents();
                    }
                } finally {
                    isSavingOrder = false;
                    setTimeout(function() {
                        localReorderInProgress = false;
                        if (calendar.view && calendar.view.type === 'timeGridDay') {
                            initDayViewSortable();
                        }
                    }, 300);
                }
            }

            function initDayViewSortable() {
                if (isSavingOrder || localReorderInProgress || dayViewDragging) {
                    return;
                }

                destroyDayViewSortable();
                toggleDayReorderHint();

                if (!calendar.view || calendar.view.type !== 'timeGridDay') {
                    return;
                }

                if (typeof Sortable === 'undefined') {
                    console.warn('SortableJS non chargé');
                    return;
                }

                const container = getDayViewEventsContainer();
                if (!container) {
                    return;
                }

                const harnesses = container.querySelectorAll('.fc-daygrid-event-harness[data-tache-id]');
                if (harnesses.length < 2) {
                    return;
                }

                harnesses.forEach(function(harness) {
                    harness.style.position = 'relative';
                    harness.style.top = 'auto';
                    harness.style.left = 'auto';
                    harness.style.right = 'auto';
                    harness.style.width = '100%';
                });

                daySortable = Sortable.create(container, {
                    animation: 150,
                    handle: '.fc-day-reorder-handle',
                    draggable: '.fc-daygrid-event-harness[data-tache-id]',
                    forceFallback: true,
                    fallbackTolerance: 5,
                    ghostClass: 'fc-event-sortable-ghost',
                    chosenClass: 'fc-event-sortable-chosen',
                    onStart: function() {
                        dayViewDragging = true;
                        localReorderInProgress = true;
                        hideActiveEventTooltip();
                        preDragTacheOrder = getContainerTacheIds(container);
                    },
                    onEnd: function() {
                        dayViewDragging = false;
                        saveDayViewOrder(container);
                    },
                });
            }

            calendar.render();
            console.log('Calendrier FullCalendar initialisé et rendu');
            
            // Fonction pour compter les événements du jour actuel
            function countEventsForCurrentDay() {
                if (!calendar.view || calendar.view.type !== 'timeGridDay') {
                    return 0;
                }
                
                const currentDate = calendar.view.activeStart;
                if (!currentDate) {
                    return 0;
                }
                
                // Normaliser la date du jour actuel (début de journée)
                const dayStart = new Date(currentDate);
                dayStart.setHours(0, 0, 0, 0);
                
                const dayEnd = new Date(currentDate);
                dayEnd.setHours(23, 59, 59, 999);
                
                // Formater pour comparaison
                const dayDateStr = dayStart.toISOString().split('T')[0];
                
                // Méthode 1: Utiliser getEvents avec un filtre de date
                let eventCount = 0;
                try {
                    const allEvents = calendar.getEvents();
                    
                    allEvents.forEach(function(event) {
                        if (!event.start) return;
                        
                        const eventStart = new Date(event.start);
                        const eventStartStr = eventStart.toISOString().split('T')[0];
                        
                        // Vérifier si l'événement commence ce jour
                        if (eventStartStr === dayDateStr) {
                            eventCount++;
                        }
                    });
                } catch (e) {
                    console.error('Erreur lors du comptage des événements:', e);
                }
                
                // Méthode 2: Vérifier aussi les éléments DOM visibles (backup)
                if (eventCount === 0) {
                    const visibleEvents = document.querySelectorAll('.fc-timeGridDay-view .fc-event, .fc-timeGridDay-view .fc-daygrid-event');
                    if (visibleEvents.length > 0) {
                        console.log('⚠️ Événements trouvés via DOM:', visibleEvents.length);
                        // Ne pas utiliser cette méthode comme principale car elle peut être trompeuse
                    }
                }
                
                console.log('📊 Jour:', dayDateStr, '- Événements trouvés:', eventCount);
                return eventCount;
            }
            
            // Fonction pour afficher/masquer le bouton Export Excel (uniquement en vue jour avec événements)
            function toggleExportButton() {
                // Essayer plusieurs sélecteurs possibles
                let exportButton = document.querySelector('.fc-exportExcel-button');
                if (!exportButton) {
                    exportButton = document.querySelector('button.fc-exportExcel-button');
                }
                if (!exportButton) {
                    // Chercher par texte si le sélecteur de classe ne fonctionne pas
                    const buttons = document.querySelectorAll('.fc-button');
                    buttons.forEach(btn => {
                        const text = btn.textContent ? btn.textContent.trim() : '';
                        if (text === 'Export Excel' || text.includes('Export Excel')) {
                            exportButton = btn;
                            // Ajouter la classe pour les prochaines recherches
                            if (!btn.classList.contains('fc-exportExcel-button')) {
                                btn.classList.add('fc-exportExcel-button');
                            }
                        }
                    });
                }
                
                if (!exportButton) {
                    console.warn('⚠️ Bouton Export Excel non trouvé dans le DOM');
                    return;
                }
                
                // Vérifier strictement que la vue est timeGridDay
                const isDayView = calendar.view && calendar.view.type === 'timeGridDay';
                
                if (!isDayView) {
                    // Masquer le bouton dans toutes les autres vues
                    exportButton.style.display = 'none';
                    exportButton.style.visibility = 'hidden';
                    exportButton.style.opacity = '0';
                    exportButton.style.pointerEvents = 'none';
                    exportButton.disabled = true;
                    exportButton.classList.add('fc-button-hidden');
                    return;
                }
                
                // On est en vue jour - compter les événements
                const eventCount = countEventsForCurrentDay();
                
                // Afficher le bouton UNIQUEMENT s'il y a au moins un événement (strictement > 0)
                if (eventCount > 0) {
                    // IL Y A DES ÉVÉNEMENTS - AFFICHER LE BOUTON
                    exportButton.style.display = 'inline-block';
                    exportButton.style.visibility = 'visible';
                    exportButton.style.opacity = '1';
                    exportButton.style.pointerEvents = 'auto';
                    exportButton.disabled = false;
                    exportButton.classList.remove('fc-button-hidden');
                    console.log('✅✅✅ Bouton Export Excel AFFICHÉ (vue jour avec', eventCount, 'événement(s))');
                } else {
                    // IL N'Y A PAS D'ÉVÉNEMENTS - MASQUER LE BOUTON FORTEMENT
                    exportButton.style.display = 'none !important';
                    exportButton.style.visibility = 'hidden !important';
                    exportButton.style.opacity = '0 !important';
                    exportButton.style.pointerEvents = 'none !important';
                    exportButton.disabled = true;
                    exportButton.classList.add('fc-button-hidden');
                    // Forcer le masquage avec setAttribute aussi
                    exportButton.setAttribute('style', 'display: none !important; visibility: hidden !important; opacity: 0 !important; pointer-events: none !important;');
                    console.log('❌❌❌ Bouton Export Excel MASQUÉ (aucun événement pour ce jour -', eventCount, 'événement(s))');
                }
            }
            
            // Variable pour stocker le dernier nombre d'événements détecté
            let lastEventCount = -1;
            
            // Fonction pour forcer la mise à jour du bouton
            function forceUpdateExportButton() {
                if (calendar.view && calendar.view.type === 'timeGridDay') {
                    const currentEventCount = countEventsForCurrentDay();
                    
                    // Ne mettre à jour que si le nombre d'événements a changé
                    if (currentEventCount !== lastEventCount) {
                        lastEventCount = currentEventCount;
                        toggleExportButton();
                    }
                } else {
                    // Si on n'est pas en vue jour, masquer le bouton
                    if (lastEventCount !== -1) {
                        lastEventCount = -1;
                        toggleExportButton();
                    }
                }
            }
            
            // Écouter les changements de vue
            calendar.on('viewDidMount', function() {
                hideActiveEventTooltip();
                console.log('🔄 Vue changée:', calendar.view.type);
                setTimeout(function() {
                    forceUpdateExportButton();
                    scheduleDayViewSortableInit();
                }, 500);
            });
            
            calendar.on('datesSet', function() {
                hideActiveEventTooltip();
                console.log('📅 Dates changées, vue:', calendar.view.type);
                setTimeout(function() {
                    forceUpdateExportButton();
                    scheduleDayViewSortableInit();
                }, 500);
            });
            
            calendar.on('eventsSet', function() {
                console.log('📥 Événements chargés/mis à jour');
                setTimeout(function() {
                    forceUpdateExportButton();
                    scheduleDayViewSortableInit();
                }, 300);
            });
            
            // Écouter aussi les changements de navigation (prev/next)
            calendar.on('dateClick', function() {
                setTimeout(forceUpdateExportButton, 500);
            });
            
            // Masquer le bouton au chargement initial (par défaut masqué)
            setTimeout(function() {
                console.log('🔍 Vérification initiale du bouton Export Excel...');
                forceUpdateExportButton();
            }, 2000);
            
            // Vérifier périodiquement en temps réel (toutes les 1.5 secondes)
            setInterval(function() {
                if (calendar.view && calendar.view.type === 'timeGridDay') {
                    forceUpdateExportButton();
                }
            }, 1500);

            // Système de mise à jour en temps réel
            let isChecking = false;
            let checkInterval = null;

            // Fonction pour vérifier les mises à jour
            async function checkForUpdates() {
                if (isChecking || localReorderInProgress) return;
                if (Date.now() < suppressCalendarRefetchUntil) return;
                isChecking = true;

                try {
                    const response = await fetch('{{ route("app.commandes.calendar.check-version") }}', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Erreur HTTP: ' + response.status);
                    }

                    const data = await response.json();
                    
                    // Si c'est la première vérification, enregistrer la version
                    if (lastKnownVersion === null) {
                        lastKnownVersion = data.version;
                        isChecking = false;
                        return;
                    }

                    // Si la version a changé, recharger les événements
                    if (data.version !== lastKnownVersion) {
                        console.log('Mise à jour détectée! Version:', lastKnownVersion, '->', data.version);
                        lastKnownVersion = data.version;

                        if (Date.now() >= suppressCalendarRefetchUntil) {
                            showUpdateNotification();
                            calendar.refetchEvents();
                        }
                    }
                } catch (error) {
                    console.error('Erreur lors de la vérification des mises à jour:', error);
                } finally {
                    isChecking = false;
                }
            }

            // Fonction pour afficher une notification de mise à jour
            function showUpdateNotification() {
                // Vérifier si une notification existe déjà
                let notification = document.getElementById('calendar-update-notification');
                
                if (!notification) {
                    // Créer la notification
                    notification = document.createElement('div');
                    notification.id = 'calendar-update-notification';
                    notification.className = 'fixed top-4 right-4 z-50 bg-primary text-white px-4 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-slide-in';
                    notification.style.cssText = 'animation: slideInRight 0.3s ease-out;';
                    
                    notification.innerHTML = `
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span class="text-sm font-medium">Calendrier mis à jour</span>
                        <button onclick="this.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;
                    
                    document.body.appendChild(notification);
                    
                    // Supprimer automatiquement après 3 secondes
                    setTimeout(() => {
                        if (notification && notification.parentNode) {
                            notification.style.animation = 'slideOutRight 0.3s ease-out';
                            setTimeout(() => {
                                if (notification && notification.parentNode) {
                                    notification.remove();
                                }
                            }, 300);
                        }
                    }, 3000);
                } else {
                    // Réinitialiser le timer si la notification existe déjà
                    clearTimeout(notification._timeout);
                    notification._timeout = setTimeout(() => {
                        if (notification && notification.parentNode) {
                            notification.style.animation = 'slideOutRight 0.3s ease-out';
                            setTimeout(() => {
                                if (notification && notification.parentNode) {
                                    notification.remove();
                                }
                            }, 300);
                        }
                    }, 3000);
                }
            }

            // Démarrer la vérification périodique (toutes les 3 secondes)
            checkInterval = setInterval(checkForUpdates, 3000);
            
            // Vérifier immédiatement au chargement
            setTimeout(checkForUpdates, 1000);

            // Arrêter la vérification quand la page est en arrière-plan (pour économiser les ressources)
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    if (checkInterval) {
                        clearInterval(checkInterval);
                        checkInterval = null;
                    }
                } else {
                    if (!checkInterval) {
                        checkInterval = setInterval(checkForUpdates, 3000);
                        checkForUpdates(); // Vérifier immédiatement quand la page redevient visible
                    }
                }
            });

            // Nettoyer l'intervalle quand la page est déchargée
            window.addEventListener('beforeunload', function() {
                if (checkInterval) {
                    clearInterval(checkInterval);
                }
            });
        });
    </script>
    @endpush
</x-app-layout>

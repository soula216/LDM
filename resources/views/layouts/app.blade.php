<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts - Manrope selon la charte LDM v2 -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
        
        @stack('styles')
    </head>
    <body class="font-sans antialiased" data-user-role="{{ auth()->user()->roles->first()->name ?? '' }}">
        <x-banner />

        <div class="min-h-screen bg-app" x-data="{ sidebarOpen: false }" @keydown.window.escape="sidebarOpen = false">
            @livewire('navigation-menu')

            <div class="app-main lg:pl-[17.5rem] pt-16 lg:pt-0 min-h-screen flex flex-col transition-[padding] duration-300">
                @if (isset($header))
                    <header class="bg-card/80 backdrop-blur-md shadow-sm border-b border-border sticky top-0 z-20 lg:top-0">
                        <div class="max-w-7xl mx-auto py-4 sm:py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @stack('modals')

        <!-- Toast Container pour les notifications globales -->
        @auth
        @can('view_commandes')
        <div id="toast-container" class="fixed top-4 right-4 z-[9999] space-y-3" style="max-width: 400px; pointer-events: none; position: fixed !important; top: 1rem !important; right: 1rem !important; z-index: 9999 !important;"></div>
        @endcan
        @endauth

        @livewireScripts
        
        @stack('scripts')

        @auth
        @can('view_commandes')
        <script>
            // Système de notifications toast global pour les nouvelles commandes
            document.addEventListener('DOMContentLoaded', function() {
                let lastCommandeId = null;
                let lastCheckTime = null;
                let isChecking = false;
                let checkInterval = null;
                // Système de déduplication : garder en mémoire les commandes déjà notifiées
                // Format: { commandeId: 'updated_at_timestamp' }
                let notifiedCommandes = new Map();
                // ID de l'utilisateur actuel (pour exclure ses propres modifications)
                const currentUserId = {{ auth()->id() ?? 'null' }};
                // Rôles de l'utilisateur actuel (pour les notifications de commandes terminées)
                const currentUserRoles = @json(auth()->user()->getRoleNames()->toArray() ?? []);
                const isAdminOrResponsable = currentUserRoles.includes('admin') || currentUserRoles.includes('responsable');
                
                // Variable globale pour le contexte audio (réutilisable)
                let audioContextInstance = null;
                let audioContextReady = false;
                
                // Fonction pour obtenir ou créer le contexte audio
                function getAudioContext() {
                    if (!audioContextInstance) {
                        try {
                            audioContextInstance = new (window.AudioContext || window.webkitAudioContext)();
                        } catch (e) {
                            console.error('❌ Impossible de créer le contexte audio:', e);
                            return null;
                        }
                    }
                    return audioContextInstance;
                }
                
                // Fonction pour activer le contexte audio (nécessaire après une interaction utilisateur)
                function enableAudioContext() {
                    const audioContext = getAudioContext();
                    if (!audioContext) return Promise.resolve();
                    
                    if (audioContext.state === 'suspended') {
                        return audioContext.resume().then(() => {
                            audioContextReady = true;
                        }).catch(err => {
                            console.error('❌ Erreur lors de l\'activation du contexte audio:', err);
                        });
                    } else {
                        audioContextReady = true;
                        return Promise.resolve();
                    }
                }
                
                // Activer le contexte audio lors de la première interaction utilisateur
                // Les navigateurs modernes bloquent l'audio automatique jusqu'à une interaction
                function initAudioOnInteraction() {
                    enableAudioContext().then(() => {
                    });
                }
                
                // Écouter plusieurs types d'événements pour activer l'audio
                ['click', 'touchstart', 'keydown', 'mousedown'].forEach(eventType => {
                    document.addEventListener(eventType, initAudioOnInteraction, { once: true, passive: true });
                });
                
                // Aussi activer lors du chargement si possible
                document.addEventListener('DOMContentLoaded', function() {
                    // Essayer d'activer immédiatement (peut échouer, mais on essaie)
                    enableAudioContext().catch(() => {
                    });
                });
                
                // Fonction pour jouer un son de notification (mélodie agréable)
                function playNotificationSound() {
                    try {
                        const audioContext = getAudioContext();
                        if (!audioContext) {
                            return;
                        }
                        
                        // S'assurer que le contexte est actif
                        if (audioContext.state === 'suspended') {
                            audioContext.resume().then(() => {
                                playSoundNow(audioContext);
                            }).catch(err => {
                                console.error('❌ Erreur lors de la reprise du contexte audio:', err);
                            });
                            return;
                        }
                        
                        playSoundNow(audioContext);
                    } catch (error) {
                        console.error('❌ Impossible de jouer le son de notification:', error);
                    }
                }
                
                // Fonction helper pour jouer le son une fois le contexte prêt
                function playSoundNow(audioContext) {
                    try {
                        const now = audioContext.currentTime;
                        
                        // Mélodie de notification : trois notes ascendantes (Do-Mi-Sol)
                        const notes = [
                            { freq: 523.25, time: 0, duration: 0.15 },    // Do (C5)
                            { freq: 659.25, time: 0.1, duration: 0.15 },  // Mi (E5)
                            { freq: 783.99, time: 0.2, duration: 0.2 }    // Sol (G5)
                        ];
                        
                        notes.forEach((note, index) => {
                            const oscillator = audioContext.createOscillator();
                            const gainNode = audioContext.createGain();
                            
                            oscillator.connect(gainNode);
                            gainNode.connect(audioContext.destination);
                            
                            // Configurer la note
                            oscillator.frequency.value = note.freq;
                            oscillator.type = 'sine'; // Son doux et agréable
                            
                            // Volume avec fade in/out pour un son plus doux
                            const startTime = now + note.time;
                            const endTime = startTime + note.duration;
                            
                            gainNode.gain.setValueAtTime(0, startTime);
                            gainNode.gain.linearRampToValueAtTime(0.3, startTime + 0.02); // Fade in rapide (volume augmenté)
                            gainNode.gain.linearRampToValueAtTime(0.2, endTime - 0.05); // Fade out doux
                            gainNode.gain.linearRampToValueAtTime(0, endTime);
                            
                            // Jouer la note
                            oscillator.start(startTime);
                            oscillator.stop(endTime);
                        });
                    } catch (error) {
                        console.error('❌ Erreur lors de la lecture du son:', error);
                    }
                }
                
                // Intercepter les soumissions de formulaires de modification de commandes
                // pour stocker dans sessionStorage les commandes modifiées par l'utilisateur
                document.addEventListener('submit', function(e) {
                    const form = e.target;
                    if (form && form.action) {
                        // Vérifier si c'est un formulaire de modification de commande
                        const updateMatch = form.action.match(/\/admin\/commandes\/(\d+)/);
                        if (updateMatch && form.method && form.method.toUpperCase() === 'PATCH') {
                            const commandeId = updateMatch[1];
                            
                            // Stocker dans sessionStorage
                            const userModifiedKey = 'user_modified_commandes_' + currentUserId;
                            const userModifiedCommandes = JSON.parse(sessionStorage.getItem(userModifiedKey) || '[]');
                            
                            // Ajouter cette commande avec le timestamp actuel
                            userModifiedCommandes.push({
                                commande_id: parseInt(commandeId),
                                updated_at: new Date().toISOString(),
                                timestamp: Date.now()
                            });
                            
                            // Nettoyer les anciennes entrées (plus de 60 secondes)
                            const now = Date.now();
                            const cleaned = userModifiedCommandes.filter(item => {
                                return (now - item.timestamp) < 60000; // 60 secondes
                            });
                            
                            // Garder seulement les 20 dernières
                            const final = cleaned.slice(-20);
                            
                            sessionStorage.setItem(userModifiedKey, JSON.stringify(final));
                        }
                    }
                });

                // Fonction pour créer et afficher une toast
                function showToast(message, type = 'info', link = null, customTitle = null) {
                    // Jouer le son de notification
                    playNotificationSound();
                    
                    const container = document.getElementById('toast-container');
                    if (!container) {
                        return;
                    }

                    const toast = document.createElement('div');
                    const borderColor = type === 'success' ? 'border-green-500' : 'border-primary';
                    toast.className = `bg-white rounded-lg shadow-lg border-l-4 p-4 flex items-start gap-3 ${borderColor}`;
                    // Styles inline pour forcer l'affichage
                    toast.style.cssText = `
                        animation: slideInRight 0.3s ease-out;
                        min-width: 300px;
                        max-width: 400px;
                        pointer-events: auto;
                        position: relative;
                        z-index: 10000;
                        display: flex !important;
                        visibility: visible !important;
                        opacity: 1 !important;
                        background-color: white !important;
                        margin-bottom: 0.75rem;
                    `;

                    const iconColor = type === 'success' ? 'text-green-500' : 'text-primary';
                    // Utiliser le titre personnalisé si fourni, sinon utiliser le titre par défaut
                    const title = customTitle || (type === 'success' ? 'Nouvelle commande' : 'Commande modifiée');
                    const iconSvg = type === 'success' 
                        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>';

                    toast.innerHTML = `
                        <div class="flex-shrink-0 ${iconColor}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                ${iconSvg}
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-primary mb-1">${title}</p>
                            <p class="text-sm text-secondary">${message}</p>
                            ${link ? `<a href="${link}" class="text-sm text-primary hover:text-primary/80 font-medium mt-2 inline-block">Voir la commande →</a>` : ''}
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="flex-shrink-0 text-secondary hover:text-primary transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;

                    // S'assurer que le container est visible
                    container.style.display = 'block';
                    container.style.visibility = 'visible';
                    container.style.opacity = '1';
                    
                    container.appendChild(toast);

                    // Supprimer automatiquement après 5 secondes
                    setTimeout(() => {
                        if (toast && toast.parentNode) {
                            toast.style.animation = 'slideOutRight 0.3s ease-out';
                            setTimeout(() => {
                                if (toast && toast.parentNode) {
                                    toast.remove();
                                }
                            }, 300);
                        }
                    }, 5000);
                }

                // Fonction pour vérifier les nouvelles commandes
                async function checkForNewCommandes() {
                    if (isChecking) {
                        return;
                    }
                    isChecking = true;

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]');
                        if (!csrfToken) {
                            console.error('Token CSRF non trouvé');
                            isChecking = false;
                            return;
                        }

                        const baseUrl = '{{ route("app.commandes.check-new") }}';
                        const params = new URLSearchParams();
                        if (lastCommandeId !== null) {
                            params.append('last_id', lastCommandeId);
                        }
                        if (lastCheckTime !== null) {
                            params.append('last_check_time', lastCheckTime);
                        }
                        const url = params.toString() ? `${baseUrl}?${params.toString()}` : baseUrl;
                        
                        const response = await fetch(url, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken.content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            credentials: 'same-origin'
                        });

                        if (!response.ok) {
                            const errorText = await response.text();
                            console.error('Erreur HTTP:', response.status, errorText);
                            throw new Error('Erreur HTTP: ' + response.status);
                        }

                        const data = await response.json();
                        
                        // Si c'est la première vérification, enregistrer l'ID et le timestamp
                        if (lastCommandeId === null) {
                            lastCommandeId = data.last_commande_id || 0;
                            lastCheckTime = data.last_check_time || new Date().toISOString();
                            isChecking = false;
                            return;
                        }

                        // Traiter les nouvelles commandes créées
                        if (data.has_new && data.new_commandes && data.new_commandes.length > 0) {
                            // Afficher une toast pour chaque nouvelle commande (avec déduplication)
                            data.new_commandes.forEach(commande => {
                                // Créer une clé unique : commande_id + created_at
                                const notificationKey = `new_${commande.id}_${commande.created_at}`;
                                
                                // Vérifier si cette commande a déjà été notifiée
                                if (notifiedCommandes.has(notificationKey)) {
                                    return;
                                }
                                
                                // Marquer comme notifiée
                                notifiedCommandes.set(notificationKey, Date.now());
                                
                                const numCmd = commande.num_cmd || ('#' + commande.id);
                                const message = `Commande ${numCmd} créée${commande.nom_patient ? ' pour ' + commande.nom_patient : ''}`;
                                const link = '{{ route("admin.commandes.show", ":id") }}'.replace(':id', commande.id);
                                showToast(message, 'success', link);
                            });
                            
                            lastCommandeId = data.last_commande_id;
                        }

                        // Traiter les commandes modifiées
                        if (data.has_updates && data.updated_commandes && data.updated_commandes.length > 0) {
                            // Stocker les commandes modifiées récemment par l'utilisateur actuel (dans sessionStorage)
                            // pour éviter les notifications même si le serveur ne les filtre pas
                            const userModifiedKey = 'user_modified_commandes_' + currentUserId;
                            const userModifiedCommandes = JSON.parse(sessionStorage.getItem(userModifiedKey) || '[]');
                            const now = Date.now();
                            
                            // Nettoyer les anciennes entrées (plus de 60 secondes)
                            const cleanedUserModified = userModifiedCommandes.filter(item => {
                                return (now - item.timestamp) < 60000; // 60 secondes
                            });
                            
                            // Afficher une toast pour chaque commande modifiée (avec déduplication)
                            data.updated_commandes.forEach(commande => {
                                // Créer une clé unique : commande_id + updated_at
                                const notificationKey = `${commande.id}_${commande.updated_at}`;
                                
                                // Vérifier si cette modification a déjà été notifiée
                                if (notifiedCommandes.has(notificationKey)) {
                                    return;
                                }
                                
                                // Vérifier si cette commande a été modifiée par l'utilisateur actuel récemment
                                const userModified = cleanedUserModified.find(item => {
                                    return item.commande_id === commande.id && 
                                           Math.abs(new Date(item.updated_at) - new Date(commande.updated_at)) < 5000; // 5 secondes de tolérance
                                });
                                
                                if (userModified) {
                                    return;
                                }
                                
                                // Marquer comme notifiée
                                notifiedCommandes.set(notificationKey, Date.now());
                                
                                // Nettoyer les anciennes entrées (plus de 60 secondes)
                                const now2 = Date.now();
                                for (const [key, timestamp] of notifiedCommandes.entries()) {
                                    if (now2 - timestamp > 60000) { // 60 secondes
                                        notifiedCommandes.delete(key);
                                    }
                                }
                                
                                const numCmd = commande.num_cmd || ('#' + commande.id);
                                const statusText = commande.status ? ` (Statut: ${commande.status})` : '';
                                const message = `Commande ${numCmd} modifiée${commande.nom_patient ? ' - ' + commande.nom_patient : ''}${statusText}`;
                                const link = '{{ route("admin.commandes.show", ":id") }}'.replace(':id', commande.id);
                                showToast(message, 'info', link);
                            });
                            
                            // Sauvegarder les commandes modifiées par l'utilisateur
                            sessionStorage.setItem(userModifiedKey, JSON.stringify(cleanedUserModified));
                        }
                        
                        // Traiter les commandes terminées (notifications pour admin et responsable uniquement)
                        if (isAdminOrResponsable && data.has_finished && data.finished_commandes && data.finished_commandes.length > 0) {
                            data.finished_commandes.forEach(commande => {
                                // Créer une clé unique : commande_id + finished_by + updated_at
                                const notificationKey = `finished_${commande.id}_${commande.finished_by.id}_${commande.updated_at}`;
                                
                                // Vérifier si cette notification a déjà été affichée
                                if (notifiedCommandes.has(notificationKey)) {
                                    return;
                                }
                                
                                // Marquer comme notifiée
                                notifiedCommandes.set(notificationKey, Date.now());
                                
                                // Nettoyer les anciennes entrées (plus de 60 secondes)
                                const now3 = Date.now();
                                for (const [key, timestamp] of notifiedCommandes.entries()) {
                                    if (now3 - timestamp > 60000) { // 60 secondes
                                        notifiedCommandes.delete(key);
                                    }
                                }
                                
                                const numCmd = commande.num_cmd || ('#' + commande.id);
                                const userName = commande.finished_by.name || 'Utilisateur';
                                const message = `${userName} a terminé la commande ${numCmd}`;
                                const link = '{{ route("admin.commandes.show", ":id") }}'.replace(':id', commande.id);
                                showToast(message, 'success', link, 'Commande Terminée');
                            });
                        }

                        // Mettre à jour les timestamps
                        if (data.last_commande_id && data.last_commande_id > lastCommandeId) {
                            lastCommandeId = data.last_commande_id;
                        }
                        if (data.last_check_time) {
                            lastCheckTime = data.last_check_time;
                        }
                    } catch (error) {
                        console.error('❌ Erreur lors de la vérification des nouvelles commandes:', error);
                    } finally {
                        isChecking = false;
                    }
                }

                // Démarrer la vérification périodique (toutes les 3 secondes pour un temps réel plus rapide)
                checkInterval = setInterval(checkForNewCommandes, 3000);
                
                // Vérifier immédiatement au chargement (après un délai pour éviter les notifications au chargement initial)
                setTimeout(checkForNewCommandes, 3000);

                // Arrêter la vérification quand la page est en arrière-plan
                document.addEventListener('visibilitychange', function() {
                    if (document.hidden) {
                        if (checkInterval) {
                            clearInterval(checkInterval);
                            checkInterval = null;
                        }
                    } else {
                        if (!checkInterval) {
                            checkInterval = setInterval(checkForNewCommandes, 5000);
                            checkForNewCommandes(); // Vérifier immédiatement quand la page redevient visible
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
        <style>
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

            .animate-slide-in-right {
                animation: slideInRight 0.3s ease-out;
            }
        </style>
        @endcan
        @endauth
    </body>
</html>

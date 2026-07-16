<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-primary rounded-lg">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h2 class="text-xl sm:text-2xl font-semibold text-primary">{{ __('Candidatures d\'offres d\'emploi') }}</h2>
            </div>
            <div class="px-4 py-2 bg-primary/10 border border-primary/20 rounded-xl">
                <p class="text-xs text-secondary uppercase tracking-wide font-medium">Total</p>
                <p class="text-lg font-bold text-primary">{{ $applications->total() }}</p>
            </div>
        </div>
    </x-slot>

    <div
        x-data="{
            showDeleteModal: false,
            showViewModal: false,
            deleteFormAction: '',
            selectedApplication: null,
            openApplication(application) {
                this.selectedApplication = application;
                this.showViewModal = true;
            },
            closeViewModal() {
                this.showViewModal = false;
                this.selectedApplication = null;
            },
            confirmDeleteFromView() {
                if (!this.selectedApplication) return;
                this.deleteFormAction = this.selectedApplication.delete_url;
                this.showViewModal = false;
                this.showDeleteModal = true;
            }
        }"
        x-cloak
        class="py-4 sm:py-8 bg-app min-h-screen"
        @keydown.escape.window="showViewModal ? closeViewModal() : (showDeleteModal = false)"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-accent-secondary/10 border-l-4 border-accent-secondary rounded-lg">
                    <span class="text-accent-secondary font-medium text-sm sm:text-base">{{ session('success') }}</span>
                </div>
            @endif

            <div class="card mb-6">
                <form method="GET" action="{{ route('admin.job-applications.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <input
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Rechercher par offre, nom, email, téléphone…"
                        class="input-field flex-1"
                    >
                    <button type="submit" class="btn-primary whitespace-nowrap">Rechercher</button>
                    @if(request('search'))
                        <a href="{{ route('admin.job-applications.index') }}" class="btn-secondary whitespace-nowrap text-center">Réinitialiser</a>
                    @endif
                </form>
            </div>

            <div class="card">
                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <table class="min-w-full divide-y divide-border">
                        <thead class="bg-neutral-100">
                            <tr>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Date</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Offre</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Candidat</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider hidden sm:table-cell">Email</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider hidden md:table-cell">Téléphone</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider hidden lg:table-cell">CV</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-card divide-y divide-border">
                            @forelse($applications as $application)
                                @php
                                    $applicationPayload = [
                                        'id' => $application->id,
                                        'job_title' => $application->job_title,
                                        'name' => $application->name,
                                        'email' => $application->email,
                                        'phone' => $application->phone,
                                        'cover_letter' => $application->cover_letter,
                                        'created_at' => $application->created_at->format('d/m/Y à H:i'),
                                        'has_cv' => $application->hasCv(),
                                        'cv_name' => $application->cv_name,
                                        'cv_url' => $application->hasCv()
                                            ? route('admin.job-applications.cv', $application)
                                            : null,
                                        'delete_url' => route('admin.job-applications.destroy', $application),
                                    ];
                                @endphp
                                <tr class="hover:bg-neutral-100/50 transition-colors">
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm text-secondary">
                                        {{ $application->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-3 sm:px-6 py-4">
                                        <div class="text-sm font-medium text-primary max-w-[220px]">{{ $application->job_title }}</div>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4">
                                        <div class="text-sm font-medium text-primary">{{ $application->name }}</div>
                                        <div class="text-xs text-secondary sm:hidden mt-1">{{ $application->email }}</div>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                        <a href="mailto:{{ $application->email }}" class="text-sm text-primary hover:underline">{{ $application->email }}</a>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden md:table-cell text-sm text-secondary">
                                        {{ $application->phone ?: '—' }}
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                                        @if($application->hasCv())
                                            <a href="{{ route('admin.job-applications.cv', $application) }}"
                                               class="inline-flex items-center gap-1.5 text-sm text-primary hover:underline max-w-[180px] truncate"
                                               title="{{ $application->cv_name }}">
                                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                </svg>
                                                <span class="truncate">{{ $application->cv_name }}</span>
                                            </a>
                                        @else
                                            <span class="text-sm text-secondary">—</span>
                                        @endif
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex items-center gap-2">
                                            <button
                                                type="button"
                                                @click="openApplication(@js($applicationPayload))"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-primary hover:bg-primary/10 border border-transparent hover:border-primary/20 transition-colors"
                                                title="Voir la candidature"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                <span class="hidden lg:inline text-xs font-medium">Voir</span>
                                            </button>
                                            <button
                                                type="button"
                                                @click="deleteFormAction = '{{ route('admin.job-applications.destroy', $application) }}'; showDeleteModal = true"
                                                class="inline-flex items-center p-1.5 rounded-lg text-danger hover:bg-danger/10 transition-colors"
                                                title="Supprimer"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-secondary">
                                        Aucune candidature reçue pour le moment.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($applications->hasPages())
                    <div class="mt-6 px-4 sm:px-0">
                        {{ $applications->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Modal lecture candidature --}}
        <div
            x-show="showViewModal"
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
            style="display: none;"
            role="dialog"
            aria-modal="true"
            aria-labelledby="job-application-view-title"
        >
            <div
                class="bg-card rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col border border-border"
                @click.outside="closeViewModal()"
            >
                <div class="flex items-start justify-between gap-4 px-5 sm:px-6 py-4 border-b border-border">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wide text-secondary mb-1">Candidature</p>
                        <h3 id="job-application-view-title" class="text-lg font-semibold text-primary truncate" x-text="selectedApplication?.name || ''"></h3>
                        <p class="text-sm text-secondary mt-1" x-text="selectedApplication?.job_title || ''"></p>
                        <p class="text-xs text-secondary mt-1" x-text="selectedApplication?.created_at || ''"></p>
                    </div>
                    <button type="button" @click="closeViewModal()" class="p-2 rounded-lg text-secondary hover:text-primary hover:bg-neutral-100 transition-colors" aria-label="Fermer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="px-5 sm:px-6 py-4 space-y-4 overflow-y-auto">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="rounded-xl border border-border bg-neutral-50/80 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-secondary mb-1">Email</p>
                            <a :href="selectedApplication ? 'mailto:' + selectedApplication.email : '#'" class="text-sm text-primary hover:underline break-all" x-text="selectedApplication?.email || ''"></a>
                        </div>
                        <div class="rounded-xl border border-border bg-neutral-50/80 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-secondary mb-1">Téléphone</p>
                            <p class="text-sm text-primary" x-text="selectedApplication?.phone || '—'"></p>
                        </div>
                    </div>

                    <div class="rounded-xl border border-border bg-neutral-50/80 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-secondary mb-2">CV</p>
                        <template x-if="selectedApplication?.has_cv">
                            <a :href="selectedApplication.cv_url" class="inline-flex items-center gap-2 text-sm font-medium text-primary hover:underline">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                <span x-text="selectedApplication.cv_name || 'Télécharger le CV'"></span>
                            </a>
                        </template>
                        <template x-if="!selectedApplication?.has_cv">
                            <p class="text-sm text-secondary">Aucun CV</p>
                        </template>
                    </div>

                    <div class="rounded-xl border border-border px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-secondary mb-2">Lettre de motivation</p>
                        <p class="text-sm text-primary whitespace-pre-wrap" x-text="selectedApplication?.cover_letter || '—'"></p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 px-5 sm:px-6 py-4 border-t border-border">
                    <button type="button" @click="confirmDeleteFromView()" class="btn-secondary text-danger border-danger/30 hover:bg-danger/10">Supprimer</button>
                    <button type="button" @click="closeViewModal()" class="btn-primary">Fermer</button>
                </div>
            </div>
        </div>

        {{-- Modal suppression --}}
        <div
            x-show="showDeleteModal"
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
            style="display: none;"
            role="dialog"
            aria-modal="true"
        >
            <div class="bg-card rounded-2xl shadow-xl w-full max-w-md border border-border p-6" @click.outside="showDeleteModal = false">
                <h3 class="text-lg font-semibold text-primary mb-2">Supprimer la candidature ?</h3>
                <p class="text-sm text-secondary mb-6">Cette action est définitive. Le CV associé sera également supprimé.</p>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="showDeleteModal = false" class="btn-secondary">Annuler</button>
                    <form method="POST" :action="deleteFormAction">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-primary bg-danger hover:bg-danger/90 shadow-danger/20">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

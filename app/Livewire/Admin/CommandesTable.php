<?php

namespace App\Livewire\Admin;

use App\Models\Commande;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Cache;

class CommandesTable extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $urgentFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingUrgentFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();
        $cacheKey = "admin.commandes.table.{$this->search}.{$this->statusFilter}.{$this->urgentFilter}.{$user->id}";

        $commandes = Cache::remember($cacheKey, 120, function () use ($user) {
            $query = Commande::with(['dentiste', 'taches']);

            // Filtrer par groupe si l'utilisateur est un employé
            if ($user->hasRole('employer')) {
                $query->whereHas('taches.service', function ($q) use ($user) {
                    $q->where('groupe_id', $user->groupe_id);
                });
            } elseif ($user->hasRole('dentist')) {
                // Filtrer par dentiste si l'utilisateur est un dentiste
                $query->where('dentiste_id', $user->id);
            }
            // admin, responsable, secretaire, prothesiste voient toutes les commandes

            $query->latest();

            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('num_cmd', 'like', "%{$this->search}%")
                        ->orWhere('nom_patient', 'like', "%{$this->search}%")
                        ->orWhereHas('dentiste', function ($q) {
                            $q->where('nom', 'like', "%{$this->search}%")
                                ->orWhere('prénom', 'like', "%{$this->search}%");
                        });
                });
            }

            if ($this->statusFilter) {
                $query->where('status', $this->statusFilter);
            }

            if ($this->urgentFilter !== '') {
                $query->where('urgent', $this->urgentFilter === '1');
            }

            return $query->paginate(25);
        });

        return view('livewire.admin.commandes-table', [
            'commandes' => $commandes
        ]);
    }
}

<?php

namespace App\Exports;

use App\Models\CommandeTache;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class TachesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $date;
    protected $user;

    public function __construct($date, $user)
    {
        $this->date = $date;
        $this->user = $user;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Convertir la date en Carbon
        $dateCarbon = Carbon::parse($this->date)->startOfDay();
        $endDate = $dateCarbon->copy()->endOfDay();

        // Construire la requête pour récupérer les tâches du jour
        $query = CommandeTache::with([
            'commande.dentiste',
            'commande.createdBy',
            'service.groupe',
            'groupe',
        ])
        ->whereDate('date_livraison', '>=', $dateCarbon)
        ->whereDate('date_livraison', '<=', $endDate);

        // Appliquer les filtres selon les permissions de l'utilisateur
        if ($this->user->hasRole('employer')) {
            $groupeId = $this->user->groupe_id;
            $query->where(function ($q) use ($groupeId) {
                $q->where('groupe_id', $groupeId)
                    ->orWhereHas('service', fn ($q2) => $q2->where('groupe_id', $groupeId));
            });
        } elseif ($this->user->hasRole('dentist')) {
            $query->whereHas('commande', function ($q) {
                $q->where('dentiste_id', $this->user->id);
            });
        }

        return $query
            ->orderByRaw('calendar_sort_order IS NULL')
            ->orderBy('calendar_sort_order')
            ->orderBy('id')
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'N° Commande',
            'Date & Heure Livraison',
            'Patient',
            'Dentiste',
            'Statut Commande',
            'Urgent',
            'Service',
            'Groupe',
            'Nombre d\'éléments',
            'Teinte',
            'Commentaire',
            'Créé par',
        ];
    }

    /**
     * @param mixed $tache
     * @return array
     */
    public function map($tache): array
    {
        $dateLivraison = $tache->date_livraison 
            ? Carbon::parse($tache->date_livraison)->format('d/m/Y H:i')
            : '-';

        return [
            $tache->commande->num_cmd ?? '-',
            $dateLivraison,
            $tache->commande->nom_patient ?? '-',
            $tache->commande->dentiste->full_name ?? $tache->commande->dentiste->name ?? '-',
            $tache->commande->status ?? '-',
            $tache->commande->urgent ? 'Oui' : 'Non',
            $tache->service?->nom ?? $tache->custom_service ?? '-',
            $tache->groupe?->nom ?? $tache->service?->groupe?->nom ?? '-',
            $tache->nb_elem ?? '-',
            $tache->teinte ?? '-',
            $tache->commande->commentaire ?? '-',
            $tache->commande->createdBy->full_name ?? $tache->commande->createdBy->name ?? '-',
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E5E7EB']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}

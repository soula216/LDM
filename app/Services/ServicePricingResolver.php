<?php

namespace App\Services;

use App\Models\DentistServicePrice;
use App\Models\Service;
use Illuminate\Support\Facades\Cache;

class ServicePricingResolver
{
    /**
     * Résout le prix TTC d'un service pour un dentiste donné
     * Priorité : override dentiste > prix défaut service
     */
    public function resolvePriceTtc(int $dentistId, int $serviceId): float
    {
        $cacheKey = "pricing.dentist_service.{$dentistId}.{$serviceId}";

        return Cache::remember($cacheKey, 600, function () use ($dentistId, $serviceId) {
            // Chercher override dentiste
            $override = DentistServicePrice::where('dentist_id', $dentistId)
                ->where('service_id', $serviceId)
                ->first();

            if ($override) {
                return (float) $override->prix_unitaire_ttc;
            }

            // Retourner prix défaut service
            $service = Service::find($serviceId);
            return (float) ($service->prix_unitaire_ttc ?? 0);
        });
    }
}

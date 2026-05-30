<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Groupe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ServicePricingController extends Controller
{
    private const SERVICES_PER_PAGE = 20;

    public function index(Request $request)
    {
        $services = Service::with('groupe')
            ->orderBy('nom')
            ->paginate(self::SERVICES_PER_PAGE);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.services.partials.rows', compact('services'))->render(),
                'has_more' => $services->hasMorePages(),
            ]);
        }

        $groupes = Groupe::all();

        return view('admin.services.index', compact('services', 'groupes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:services,nom',
            'prix_unitaire_ttc' => 'required|numeric|min:0',
            'groupe_id' => 'nullable|exists:groupes,id',
        ]);

        Service::create([
            'nom' => $validated['nom'],
            'prix_unitaire_ttc' => $validated['prix_unitaire_ttc'],
            'groupe_id' => $validated['groupe_id'] ?? null,
        ]);

        // Nettoyer le cache
        Cache::flush();

        return redirect()->back()->with('success', 'Service créé avec succès');
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:services,nom,' . $service->id,
            'prix_unitaire_ttc' => 'required|numeric|min:0',
            'groupe_id' => 'nullable|exists:groupes,id',
        ]);

        $service->update([
            'nom' => $validated['nom'],
            'prix_unitaire_ttc' => $validated['prix_unitaire_ttc'],
            'groupe_id' => $validated['groupe_id'] ?? null,
        ]);

        // Nettoyer le cache de pricing pour tous les dentistes qui utilisent ce service
        Cache::flush(); // On peut optimiser en ne nettoyant que les caches concernés

        return redirect()->back()->with('success', 'Service mis à jour avec succès');
    }

    public function destroy(Service $service)
    {
        // Vérifier si le service est utilisé dans des commandes
        if ($service->taches()->exists()) {
            return redirect()->back()->with('error', 'Impossible de supprimer ce service car il est utilisé dans des commandes.');
        }

        $service->delete();

        // Nettoyer le cache
        Cache::flush();

        return redirect()->back()->with('success', 'Service supprimé avec succès');
    }
}

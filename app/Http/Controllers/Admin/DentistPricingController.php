<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DentistServicePrice;
use App\Models\User;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DentistPricingController extends Controller
{
    public function index(Request $request)
    {
        $dentists = User::role('dentist')->get();
        $services = Service::all();
        
        $query = DentistServicePrice::with(['dentist', 'service'])
            ->orderBy('id', 'desc');
        
        // Filtrer par service si spécifié dans l'URL
        if ($request->has('service_id') && $request->service_id) {
            $query->where('service_id', $request->service_id);
        }
        
        $prices = $query->paginate(10)->withQueryString();

        return view('admin.pricing.dentists.index', compact('dentists', 'services', 'prices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'dentist_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'prix_unitaire_ttc' => 'required|numeric|min:0',
        ]);

        DentistServicePrice::updateOrCreate(
            ['dentist_id' => $validated['dentist_id'], 'service_id' => $validated['service_id']],
            ['prix_unitaire_ttc' => $validated['prix_unitaire_ttc']]
        );

        Cache::forget("pricing.dentist_service.{$validated['dentist_id']}.{$validated['service_id']}");

        return redirect()->back()->with('success', 'Prix sauvegardé');
    }

    public function destroy(DentistServicePrice $row)
    {
        $dentistId = $row->dentist_id;
        $serviceId = $row->service_id;

        $row->delete();

        Cache::forget("pricing.dentist_service.{$dentistId}.{$serviceId}");

        return redirect()->back()->with('success', 'Prix supprimé');
    }
}

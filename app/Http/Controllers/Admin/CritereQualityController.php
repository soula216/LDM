<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CritereQuality;
use Illuminate\Http\Request;

class CritereQualityController extends Controller
{
    public function store(Request $request)
    {
        $this->authorize('manage_config');

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'groupe_id' => 'required|exists:groupes,id',
            'type' => 'required|in:Empreinte,Contrôle visuel,Occlusion,Livraison,Marque des Matériaux',
        ]);

        CritereQuality::create($validated);

        return redirect()->route('admin.config.index')->with('success', 'Critère de qualité créé avec succès');
    }

    public function update(Request $request, CritereQuality $critereQuality)
    {
        $this->authorize('manage_config');

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'groupe_id' => 'required|exists:groupes,id',
            'type' => 'required|in:Empreinte,Contrôle visuel,Occlusion,Livraison,Marque des Matériaux',
        ]);

        $critereQuality->update($validated);

        return redirect()->route('admin.config.index')->with('success', 'Critère de qualité mis à jour avec succès');
    }

    public function destroy(CritereQuality $critereQuality)
    {
        $this->authorize('manage_config');

        $critereQuality->delete();

        return redirect()->route('admin.config.index')->with('success', 'Critère de qualité supprimé avec succès');
    }
}
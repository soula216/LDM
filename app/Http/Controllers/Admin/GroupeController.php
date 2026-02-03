<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Groupe;
use Illuminate\Http\Request;

class GroupeController extends Controller
{
    public function store(Request $request)
    {
        $this->authorize('manage_config');

        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:groupes,nom',
            'description' => 'nullable|string',
        ]);

        Groupe::create($validated);

        return redirect()->route('admin.config.index')->with('success', 'Groupe créé avec succès');
    }

    public function update(Request $request, Groupe $groupe)
    {
        $this->authorize('manage_config');

        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:groupes,nom,' . $groupe->id,
            'description' => 'nullable|string',
        ]);

        $groupe->update($validated);

        return redirect()->route('admin.config.index')->with('success', 'Groupe mis à jour avec succès');
    }

    public function destroy(Groupe $groupe)
    {
        $this->authorize('manage_config');

        // Vérifier si le groupe est utilisé
        if ($groupe->users()->exists() || $groupe->services()->exists()) {
            return redirect()->route('admin.config.index')->with('error', 'Impossible de supprimer ce groupe car il est utilisé.');
        }

        $groupe->delete();

        return redirect()->route('admin.config.index')->with('success', 'Groupe supprimé avec succès');
    }
}
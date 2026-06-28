<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Depense;
use Illuminate\Http\Request;

class DepenseController extends Controller
{
    private const DEPENSES_PER_PAGE = 20;

    private function buildDepensesQuery(Request $request)
    {
        $query = Depense::query();

        if ($request->filled('nom')) {
            $nom = trim($request->input('nom'));
            $query->where('nom', 'like', '%' . $nom . '%');
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->input('date_debut'));
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->input('date_fin'));
        }

        return $query;
    }

    private function ensureAdmin(): void
    {
        if (! auth()->user()?->hasRole('admin')) {
            abort(404);
        }
    }

    public function index(Request $request)
    {
        $this->ensureAdmin();

        $baseQuery = $this->buildDepensesQuery($request);

        $depenses = (clone $baseQuery)
            ->orderByDesc('id')
            ->paginate(self::DEPENSES_PER_PAGE)
            ->withQueryString();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'html' => view('admin.depenses.partials.rows', compact('depenses'))->render(),
                'has_more' => $depenses->hasMorePages(),
            ]);
        }

        $totalMontant = (clone $baseQuery)->sum('montant');

        return view('admin.depenses.index', compact('depenses', 'totalMontant'));
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'qte' => 'nullable|integer|min:1',
            'date' => 'required|date',
            'montant' => 'required|numeric|min:0',
        ]);

        Depense::create($validated);

        return redirect()->back()->with('success', 'Dépense ajoutée avec succès.');
    }

    public function update(Request $request, Depense $depense)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'qte' => 'nullable|integer|min:1',
            'date' => 'required|date',
            'montant' => 'required|numeric|min:0',
        ]);

        $depense->update($validated);

        return redirect()->back()->with('success', 'Dépense mise à jour avec succès.');
    }

    public function destroy(Depense $depense)
    {
        $this->ensureAdmin();

        $depense->delete();

        return redirect()->back()->with('success', 'Dépense supprimée avec succès.');
    }
}

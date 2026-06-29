<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Element;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StockController extends Controller
{
    private const ELEMENTS_PER_PAGE = 20;
    private const STOCKS_PER_PAGE = 20;

    private function ensureCanView(): void
    {
        $user = auth()->user();
        if (! $user?->can('view_stock') && ! $user?->can('manage_stock')) {
            abort(404);
        }
    }

    private function ensureCanManage(): void
    {
        if (! auth()->user()?->can('manage_stock')) {
            abort(404);
        }
    }

    public function index(Request $request)
    {
        $this->ensureCanView();

        $activeTab = in_array(old('tab', $request->query('tab')), ['elements', 'stock'], true)
            ? old('tab', $request->query('tab'))
            : 'stock';

        $elementsForSelect = Element::query()->orderBy('nom')->get();

        $elements = Element::query()
            ->orderBy('nom')
            ->paginate(self::ELEMENTS_PER_PAGE, ['*'], 'elements_page')
            ->withQueryString();

        $stocks = Stock::with('element')
            ->orderByDesc('id')
            ->paginate(self::STOCKS_PER_PAGE, ['*'], 'stock_page')
            ->withQueryString();

        if ($request->ajax() || $request->wantsJson()) {
            if ($request->query('tab') === 'elements') {
                return response()->json([
                    'html' => view('admin.stock.partials.elements-rows', compact('elements'))->render(),
                    'has_more' => $elements->hasMorePages(),
                ]);
            }

            if ($request->query('tab') === 'stock') {
                return response()->json([
                    'html' => view('admin.stock.partials.stocks-rows', compact('stocks'))->render(),
                    'has_more' => $stocks->hasMorePages(),
                ]);
            }
        }

        return view('admin.stock.index', compact('activeTab', 'elements', 'elementsForSelect', 'stocks'));
    }

    public function storeElement(Request $request)
    {
        $this->ensureCanManage();

        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:elements,nom',
        ]);

        Element::create($validated);

        return redirect()
            ->route('admin.stock.index', ['tab' => 'elements'])
            ->with('success', 'Élément ajouté avec succès.');
    }

    public function updateElement(Request $request, Element $element)
    {
        $this->ensureCanManage();

        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255', Rule::unique('elements', 'nom')->ignore($element->id)],
        ]);

        $element->update($validated);

        return redirect()
            ->route('admin.stock.index', ['tab' => 'elements'])
            ->with('success', 'Élément mis à jour avec succès.');
    }

    public function destroyElement(Element $element)
    {
        $this->ensureCanManage();

        $element->delete();

        return redirect()
            ->route('admin.stock.index', ['tab' => 'elements'])
            ->with('success', 'Élément supprimé avec succès.');
    }

    public function storeStock(Request $request)
    {
        $this->ensureCanManage();

        $validated = $request->validate([
            'element_id' => 'required|exists:elements,id',
            'qte' => 'required|integer|min:1',
        ]);

        $stock = Stock::query()->where('element_id', $validated['element_id'])->first();

        if ($stock) {
            $stock->increment('qte', $validated['qte']);

            return redirect()
                ->route('admin.stock.index', ['tab' => 'stock'])
                ->with('success', 'Quantité ajoutée au stock existant.');
        }

        Stock::create($validated);

        return redirect()
            ->route('admin.stock.index', ['tab' => 'stock'])
            ->with('success', 'Stock ajouté avec succès.');
    }

    public function updateStock(Request $request, Stock $stock)
    {
        $this->ensureCanManage();

        $validated = $request->validate([
            'element_id' => ['required', 'exists:elements,id', Rule::unique('stocks', 'element_id')->ignore($stock->id)],
            'qte' => 'required|integer|min:0',
        ]);

        $stock->update($validated);

        return redirect()
            ->route('admin.stock.index', ['tab' => 'stock'])
            ->with('success', 'Stock mis à jour avec succès.');
    }

    public function destroyStock(Stock $stock)
    {
        $this->ensureCanManage();

        $stock->delete();

        return redirect()
            ->route('admin.stock.index', ['tab' => 'stock'])
            ->with('success', 'Stock supprimé avec succès.');
    }
}

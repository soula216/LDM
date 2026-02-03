<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\BonLivraison;
use Illuminate\Support\Facades\Cache;

class BonLivraisonController extends Controller
{
    public function show(BonLivraison $bl)
    {
        $this->authorize('view_bons_livraison');

        $bl = Cache::remember("bl.commande.{$bl->id}", 300, function () use ($bl) {
            return $bl->load(['commande.dentiste', 'lignes.service']);
        });

        return view('app.bl.show', compact('bl'));
    }

    public function print(BonLivraison $bl)
    {
        $this->authorize('print_bons_livraison');

        $bl = $bl->load(['commande.dentiste', 'lignes.service']);

        return view('app.bl.print', compact('bl'));
    }
}

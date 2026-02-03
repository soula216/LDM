<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Groupe;
use App\Models\CritereQuality;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function index()
    {
        $this->authorize('manage_config');
        
        $groupes = Groupe::orderBy('nom')->get();
        $criteres = CritereQuality::with('groupe')->orderBy('id', 'asc')->get();

        return view('admin.config.index', compact('groupes', 'criteres'));
    }
}
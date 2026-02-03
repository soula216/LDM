<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DashboardController extends Controller
{
    /**
     * User Dashboard - Accessible à tous les users avec affichage selon les permissions
     * GET /dashboard
     */
    public function userDashboard()
    {
        $user = auth()->user();
        
        // Récupérer les stats selon les permissions
        $stats = [];
        
        // Stats disponibles uniquement pour les admins et responsables (permission view_statistics)
        if ($user->can('view_statistics')) {
            $stats = Cache::remember('admin.dashboard.stats', 300, function () {
                return [
                    'total_teams' => User::whereHas('roles', function($query) {
                        $query->whereNotIn('name', ['admin', 'dentist']);
                    })->count(),
                    'total_dentists' => User::role('dentist')->count(),
                    'total_roles' => Role::count(),
                    'total_permissions' => Permission::count(),
                ];
            });
        }

        return view('dashboard', compact('stats'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Cache;

class PermissionController extends Controller
{
    /**
     * GET /admin/permissions
     * Affiche toutes les permissions avec leurs rôles
     */
    public function index()
    {
        $this->authorize('manage_permissions');

        // Cache - 5 minutes
        $permissions = Cache::remember('admin.permissions.index', 300, function () {
            return Permission::with('roles')
                ->get()
                ->groupBy(function ($permission) {
                    // Grouper par préfixe pour organisation
                    $parts = explode('_', $permission->name);
                    return $parts[0] ?? 'other';
                });
        });

        $roles = Role::where('name', '!=', 'admin')->get();

        return view('admin.permissions.index', compact('permissions', 'roles'));
    }
}

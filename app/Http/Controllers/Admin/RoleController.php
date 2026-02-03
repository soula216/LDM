<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RoleController extends Controller
{
    /**
     * GET /admin/roles
     * Affiche rôles SAUF admin
     */
    public function index()
    {
        $this->authorize('view_roles');

        // Cache - 10 minutes
        $roles = Cache::remember('admin.roles.index', 600, function () {
            return Role::query()
                ->where('name', '!=', 'admin') // ❌ Hide admin role
                ->with('permissions')
                ->withCount('users')
                ->get();
        });

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * GET /admin/roles/create
     * Affiche le formulaire de création d'un nouveau rôle
     */
    public function create()
    {
        $this->authorize('manage_permissions');

        $allPermissions = Permission::all()->groupBy(function ($permission) {
            $parts = explode('_', $permission->name);
            return $parts[0] ?? 'other';
        });

        return view('admin.roles.create', compact('allPermissions'));
    }

    /**
     * POST /admin/roles
     * Créer un nouveau rôle
     */
    public function store(Request $request)
    {
        $this->authorize('manage_permissions');

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Empêcher créer un rôle admin
        if (strtolower($validated['name']) === 'admin') {
            return redirect()
                ->route('admin.roles.create')
                ->with('error', 'Impossible de créer un rôle "admin".');
        }

        $role = Role::create(['name' => $validated['name']]);

        if (!empty($validated['permissions'])) {
            $validPermissions = Permission::whereIn('id', $validated['permissions'])
                ->pluck('id')
                ->toArray();
            $role->syncPermissions($validPermissions);
        }

        // Invalidate cache
        Cache::forget('admin.roles.index');
        Cache::forget('admin.permissions.index');
        cache()->forget('spatie.permission.cache');
        cache()->forget('admin.nav.*');

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Rôle créé avec succès.');
    }

    /**
     * GET /admin/roles/{role}
     * Voir détails rôle
     */
    public function show(Role $role)
    {
        $this->authorize('view_roles');

        // Empêcher voir admin role
        if ($role->name === 'admin') {
            abort(404);
        }

        $permissions = $role->permissions;
        $allPermissions = Permission::all()->groupBy(function ($permission) {
            // Grouper par préfixe (view_, create_, etc.)
            $parts = explode('_', $permission->name);
            return $parts[0] ?? 'other';
        });

        return view('admin.roles.show', compact('role', 'permissions', 'allPermissions'));
    }

    /**
     * GET /admin/roles/{role}/edit
     * Éditer permissions du rôle
     */
    public function edit(Role $role)
    {
        $this->authorize('manage_permissions');

        if ($role->name === 'admin') {
            abort(404);
        }

        $allPermissions = Permission::all()->groupBy(function ($permission) {
            $parts = explode('_', $permission->name);
            return $parts[0] ?? 'other';
        });
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', [
            'role' => $role,
            'allPermissions' => $allPermissions,
            'rolePermissions' => $rolePermissions,
        ]);
    }

    /**
     * PATCH /admin/roles/{role}
     * Mettre à jour permissions
     */
    public function update(Request $request, Role $role)
    {
        $this->authorize('manage_permissions');

        if ($role->name === 'admin') {
            abort(404);
        }

        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $permissions = $validated['permissions'] ?? [];
        
        // Validation: permissions doivent exister
        $validPermissions = Permission::whereIn('id', $permissions)
            ->pluck('id')
            ->toArray();

        $role->syncPermissions($validPermissions);

        // Invalidate cache
        Cache::forget('admin.roles.index');
        Cache::forget('admin.permissions.index');
        cache()->forget('spatie.permission.cache');
        cache()->forget('admin.nav.*'); // Tous les users

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Permissions mises à jour.');
    }

    /**
     * DELETE /admin/roles/{role}
     * Supprimer un rôle
     */
    public function destroy(Role $role)
    {
        $this->authorize('manage_permissions');

        // Empêcher supprimer admin role
        if ($role->name === 'admin') {
            abort(404);
        }

        // Vérifier qu'aucun utilisateur n'a ce rôle
        if ($role->users()->count() > 0) {
            return redirect()
                ->route('admin.roles.index')
                ->with('error', 'Impossible de supprimer ce rôle car il est assigné à des utilisateurs.');
        }

        $role->delete();

        // Invalidate cache
        Cache::forget('admin.roles.index');
        cache()->forget('spatie.permission.cache');
        cache()->forget('admin.nav.*');

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Rôle supprimé avec succès.');
    }
}

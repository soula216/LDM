<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class PermissionController extends Controller
{
    /**
     * Redirige vers la page Rôles / Permissions (onglet Permissions)
     */
    public function index()
    {
        $this->authorize('manage_permissions');

        return redirect()->route('admin.roles.index', ['tab' => 'permissions']);
    }
}

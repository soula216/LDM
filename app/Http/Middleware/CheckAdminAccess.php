<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminAccess
{
    /**
     * Vérifie que l'utilisateur a le rôle admin OU les permissions nécessaires
     * Retourne 404 (DenyAsNotFound) si pas autorisé
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Non authentifié → redirect login
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user();

        // Admin → accès complet
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // Routes spécifiques accessibles avec permissions
        $route = $request->route();
        $routeName = $route ? $route->getName() : null;

        if ($routeName) {
            // Routes utilisateurs : accessible avec view_users
            if (str_starts_with($routeName, 'admin.users.') && $user->can('view_users')) {
                return $next($request);
            }

            // Routes dentistes : accessible avec view_users
            if (str_starts_with($routeName, 'admin.dentists.') && $user->can('view_users')) {
                return $next($request);
            }

            // Routes équipes : accessible avec view_users
            if (str_starts_with($routeName, 'admin.teams.') && $user->can('view_users')) {
                return $next($request);
            }

            // Routes services : accessible avec manage_service_pricing
            if (str_starts_with($routeName, 'admin.services.') && $user->can('manage_service_pricing')) {
                return $next($request);
            }

            // Routes pricing : accessible avec manage_service_pricing
            if (str_starts_with($routeName, 'admin.pricing.') && $user->can('manage_service_pricing')) {
                return $next($request);
            }

            // Routes commandes : accessible avec view_commandes ou create_commandes ou edit_commandes
            if (str_starts_with($routeName, 'admin.commandes.')) {
                if ($user->can('view_commandes') || 
                    $user->can('create_commandes') || 
                    $user->can('edit_commandes')) {
                    return $next($request);
                }
            }

            // Routes factures : accessible avec view_factures ou create_factures
            if (str_starts_with($routeName, 'admin.factures.')) {
                if ($user->can('view_factures') || 
                    $user->can('create_factures') || 
                    $user->can('edit_factures')) {
                    return $next($request);
                }
            }

            // Routes config : admin uniquement (déjà géré par la vérification admin en haut)
            // Les routes config sont accessibles uniquement aux admins

            // Routes dashboard, roles, permissions : admin uniquement
            if (str_starts_with($routeName, 'admin.dashboard') || 
                str_starts_with($routeName, 'admin.roles') || 
                str_starts_with($routeName, 'admin.permissions')) {
                abort(404, 'Page not found');
            }
        }

        // Par défaut : 404 (masquer l'existence de l'admin panel)
        abort(404, 'Page not found');
    }
}

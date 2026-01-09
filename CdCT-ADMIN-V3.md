# CAHIER DES CHARGES TECHNIQUE (CdCT) – V3 (Ultra Clean)
## Système de Gestion de Laboratoire de Prothèse Dentaire
### Stack : Laravel 12 + Livewire 3 + Spatie Permission 6 + Redis/File Cache

**Version :** 3.0 (V2 + V3 consolidées)
**Date :** 9 janvier 2026
**Statut :** ✅ Spécifications techniques consolidées (sans annexe)

---

## Table des matières
- 1. Architecture & zones d’accès
- 2. Permissions & rôles (V2 + V3)
- 3. Modèle de données (MySQL)
- 4. Pricing (résolution + snapshot)
- 5. Calendrier commandes (API + filtrage)
- 6. Statuts & génération BL
- 7. Routes (admin vs app)
- 8. Cache (keys + invalidation)
- 9. Référence V2 (implémentation admin caché)

---

## 1. Architecture & zones d’accès
### 1.1 Principes hérités V2 (admin caché)
- L’espace `/admin/*` reste **admin-only** et **caché** (404 si non-admin) via middleware `admin.access`.
- La navigation admin ne doit pas être exposée aux non-admin (pas de liens hardcodés, menus conditionnels côté Blade).

### 1.2 Extension V3 (espace interne /app)
- Ajouter un espace `/app/*` (non-caché) pour les rôles internes + dentist + employer, protégé par `auth` + permissions.
- `/app/*` contient : calendrier, détails commande (modal), update statut, pricing, BL.

---

## 2. Permissions & rôles (V3)
### 2.1 Nouvelles permissions
- `view_commandes_calendar`
- `view_commande_details`
- `change_commande_status`
- `manage_service_pricing`
- `view_bons_livraison`
- `print_bons_livraison`

### 2.2 Attribution recommandée
- Admin : toutes permissions.
- Responsable / Secrétaire / Prothésiste : calendar + details + status + pricing + BL + print.
- Employer : calendar + details (filtré groupe) + view BL.
- Dentist : calendar (propres) + details (propres) + view BL (propres).

---

## 3. Modèle de données (MySQL) – V3
### 3.1 services (prix défaut TTC)
```sql
ALTER TABLE services ADD COLUMN prix_unitaire_ttc DECIMAL(10,2) NOT NULL DEFAULT 0;
```

### 3.2 dentist_service_prices (override)
```sql
CREATE TABLE dentist_service_prices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    dentist_id BIGINT UNSIGNED NOT NULL,
    service_id BIGINT UNSIGNED NOT NULL,
    prix_unitaire_ttc DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY unique_dentist_service (dentist_id, service_id),
    FOREIGN KEY (dentist_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
);
```

### 3.3 commande_taches (snapshot)
```sql
ALTER TABLE commande_taches 
ADD COLUMN prix_unitaire_ttc_snapshot DECIMAL(10,2) NOT NULL DEFAULT 0,
ADD COLUMN total_ligne_ttc DECIMAL(10,2) NOT NULL DEFAULT 0;
```

### 3.4 bons_livraison
```sql
CREATE TABLE bons_livraison (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    commande_id BIGINT UNSIGNED NOT NULL UNIQUE,
    numero_bl VARCHAR(50) NOT NULL UNIQUE,
    total_ttc DECIMAL(10,2),
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);
```

### 3.5 bon_livraison_lignes
```sql
CREATE TABLE bon_livraison_lignes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    bon_livraison_id BIGINT UNSIGNED NOT NULL,
    service_id BIGINT UNSIGNED NULL,
    service_name_snapshot VARCHAR(255) NOT NULL,
    prix_unitaire_ttc_snapshot DECIMAL(10,2) NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    total_ligne_ttc DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (bon_livraison_id) REFERENCES bons_livraison(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL
);
```

---

## 4. Pricing (résolution + snapshot) – V3
### 4.1 ServicePricingResolver
```php
namespace App\Services;

class ServicePricingResolver
{
    public function resolvePriceTtc(int $dentistId, int $serviceId): float
    {
        $override = \App\Models\DentistServicePrice::where('dentist_id', $dentistId)
            ->where('service_id', $serviceId)
            ->first();

        if ($override) {
            return (float) $override->prix_unitaire_ttc;
        }

        $service = \App\Models\Service::find($serviceId);
        return (float) ($service->prix_unitaire_ttc ?? 0);
    }
}
```

### 4.2 Snapshot TTC au niveau tâche
```php
$priceTtc = app(ServicePricingResolver::class)
    ->resolvePriceTtc($commande->dentiste_id, $serviceData['service_id']);

$commande->taches()->create([
    ...$serviceData,
    'prix_unitaire_ttc_snapshot' => $priceTtc,
    'total_ligne_ttc' => $priceTtc * $serviceData['nb_elem'],
]);
```

---

## 5. Calendrier commandes (API + filtrage serveur) – V3
### 5.1 Endpoint events (JSON)
Route :
```
GET /app/commandes/calendar/events?start=YYYY-MM-DD&end=YYYY-MM-DD
```

Implémentation (filtrage serveur obligatoire) :
```php
$events = Commande::with('taches')
    ->whereHas('taches', function ($q) use ($start, $end) {
        $q->whereBetween('date_livraison', [$start, $end]);
    });

if (auth()->user()->hasRole('employer')) {
    $events->whereHas('taches', function ($q) {
        $q->where('groupe_id', auth()->user()->groupe_id);
    });
} elseif (auth()->user()->hasRole('dentist')) {
    $events->where('dentiste_id', auth()->id());
}

return $events->get();
```

### 5.2 Détails commande (modal)
Route :
```
GET /app/commandes/{commande}
```

CommandePolicy (règles V3) :
```php
public function view(User $user, Commande $commande)
{
    if ($user->hasRole('admin') || $user->hasRole(['responsable', 'secretaire', 'prothesiste'])) {
        return true;
    }

    if ($user->hasRole('employer')) {
        return $commande->taches()->where('groupe_id', $user->groupe_id)->exists();
    }

    if ($user->hasRole('dentist')) {
        return $commande->dentiste_id === $user->id;
    }

    return false;
}
```

Filtrage tâches (employer) :
```php
$taches = $commande->taches();

if (auth()->user()->hasRole('employer')) {
    $taches = $taches->where('groupe_id', auth()->user()->groupe_id);
}

return $taches->get();
```

---

## 6. Statuts & génération BL – V3
### 6.1 Enum statut
```php
enum CommandeStatus: string
{
    case RECUE = 'Reçue';
    case EN_COURS = 'En cours';
    case TERMINEE = 'Terminée';
    case LIVREE = 'Livrée';
}
```

### 6.2 Update statut + trigger BL
```php
public function updateStatus(Commande $commande, Request $request)
{
    $oldStatus = $commande->status;
    $newStatus = $request->input('status');

    $commande->update(['status' => $newStatus]);

    if ($newStatus === 'Terminée' && $oldStatus !== 'Terminée') {
        app(BonLivraisonService::class)->generateFromCommande($commande);
    }

    Cache::forget("app.commandes.modal.{$commande->id}." . auth()->id());

    return redirect()->back()->with('success', 'Statut mis à jour');
}
```

### 6.3 BonLivraisonService (idempotent)
```php
namespace App\Services;

class BonLivraisonService
{
    public function generateFromCommande(Commande $commande)
    {
        if ($commande->bonLivraison) {
            return $commande->bonLivraison;
        }

        $bl = BonLivraison::create([
            'commande_id' => $commande->id,
            'numero_bl' => $this->generateNumberBl(),
            'created_by' => auth()->id(),
        ]);

        $totalTtc = 0;
        foreach ($commande->taches as $tache) {
            $lineTotalTtc = $tache->prix_unitaire_ttc_snapshot * $tache->nb_elem;
            $totalTtc += $lineTotalTtc;

            BonLivraisonLigne::create([
                'bon_livraison_id' => $bl->id,
                'service_id' => $tache->service_id,
                'service_name_snapshot' => $tache->service->nom,
                'prix_unitaire_ttc_snapshot' => $tache->prix_unitaire_ttc_snapshot,
                'quantite' => $tache->nb_elem,
                'total_ligne_ttc' => $lineTotalTtc,
            ]);
        }

        $bl->update(['total_ttc' => $totalTtc]);
        Cache::forget("bl.commande.{$commande->id}");

        return $bl;
    }

    private function generateNumberBl(): string
    {
        $year = now()->year;
        $count = BonLivraison::whereYear('created_at', $year)->count() + 1;
        return "BL-{$year}-" . str_pad($count, 5, '0', STR_PAD_LEFT);
    }
}
```

---

## 7. Routes (admin vs app) – V3
### 7.1 /admin (admin-only hidden)
- Inchangé V2 : routes admin protégées par `auth` + `admin.access`.

### 7.2 /app (interne permissionné)
```php
Route::middleware(['auth'])->prefix('app')->group(function () {

    // Calendrier
    Route::get('commandes/calendar', [CommandeCalendarController::class, 'index'])
        ->middleware('can:view_commandes_calendar')
        ->name('app.commandes.calendar');

    Route::get('commandes/calendar/events', [CommandeCalendarController::class, 'events'])
        ->middleware('can:view_commandes_calendar')
        ->name('app.commandes.calendar.events');

    // Détails (modal)
    Route::get('commandes/{commande}', [CommandeController::class, 'show'])
        ->middleware('can:view_commande_details')
        ->name('app.commandes.show');

    // Statut
    Route::patch('commandes/{commande}/status', [CommandeStatusController::class, 'update'])
        ->middleware('can:change_commande_status')
        ->name('app.commandes.status.update');

    // Pricing
    Route::get('pricing', [DentistPricingController::class, 'index'])
        ->middleware('can:manage_service_pricing')
        ->name('app.pricing.index');

    Route::post('pricing', [DentistPricingController::class, 'store'])
        ->middleware('can:manage_service_pricing')
        ->name('app.pricing.store');

    Route::delete('pricing/{row}', [DentistPricingController::class, 'destroy'])
        ->middleware('can:manage_service_pricing')
        ->name('app.pricing.destroy');

    // BL
    Route::get('bons-livraison/{bl}', [BonLivraisonController::class, 'show'])
        ->middleware('can:view_bons_livraison')
        ->name('app.bl.show');

    Route::get('bons-livraison/{bl}/print', [BonLivraisonController::class, 'print'])
        ->middleware('can:print_bons_livraison')
        ->name('app.bl.print');
});
```

---

## 8. Cache (keys + invalidation) – V3
### 8.1 Cache keys
```
app.commandes.calendar.events.{user_id}.{start}.{end}     → 120 sec
app.commandes.modal.{commande_id}.{user_id}              → 120 sec
pricing.dentist_service.{dentist_id}.{service_id}        → 600 sec
bl.commande.{commande_id}                                → 300 sec
```

### 8.2 Invalidation triggers
- Task create/update/delete → invalider calendar events + modal.
- Status change → invalider modal + calendar; si « Terminée » → générer BL.
- Pricing override update → invalider uniquement cache pricing.
- BL generation → invalider cache BL commande.

---

## 9. Référence V2 (implémentation admin caché)
Le contenu V2 ci-dessous est conservé comme référence unique (sans annexe séparée) pour : middlewares (`CheckAdminAccess`, `CheckPermission`), routes `/admin/*`, controllers admin, Livewire admin, services, config Spatie cache, tests et checklist déploiement.



---

## Référence technique V2 – Admin caché & cache (détails)

# CAHIER DES CHARGES TECHNIQUE (CdCT) - ADMIN CACHE
## Système de Gestion de Laboratoire de Prothèse Dentaire
### Implementation Technique Détaillée - Admin Visibility Cache & Security Layers
### Stack : Laravel 12 + Livewire 3 + Spatie Permission 6 + Redis/File Cache

---

## 1. ARCHITECTURE GLOBALE - ADMIN CACHE SYSTEM

### 1.1 Système de Sécurité en Couches

```
┌────────────────────────────────────────────────────────────┐
│           CLIENT (Browser)                                 │
│  - Navigateur standard, pas JS exposant admin routes      │
│  - Navigation dynamique basée sur Blade rendering         │
└────────────┬─────────────────────────────────────────────┘
             │
┌────────────▼─────────────────────────────────────────────┐
│       ROUTE LAYER - Middleware Chain                      │
│  ┌────────────────────────────────────────────────────┐  │
│  │ Route::middleware(['auth', 'admin.access'])->group │  │
│  │ - CheckAdminAccess @ middleware level              │  │
│  │ - Returns 404 (DenyAsNotFound) if not admin        │  │
│  │ - Cache: 30 sec per permission check               │  │
│  └────────────────────────────────────────────────────┘  │
└────────────┬─────────────────────────────────────────────┘
             │
┌────────────▼─────────────────────────────────────────────┐
│      CONTROLLER LAYER - Authorization                     │
│  ┌────────────────────────────────────────────────────┐  │
│  │ $this->authorize('view_users');                    │  │
│  │ - Policy check: UserPolicy@view                    │  │
│  │ - Gates if simple checks                           │  │
│  │ - Filters hidden users/roles                       │  │
│  │ - Cache query results: 2-5 min                     │  │
│  └────────────────────────────────────────────────────┘  │
└────────────┬─────────────────────────────────────────────┘
             │
┌────────────▼─────────────────────────────────────────────┐
│       LIVEWIRE LAYER - Dynamic Components                 │
│  ┌────────────────────────────────────────────────────┐  │
│  │ wire:lazy, wire:model.live with filtering         │  │
│  │ - Hidden fields: whereDoesntHave('admin')         │  │
│  │ - Cache computed properties: 5 min                │  │
│  │ - Validate on server-side: FormRequest class     │  │
│  └────────────────────────────────────────────────────┘  │
└────────────┬─────────────────────────────────────────────┘
             │
┌────────────▼─────────────────────────────────────────────┐
│         VIEW LAYER - Blade Rendering                      │
│  ┌────────────────────────────────────────────────────┐  │
│  │ @if($user->can('view_users')) show menu          │  │
│  │ - View composers for dynamic navigation           │  │
│  │ - No hardcoded /admin links in code               │  │
│  │ - Cache view structures: 2 min                     │  │
│  └────────────────────────────────────────────────────┘  │
└────────────┬─────────────────────────────────────────────┘
             │
┌────────────▼─────────────────────────────────────────────┐
│      CACHE LAYER - Multi-tier Caching                     │
│  ┌────────────────────────────────────────────────────┐  │
│  │ Driver: Redis (prod) | File (dev/test)            │  │
│  │ - Permission cache: 5 min (Spatie)                │  │
│  │ - Query cache: 2-10 min per type                  │  │
│  │ - Navigation cache: 15 min per user               │  │
│  │ - Auto invalidation on model changes              │  │
│  └────────────────────────────────────────────────────┘  │
└────────────┬─────────────────────────────────────────────┘
             │
┌────────────▼─────────────────────────────────────────────┐
│        DATABASE LAYER - Optimized Queries                 │
│  ┌────────────────────────────────────────────────────┐  │
│  │ - Eager loading: with('roles', 'permissions')     │  │
│  │ - Indexes: email, deleted_at, role_id             │  │
│  │ - Soft deletes via SoftDeletes trait              │  │
│  │ - Spatie tables: roles, permissions, pivot tables │  │
│  └────────────────────────────────────────────────────┘  │
└────────────────────────────────────────────────────────────┘
```

### 1.2 Dépendances Mises à Jour

```json
{
  "require": {
    "php": "^8.2",
    "laravel/framework": "^12.0",
    "laravel/breeze": "^2.0",
    "spatie/laravel-permission": "^6.0",
    "livewire/livewire": "^3.0"
  },
  "require-dev": {
    "phpunit/phpunit": "^10.0",
    "laravel/pint": "^1.0",
    "mockery/mockery": "^1.5"
  }
}
```

---

## 2. MIDDLEWARE PERSONNALISÉS

### 2.1 CheckAdminAccess Middleware (Route Protection)

```php
// app/Http/Middleware/CheckAdminAccess.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\Response;

class CheckAdminAccess
{
    /**
     * Vérifie que l'utilisateur a le rôle admin
     * Retourne 404 (DenyAsNotFound) si pas admin
     */
    public function handle(Request $request, Closure $next)
    {
        // Non authentifié → redirect login
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Pas admin → 404 (masquer l'existence de l'admin panel)
        if (!$request->user()->hasRole('admin')) {
            abort(404, 'Page not found');
        }

        // Admin → Continue
        return $next($request);
    }
}
```

**Enregistrement dans Kernel :**
```php
// app/Http/Kernel.php

protected $routeMiddleware = [
    // ... autres middleware
    'admin.access' => \App\Http\Middleware\CheckAdminAccess::class,
];
```

### 2.2 CheckPermission Middleware (Fine-grained)

```php
// app/Http/Middleware/CheckPermission.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    /**
     * handle($request, $next, $permission)
     * Vérifie une permission spécifique + cache
     */
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Cache check: 30 secondes
        $cacheKey = "user.{$request->user()->id}.permissions";
        
        $userPermissions = cache()->remember($cacheKey, 30, function () use ($request) {
            return $request->user()->getAllPermissions()->pluck('name')->toArray();
        });

        // Vérifier si l'utilisateur a au moins une des permissions
        $hasPermission = collect($permissions)->some(function ($permission) use ($userPermissions) {
            return in_array($permission, $userPermissions);
        });

        if (!$hasPermission) {
            abort(403, 'Non autorisé');
        }

        return $next($request);
    }
}
```

---

## 3. ROUTES SÉCURISÉES

### 3.1 routes/web.php - Admin Routes Block

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    DashboardController,
    UserController as AdminUserController,
    RoleController as AdminRoleController,
    PermissionController as AdminPermissionController
};

// ... Routes publiques (login, register, guest pages)

// 🔐 ADMIN ROUTES - Complètement cachées
Route::middleware(['auth', 'admin.access'])->prefix('admin')->group(function () {
    
    // Dashboard admin
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('admin.dashboard')
        ->middleware('can:view_dashboard');
    
    // Gestion utilisateurs
    Route::resource('users', AdminUserController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
        ->middleware('can:view_users'); // Sur resource group
    
    // Gestion rôles
    Route::prefix('roles')->group(function () {
        Route::get('/', [AdminRoleController::class, 'index'])
            ->name('roles.index')
            ->middleware('can:view_roles');
        
        Route::get('{role}', [AdminRoleController::class, 'show'])
            ->name('roles.show')
            ->middleware('can:view_roles');
        
        Route::get('{role}/edit', [AdminRoleController::class, 'edit'])
            ->name('roles.edit')
            ->middleware('can:manage_permissions');
        
        Route::patch('{role}', [AdminRoleController::class, 'update'])
            ->name('roles.update')
            ->middleware('can:manage_permissions');
    });
    
    // Gestion permissions
    Route::get('permissions', [AdminPermissionController::class, 'index'])
        ->name('permissions.index')
        ->middleware('can:manage_permissions');
});

// Routes utilisateur (accessibles à tous les users authentifiés)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'userDashboard'])
        ->name('dashboard');
    
    // Profil personnel
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/password', [ProfileController::class, 'updatePassword'])
            ->name('profile.update-password');
    });
});

// Routes d'authentification (Breeze)
require __DIR__.'/auth.php';
```

**Comportement :**
- ✅ `/admin/*` → 404 si non-admin (CheckAdminAccess middleware)
- ✅ `/admin/users` → 404 + message silencieux
- ✅ `/admin/roles` → 404 (masquer l'existence du système)
- ✅ `/dashboard` → Accessible à tous (user dashboard)

---

## 4. CONTROLLERS - HIDDEN ADMIN

### 4.1 DashboardController (Admin Hidden)

```php
// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DashboardController extends Controller
{
    /**
     * Admin Dashboard - Complètement caché
     * GET /admin/dashboard (404 si non-admin)
     */
    public function index()
    {
        // Double check (middleware déjà fait, mais sécurité en couche)
        if (!auth()->user()->hasRole('admin')) {
            abort(404);
        }

        // Cache les stats - 5 minutes
        $stats = Cache::remember('admin.dashboard.stats', 300, function () {
            return [
                'total_users' => User::count(),
                'active_users' => User::whereNull('deleted_at')->count(),
                'deleted_users' => User::onlyTrashed()->count(),
                'total_roles' => Role::count(),
                'total_permissions' => Permission::count(),
            ];
        });

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * User Dashboard - Accessible à tous les users
     * GET /dashboard (route publique)
     */
    public function userDashboard()
    {
        $user = auth()->user();
        
        // Cache par user - 15 min
        $data = Cache::remember("user.{$user->id}.dashboard", 900, function () use ($user) {
            return [
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ];
        });

        return view('dashboard', $data);
    }
}
```

### 4.2 AdminUserController (Hidden Admin Users)

```php
// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\UserService;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private UserService $userService) {}

    /**
     * GET /admin/users
     * Affiche tous les utilisateurs SAUF les admins
     */
    public function index()
    {
        $this->authorize('view_users');

        // Cache list - 2 minutes après recherche
        $users = cache()->remember('admin.users.list', 120, function () {
            return User::query()
                ->notDeleted() // scope
                ->whereDoesntHave('roles', function ($q) {
                    $q->where('name', 'admin'); // ❌ Hide admin users
                })
                ->with('roles') // eager load
                ->orderBy('created_at', 'desc')
                ->paginate(25);
        });

        return view('admin.users.index', compact('users'));
    }

    /**
     * POST /admin/users
     * Créer utilisateur - impossible de donner le rôle admin
     */
    public function store(StoreUserRequest $request)
    {
        $this->authorize('create_users');

        // Validation : role ne doit pas être 'admin'
        if ($request->input('role') === 'admin') {
            return back()->with('error', 'Cannot assign admin role');
        }

        $user = $this->userService->createUser($request->validated());

        // Invalidate cache
        Cache::forget('admin.users.list');
        Cache::forget('admin.dashboard.stats');
        cache()->forget('spatie.permission.cache');

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Utilisateur {$user->full_name} créé.");
    }

    /**
     * PATCH /admin/users/{user}
     * Modifier utilisateur - jamais pouvoir changer son rôle en admin
     */
    public function update(User $user, UpdateUserRequest $request)
    {
        $this->authorize('update', $user);

        // Protection: ne pas permettre passer à admin
        if ($request->has('role') && $request->input('role') === 'admin') {
            return back()->with('error', 'Cannot assign admin role');
        }

        $user = $this->userService->updateUser($user, $request->validated());

        // Invalidate cache
        Cache::forget('admin.users.list');
        cache()->forget("admin.nav.{$user->id}");

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Utilisateur modifié.');
    }

    /**
     * DELETE /admin/users/{user}
     * Soft delete utilisateur
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        if ($user->hasRole('admin')) {
            return back()->with('error', 'Cannot delete admin user');
        }

        $user->delete(); // Soft delete

        // Invalidate cache
        Cache::forget('admin.users.list');
        Cache::forget('admin.dashboard.stats');

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé.');
    }
}
```

### 4.3 AdminRoleController (Hide Admin Role)

```php
// app/Http/Controllers/Admin/RoleController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
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
                ->withCount('users') // Affiche combien d'users par rôle
                ->get();
        });

        return view('admin.roles.index', compact('roles'));
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
        return view('admin.roles.show', compact('role', 'permissions'));
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

        $allPermissions = Permission::all();
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
    public function update(Role $role)
    {
        $this->authorize('manage_permissions');

        if ($role->name === 'admin') {
            abort(404);
        }

        $permissions = request()->input('permissions', []);
        
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
}
```

---

## 5. LIVEWIRE COMPONENTS - HIDDEN & CACHED

### 5.1 UserTable Component (Hide Admin Users)

```php
// app/Livewire/Admin/UserTable.php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserTable extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';
    public $sortBy = 'created_at';
    public $sortDir = 'desc';

    // Livewire lifecycle
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Cache key unique par filters
        $cacheKey = "admin.users.table.{$this->search}.{$this->roleFilter}";
        
        $users = cache()->remember($cacheKey, 120, function () {
            $query = User::query()
                ->notDeleted()
                ->whereDoesntHave('roles', function ($q) {
                    $q->where('name', 'admin'); // ❌ Hide admin users
                });

            // Recherche
            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('nom', 'like', "%{$this->search}%")
                        ->orWhere('prénom', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            }

            // Filtre rôle
            if ($this->roleFilter) {
                $query->role($this->roleFilter);
            }

            return $query
                ->with('roles')
                ->orderBy($this->sortBy, $this->sortDir)
                ->paginate(25);
        });

        return view('livewire.admin.user-table', ['users' => $users]);
    }

    /**
     * Soft delete via Livewire
     */
    public function deleteUser(User $user)
    {
        $this->authorize('delete_users');

        $user->delete();

        // Invalidate cache
        Cache::forget("admin.users.table.*");
        Cache::forget('admin.dashboard.stats');

        $this->dispatch('user-deleted', name: $user->full_name);
    }
}
```

**Vue Livewire :**
```blade
{{-- resources/views/livewire/admin/user-table.blade.php --}}

<div>
    {{-- Recherche & Filtre --}}
    <div class="mb-4 row">
        <div class="col-md-6">
            <input type="text" 
                wire:model.live.debounce.500ms="search" 
                placeholder="Rechercher utilisateur..."
                class="form-control">
        </div>
        <div class="col-md-6">
            <select wire:model.live="roleFilter" class="form-select">
                <option value="">Tous les rôles</option>
                <option value="responsable">Responsable</option>
                <option value="secrétaire">Secrétaire</option>
                <option value="coursier">Coursier</option>
                <option value="employer">Employer</option>
                <option value="dentist">Dentist</option>
                {{-- ❌ Admin role NOT listed --}}
            </select>
        </div>
    </div>

    {{-- Tableau --}}
    <table class="table">
        <thead>
            <tr>
                <th wire:click="$set('sortBy', 'id')" style="cursor: pointer;">ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->full_name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @foreach($user->getRoleNames() as $role)
                            <span class="badge bg-primary">{{ $role }}</span>
                        @endforeach
                    </td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user) }}" 
                           class="btn btn-sm btn-warning">✎</a>
                        <button wire:click="deleteUser({{ $user->id }})" 
                                class="btn btn-sm btn-danger">🗑</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">Aucun utilisateur</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $users->links() }}
    </div>
</div>
```

### 5.2 RoleTable Component (Hide Admin Role)

```php
// app/Livewire/Admin/RoleTable.php

namespace App\Livewire\Admin;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Cache;

class RoleTable extends Component
{
    public function render()
    {
        // Cache - 10 minutes
        $roles = Cache::remember('admin.roles.table', 600, function () {
            return Role::query()
                ->where('name', '!=', 'admin') // ❌ Hide admin role
                ->with('permissions')
                ->withCount('users')
                ->orderBy('created_at')
                ->get();
        });

        return view('livewire.admin.role-table', ['roles' => $roles]);
    }
}
```

---

## 6. SERVICES - CACHE & BUSINESS LOGIC

### 6.1 UserService avec Cache Invalidation

```php
// app/Services/UserService.php

namespace App\Services;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class UserService
{
    /**
     * Créer utilisateur avec rôle assigné
     */
    public function createUser(array $data): User
    {
        $user = User::create([
            'nom' => $data['nom'],
            'prénom' => $data['prénom'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'gouvernorat' => $data['gouvernorat'],
            'ville' => $data['ville'],
            'adresse' => $data['adresse'],
            'tél' => $data['tél'],
            'num_ordinaire' => $data['num_ordinaire'] ?? null,
        ]);

        // Assigner rôle (pas admin)
        $roleName = $data['role'];
        if ($roleName !== 'admin') {
            $user->assignRole($roleName);
        }

        // Invalidate caches
        $this->invalidateUserCaches();

        return $user;
    }

    /**
     * Mettre à jour utilisateur
     */
    public function updateUser(User $user, array $data): User
    {
        $user->update([
            'nom' => $data['nom'] ?? $user->nom,
            'prénom' => $data['prénom'] ?? $user->prénom,
            'email' => $data['email'] ?? $user->email,
            'gouvernorat' => $data['gouvernorat'] ?? $user->gouvernorat,
            'ville' => $data['ville'] ?? $user->ville,
            'adresse' => $data['adresse'] ?? $user->adresse,
            'tél' => $data['tél'] ?? $user->tél,
            'num_ordinaire' => $data['num_ordinaire'] ?? $user->num_ordinaire,
        ]);

        // Changer rôle si fourni (pas en admin)
        if (isset($data['role']) && $data['role'] !== 'admin') {
            $user->syncRoles($data['role']);
        }

        // Invalidate caches
        $this->invalidateUserCaches($user->id);

        return $user->fresh();
    }

    /**
     * Supprimer utilisateur (soft delete)
     */
    public function deleteUser(User $user): void
    {
        $user->delete(); // Soft delete

        $this->invalidateUserCaches($user->id);
    }

    /**
     * Invalider les caches liés à utilisateur
     */
    private function invalidateUserCaches(?int $userId = null): void
    {
        // Caches globaux
        Cache::forget('admin.users.list');
        Cache::forget('admin.users.table.*');
        Cache::forget('admin.dashboard.stats');
        cache()->forget('spatie.permission.cache');

        // Cache user spécifique si applicable
        if ($userId) {
            cache()->forget("user.{$userId}.dashboard");
            cache()->forget("admin.nav.{$userId}");
        }

        // Tous les users
        cache()->forget('admin.nav.*');
    }
}
```

---

## 7. VIEWS - NAVIGATION CACHÉE

### 7.1 Layout Principal - Navigation Dynamique

```blade
{{-- resources/views/layouts/app.blade.php --}}

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Labo Prothèse')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    {{-- Navigation --}}
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                🏥 Labo Prothèse
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    {{-- Admin Menu - Completely hidden if not admin --}}
                    @if(auth()->user()?->hasRole('admin'))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="adminMenu" 
                               role="button" data-bs-toggle="dropdown">
                                🔐 Admin
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="adminMenu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        Dashboard
                                    </a>
                                </li>
                                @can('view_users')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.users.index') }}">
                                            👥 Utilisateurs
                                        </a>
                                    </li>
                                @endcan
                                @can('view_roles')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.roles.index') }}">
                                            🔑 Rôles
                                        </a>
                                    </li>
                                @endcan
                                @can('manage_permissions')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.permissions.index') }}">
                                            ⚙️ Permissions
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endif
                    
                    {{-- User Menu (Visible à tous) --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('profile.edit') }}">
                            👤 Profil
                        </a>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link">
                                🚪 Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="container mt-4">
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="mt-5 py-3 bg-light text-center">
        <p>&copy; 2026 Labo Prothèse Dentaire. Tous droits réservés.</p>
    </footer>
</body>
</html>
```

**Points clés :**
- ✅ Menu Admin **complètement absent** si non-admin
- ✅ Pas de hardcoded `/admin` links dans le code
- ✅ Navigation rendue dynamiquement avec `@if(auth()->user()?->hasRole('admin'))`
- ✅ Sous-menus filtrés par permissions individuelles

---

## 8. CONFIGURATION - CACHE & PERMISSIONS

### 8.1 config/permission.php (Spatie)

```php
// config/permission.php

return [
    'models' => [
        'permission' => \Spatie\Permission\Models\Permission::class,
        'role' => \Spatie\Permission\Models\Role::class,
    ],

    'table_names' => [
        'roles' => 'roles',
        'permissions' => 'permissions',
        'model_has_roles' => 'model_has_roles',
        'role_has_permissions' => 'role_has_permissions',
        'model_has_permissions' => 'model_has_permissions',
    ],

    'column_names' => [
        'model_morph_key' => 'model_id',
        'role_pivot_key' => 'role_id',
        'permission_pivot_key' => 'permission_id',
        'model_morph_type' => 'model_type',
    ],

    'cache' => [
        'enable' => true,
        'expiration_time' => 300, // 5 minutes
        'key' => 'spatie.permission.cache',
        'store' => env('PERMISSION_CACHE_DRIVER', env('CACHE_DRIVER', 'file')),
    ],

    'enable_wildcard_permission' => false,

    'permissions' => [
        // User Management
        'view_users',
        'create_users',
        'edit_users',
        'delete_users',
        
        // Profile
        'edit_own_profile',
        'change_own_password',
        
        // Roles
        'view_roles',
        'manage_permissions',
        
        // Dashboard
        'view_dashboard',
        
        // Orders (phase 2)
        'view_orders',
        'create_orders',
        
        // Dentist
        'access_dentist_portal',
    ],
];
```

### 8.2 .env Cache Configuration

```env
CACHE_DRIVER=redis
PERMISSION_CACHE_DRIVER=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Ou File cache (dev)
# CACHE_DRIVER=file
# PERMISSION_CACHE_DRIVER=file
```

---

## 9. TESTS AUTOMATISÉS

### 9.1 Feature Test - Admin Access Hidden

```php
// tests/Feature/Admin/AdminAccessTest.php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    /**
     * Non-admin ne peut pas accéder /admin/users
     */
    public function test_non_admin_cannot_access_admin_users()
    {
        $employer = User::factory()->create();
        $employer->assignRole('employer');

        $response = $this->actingAs($employer)->get('/admin/users');
        
        $response->assertStatus(404);
    }

    /**
     * Admin peut accéder /admin/users
     */
    public function test_admin_can_access_admin_users()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/admin/users');
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index');
    }

    /**
     * Admin users sont cachés de la liste
     */
    public function test_admin_users_are_hidden_from_list()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        $employer = User::factory()->create();
        $employer->assignRole('employer');

        $response = $this->actingAs($admin)->get('/admin/users');
        
        // Admin user ne doit pas apparaître
        $response->assertDontSee($admin->email);
        // Employer user doit apparaître
        $response->assertSee($employer->email);
    }

    /**
     * Admin role est caché de la liste des rôles
     */
    public function test_admin_role_is_hidden_from_roles_list()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/admin/roles');
        
        // Admin role ne doit pas apparaître
        $response->assertDontSee('admin');
    }
}
```

### 9.2 Unit Test - Cache Invalidation

```php
// tests/Unit/Services/UserServiceTest.php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Cache invalidation après user création
     */
    public function test_user_creation_invalidates_caches()
    {
        $service = new UserService();

        // Pré-remplir cache
        Cache::put('admin.users.list', 'cached_data', 3600);
        Cache::put('admin.dashboard.stats', 'cached_stats', 3600);

        // Créer utilisateur
        $service->createUser([
            'nom' => 'Test',
            'prénom' => 'User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'gouvernorat' => 'Sousse',
            'ville' => 'Sousse',
            'adresse' => 'Test',
            'tél' => '123',
            'role' => 'employer',
        ]);

        // Cache doit être invalide
        $this->assertFalse(Cache::has('admin.users.list'));
        $this->assertFalse(Cache::has('admin.dashboard.stats'));
    }
}
```

---

## 10. SÉCURITÉ CHECKLIST

### 10.1 Implementation Security

- ✅ **Route Protection :** CheckAdminAccess middleware sur `/admin/*`
- ✅ **Policy Checks :** UserPolicy avec authorize() dans controllers
- ✅ **Hidden Data :** whereDoesntHave('admin') dans queries
- ✅ **Cache Invalidation :** Automatic après model changes
- ✅ **CSRF Tokens :** Sur tous les formulaires POST/PATCH/DELETE
- ✅ **No Hardcoded Links :** Admin URLs générées via route() helpers
- ✅ **Rate Limiting :** Optionnel sur login (phase 2)
- ✅ **Soft Deletes :** deleted_at column pour audit trail
- ✅ **No Console Logs :** Pas de logs sensibles exposées
- ✅ **Input Validation :** FormRequest classes
- ✅ **Password Hashing :** Bcrypt avec Hash::make()

### 10.2 Code Obfuscation

```php
// ❌ MAUVAIS - Admin role visibles en dur
$roles = Role::all(); // Peut révéler admin

// ✅ BON - Admin role explicitement caché
$roles = Role::where('name', '!=', 'admin')->get();

// ❌ MAUVAIS - URL visible en source
<a href="/admin/users">Users</a> <!-- Visible en HTML source -->

// ✅ BON - URL générée via Laravel route()
<a href="{{ route('admin.users.index') }}">Users</a>
<!-- HTML: <a href="/admin/users">Users</a> -->
<!-- Mais seulement si user authentifié + admin -->
```

---

## 11. PERFORMANCE OPTIMIZATION

### 11.1 Eager Loading Strategy

```php
// ❌ N+1 queries problem
$users = User::all();
foreach ($users as $user) {
    echo $user->roles; // 1 query per user = N+1
}

// ✅ Eager loading
$users = User::with('roles', 'permissions')->get(); // 2 queries total
foreach ($users as $user) {
    echo $user->roles; // données déjà chargées
}
```

### 11.2 Database Indexes

```sql
-- Créés via migrations
ALTER TABLE users ADD INDEX idx_email (email);
ALTER TABLE users ADD INDEX idx_deleted_at (deleted_at);
ALTER TABLE roles ADD UNIQUE INDEX idx_role_name (name);
ALTER TABLE permissions ADD UNIQUE INDEX idx_permission_name (name);
```

### 11.3 Cache Hit Rate Target

| Endpoint | Cache Hit Target | Strategy |
|----------|------------------|----------|
| /admin/users | 80% | 2 min cache + search key |
| /admin/roles | 90% | 10 min cache |
| /admin/dashboard | 85% | 5 min cache |
| /profile | 95% | 15 min per-user cache |

---

## 12. SEEDER - HIDDEN ADMIN

### 12.1 RolePermissionSeeder

```php
// database/seeders/RolePermissionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Réinitialiser cache
        app()['cache']->forget('spatie.permission.cache');

        // Permissions
        $permissions = [
            'view_users', 'create_users', 'edit_users', 'delete_users',
            'edit_own_profile', 'change_own_password',
            'view_roles', 'manage_permissions',
            'view_dashboard', 'view_orders', 'create_orders',
            'access_dentist_portal',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Rôles & permissions
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        $responsable = Role::firstOrCreate(['name' => 'responsable']);
        $responsable->syncPermissions([
            'edit_own_profile', 'change_own_password',
            'view_dashboard', 'view_orders',
        ]);

        // ... autres rôles
    }
}
```

---

## 13. DEPLOYMENT - PRODUCTION CHECKLIST

- ✅ `php artisan config:cache` (config cache)
- ✅ `php artisan route:cache` (route cache)
- ✅ `php artisan view:cache` (blade cache)
- ✅ `php artisan optimize` (optimization)
- ✅ Redis server running (cache + sessions)
- ✅ HTTPS enabled (.env: APP_URL=https://...)
- ✅ APP_DEBUG=false (.env)
- ✅ CSRF_TRUSTED_HOSTS configured
- ✅ Rate limiting configured (.env: THROTTLE=60,1)
- ✅ Logs monitored (Laravel Telescope optionnel)

---

## 14. TIMELINE DEVELOPMENT

| Phase | Task | Days | Priority |
|-------|------|------|----------|
| Setup | Hidden routes + middleware | 1.5 | 🔴 CRITICAL |
| Dev | Admin controllers + policies | 2.5 | 🔴 CRITICAL |
| Dev | Livewire components + caching | 3.5 | 🔴 CRITICAL |
| Dev | Views + navigation | 2 | 🟡 IMPORTANT |
| Test | Security + cache tests | 2 | 🟡 IMPORTANT |
| QA | Manual testing + audit | 1.5 | 🟡 IMPORTANT |
| **TOTAL** | | **13 days** | |

---

**Document Version :** 1.0 CdCT Admin Cache  
**Date :** 6 janvier 2026  
**Stack :** Laravel 12 + Livewire 3 + Spatie Permission 6 + Redis Cache  
**Status :** ✅ READY FOR IMPLEMENTATION

# GUIDE D'IMPLÉMENTATION – FINAL (V2 + V3)
## Admin Cache System + Module Commandes + Calendrier + Pricing + BL
### Quick Start pour développeurs (Laravel 12 + Livewire 3 + Spatie Permission + Redis)

**Version :** 3.0 (merge)
**Date :** 9 janvier 2026

---

## Table des matières
- 0. Pré-requis
- 1. Middleware & sécurité admin
- 2. Permissions & seeding
- 3. Migrations
- 4. Models
- 5. Services
- 6. Controllers
- 7. Routes
- 8. Snapshot prix sur tâches
- 9. Cache keys & invalidation
- 10. Tests
- 11. Checklist & commandes Artisan

---

## 0) Pré-requis
- Laravel 12, Livewire 3, Spatie Permission, cache driver Redis (prod) / File (dev).
- Règle importante : `/admin/*` reste admin-only (404 si non-admin) ; `/app/*` sert aux rôles internes (permissionné).

---

## 1) Middleware & sécurité admin
- V2 : `CheckAdminAccess` + middleware `admin.access` (404 si non-admin).
- V2 : routes `/admin/*` cachées + pas de liens hardcodés.

---

## 2) Permissions & seeding
### 2.1 Permissions V2 (Commandes)
- `view_commandes`, `create_commandes`, `edit_commandes`, `delete_commandes`
- `upload_commande_files`, `delete_commande_files`
- `change_commande_status`

### 2.2 Permissions V3 (Calendrier, détails, pricing, BL)
- `view_commandes_calendar`
- `view_commande_details`
- `manage_service_pricing`
- `view_bons_livraison`
- `print_bons_livraison`

### 2.3 Seeder (fusion)
- Partir du seeder V2 et ajouter les permissions V3 + attributions recommandées.

---

## 3) Migrations
### 3.1 V2 (Commandes)
- `create_commandes_table`
- `create_commande_taches_table`
- `create_commande_files_table`

### 3.2 V3 (Pricing + Snapshot + BL)
- `add_prix_to_services_table`
- `create_dentist_service_prices_table`
- `add_snapshot_to_commande_taches_table`
- `create_bons_livraison_table`
- `create_bon_livraison_lignes_table`

---

## 4) Models
### 4.1 V2
- `Commande`, `CommandeTache`, `CommandeFile`.

### 4.2 V3
- `DentistServicePrice`, `BonLivraison`, `BonLivraisonLigne`.
- Mettre à jour `Commande` avec `bonLivraison()`.

---

## 5) Services
- V3 : `ServicePricingResolver` (prix défaut vs override)
- V3 : `BonLivraisonService` (idempotent, génère BL)

---

## 6) Controllers
### 6.1 V2 (/admin)
- `Admin/CommandeController` (CRUD)
- `Admin/CommandeFileController` (upload/delete)

### 6.2 V3 (/app)
- `App/CommandeCalendarController` (index + events JSON)
- `App/CommandeStatusController` (update status + trigger BL)
- `App/DentistPricingController` (CRUD override)
- `App/BonLivraisonController` (show + print)

---

## 7) Routes
- V2 : `/admin/commandes*` sous `auth + admin.access`.
- V3 : `/app/*` sous `auth` + `can:*`.

---

## 8) Snapshot prix sur tâches
- Mettre à jour `Admin/CommandeController@store()` (et idéalement `update()`) pour calculer et stocker :
  - `prix_unitaire_ttc_snapshot`
  - `total_ligne_ttc`

---

## 9) Cache keys & invalidation
### 9.1 V2
- `admin.commandes.list` (120s)
- `admin.commandes.show.{id}` (300s)

### 9.2 V3
- `app.commandes.calendar.events.{USER_ID}.{START}.{END}` (120s)
- `app.commandes.modal.{COMMANDE_ID}.{USER_ID}` (120s)
- `pricing.dentist_service.{DENTIST_ID}.{SERVICE_ID}` (600s)
- `bl.commande.{COMMANDE_ID}` (300s)

---

## 10) Tests
- V2 : Feature tests Commandes (404 non-admin, validations, urgent -> date).
- V3 : tests permissions calendrier/détails, snapshot pricing, génération BL.

---

## 11) Checklist & commandes Artisan
### 11.1 Ordre conseillé
1. Migrations V2 + V3.
2. Seeder permissions (V2 + V3).
3. Models V2 + V3.
4. Services V3.
5. Controllers + routes.
6. Views + intégrations UI.
7. Tests + cache clear.

### 11.2 Commandes
```bash
php artisan migrate
php artisan db:seed --class=RolePermissionSeeder
php artisan cache:clear
php artisan test
```

---

## Détails V2 (contenu source)
# GUIDE D'IMPLÉMENTATION RAPIDE - V2
## Admin Cache System + Module Commandes
### Pour développeurs - Quick Reference

---

## 📋 RÉSUMÉ V2

Ajout du module **Commandes** (interne) + renommage complet des permissions "Orders" → "Commandes".

---

## 1️⃣ MIDDLEWARE ADMIN

### Créer et enregistrer

```bash
php artisan make:middleware CheckAdminAccess
```

**Contenu :** `app/Http/Middleware/CheckAdminAccess.php`
```php
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->hasRole('admin')) {
            abort(404); // Admin panel complètement invisible
        }

        return $next($request);
    }
}
```

**Enregistrer dans** `app/Http/Kernel.php`:
```php
protected $routeMiddleware = [
    // ...
    'admin.access' => \App\Http\Middleware\CheckAdminAccess::class,
];
```

---

## 2️⃣ PERMISSIONS (RENOMMAGE COMPLET)

### À supprimer
- ❌ `view_orders`
- ❌ `create_orders`
- ❌ `access_dentist_portal`

### À ajouter (Commandes)
- ✅ `view_commandes`
- ✅ `create_commandes`
- ✅ `edit_commandes`
- ✅ `delete_commandes`
- ✅ `upload_commande_files`
- ✅ `delete_commande_files`
- ✅ `change_commande_status`

### Seeder - `database/seeders/RolePermissionSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Vider ancien cache
        cache()->forget('spatie.permission.cache');

        // Créer permissions Commandes
        $commandesPermissions = [
            'view_commandes',
            'create_commandes',
            'edit_commandes',
            'delete_commandes',
            'upload_commande_files',
            'delete_commande_files',
            'change_commande_status',
        ];

        foreach ($commandesPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Admin = toutes les permissions
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        // Autres rôles selon besoins
        $employer = Role::firstOrCreate(['name' => 'employer']);
        $employer->syncPermissions([
            'view_commandes',
            'create_commandes',
            'edit_commandes',
            'upload_commande_files',
        ]);

        // Clear cache
        cache()->forget('spatie.permission.cache');
    }
}
```

**Exécuter :**
```bash
php artisan db:seed --class=RolePermissionSeeder
php artisan cache:clear
```

---

## 3️⃣ MIGRATIONS

### Créer les tables

```bash
php artisan make:migration create_commandes_table
php artisan make:migration create_commande_taches_table
php artisan make:migration create_commande_files_table
```

### Migration 1 : `create_commandes_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dentiste_id')->constrained('users');
            $table->string('num_cmd')->unique(); // Manuel, unique
            $table->string('nom_patient');
            $table->boolean('urgent')->default(false);
            $table->enum('status', ['in_progress', 'completed', 'delivered', 'cancelled'])
                ->default('in_progress');
            $table->longText('commentaire')->nullable();
            $table->timestamps();
            
            $table->index('dentiste_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
```

### Migration 2 : `create_commande_taches_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commande_taches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained('commandes')->cascadeOnDelete();
            $table->foreignId('groupe_id')->constrained('groupes');
            $table->foreignId('service_id')->constrained('services');
            $table->unsignedInteger('nb_elem');
            $table->string('teinte')->nullable();
            $table->date('date_livraison');
            $table->timestamps();
            
            $table->index('commande_id');
            $table->index('groupe_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commande_taches');
    }
};
```

### Migration 3 : `create_commande_files_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commande_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained('commandes')->cascadeOnDelete();
            $table->enum('type', ['empreinte', 'image']);
            $table->string('path');
            $table->string('original_name');
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('commande_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commande_files');
    }
};
```

**Exécuter :**
```bash
php artisan migrate
```

---

## 4️⃣ MODELS

### `app/Models/Commande.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    protected $fillable = [
        'dentiste_id', 'num_cmd', 'nom_patient', 'urgent', 'status', 'commentaire'
    ];

    protected $casts = [
        'urgent' => 'boolean',
    ];

    public function dentiste()
    {
        return $this->belongsTo(User::class, 'dentiste_id');
    }

    public function taches()
    {
        return $this->hasMany(CommandeTache::class);
    }

    public function files()
    {
        return $this->hasMany(CommandeFile::class);
    }
}
```

### `app/Models/CommandeTache.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommandeTache extends Model
{
    protected $table = 'commande_taches';
    protected $fillable = [
        'commande_id', 'groupe_id', 'service_id', 'nb_elem', 'teinte', 'date_livraison'
    ];

    protected $casts = [
        'date_livraison' => 'date',
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }
}
```

### `app/Models/CommandeFile.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommandeFile extends Model
{
    protected $table = 'commande_files';
    protected $fillable = [
        'commande_id', 'type', 'path', 'original_name', 'mime', 'size', 'uploaded_by'
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }
}
```

---

## 5️⃣ FORM REQUESTS

### `app/Http/Requests/StoreCommandeRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommandeRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->can('create_commandes');
    }

    public function rules()
    {
        return [
            'dentiste_id' => 'required|exists:users,id',
            'num_cmd' => 'required|string|max:50|unique:commandes,num_cmd',
            'nom_patient' => 'required|string|max:255',
            'urgent' => 'boolean',
            'commentaire' => 'nullable|string',
            'taches' => 'required|array|min:1',
            'taches.*.groupe_id' => 'required|exists:groupes,id',
            'taches.*.service_id' => 'required|exists:services,id',
            'taches.*.nb_elem' => 'required|integer|min:1',
            'taches.*.teinte' => 'nullable|string|max:100',
            'taches.*.date_livraison' => 'required|date',
        ];
    }
}
```

### `app/Http/Requests/StoreCommandeFilesRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommandeFilesRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->can('upload_commande_files');
    }

    public function rules()
    {
        $type = request('type');

        return [
            'type' => 'required|in:empreinte,image',
            'files' => 'required|array',
            'files.*' => $type === 'empreinte' 
                ? 'file|max:50000|mimes:stl,jpy'
                : 'file|max:5000|mimes:png,jpg,jpeg',
        ];
    }
}
```

---

## 6️⃣ CONTROLLERS

### `app/Http/Controllers/Admin/CommandeController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommandeRequest;
use App\Models\Commande;
use App\Models\User;
use App\Models\Groupe;
use App\Models\Service;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class CommandeController extends Controller
{
    public function index()
    {
        $commandes = Cache::remember('admin.commandes.list', 120, function () {
            return Commande::with(['dentiste', 'taches'])
                ->latest()
                ->paginate(25);
        });

        return view('admin.commandes.index', compact('commandes'));
    }

    public function create()
    {
        $dentistes = User::role('dentist')->pluck('nom', 'id');
        $groupes = Groupe::pluck('nom', 'id');
        $services = Service::pluck('nom', 'id');

        return view('admin.commandes.create', compact('dentistes', 'groupes', 'services'));
    }

    public function store(StoreCommandeRequest $request)
    {
        $commande = Commande::create($request->validated());

        foreach ($request->input('taches', []) as $tacheData) {
            $tacheData['date_livraison'] = $this->calculateDeliveryDate(
                $tacheData['date_livraison'] ?? null,
                $request->input('urgent', false)
            );
            $commande->taches()->create($tacheData);
        }

        Cache::forget('admin.commandes.list');

        return redirect()->route('admin.commandes.show', $commande)
            ->with('success', 'Commande créée avec succès');
    }

    public function show(Commande $commande)
    {
        $commande = Cache::remember("admin.commandes.show.{$commande->id}", 300, function () use ($commande) {
            return $commande->load(['dentiste', 'taches', 'files']);
        });

        return view('admin.commandes.show', compact('commande'));
    }

    public function edit(Commande $commande)
    {
        $dentistes = User::role('dentist')->pluck('nom', 'id');
        $groupes = Groupe::pluck('nom', 'id');
        $services = Service::pluck('nom', 'id');

        return view('admin.commandes.edit', compact('commande', 'dentistes', 'groupes', 'services'));
    }

    public function update(StoreCommandeRequest $request, Commande $commande)
    {
        $commande->update($request->validated());

        $commande->taches()->delete();
        foreach ($request->input('taches', []) as $tacheData) {
            $tacheData['date_livraison'] = $this->calculateDeliveryDate(
                $tacheData['date_livraison'] ?? null,
                $request->input('urgent', false)
            );
            $commande->taches()->create($tacheData);
        }

        Cache::forget('admin.commandes.list');
        Cache::forget("admin.commandes.show.{$commande->id}");

        return redirect()->route('admin.commandes.show', $commande)
            ->with('success', 'Commande mise à jour');
    }

    public function destroy(Commande $commande)
    {
        $commande->delete();
        Cache::forget('admin.commandes.list');
        Cache::forget("admin.commandes.show.{$commande->id}");

        return redirect()->route('admin.commandes.index')
            ->with('success', 'Commande supprimée');
    }

    private function calculateDeliveryDate(?string $baseDate, bool $urgent): string
    {
        if ($urgent) {
            return Carbon::now()->addDay()->toDateString();
        }

        if ($baseDate) {
            return Carbon::parse($baseDate)->toDateString();
        }

        return Carbon::now()->addDays(3)->toDateString();
    }
}
```

### `app/Http/Controllers/Admin/CommandeFileController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommandeFilesRequest;
use App\Models\Commande;
use App\Models\CommandeFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class CommandeFileController extends Controller
{
    public function store(StoreCommandeFilesRequest $request, Commande $commande)
    {
        foreach ($request->file('files', []) as $file) {
            $path = $file->store("commandes/{$commande->id}", 'public');

            CommandeFile::create([
                'commande_id' => $commande->id,
                'type' => $request->input('type'),
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime' => $file->getMimeType(),
                'size' => $file->getSize(),
                'uploaded_by' => auth()->id(),
            ]);
        }

        Cache::forget("admin.commandes.show.{$commande->id}");

        return back()->with('success', 'Fichiers uploadés avec succès');
    }

    public function destroy(Commande $commande, CommandeFile $file)
    {
        Storage::disk('public')->delete($file->path);
        $file->delete();

        Cache::forget("admin.commandes.show.{$commande->id}");

        return back()->with('success', 'Fichier supprimé');
    }
}
```

---

## 7️⃣ ROUTES

### `routes/web.php` - Admin Commandes Block

```php
// 🔐 ADMIN ROUTES (sous auth + admin.access)
Route::middleware(['auth', 'admin.access'])->prefix('admin')->group(function () {
    
    // ... autres routes admin ...

    // Commandes
    Route::prefix('commandes')->group(function () {
        Route::get('/', [Admin\CommandeController::class, 'index'])
            ->name('admin.commandes.index')
            ->middleware('can:view_commandes');

        Route::get('/create', [Admin\CommandeController::class, 'create'])
            ->name('admin.commandes.create')
            ->middleware('can:create_commandes');

        Route::post('/', [Admin\CommandeController::class, 'store'])
            ->name('admin.commandes.store')
            ->middleware('can:create_commandes');

        Route::get('/{commande}', [Admin\CommandeController::class, 'show'])
            ->name('admin.commandes.show')
            ->middleware('can:view_commandes');

        Route::get('/{commande}/edit', [Admin\CommandeController::class, 'edit'])
            ->name('admin.commandes.edit')
            ->middleware('can:edit_commandes');

        Route::patch('/{commande}', [Admin\CommandeController::class, 'update'])
            ->name('admin.commandes.update')
            ->middleware('can:edit_commandes');

        Route::delete('/{commande}', [Admin\CommandeController::class, 'destroy'])
            ->name('admin.commandes.destroy')
            ->middleware('can:delete_commandes');

        // Files
        Route::post('/{commande}/files', [Admin\CommandeFileController::class, 'store'])
            ->name('admin.commandes.files.store')
            ->middleware('can:upload_commande_files');

        Route::delete('/{commande}/files/{file}', [Admin\CommandeFileController::class, 'destroy'])
            ->name('admin.commandes.files.destroy')
            ->middleware('can:delete_commande_files');
    });
});
```

---

## 8️⃣ CACHE KEYS

### Ajouter à l'app

```php
// Durées de cache Commandes
'admin.commandes.list'          // 120 sec (2 min)
'admin.commandes.show.{id}'     // 300 sec (5 min)
```

**Invalidation :**
- Après create/update/delete commande : `Cache::forget('admin.commandes.list')`
- Après create/update/delete tâche : `Cache::forget("admin.commandes.show.{id}")`
- Après upload/delete fichier : `Cache::forget("admin.commandes.show.{id}")`

---

## 9️⃣ TESTS

### `tests/Feature/Admin/CommandesTest.php`

```php
<?php

namespace Tests\Feature\Admin;

use App\Models\Commande;
use App\Models\User;
use App\Models\Groupe;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommandesTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_commandes()
    {
        $user = User::factory()->create();
        $user->assignRole('employer');

        $response = $this->actingAs($user)->get('/admin/commandes');
        $response->assertStatus(404);
    }

    public function test_admin_can_view_commandes()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $admin->givePermissionTo('view_commandes');

        $response = $this->actingAs($admin)->get('/admin/commandes');
        $response->assertStatus(200);
    }

    public function test_create_commande_with_valid_data()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $dentiste = User::factory()->create();
        $dentiste->assignRole('dentist');
        $groupe = Groupe::factory()->create();
        $service = Service::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/commandes', [
            'dentiste_id' => $dentiste->id,
            'num_cmd' => 'CMD-001-2026',
            'nom_patient' => 'Ahmed Ben Ali',
            'urgent' => false,
            'taches' => [
                [
                    'groupe_id' => $groupe->id,
                    'service_id' => $service->id,
                    'nb_elem' => 2,
                    'teinte' => 'blanc',
                    'date_livraison' => now()->addDays(3)->toDateString(),
                ]
            ]
        ]);

        $this->assertDatabaseHas('commandes', ['nom_patient' => 'Ahmed Ben Ali']);
    }

    public function test_num_cmd_must_be_unique()
    {
        Commande::factory()->create(['num_cmd' => 'CMD-001']);

        $response = $this->post('/admin/commandes', [
            'num_cmd' => 'CMD-001',
            // ...
        ]);

        $response->assertSessionHasErrors('num_cmd');
    }

    public function test_urgent_sets_delivery_date_to_tomorrow()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $dentiste = User::factory()->create();
        $dentiste->assignRole('dentist');
        $groupe = Groupe::factory()->create();
        $service = Service::factory()->create();

        $this->actingAs($admin)->post('/admin/commandes', [
            'dentiste_id' => $dentiste->id,
            'num_cmd' => 'CMD-URGENT',
            'nom_patient' => 'Test Urgent',
            'urgent' => true,
            'taches' => [
                [
                    'groupe_id' => $groupe->id,
                    'service_id' => $service->id,
                    'nb_elem' => 1,
                    'date_livraison' => now()->addDays(10)->toDateString(),
                ]
            ]
        ]);

        $commande = Commande::where('nom_patient', 'Test Urgent')->first();
        $this->assertTrue(
            $commande->taches->first()->date_livraison->isToday() || 
            $commande->taches->first()->date_livraison->isTomorrow()
        );
    }
}
```

**Exécuter les tests :**
```bash
php artisan test tests/Feature/Admin/CommandesTest.php
```

---

## 🔟 COMMANDES ARTISAN

```bash
# Créer Middleware
php artisan make:middleware CheckAdminAccess

# Créer Models
php artisan make:model Commande -m
php artisan make:model CommandeTache -m
php artisan make:model CommandeFile -m

# Créer Requests
php artisan make:request StoreCommandeRequest
php artisan make:request StoreCommandeFilesRequest

# Créer Controllers
php artisan make:controller Admin/CommandeController --resource
php artisan make:controller Admin/CommandeFileController

# Migrer
php artisan migrate

# Seeder
php artisan db:seed --class=RolePermissionSeeder

# Clear caches
php artisan cache:clear
php artisan config:cache
php artisan route:cache

# Tests
php artisan test tests/Feature/Admin/CommandesTest.php
```

---

## ✅ CHECKLIST IMPLÉMENTATION

### Phase 1 : Setup (1 jour)
- [ ] Créer CheckAdminAccess middleware + enregistrer
- [ ] Mettre à jour Seeder permissions (renomage Orders → Commandes)
- [ ] Créer migrations (commandes, taches, files)
- [ ] Créer Models (Commande, CommandeTache, CommandeFile)

### Phase 2 : Logic (2 jours)
- [ ] Créer Form Requests (StoreCommandeRequest, StoreCommandeFilesRequest)
- [ ] Créer Controllers (CommandeController, CommandeFileController)
- [ ] Mettre à jour routes `/admin/commandes*`
- [ ] Ajouter cache keys + invalidation

### Phase 3 : Frontend (1 jour)
- [ ] Créer views index/create/edit/show
- [ ] Tâches répétables (formulaire dynamique avec "+" bouton)
- [ ] Urgent switch (recalcule dates)
- [ ] Upload fichiers (empreintes + images)

### Phase 4 : Tests & Deployment (1-2 jours)
- [ ] Écrire Feature tests (CommandesTest)
- [ ] Tester permissions granulaires
- [ ] Tester urgent recalcul dates
- [ ] Tester validations fichiers
- [ ] Deploy + seed roles/permissions

---

**Version :** 2.0 Implementation Guide
**Date :** 8 janvier 2026
**Estimation :** 5-7 jours (dev + tests + deploy)

---

## Détails V3 (contenu source)
# GUIDE D'IMPLÉMENTATION – V3
## Calendrier Commandes + Prix par dentiste + Snapshot + BL
### Quick Start pour développeurs

**Version :** 3.0  
**Date :** 9 janvier 2026

---

## 0) Rappels règles V3
- **Admin + (responsable/secretaire/prothesiste)** : voient toutes commandes + toutes tâches modal.
- **Employer** : voit seulement commandes liées à son groupe + modal filtrée par groupe.
- **Dentist** : voit ses commandes, ne change pas statut.
- **Prix** : défaut service + override par dentiste, snapshot dans `commande_taches` DECIMAL(10,2).
- **BL** : auto-généré à statut Terminée, TTC uniquement.

---

## 1) Migrations

### 1.1 services (prix défaut)
```bash
php artisan make:migration add_prix_to_services_table
```

Contenu :
```php
public function up()
{
    Schema::table('services', function (Blueprint $table) {
        $table->decimal('prix_unitaire_ttc', 10, 2)->default(0)->after('nom');
    });
}

public function down()
{
    Schema::table('services', function (Blueprint $table) {
        $table->dropColumn('prix_unitaire_ttc');
    });
}
```

### 1.2 dentist_service_prices (override)
```bash
php artisan make:migration create_dentist_service_prices_table
```

Contenu :
```php
public function up()
{
    Schema::create('dentist_service_prices', function (Blueprint $table) {
        $table->id();
        $table->foreignId('dentist_id')->constrained('users')->cascadeOnDelete();
        $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
        $table->decimal('prix_unitaire_ttc', 10, 2)->notNullable();
        $table->timestamps();
        
        $table->unique(['dentist_id', 'service_id']);
        $table->index('dentist_id');
        $table->index('service_id');
    });
}

public function down()
{
    Schema::dropIfExists('dentist_service_prices');
}
```

### 1.3 commande_taches (snapshot)
```bash
php artisan make:migration add_snapshot_to_commande_taches_table
```

Contenu :
```php
public function up()
{
    Schema::table('commande_taches', function (Blueprint $table) {
        $table->decimal('prix_unitaire_ttc_snapshot', 10, 2)->default(0)->after('nb_elem');
        $table->decimal('total_ligne_ttc', 10, 2)->default(0)->after('prix_unitaire_ttc_snapshot');
    });
}

public function down()
{
    Schema::table('commande_taches', function (Blueprint $table) {
        $table->dropColumn(['prix_unitaire_ttc_snapshot', 'total_ligne_ttc']);
    });
}
```

### 1.4 bons_livraison
```bash
php artisan make:migration create_bons_livraison_table
```

Contenu :
```php
public function up()
{
    Schema::create('bons_livraison', function (Blueprint $table) {
        $table->id();
        $table->foreignId('commande_id')->unique()->constrained('commandes')->cascadeOnDelete();
        $table->string('numero_bl')->unique();
        $table->decimal('total_ttc', 10, 2)->nullable();
        $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamps();
        
        $table->index('commande_id');
    });
}

public function down()
{
    Schema::dropIfExists('bons_livraison');
}
```

### 1.5 bon_livraison_lignes
```bash
php artisan make:migration create_bon_livraison_lignes_table
```

Contenu :
```php
public function up()
{
    Schema::create('bon_livraison_lignes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('bon_livraison_id')->constrained('bons_livraison')->cascadeOnDelete();
        $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
        $table->string('service_name_snapshot');
        $table->decimal('prix_unitaire_ttc_snapshot', 10, 2)->notNullable();
        $table->unsignedInteger('quantite')->default(1);
        $table->decimal('total_ligne_ttc', 10, 2)->notNullable();
        $table->timestamps();
        
        $table->index('bon_livraison_id');
    });
}

public function down()
{
    Schema::dropIfExists('bon_livraison_lignes');
}
```

Exécuter :
```bash
php artisan migrate
```

---

## 2) Permissions (Seeder)

### Mettre à jour `database/seeders/RolePermissionSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        cache()->forget('spatie.permission.cache');

        // Ajouter permissions V3
        $newPermissions = [
            'view_commandes_calendar',
            'view_commande_details',
            'change_commande_status',
            'manage_service_pricing',
            'view_bons_livraison',
            'print_bons_livraison',
        ];

        foreach ($newPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Admin = toutes
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        // Responsable / Secrétaire / Prothésiste
        foreach (['responsable', 'secretaire', 'prothesiste'] as $role) {
            $r = Role::firstOrCreate(['name' => $role]);
            $r->syncPermissions([
                'view_commandes_calendar',
                'view_commande_details',
                'change_commande_status',
                'manage_service_pricing',
                'view_bons_livraison',
                'print_bons_livraison',
            ]);
        }

        // Employer
        $employer = Role::firstOrCreate(['name' => 'employer']);
        $employer->syncPermissions([
            'view_commandes_calendar',
            'view_commande_details',
            'view_bons_livraison',
        ]);

        // Dentist
        $dentist = Role::firstOrCreate(['name' => 'dentist']);
        $dentist->syncPermissions([
            'view_commandes_calendar',
            'view_commande_details',
            'view_bons_livraison',
        ]);

        cache()->forget('spatie.permission.cache');
    }
}
```

Exécuter :
```bash
php artisan db:seed --class=RolePermissionSeeder
php artisan cache:clear
```

---

## 3) Models

### 3.1 `app/Models/DentistServicePrice.php` (NEW)
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DentistServicePrice extends Model
{
    protected $table = 'dentist_service_prices';
    protected $fillable = ['dentist_id', 'service_id', 'prix_unitaire_ttc'];
    protected $casts = [
        'prix_unitaire_ttc' => 'float',
    ];

    public function dentist()
    {
        return $this->belongsTo(User::class, 'dentist_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
```

### 3.2 `app/Models/BonLivraison.php` (NEW)
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonLivraison extends Model
{
    protected $table = 'bons_livraison';
    protected $fillable = ['commande_id', 'numero_bl', 'total_ttc', 'created_by'];
    protected $casts = [
        'total_ttc' => 'float',
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    public function lignes()
    {
        return $this->hasMany(BonLivraisonLigne::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
```

### 3.3 `app/Models/BonLivraisonLigne.php` (NEW)
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonLivraisonLigne extends Model
{
    protected $table = 'bon_livraison_lignes';
    protected $fillable = [
        'bon_livraison_id', 'service_id', 'service_name_snapshot',
        'prix_unitaire_ttc_snapshot', 'quantite', 'total_ligne_ttc'
    ];
    protected $casts = [
        'prix_unitaire_ttc_snapshot' => 'float',
        'total_ligne_ttc' => 'float',
    ];

    public function bonLivraison()
    {
        return $this->belongsTo(BonLivraison::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
```

### 3.4 Mettre à jour `app/Models/Commande.php`
```php
// Ajouter au modèle existant
public function bonLivraison()
{
    return $this->hasOne(BonLivraison::class);
}
```

---

## 4) Services

### 4.1 `app/Services/ServicePricingResolver.php` (NEW)
```php
<?php

namespace App\Services;

use App\Models\DentistServicePrice;
use App\Models\Service;

class ServicePricingResolver
{
    public function resolvePriceTtc(int $dentistId, int $serviceId): float
    {
        // Chercher override
        $override = DentistServicePrice::where('dentist_id', $dentistId)
            ->where('service_id', $serviceId)
            ->first();

        if ($override) {
            return (float) $override->prix_unitaire_ttc;
        }

        // Retourner prix défaut
        $service = Service::find($serviceId);
        return (float) ($service->prix_unitaire_ttc ?? 0);
    }
}
```

### 4.2 `app/Services/BonLivraisonService.php` (NEW)
```php
<?php

namespace App\Services;

use App\Models\BonLivraison;
use App\Models\BonLivraisonLigne;
use App\Models\Commande;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class BonLivraisonService
{
    public function generateFromCommande(Commande $commande)
    {
        // Idempotent : si BL existe, ne pas recréer
        if ($commande->bonLivraison) {
            return $commande->bonLivraison;
        }

        $bl = BonLivraison::create([
            'commande_id' => $commande->id,
            'numero_bl' => $this->generateNumberBl(),
            'created_by' => Auth::id(),
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

## 5) Controllers (V3)

### 5.1 `app/Http/Controllers/App/CommandeCalendarController.php` (NEW)
```php
<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CommandeCalendarController extends Controller
{
    public function index()
    {
        return view('app.commandes.calendar');
    }

    public function events(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');
        $user = auth()->user();

        $cacheKey = "app.commandes.calendar.events.{$user->id}.{$start}.{$end}";

        $events = Cache::remember($cacheKey, 120, function () use ($start, $end, $user) {
            $query = Commande::with('taches')
                ->whereHas('taches', function ($q) use ($start, $end) {
                    $q->whereBetween('date_livraison', [$start, $end]);
                });

            // Filtrage rôle
            if ($user->hasRole('employer')) {
                $query->whereHas('taches', function ($q) use ($user) {
                    $q->where('groupe_id', $user->groupe_id);
                });
            } elseif ($user->hasRole('dentist')) {
                $query->where('dentiste_id', $user->id);
            }

            return $query->get();
        });

        return response()->json($events->map(fn ($c) => [
            'id' => $c->id,
            'title' => "CMD #{$c->num_cmd}",
            'date' => $c->taches->first()?->date_livraison,
            'commande_id' => $c->id,
        ]));
    }
}
```

### 5.2 `app/Http/Controllers/App/CommandeStatusController.php` (NEW)
```php
<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Services\BonLivraisonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CommandeStatusController extends Controller
{
    public function update(Request $request, Commande $commande)
    {
        $this->authorize('change_commande_status', $commande);

        $oldStatus = $commande->status;
        $newStatus = $request->input('status');

        $commande->update(['status' => $newStatus]);

        // Générer BL si passage à Terminée
        if ($newStatus === 'Terminée' && $oldStatus !== 'Terminée') {
            app(BonLivraisonService::class)->generateFromCommande($commande);
        }

        // Invalider caches
        Cache::forget("app.commandes.modal.{$commande->id}." . auth()->id());
        Cache::forget("app.commandes.calendar.events.*");

        return redirect()->back()->with('success', 'Statut mis à jour');
    }
}
```

### 5.3 `app/Http/Controllers/App/DentistPricingController.php` (NEW)
```php
<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\DentistServicePrice;
use App\Models\User;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DentistPricingController extends Controller
{
    public function index()
    {
        $dentists = User::role('dentist')->get();
        $services = Service::all();
        $prices = DentistServicePrice::all();

        return view('app.pricing.index', compact('dentists', 'services', 'prices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'dentist_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'prix_unitaire_ttc' => 'required|numeric|min:0',
        ]);

        DentistServicePrice::updateOrCreate(
            ['dentist_id' => $validated['dentist_id'], 'service_id' => $validated['service_id']],
            ['prix_unitaire_ttc' => $validated['prix_unitaire_ttc']]
        );

        Cache::forget("pricing.dentist_service.{$validated['dentist_id']}.{$validated['service_id']}");

        return redirect()->back()->with('success', 'Prix sauvegardé');
    }

    public function destroy(DentistServicePrice $row)
    {
        $dentistId = $row->dentist_id;
        $serviceId = $row->service_id;

        $row->delete();

        Cache::forget("pricing.dentist_service.{$dentistId}.{$serviceId}");

        return redirect()->back()->with('success', 'Prix supprimé');
    }
}
```

### 5.4 `app/Http/Controllers/App/BonLivraisonController.php` (NEW)
```php
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
            return $bl->load(['commande', 'lignes']);
        });

        return view('app.bl.show', compact('bl'));
    }

    public function print(BonLivraison $bl)
    {
        $this->authorize('print_bons_livraison');

        $bl = $bl->load(['commande', 'lignes']);

        return view('app.bl.print', compact('bl'));
    }
}
```

---

## 6) Routes

Ajouter à `routes/web.php` :

```php
// /app routes (interne permissionné)
Route::middleware(['auth'])->prefix('app')->group(function () {

    // Calendrier
    Route::get('commandes/calendar', [App\CommandeCalendarController::class, 'index'])
        ->middleware('can:view_commandes_calendar')
        ->name('app.commandes.calendar');

    Route::get('commandes/calendar/events', [App\CommandeCalendarController::class, 'events'])
        ->middleware('can:view_commandes_calendar')
        ->name('app.commandes.calendar.events');

    // Modal show
    Route::get('commandes/{commande}', [Admin\CommandeController::class, 'show'])
        ->middleware('can:view_commande_details')
        ->name('app.commandes.show');

    // Statut
    Route::patch('commandes/{commande}/status', [App\CommandeStatusController::class, 'update'])
        ->middleware('can:change_commande_status')
        ->name('app.commandes.status.update');

    // Pricing
    Route::get('pricing', [App\DentistPricingController::class, 'index'])
        ->middleware('can:manage_service_pricing')
        ->name('app.pricing.index');

    Route::post('pricing', [App\DentistPricingController::class, 'store'])
        ->middleware('can:manage_service_pricing')
        ->name('app.pricing.store');

    Route::delete('pricing/{row}', [App\DentistPricingController::class, 'destroy'])
        ->middleware('can:manage_service_pricing')
        ->name('app.pricing.destroy');

    // BL
    Route::get('bons-livraison/{bl}', [App\BonLivraisonController::class, 'show'])
        ->middleware('can:view_bons_livraison')
        ->name('app.bl.show');

    Route::get('bons-livraison/{bl}/print', [App\BonLivraisonController::class, 'print'])
        ->middleware('can:print_bons_livraison')
        ->name('app.bl.print');
});
```

---

## 7) Snapshot lors création commande

Mettre à jour `app/Http/Controllers/Admin/CommandeController.php` -> méthode `store()` :

```php
public function store(StoreCommandeRequest $request)
{
    $commande = Commande::create($request->validated());

    $resolver = app(ServicePricingResolver::class);

    foreach ($request->input('taches', []) as $tacheData) {
        $tacheData['date_livraison'] = $this->calculateDeliveryDate(
            $tacheData['date_livraison'] ?? null,
            $request->input('urgent', false)
        );

        // Résoudre prix + snapshot
        $precio = $resolver->resolvePriceTtc(
            $commande->dentiste_id,
            $tacheData['service_id']
        );

        $tacheData['prix_unitaire_ttc_snapshot'] = $precio;
        $tacheData['total_ligne_ttc'] = $precio * $tacheData['nb_elem'];

        $commande->taches()->create($tacheData);
    }

    Cache::forget('admin.commandes.list');

    return redirect()->route('admin.commandes.show', $commande)
        ->with('success', 'Commande créée avec succès');
}
```

---

## 8) Checklist implémentation V3

### Phase 1 : DB (1 jour)
- [ ] Créer 5 migrations (services prix, override, snapshot, BL, lignes BL)
- [ ] Exécuter `php artisan migrate`
- [ ] Mettre à jour seeder permissions + exécuter

### Phase 2 : Code (2 jours)
- [ ] Créer Models (DentistServicePrice, BonLivraison, BonLivraisonLigne)
- [ ] Créer Services (ServicePricingResolver, BonLivraisonService)
- [ ] Créer Controllers (CommandeCalendarController, CommandeStatusController, DentistPricingController, BonLivraisonController)
- [ ] Ajouter routes `/app/*`
- [ ] Mettre à jour Commande controller pour snapshot

### Phase 3 : Frontend (2 jours)
- [ ] Calendar page + FullCalendar JS integration
- [ ] Modal details (filtre employer si besoin)
- [ ] Pricing management UI
- [ ] BL show + print

### Phase 4 : Tests (1 jour)
- [ ] Feature tests (permissions, visibilité, snapshot, BL)
- [ ] Tests permissions granulaires
- [ ] Deploy + seed

---

**Estimation totale :** 6-8 jours (dev + tests + deploy)

**Fin Implementation Guide V3**

---

**Fin Implementation Guide FINAL.**

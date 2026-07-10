<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    DashboardController,
    UserController,
    RoleController,
    PermissionController,
    CommandeController,
    CommandeFileController,
    ServicePricingController,
    DentistPricingController,
    FactureController,
    EcheanceController,
    ConfigController,
    GroupeController,
    CritereQualityController,
    DepenseController,
    StockController,
    VitrineController as AdminVitrineController,
    ContactMessageController as AdminContactMessageController
};
use App\Http\Controllers\App\{
    CommandeCalendarController,
    CommandeStatusController,
    BonLivraisonController
};
use App\Http\Controllers\VitrineController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\Auth\DentistRegistrationController;

Route::get('/', [VitrineController::class, 'show'])->name('vitrine');
Route::post('/contact', [ContactMessageController::class, 'store'])->name('contact.store');
Route::middleware(['guest', 'throttle:6,1'])->group(function () {
    Route::get('/register', [DentistRegistrationController::class, 'create'])->name('register');
    Route::post('/register', [DentistRegistrationController::class, 'store'])->name('register.store');
});
Route::get('/services', [VitrineController::class, 'services'])->name('vitrine.services');
Route::get('/services/{slug}', [VitrineController::class, 'serviceShow'])->name('vitrine.services.show');
Route::get('/process', [VitrineController::class, 'process'])->name('vitrine.process');
Route::get('/gallery', [VitrineController::class, 'gallery'])->name('vitrine.gallery');
Route::get('/about', [VitrineController::class, 'about'])->name('vitrine.about');
Route::get('/laboratoire', [VitrineController::class, 'laboratory'])->name('vitrine.laboratory');
Route::get('/academy', [VitrineController::class, 'academy'])->name('vitrine.academy');
Route::get('/academy/documents', [VitrineController::class, 'academyDocuments'])->name('vitrine.academy.documents');
Route::get('/faq', [VitrineController::class, 'faq'])->name('vitrine.faq');

Route::get('/dashboard', [DashboardController::class, 'userDashboard'])
    ->middleware('auth')
    ->name('dashboard');

// 🔐 ADMIN ROUTES (sous auth + admin.access)
Route::middleware(['auth', 'admin.access'])->prefix('admin')->group(function () {
    
    // Gestion utilisateurs
    Route::resource('users', UserController::class)
        ->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy'])
        ->names([
            'index' => 'admin.users.index',
            'show' => 'admin.users.show',
            'create' => 'admin.users.create',
            'store' => 'admin.users.store',
            'edit' => 'admin.users.edit',
            'update' => 'admin.users.update',
            'destroy' => 'admin.users.destroy',
        ])
        ->middleware('can:view_users');
    
    // Gestion dentistes
    Route::get('dentists', [UserController::class, 'dentists'])
        ->name('admin.dentists.index')
        ->middleware('can:view_users');
    
    Route::get('dentists/create', [UserController::class, 'createDentist'])
        ->name('admin.dentists.create')
        ->middleware('can:create_users');
    
    Route::post('dentists', [UserController::class, 'storeDentist'])
        ->name('admin.dentists.store')
        ->middleware('can:create_users');
    
    Route::get('dentists/{user}', [UserController::class, 'showDentist'])
        ->name('admin.dentists.show')
        ->middleware('can:view_users');
    
    Route::get('dentists/{user}/edit', [UserController::class, 'editDentist'])
        ->name('admin.dentists.edit')
        ->middleware('can:edit_users');
    
    Route::patch('dentists/{user}', [UserController::class, 'updateDentist'])
        ->name('admin.dentists.update')
        ->middleware('can:edit_users');

    Route::get('dentists/{user}/commandes', [CommandeController::class, 'dentistCommandes'])
        ->name('admin.dentists.commandes.index')
        ->middleware('can:view_commandes');

    Route::get('dentists/{user}/factures', [FactureController::class, 'dentistFactures'])
        ->name('admin.dentists.factures.index')
        ->middleware('can:view_factures');

    // Gestion équipes
    Route::get('teams', [UserController::class, 'teams'])
        ->name('admin.teams.index')
        ->middleware('can:view_users');
    
    Route::get('teams/create', [UserController::class, 'createTeam'])
        ->name('admin.teams.create')
        ->middleware('can:create_users');
    
    Route::post('teams', [UserController::class, 'storeTeam'])
        ->name('admin.teams.store')
        ->middleware('can:create_users');
    
    Route::get('teams/{user}', [UserController::class, 'showTeam'])
        ->name('admin.teams.show')
        ->middleware('can:view_users');
    
    Route::get('teams/{user}/edit', [UserController::class, 'editTeam'])
        ->name('admin.teams.edit')
        ->middleware('can:edit_users');
    
    Route::patch('teams/{user}', [UserController::class, 'updateTeam'])
        ->name('admin.teams.update')
        ->middleware('can:edit_users');
    
    // Gestion rôles
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])
            ->name('admin.roles.index');
        
        Route::get('/create', [RoleController::class, 'create'])
            ->name('admin.roles.create')
            ->middleware('can:manage_permissions');
        
        Route::post('/', [RoleController::class, 'store'])
            ->name('admin.roles.store')
            ->middleware('can:manage_permissions');
        
        Route::get('{role}', [RoleController::class, 'show'])
            ->name('admin.roles.show')
            ->middleware('can:view_roles');
        
        Route::get('{role}/edit', [RoleController::class, 'edit'])
            ->name('admin.roles.edit')
            ->middleware('can:manage_permissions');
        
        Route::patch('{role}', [RoleController::class, 'update'])
            ->name('admin.roles.update')
            ->middleware('can:manage_permissions');
        
        Route::delete('{role}', [RoleController::class, 'destroy'])
            ->name('admin.roles.destroy')
            ->middleware('can:manage_permissions');
    });
    
    // Gestion permissions
    Route::get('permissions', [PermissionController::class, 'index'])
        ->name('admin.permissions.index')
        ->middleware('can:manage_permissions');

    // Messages contact (formulaire vitrine)
    Route::prefix('contact-messages')->group(function () {
        Route::get('/', [AdminContactMessageController::class, 'index'])
            ->name('admin.contact-messages.index');

        Route::get('{contactMessage}/attachment', [AdminContactMessageController::class, 'downloadAttachment'])
            ->name('admin.contact-messages.attachment');

        Route::delete('{contactMessage}', [AdminContactMessageController::class, 'destroy'])
            ->name('admin.contact-messages.destroy');
    });

    // Site vitrine
    Route::prefix('vitrine')->group(function () {
        Route::get('/', [AdminVitrineController::class, 'index'])
            ->name('admin.vitrine.index')
            ->middleware('can:manage_vitrine');

        Route::patch('{vitrineBlock}', [AdminVitrineController::class, 'update'])
            ->name('admin.vitrine.update')
            ->middleware('can:manage_vitrine');
    });

    // Configuration - Protégé par permission
    Route::get('config', [ConfigController::class, 'index'])
        ->name('admin.config.index')
        ->middleware('can:manage_config');

    // Groupes - Protégé par permission
    Route::prefix('groupes')->group(function () {
        Route::post('/', [GroupeController::class, 'store'])
            ->name('admin.groupes.store')
            ->middleware('can:manage_config');

        Route::patch('{groupe}', [GroupeController::class, 'update'])
            ->name('admin.groupes.update')
            ->middleware('can:manage_config');

        Route::delete('{groupe}', [GroupeController::class, 'destroy'])
            ->name('admin.groupes.destroy')
            ->middleware('can:manage_config');
    });

    // Critères qualité - Protégé par permission
    Route::prefix('criteres-quality')->group(function () {
        Route::post('/', [CritereQualityController::class, 'store'])
            ->name('admin.criteres-quality.store')
            ->middleware('can:manage_config');

        Route::patch('{critereQuality}', [CritereQualityController::class, 'update'])
            ->name('admin.criteres-quality.update')
            ->middleware('can:manage_config');

        Route::delete('{critereQuality}', [CritereQualityController::class, 'destroy'])
            ->name('admin.criteres-quality.destroy')
            ->middleware('can:manage_config');
    });

    // Commandes
    Route::prefix('commandes')->group(function () {
        Route::get('/', [CommandeController::class, 'index'])
            ->name('admin.commandes.index')
            ->middleware('can:view_commandes');

        Route::patch('/bulk-status', [CommandeController::class, 'bulkUpdateStatus'])
            ->name('admin.commandes.bulk-status')
            ->middleware('can:change_commande_status');

        Route::get('/create', [CommandeController::class, 'create'])
            ->name('admin.commandes.create')
            ->middleware('can:create_commandes');

        Route::post('/', [CommandeController::class, 'store'])
            ->name('admin.commandes.store')
            ->middleware('can:create_commandes');

        Route::get('/{commande}', [CommandeController::class, 'show'])
            ->name('admin.commandes.show')
            ->middleware('can:view_commandes');

        Route::get('/{commande}/edit', [CommandeController::class, 'edit'])
            ->name('admin.commandes.edit')
            ->middleware('can:edit_commandes');

        Route::patch('/{commande}', [CommandeController::class, 'update'])
            ->name('admin.commandes.update')
            ->middleware('can:edit_commandes');

        Route::delete('/{commande}', [CommandeController::class, 'destroy'])
            ->name('admin.commandes.destroy')
            ->middleware('can:delete_commandes');

        // Critères de qualité pour une tâche
        Route::get('/taches/{tache}/criteres', [CommandeController::class, 'getTacheCriteres'])
            ->name('admin.commandes.taches.criteres')
            ->middleware('can:view_fiche_controle_quality');
        
        Route::post('/taches/{tache}/fiche-controle-quality', [CommandeController::class, 'storeFicheControleQuality'])
            ->name('admin.commandes.taches.fiche.store');

        // Files
        Route::post('/{commande}/files', [CommandeFileController::class, 'store'])
            ->name('admin.commandes.files.store')
            ->middleware('can:upload_commande_files');

        Route::delete('/{commande}/files/{file}', [CommandeFileController::class, 'destroy'])
            ->name('admin.commandes.files.destroy')
            ->middleware('can:delete_commande_files');

        // Generate Bon de Livraison
        Route::post('/{commande}/generate-bl', [CommandeController::class, 'generateBonLivraison'])
            ->name('admin.commandes.generate-bl')
            ->middleware('can:view_bons_livraison');
    });

    // Services - Gestion des services
    Route::prefix('services')->group(function () {
        Route::get('/', [ServicePricingController::class, 'index'])
            ->name('admin.services.index')
            ->middleware('can:manage_service_pricing');

        Route::post('/', [ServicePricingController::class, 'store'])
            ->name('admin.services.store')
            ->middleware('can:manage_service_pricing');

        Route::patch('{service}', [ServicePricingController::class, 'update'])
            ->name('admin.services.update')
            ->middleware('can:manage_service_pricing');

        Route::delete('{service}', [ServicePricingController::class, 'destroy'])
            ->name('admin.services.destroy')
            ->middleware('can:manage_service_pricing');
    });

    // Stock — admin uniquement
    Route::prefix('stock')->group(function () {
        Route::get('/', [StockController::class, 'index'])
            ->name('admin.stock.index');

        Route::post('elements', [StockController::class, 'storeElement'])
            ->name('admin.stock.elements.store');

        Route::patch('elements/{element}', [StockController::class, 'updateElement'])
            ->name('admin.stock.elements.update');

        Route::delete('elements/{element}', [StockController::class, 'destroyElement'])
            ->name('admin.stock.elements.destroy');

        Route::post('lines', [StockController::class, 'storeStock'])
            ->name('admin.stock.lines.store');

        Route::patch('lines/{stock}', [StockController::class, 'updateStock'])
            ->name('admin.stock.lines.update');

        Route::delete('lines/{stock}', [StockController::class, 'destroyStock'])
            ->name('admin.stock.lines.destroy');
    });

    // Dépenses — admin uniquement
    Route::prefix('depenses')->group(function () {
        Route::get('/', [DepenseController::class, 'index'])
            ->name('admin.depenses.index');

        Route::post('/', [DepenseController::class, 'store'])
            ->name('admin.depenses.store');

        Route::patch('{depense}', [DepenseController::class, 'update'])
            ->name('admin.depenses.update');

        Route::delete('{depense}', [DepenseController::class, 'destroy'])
            ->name('admin.depenses.destroy');
    });

    // Factures
    Route::prefix('factures')->group(function () {
        Route::get('/', [FactureController::class, 'index'])
            ->name('admin.factures.index')
            ->middleware('can:view_factures');

        Route::post('/', [FactureController::class, 'store'])
            ->name('admin.factures.store')
            ->middleware('can:create_factures');

        Route::get('/bons-livraison', [FactureController::class, 'getBonsLivraison'])
            ->name('admin.factures.get-bons-livraison')
            ->middleware('can:view_factures');

        Route::get('/{facture}', [FactureController::class, 'show'])
            ->name('admin.factures.show')
            ->middleware('can:view_factures');

        Route::get('/{facture}/print', [FactureController::class, 'print'])
            ->name('admin.factures.print')
            ->middleware('can:view_factures');

        Route::get('/{facture}/edit', [FactureController::class, 'edit'])
            ->name('admin.factures.edit')
            ->middleware('can:edit_factures');

        Route::put('/{facture}', [FactureController::class, 'update'])
            ->name('admin.factures.update')
            ->middleware('can:edit_factures');

        Route::delete('/{facture}', [FactureController::class, 'destroy'])
            ->name('admin.factures.destroy')
            ->middleware('can:delete_factures');

        // Échéances de paiement
        Route::post('/{facture}/echeances', [EcheanceController::class, 'store'])
            ->name('admin.factures.echeances.store')
            ->middleware('can:edit_factures');

        Route::put('/{facture}/echeances/{echeance}', [EcheanceController::class, 'update'])
            ->name('admin.factures.echeances.update')
            ->middleware('can:edit_factures');

        Route::delete('/{facture}/echeances/{echeance}', [EcheanceController::class, 'destroy'])
            ->name('admin.factures.echeances.destroy')
            ->middleware('can:edit_factures');
    });

    // Pricing - Prix par dentiste (gardé pour compatibilité)
    Route::prefix('pricing')->group(function () {
        // Pricing par dentiste
        Route::prefix('dentists')->group(function () {
            Route::get('/', [DentistPricingController::class, 'index'])
                ->name('admin.pricing.dentists.index')
                ->middleware('can:manage_service_pricing');

            Route::post('/', [DentistPricingController::class, 'store'])
                ->name('admin.pricing.dentists.store')
                ->middleware('can:manage_service_pricing');

            Route::delete('{row}', [DentistPricingController::class, 'destroy'])
                ->name('admin.pricing.dentists.destroy')
                ->middleware('can:manage_service_pricing');
        });
    });
});

// /app routes (interne permissionné)
Route::middleware(['auth'])->prefix('app')->group(function () {

    // Calendrier
    Route::get('commandes/calendar', [CommandeCalendarController::class, 'index'])
        ->middleware('can:view_commandes_calendar')
        ->name('app.commandes.calendar');

    Route::get('commandes/calendar/events', [CommandeCalendarController::class, 'events'])
        ->middleware('can:view_commandes_calendar')
        ->name('app.commandes.calendar.events');

    Route::get('commandes/calendar/check-version', [CommandeCalendarController::class, 'checkVersion'])
        ->middleware('can:view_commandes_calendar')
        ->name('app.commandes.calendar.check-version');

    Route::get('commandes/calendar/export-excel', [CommandeCalendarController::class, 'exportExcel'])
        ->middleware('can:view_commandes_calendar')
        ->name('app.commandes.calendar.export-excel');

    Route::post('commandes/calendar/reorder', [CommandeCalendarController::class, 'reorder'])
        ->middleware('can:view_commandes_calendar')
        ->name('app.commandes.calendar.reorder');

    Route::get('commandes/check-new', [CommandeCalendarController::class, 'checkNew'])
        ->middleware('can:view_commandes')
        ->name('app.commandes.check-new');

    // Modal show (utilise Admin\CommandeController mais avec vue app)
    Route::get('commandes/{commande}', [\App\Http\Controllers\Admin\CommandeController::class, 'showApp'])
        ->middleware('can:view_commande_details')
        ->name('app.commandes.show');

    // Statut
    Route::patch('commandes/{commande}/status', [CommandeStatusController::class, 'update'])
        ->middleware('can:change_commande_status')
        ->name('app.commandes.status.update');

    // BL
    Route::get('bons-livraison/{bl}', [BonLivraisonController::class, 'show'])
        ->middleware('can:view_bons_livraison')
        ->name('app.bl.show');

    Route::get('bons-livraison/{bl}/print', [BonLivraisonController::class, 'print'])
        ->middleware('can:print_bons_livraison')
        ->name('app.bl.print');
});

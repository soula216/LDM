# CAHIER DES CHARGES FONCTIONNEL (CdCF) – V3 (Clean)
## Système de Gestion de Laboratoire de Prothèse Dentaire
### Admin caché + Cache + Module Commandes + Calendrier + Prix par dentiste + BL

**Version :** 3.0
**Date :** 9 janvier 2026
**Statut :** ✅ Spécifications consolidées

---

## Table des matières
- 1. Contexte & objectifs
- 2. Périmètre fonctionnel
  - 2.1 Admin caché (V2)
  - 2.2 Commandes & calendrier (V3)
  - 2.3 Bon de livraison (BL) (V3)
  - 2.4 Prix services & prix par dentiste (V3)
- 3. Rôles, permissions & visibilité
- 4. Workflow statuts commande
- 5. Routes & zones d’accès
- 6. Cache & performance
- 7. Sécurité & audit
- 8. Livrables

---

## 1. Contexte & objectifs
### 1.1 Base V2
Le système doit implémenter une interface d'administration complètement cachée et sécurisée (routes invisibles, menu dynamique par permissions, vérifications multi-couches, impossibilité d’accès en connaissant l’URL).

### 1.2 Objectifs V3
La V3 ajoute : calendrier des commandes basé sur dates de livraison, filtrage avancé par rôle (employer par groupe), changement de statut via modal, génération automatique BL à « Terminée », prix par défaut + override par dentiste, et snapshot TTC figé par tâche.

---

## 2. Périmètre fonctionnel

### 2.1 Admin caché (V2)
# CAHIER DES CHARGES FONCTIONNEL (CdCF)
## Système de Gestion de Laboratoire de Prothèse Dentaire - MODULE ADMIN CACHÉ
### Spécifications Fonctionnelles - Admin Cache & Permissions
---

## 1. CONTEXTE & OBJECTIFS

### 1.1 Vue d'ensemble
Le système doit implémenter une interface d'administration **complètement cachée et sécurisée** avec :
- ✅ Routes d'admin **invisibles** aux utilisateurs non-autorisés (404 redirect)
- ✅ Menu de navigation **dynamique** basé sur les permissions
- ✅ Système de **caching multi-couches** pour performance
- ✅ Vérification des permissions à **chaque niveau** (middleware → controller → view)
- ✅ Impossibilité d'accéder à l'admin même en connaissant l'URL

### 1.2 Principes de sécurité
- **Rule 1 :** Admin role complètement caché de la liste publique des utilisateurs
- **Rule 2 :** Routes /admin/* retournent 404 si utilisateur non-admin
- **Rule 3 :** Aucun indice sur l'existence du panneau admin dans le code source
- **Rule 4 :** Sidebar navigation **rerendue dynamiquement** basée sur les permissions réelles
- **Rule 5 :** Cache invalidé **automatiquement** après créations/modifications

---

## 2. FONCTIONNALITÉS - INTERFACE ADMIN CACHÉE

### 2.1 Authentification & Visibilité Admin

**Comportement attendu :**

| Scénario | Utilisateur | Comportement |
|----------|-------------|--------------|
| Accès /admin sans rôle | Employer / Dentist | `404 Page Not Found` |
| Accès /admin avec rôle admin | Admin | Dashboard complet + Menus |
| Accès /login | Tous | Formulaire login (sans indice admin) |
| Sidebar visible | Admin | Menu complet + Utilisateurs + Rôles + Permissions |
| Sidebar visible | Employer | Menu réduit + Profil uniquement |
| Tentative URL directe /admin/users | Non-admin | `404 DenyAsNotFound` |

### 2.2 Dashboard Admin Caché

**Accessible uniquement si :** `auth()->user()->hasRole('admin')`

**Fonctionnalités :**
- 📊 Statistiques globales (total utilisateurs, rôles, permissions)
- 📈 Graphique actifs/inactifs (soft deletes)
- 🔐 Dernières modifications de permissions (audit log optionnel)
- ⏱️ **Cache 5 min** : statistiques regénérées seulement si expiration
- 🔄 Invalidation automatique après user creation/deletion

**Exemple URL cachée :** `/admin/dashboard` → 404 si non-admin

### 2.3 Gestion des Utilisateurs (Admin only)

**Visibilité :**
- ❌ Admin users **NOT** affichés dans la liste utilisateurs
- ✅ Affichage des rôles **sans révéler l'admin**
- 🔍 Recherche filtre automatiquement admin users
- 📋 Pagination **cachée 2 min** après recherche

**Actions disponibles (si permission) :**
- Créer utilisateur (sans pouvoir donner le rôle admin)
- Modifier infos (sauf password)
- Soft delete utilisateur
- Assigner rôles (admin excluded)

### 2.4 Gestion des Rôles (Admin only)

**Visibilité :**
- ❌ Admin role **HIDDEN** de la liste des rôles
- ✅ Affichage des rôles : Responsable, Secrétaire, Coursier, Employer, Dentist
- 🔄 Permissions assignées aux rôles via UI Livewire
- ⏱️ **Cache rôles 10 min** pour performance requêtes

**Actions :**
- Voir rôles disponibles
- Modifier permissions d'un rôle
- **Impossible créer nouveau rôle** (admin only via seeder)

### 2.5 Gestion des Permissions (Admin only)

**Visibilité :**
- ✅ Affichage grid de toutes les permissions
- 🏗️ Catégories : User Management, Profile, Orders, etc.
- 🔐 Liaison rôle ↔ permissions visuellement claire
- ⏱️ **Cache permissions 5 min** (très critique)

**Actions :**
- Voir permissions assignées
- Modifier permissions/rôles
- **Impossible créer nouvelle permission** (admin only via migration)

### 2.6 Profil Personnel (Tous les utilisateurs)

**Accessible :** Tous les utilisateurs authentifiés

**Fonctionnalités :**
- 👤 Éditer nom, prénom, email, téléphone
- 🏠 Éditer adresse, gouvernorat, ville
- 🔐 Changer mot de passe
- ⏱️ **Cache profil 15 min** (user-specific)

---

## 3. CACHING STRATEGY

### 3.1 Cache Multi-niveaux

```
┌─────────────────────────────────────┐
│   User Request (Browser)            │
└────────────┬────────────────────────┘
             │
             ▼
┌─────────────────────────────────────┐
│   Route Middleware Check            │
│   - IsAdmin? IsAuthenticated?       │
│   ✓ Cache: 30 sec (permissions)     │
└────────────┬────────────────────────┘
             │
             ▼
┌─────────────────────────────────────┐
│   Controller / Livewire Render      │
│   - Permission verification         │
│   ✓ Cache: 5-15 min (data)          │
└────────────┬────────────────────────┘
             │
             ▼
┌─────────────────────────────────────┐
│   View Rendering (Blade)            │
│   - Menu visibility                 │
│   ✓ Cache: 2 min (nav structure)    │
└────────────┬────────────────────────┘
             │
             ▼
┌─────────────────────────────────────┐
│   Database Query (Last resort)      │
│   - Eager loading with constraints  │
└─────────────────────────────────────┘
```

### 3.2 Cache Keys

| Clé | Durée | Invalidation |
|-----|-------|--------------|
| `spatie.permission.cache` | 5 min | Après role/permission change |
| `admin.dashboard.stats` | 5 min | Après user create/delete |
| `admin.users.list` | 2 min | Après user modification |
| `admin.roles.index` | 10 min | Après role update |
| `admin.permissions.index` | 5 min | Après permission assign |
| `admin.nav.{user_id}` | 15 min | Après permission revoke |

### 3.3 Cache Invalidation Points

```php
// À exécuter automatiquement :

// User CRUD
→ Cache::forget('admin.users.list')
→ Cache::forget('admin.dashboard.stats')
→ Cache::forget('spatie.permission.cache')

// Role Update
→ Cache::forget('admin.roles.index')
→ Cache::forget('spatie.permission.cache')
→ Cache::forget("admin.nav.*") // Tous les users

// Permission Change
→ Cache::forget('admin.permissions.index')
→ Cache::forget('spatie.permission.cache')
→ Cache::forget("admin.nav.*") // Tous les users
```

---

## 4. ROUTES D'ADMIN CACHÉES

### 4.1 Routes protégées avec middleware+policy

```
GET    /admin/dashboard          → DashboardController@adminIndex
GET    /admin/users              → UserController@adminIndex
GET    /admin/users/create       → UserController@create
POST   /admin/users              → UserController@store
GET    /admin/users/{user}/edit  → UserController@edit
PATCH  /admin/users/{user}       → UserController@update
DELETE /admin/users/{user}       → UserController@destroy

GET    /admin/roles              → RoleController@index
GET    /admin/roles/{role}       → RoleController@show
GET    /admin/roles/{role}/edit  → RoleController@edit
PATCH  /admin/roles/{role}       → RoleController@update

GET    /admin/permissions        → PermissionController@index
```

### 4.2 Comportement 404 sécurisé

```php
// Middleware CheckAdminAccess
if (!$request->user()?->hasRole('admin')) {
    return response()->view('errors.404', [], 404);
    // Pas de redirect, vrai 404 pour masquer l'existence
}
```

---

## 5. SÉCURITÉ & ISOLATION

### 5.1 Checklist Sécurité Admin

- ✅ Admin users jamais affichés dans listes publiques
- ✅ Admin rôle jamais visible dans liste rôles
- ✅ Routes /admin/* retournent 404 si non-admin
- ✅ Pas de JavaScript qui expose les routes admin
- ✅ Pas de lien hardcodé vers /admin dans les vues
- ✅ Permissions **vérifiées au niveau middleware** + controller + policy
- ✅ CSRF protection sur tous les formulaires
- ✅ Rate limiting sur login (5 tentatives/5min)
- ✅ Soft delete audit trail (deleted_at)

### 5.2 Hidden Admin User

**Comportement :**

```php
// Dans UserTable Livewire
public function render()
{
    $users = User::query()
        ->whereDoesntHave('roles', function ($q) {
            $q->where('name', 'admin'); // ❌ Hide admin users
        })
        ->paginate(25);
}

// Impossible chercher admin par email
$search = 'admin@labo.tn';
// → Résultat : aucun utilisateur trouvé
```

### 5.3 Hidden Admin Role

**Comportement :**

```php
// Dans RoleTable Livewire
public function render()
{
    $roles = Role::query()
        ->where('name', '!=', 'admin') // ❌ Hide admin role
        ->get();
    // Affiche : Responsable, Secrétaire, Coursier, etc.
}
```

---

## 6. USER EXPERIENCE

### 6.1 Navigation Dynamique

**Pour Admin :**
```
┌─────────────────────────┐
│ Sidebar Menu (Cachée)    │
├─────────────────────────┤
│ 🏠 Dashboard            │
│ 👥 Utilisateurs        │
│ 🔐 Rôles               │
│ 🔑 Permissions         │
│ 👤 Mon Profil          │
│ 🚪 Logout              │
└─────────────────────────┘
```

**Pour Employer/Dentist :**
```
┌─────────────────────────┐
│ Sidebar Menu            │
├─────────────────────────┤
│ 🏠 Accueil             │
│ 👤 Mon Profil          │
│ 🚪 Logout              │
└─────────────────────────┘
```

### 6.2 Messages d'erreur

**Tentative accès /admin/users en tant que non-admin :**
→ Page "404 Not Found" standard
→ Aucun message révélateur

**Tentative création utilisateur sans permission :**
→ Redirect avec message : "Vous n'avez pas accès à cette action"

---

## 7. PERFORMANCE

### 7.1 Métriques de Cache

| Métrique | Cible | Moyen |
|----------|-------|-------|
| Page load /admin/users (no cache) | < 500ms | Eager loading + indexes |
| Page load /admin/users (avec cache) | < 100ms | Cache key hit |
| Dashboard stats query | < 200ms | Cache + computed properties |
| Navigation render | < 50ms | Blade caching |

### 7.2 Optimisations

- ✅ Eager loading des rôles/permissions (`with()`)
- ✅ Pagination (25 items par page)
- ✅ Index BD sur email, deleted_at
- ✅ Cache layer middleware
- ✅ Lazy loading images si présentes
- ✅ Compression assets (CSS/JS)

---

## 8. LIVRABLES

### 8.1 Code Source

- ✅ Controllers admin (UserController, RoleController, PermissionController)
- ✅ Livewire components (UserTable, RoleForm, PermissionGrid)
- ✅ Blade views sécurisées (no links to /admin)
- ✅ Middleware (CheckAdminAccess, CheckPermission)
- ✅ Policies (UserPolicy, RolePolicy)
- ✅ Services (UserService, RoleService, CacheService)

### 8.2 Configuration

- ✅ Routes sécurisées (routes/web.php)
- ✅ Cache config (config/permission.php + custom)
- ✅ Middleware registration (app/Http/Kernel.php)

### 8.3 Tests Automatisés

- ✅ Admin access tests
- ✅ Permission check tests
- ✅ 404 return tests
- ✅ Hidden data tests (admin users/roles)
- ✅ Cache invalidation tests

### 8.4 Documentation

- ✅ Ce CdCF (spécifications fonctionnelles)
- ✅ CdCT (spécifications techniques détaillées)
- ✅ Guide admin (comment utiliser le panneau)
- ✅ Guide sécurité (checklist et best practices)

---

## 9. TIMELINE & ESTIMATION

| Phase | Tâche | Jours | Priorité |
|-------|-------|-------|----------|
| Setup | Routes + Middleware cachées | 1.5 | 🔴 CRITIQUE |
| Dev | Controllers + Policies sécurisés | 2 | 🔴 CRITIQUE |
| Dev | Livewire Components (cachés) | 3 | 🔴 CRITIQUE |
| Dev | Caching layer + invalidation | 2 | 🟡 IMPORTANT |
| Test | Tests accès/sécurité | 2 | 🟡 IMPORTANT |
| QA | Tests manuels + sécurité audit | 1.5 | 🟡 IMPORTANT |
| **TOTAL** | | **12 jours** | |

---

**Version :** 1.0 CdCF Admin Cache  
**Date :** 6 janvier 2026  
**Statut :** ✅ FONCTIONNEL - SPÉCIFICATIONS FINALES  
**Auteur :** Équipe Produit

---

### 2.2 Commandes & calendrier (V3)
- Le calendrier affiche des événements « commande » positionnés sur `date_livraison` des tâches.
- Clic sur un événement → ouverture d’une modal de détails.

### 2.3 Bon de Livraison (BL) (V3)
- Déclencheur : passage commande à **Terminée** → création automatique BL si inexistant.
- BL = lignes issues des tâches (TTC uniquement) : service, prix unitaire TTC snapshot, quantité `nb_elem`, total ligne, total BL.

### 2.4 Prix services & prix par dentiste (V3)
- Service : prix TTC par défaut.
- Override (dentiste, service) possible.
- Snapshot TTC figé au niveau tâche lors création/mise à jour commande.

---

## 3. Rôles, permissions & visibilité
### 3.1 Visibilité calendrier (V3)
- Admin : toutes les commandes.
- Responsable / Secrétaire / Prothésiste : toutes les commandes.
- Employer : commandes ayant au moins une tâche `groupe_id = user.groupe_id`.
- Dentist : uniquement commandes où `commande.dentiste_id = user.id`.

### 3.2 Visibilité des tâches dans la modal (V3)
- Admin / Responsable / Secrétaire / Prothésiste : toutes les tâches.
- Employer : seulement tâches de son groupe.
- Dentist : toutes les tâches de ses commandes.

### 3.3 Permissions clés (V3)
- Changement statut commande : permission `change_commande_status` (dentist interdit).
- Gestion pricing : permission `manage_service_pricing` (employer/dentist interdits).

---

## 4. Workflow statuts commande (V3)
Workflow : **Reçue → En cours → Terminée → Livrée**.

Règles :
- Admin : peut changer le statut.
- Rôles internes : peut changer si permission `change_commande_status`.
- Dentist : ne peut jamais changer le statut.

---

## 5. Routes & zones d’accès (V3)
### 5.1 Zone `/admin/*` (admin-only hidden)
- Inchangé V2 : protégé par `auth` + `admin.access` (404 si non-admin).
- Commandes CRUD reste dans `/admin/commandes`.

### 5.2 Zone `/app/*` (interne permissionné)
- Nouveau V3 : calendrier, statut, BL, pricing.
- Protégé par `auth` + permissions granulaires.
- Accessible selon rôle + filtrage serveur obligatoire.

---

## 6. Cache & performance
- Conserver la stratégie V2 (permissions, listes, navigation).
- V3 : ajouter cache sur events calendrier, détails modal, lookup pricing, BL.
- Calendrier : endpoint JSON optimisé + pagination events.

---

## 7. Sécurité & audit
- Filtrage serveur obligatoire (pas seulement UI).
- Audit recommandé : changements statut, génération BL, modifications pricing.

---

## 8. Livrables
- V2 : admin caché (routes, middleware, policies, UI, tests).
- V3 : calendrier, modal, workflow statuts, BL (détail + impression), pricing par dentiste.

---

**Fin CdCF V3 (Clean).**

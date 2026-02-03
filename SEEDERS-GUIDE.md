# Guide des Seeders

## Commandes pour initialiser la base de données

### Option 1 : Tout en une fois
```bash
php artisan migrate --seed
```

### Option 2 : Étape par étape
```bash
# 1. Exécuter les migrations
php artisan migrate

# 2. Créer les rôles et permissions
php artisan db:seed --class=RolePermissionSeeder

# 3. Créer les données de test (groupes, services)
php artisan db:seed --class=TestDataSeeder

# 4. Créer les utilisateurs de test
php artisan db:seed --class=AdminUserSeeder
```

## Utilisateurs de test créés

Tous les utilisateurs ont le mot de passe : **`password`**

| Email | Rôle | Description |
|-------|------|-------------|
| `admin@labo.tn` | Admin | Accès complet à toutes les fonctionnalités |
| `dentist@labo.tn` | Dentist | Peut voir ses commandes uniquement |
| `employer@labo.tn` | Employer | Peut voir les commandes de son groupe |
| `responsable@labo.tn` | Responsable | Accès aux commandes et gestion |

## Données de test créées

### Groupes
- Groupe A
- Groupe B
- Groupe C

### Services (avec prix par défaut)
- Couronne Céramo-Métallique : 150.00 TND
- Bridge 3 éléments : 450.00 TND
- Prothèse Complète : 300.00 TND
- Prothèse Partielle : 250.00 TND
- Inlay Core : 80.00 TND
- Facette Céramique : 200.00 TND

## Notes importantes

- L'utilisateur admin est **caché** de la liste des utilisateurs dans l'interface admin
- Le rôle admin est **caché** de la liste des rôles
- Les routes `/admin/*` retournent **404** pour les non-admin
- L'employer de test est assigné au premier groupe créé

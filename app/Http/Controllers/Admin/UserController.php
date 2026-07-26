<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Groupe;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(private UserService $userService) {}

    /**
     * GET /admin/users
     * Affiche tous les utilisateurs SAUF les admins
     */
    public function index()
    {
        $this->authorize('view_users');

        $users = User::query()
            ->whereNull('deleted_at')
            ->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'admin'); // ❌ Hide admin users
            })
            ->with('roles', 'groupe')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    /**
     * GET /admin/dentists
     * Affiche uniquement les utilisateurs avec le rôle "dentist"
     */
    public function dentists(Request $request)
    {
        $this->authorize('view_users');

        $search = trim((string) $request->input('search', ''));
        $approval = $request->input('approval');

        $users = User::query()
            ->whereNull('deleted_at')
            ->whereHas('roles', function ($q) {
                $q->where('name', 'dentist');
            })
            ->when($search !== '', function ($query) use ($search) {
                $like = '%' . $search . '%';
                $query->where(function ($q) use ($like) {
                    $q->where('nom', 'like', $like)
                        ->orWhere('prénom', 'like', $like)
                        ->orWhere('name', 'like', $like)
                        ->orWhere('num_dentist', 'like', $like)
                        ->orWhere('tél', 'like', $like)
                        ->orWhere('gouvernorat', 'like', $like)
                        ->orWhere('ville', 'like', $like);
                });
            })
            ->when($approval === 'approved', function ($query) {
                $query->whereNotNull('approved_at');
            })
            ->when($approval === 'pending', function ($query) {
                $query->whereNull('approved_at');
            })
            ->with('roles', 'groupe')
            ->orderByRaw('approved_at IS NULL DESC')
            ->orderBy('order', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.dentists.index', compact('users'));
    }

    /**
     * GET /admin/teams
     * Affiche les utilisateurs avec les autres rôles (sauf admin et dentist)
     */
    public function teams()
    {
        $this->authorize('view_users');

        $users = User::query()
            ->whereNull('deleted_at')
            ->whereDoesntHave('roles', function ($q) {
                $q->whereIn('name', ['admin', 'dentist']);
            })
            ->with('roles', 'groupe')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.teams.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create_users');

        $roles = Role::where('name', '!=', 'admin')->get();
        $groupes = Groupe::all();

        return view('admin.users.create', compact('roles', 'groupes'));
    }

    /**
     * GET /admin/dentists/create
     * Show the form for creating a new dentist
     */
    public function createDentist()
    {
        $this->authorize('create_users');

        $groupes = Groupe::all();

        return view('admin.dentists.create', compact('groupes'));
    }

    /**
     * GET /admin/teams/create
     * Show the form for creating a new team member
     */
    public function createTeam()
    {
        $this->authorize('create_users');

        $roles = Role::whereNotIn('name', ['admin', 'dentist'])->get();
        $groupes = Groupe::all();

        return view('admin.teams.create', compact('roles', 'groupes'));
    }

    /**
     * POST /admin/users
     * Créer utilisateur - impossible de donner le rôle admin
     */
    public function store(Request $request)
    {
        $this->authorize('create_users');

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prénom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'gouvernorat' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:255',
            'adresse' => 'nullable|string',
            'tél' => 'nullable|string|max:20',
            'num_ordinaire' => 'nullable|string|max:50',
            'groupe_id' => 'nullable|exists:groupes,id',
            'role' => 'required|string|exists:roles,name',
        ]);

        // Validation : role ne doit pas être 'admin'
        if ($validated['role'] === 'admin') {
            return back()->with('error', 'Cannot assign admin role')->withInput();
        }

        $user = $this->userService->createUser($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Utilisateur {$user->full_name} créé.");
    }

    /**
     * POST /admin/dentists
     * Créer un dentiste
     */
    public function storeDentist(Request $request)
    {
        $this->authorize('create_users');

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prénom' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users',
            'gouvernorat' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:255',
            'adresse' => 'nullable|string',
            'num_dentist' => 'nullable|integer|unique:users,num_dentist',
            'order' => 'nullable|integer|unique:users,order',
            'tél' => 'nullable|string|max:20',
            'num_ordinaire' => 'nullable|string|max:50',
        ]);

        // Forcer le rôle à "dentist" et le mot de passe à "password"
        $validated['role'] = 'dentist';
        $validated['password'] = 'password';

        $user = $this->userService->createUser($validated);

        return redirect()
            ->route('admin.dentists.index')
            ->with('success', "Dentiste {$user->full_name} créé.");
    }

    /**
     * POST /admin/teams
     * Créer un membre d'équipe
     */
    public function storeTeam(Request $request)
    {
        $this->authorize('create_users');

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prénom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'gouvernorat' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:255',
            'adresse' => 'nullable|string',
            'tél' => 'nullable|string|max:20',
            'groupe_id' => 'nullable|exists:groupes,id',
            'role' => 'required|string|exists:roles,name',
        ]);

        // Validation : role ne doit pas être 'admin' ou 'dentist'
        if (in_array($validated['role'], ['admin', 'dentist'])) {
            return back()->with('error', 'Cannot assign admin or dentist role')->withInput();
        }

        $user = $this->userService->createUser($validated);

        return redirect()
            ->route('admin.teams.index')
            ->with('success', "Membre d'équipe {$user->full_name} créé.");
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $this->authorize('view_users');

        // Empêcher voir admin user
        if ($user->hasRole('admin')) {
            abort(404);
        }

        $user->load('roles', 'groupe');

        return view('admin.users.show', compact('user'));
    }

    /**
     * GET /admin/dentists/{user}
     * Display a specific dentist
     */
    public function showDentist(User $user)
    {
        $this->authorize('view_users');

        // Vérifier que l'utilisateur est un dentiste
        if (!$user->hasRole('dentist')) {
            abort(404);
        }

        $user->load('groupe');

        return view('admin.dentists.show', compact('user'));
    }

    /**
     * GET /admin/teams/{user}
     * Display a specific team member
     */
    public function showTeam(User $user)
    {
        $this->authorize('view_users');

        // Vérifier que l'utilisateur n'est pas admin ou dentist
        if ($user->hasRole('admin') || $user->hasRole('dentist')) {
            abort(404);
        }

        $user->load('roles', 'groupe');

        return view('admin.teams.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $this->authorize('edit_users');

        // Empêcher modifier admin user
        if ($user->hasRole('admin')) {
            abort(404);
        }

        $roles = Role::where('name', '!=', 'admin')->get();
        $groupes = Groupe::all();

        return view('admin.users.edit', compact('user', 'roles', 'groupes'));
    }

    /**
     * GET /admin/dentists/{user}/edit
     * Show the form for editing a dentist
     */
    public function editDentist(User $user)
    {
        $this->authorize('edit_users');

        // Vérifier que l'utilisateur est un dentiste
        if (!$user->hasRole('dentist')) {
            abort(404);
        }

        $groupes = Groupe::all();

        return view('admin.dentists.edit', compact('user', 'groupes'));
    }

    /**
     * GET /admin/teams/{user}/edit
     * Show the form for editing a team member
     */
    public function editTeam(User $user)
    {
        $this->authorize('edit_users');

        // Vérifier que l'utilisateur n'est pas admin ou dentist
        if ($user->hasRole('admin') || $user->hasRole('dentist')) {
            abort(404);
        }

        $roles = Role::whereNotIn('name', ['admin', 'dentist'])->get();
        $groupes = Groupe::all();

        return view('admin.teams.edit', compact('user', 'roles', 'groupes'));
    }

    /**
     * PATCH /admin/users/{user}
     * Modifier utilisateur - jamais pouvoir changer son rôle en admin
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('edit_users');

        // Empêcher modifier admin user
        if ($user->hasRole('admin')) {
            abort(404);
        }

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prénom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'gouvernorat' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:255',
            'adresse' => 'nullable|string',
            'tél' => 'nullable|string|max:20',
            'num_ordinaire' => 'nullable|string|max:50',
            'groupe_id' => 'nullable|exists:groupes,id',
            'role' => 'nullable|string|exists:roles,name',
        ]);

        // Protection: ne pas permettre passer à admin
        if (isset($validated['role']) && $validated['role'] === 'admin') {
            return back()->with('error', 'Cannot assign admin role')->withInput();
        }

        $user = $this->userService->updateUser($user, $validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Utilisateur modifié.');
    }

    /**
     * PATCH /admin/dentists/{user}
     * Modifier un dentiste
     */
    public function updateDentist(Request $request, User $user)
    {
        $this->authorize('edit_users');

        // Vérifier que l'utilisateur est un dentiste
        if (!$user->hasRole('dentist')) {
            abort(404);
        }

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prénom' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
            'gouvernorat' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:255',
            'adresse' => 'nullable|string',
            'num_dentist' => 'nullable|integer|unique:users,num_dentist,' . $user->id,
            'order' => 'nullable|integer|unique:users,order,' . $user->id,
            'tél' => 'nullable|string|max:20',
            'num_ordinaire' => 'nullable|string|max:50',
        ]);

        $user = $this->userService->updateUser($user, $validated);

        return redirect()
            ->route('admin.dentists.index')
            ->with('success', 'Dentiste modifié.');
    }

    /**
     * POST /admin/dentists/{user}/approve
     * Approuver un compte dentiste (inscription publique)
     */
    public function approveDentist(User $user)
    {
        $this->authorize('edit_users');

        if (! $user->hasRole('dentist')) {
            abort(404);
        }

        if ($user->approved_at) {
            return redirect()
                ->route('admin.dentists.index')
                ->with('success', "Le dentiste {$user->full_name} est déjà approuvé.");
        }

        $this->userService->approveUser($user);

        return redirect()
            ->route('admin.dentists.index')
            ->with('success', "Le dentiste {$user->full_name} a été approuvé.");
    }

    /**
     * POST /admin/dentists/{user}/revoke
     * Révoquer l'accès d'un dentiste
     */
    public function revokeDentist(User $user)
    {
        $this->authorize('edit_users');

        if (! $user->hasRole('dentist')) {
            abort(404);
        }

        $this->userService->revokeApproval($user);

        return redirect()
            ->route('admin.dentists.index')
            ->with('success', "L'accès du dentiste {$user->full_name} a été révoqué.");
    }

    /**
     * PATCH /admin/teams/{user}
     * Modifier un membre d'équipe
     */
    public function updateTeam(Request $request, User $user)
    {
        $this->authorize('edit_users');

        // Vérifier que l'utilisateur n'est pas admin ou dentist
        if ($user->hasRole('admin') || $user->hasRole('dentist')) {
            abort(404);
        }

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prénom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'gouvernorat' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:255',
            'adresse' => 'nullable|string',
            'tél' => 'nullable|string|max:20',
            'role' => 'nullable|string|exists:roles,name',
            'groupe_id' => 'nullable|exists:groupes,id',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Protection: ne pas permettre passer à admin ou dentist
        if (isset($validated['role']) && in_array($validated['role'], ['admin', 'dentist'])) {
            return back()->with('error', 'Cannot assign admin or dentist role')->withInput();
        }

        $user = $this->userService->updateUser($user, $validated);

        return redirect()
            ->route('admin.teams.index')
            ->with('success', 'Membre d\'équipe modifié.');
    }

    /**
     * DELETE /admin/users/{user}
     * Soft delete utilisateur
     */
    public function destroy(User $user)
    {
        $this->authorize('delete_users');

        // Empêcher supprimer admin user
        if ($user->hasRole('admin')) {
            return back()->with('error', 'Cannot delete admin user');
        }

        $this->userService->deleteUser($user);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé.');
    }
}

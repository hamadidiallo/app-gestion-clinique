<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Invitation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Affiche la liste de tous les utilisateurs enregistrés.
     */
    public function index()
    {
        $roleId = request('role_id');
        $search = request('search');

        $query = User::with('role')->latest();

        if ($roleId) {
            $query->where('role_id', $roleId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                    ->orWhere('prenom', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->get();
        $roles = Role::withCount('users')->orderBy('nom')->get();

        $stats = [
            'total' => User::count(),
            'admins' => User::whereHas('role', fn ($q) => $q->where('nom', 'like', '%Admin%'))->count(),
            'medecins' => User::whereHas('role', fn ($q) => $q->where('nom', 'like', '%Medecin%')->orWhere('nom', 'like', '%Médecin%'))->count(),
            'caisse_accueil' => User::whereHas('role', fn ($q) => $q->whereIn('nom', ['Caissier', 'Réceptionniste', 'Receptionniste']))->count(),
            'comptables' => User::whereHas('role', fn ($q) => $q->where('nom', 'like', '%Comptable%'))->count(),
        ];

        $invitations = Invitation::with('role')
            ->where('clinique_id', auth()->user()?->clinique_id)
            ->whereNull('utilise_le')
            ->latest()
            ->get();

        $assignableRoles = Role::whereNotIn('nom', ['Super Administrateur', 'Admin'])
            ->orderBy('nom')
            ->get();

        return view('users.index', compact('users', 'roles', 'stats', 'roleId', 'search', 'invitations', 'assignableRoles'));
    }

    /**
     * Affiche le formulaire de création d'un utilisateur.
     */
    public function create()
    {
        // Récupère tous les rôles sous forme de tableau associatif [id => nom] pour remplir le select
        $roles = Role::pluck('nom', 'id')->toArray();

        // Retourne la vue de création en lui passant la liste des rôles
        return view('users.create', compact('roles'));
    }

    /**
     * Enregistre un nouvel utilisateur dans la base de données.
     */
    public function store(UserRequest $request)
    {
        // Récupère uniquement les données validées par le UserRequest
        $validated = $request->validated();

        // Crée le nouvel utilisateur (le mot de passe est haché automatiquement via le cast du modèle User)
        User::create($validated);

        // Redirige vers la liste des utilisateurs avec un message de succès
        return to_route('users.index')->with('alert', 'Création de l\'utilisateur réussie');
    }

    /**
     * Affiche les détails d'un utilisateur spécifique.
     */
    public function show(User $user)
    {
        // Charge la relation avec le rôle associé
        $user->load('role');

        // Retourne la vue de détails de l'utilisateur
        return view('users.show', compact('user'));
    }

    /**
     * Affiche le formulaire d'édition d'un utilisateur existant.
     */
    public function edit(User $user)
    {
        // Récupère la liste des rôles pour la liste déroulante
        $roles = Role::pluck('nom', 'id')->toArray();

        // Retourne la vue d'édition pré-remplie avec l'utilisateur et les rôles
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Met à jour les informations d'un utilisateur dans la base de données.
     */
    public function update(UserRequest $request, User $user)
    {
        // Récupère les données validées par la requête
        $validated = $request->validated();

        // Si le champ mot de passe est laissé vide lors de la modification, on ne le met pas à jour
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        // Applique la mise à jour sur l'utilisateur
        $user->update($validated);

        // Redirige vers la liste des utilisateurs avec un message de confirmation
        return to_route('users.index')->with('alert', 'Modification de l\'utilisateur réussie');
    }

    /**
     * Supprime un utilisateur de la base de données.
     */
    public function destroy(User $user)
    {
        // Supprime l'utilisateur spécifié
        $user->delete();

        // Redirige vers la liste des utilisateurs avec un message de confirmation
        return to_route('users.index')->with('alert', 'Suppression de l\'utilisateur réussie');
    }

    /**
     * Génère une nouvelle invitation sécurisée avec rôle fixé (Option 1).
     */
    public function storeInvitation(Request $request)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'prenom' => 'nullable|string|max:100',
            'nom' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
        ]);

        $role = Role::findOrFail($validated['role_id']);
        if (in_array($role->nom, ['Super Administrateur', 'Super Admin'])) {
            return back()->with('error', 'Impossible de créer une invitation pour ce rôle.');
        }

        // Préfixe de 3 lettres significatives
        $prefix = null;
        if (! empty($validated['nom'])) {
            $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $validated['nom']), 0, 3));
        } elseif (! empty($role->nom)) {
            $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $role->nom), 0, 3));
        }

        do {
            $code = ($prefix && strlen($prefix) >= 3 ? $prefix : 'INV').rand(100, 999);
        } while (Invitation::where('code', $code)->exists());

        Invitation::create([
            'clinique_id' => auth()->user()->clinique_id,
            'role_id' => $role->id,
            'code' => $code,
            'prenom' => $validated['prenom'] ?? null,
            'nom' => $validated['nom'] ?? null,
            'email' => $validated['email'] ?? null,
            'cree_par_user_id' => auth()->id(),
        ]);

        return back()->with('alert', "Invitation générée avec succès pour le rôle « {$role->nom} ». Code à transmettre : {$code}");
    }

    /**
     * Révoque une invitation non encore utilisée.
     */
    public function destroyInvitation(Invitation $invitation)
    {
        if ($invitation->clinique_id !== auth()->user()->clinique_id) {
            abort(403);
        }

        $invitation->delete();

        return back()->with('alert', "L'invitation « {$invitation->code} » a été révoquée.");
    }
}

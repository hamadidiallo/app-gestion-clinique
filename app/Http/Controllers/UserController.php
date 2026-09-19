<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Role;
use App\Models\User;

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

        return view('users.index', compact('users', 'roles', 'stats', 'roleId', 'search'));
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
}

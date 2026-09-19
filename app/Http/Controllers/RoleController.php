<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Models\Role;
use App\Models\User;

class RoleController extends Controller
{
    /**
     * Affiche la liste des rôles enregistrés et leurs métriques.
     */
    public function index()
    {
        // Récupère tous les rôles avec le nombre d'utilisateurs affectés
        $roles = Role::withCount('users')->orderBy('id')->get();
        $totalRoles = $roles->count();
        $totalUsers = User::count();
        $protectedRoles = ['Administrateur', 'Médecin', 'Caissier', 'Comptable', 'Réceptionniste'];

        // Transmet la liste des rôles et les métriques à la vue roles.index
        return view('roles.index', compact('roles', 'totalRoles', 'totalUsers', 'protectedRoles'));
    }

    /**
     * Affiche le formulaire de création d'un nouveau rôle.
     */
    public function create()
    {
        // Retourne la vue du formulaire de création de rôle
        return view('roles.create');
    }

    /**
     * Enregistre un nouveau rôle en base de données après validation.
     */
    public function store(RoleRequest $request)
    {
        // Récupère les données validées envoyées par le formulaire
        $role = $request->validated();

        // Crée le rôle dans la base de données
        Role::create($role);

        // Redirige vers la liste des rôles avec un message de succès
        return to_route('roles.index')->with('alert', 'Ajout de rôle réussi');
    }

    /**
     * Affiche les détails d'un rôle spécifique.
     */
    public function show(Role $role)
    {
        // Charge la relation des utilisateurs associés à ce rôle
        $role->load('users');

        // Retourne la vue de détail du rôle avec les données du rôle
        return view('roles.show', compact('role'));
    }

    /**
     * Affiche le formulaire d'édition d'un rôle existant.
     */
    public function edit(Role $role)
    {
        // Retourne la vue d'édition pré-remplie avec le rôle sélectionné
        return view('roles.edit', compact('role'));
    }

    /**
     * Met à jour les informations d'un rôle dans la base de données.
     */
    public function update(RoleRequest $request, Role $role)
    {
        // Récupère les données validées
        $validated = $request->validated();

        // Applique la mise à jour des données du rôle
        $role->update($validated);

        // Redirige vers la liste des rôles avec un message de confirmation
        return to_route('roles.index')->with('alert', 'Modification du rôle réussie');
    }

    /**
     * Supprime un rôle de la base de données.
     */
    public function destroy(Role $role)
    {
        // Supprime le rôle de la base de données
        $role->delete();

        // Redirige vers la liste des rôles avec un message de confirmation
        return to_route('roles.index')->with('alert', 'Suppression du rôle réussie');
    }
}

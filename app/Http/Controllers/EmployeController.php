<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeRequest;
use App\Models\Employe;

class EmployeController extends Controller
{
    /**
     * Affiche la liste complète des employés de la clinique.
     */
    public function index()
    {
        // Récupère l'ensemble des employés triés par nom
        $employes = Employe::orderBy('nom')->get();

        // Retourne la vue d'index avec la liste transmise
        return view('employes.index', compact('employes'));
    }

    /**
     * Affiche le formulaire de création d'un employé.
     */
    public function create()
    {
        // Tableau d'options pour le statut de l'employé
        $statuts = [
            '1' => 'Actif',
            '0' => 'Inactif',
        ];

        // Retourne la vue de création
        return view('employes.create', compact('statuts'));
    }

    /**
     * Enregistre un nouvel employé dans la base de données.
     */
    public function store(EmployeRequest $request)
    {
        // Extrait les champs validés
        $validated = $request->validated();

        // Enregistre l'employé
        Employe::create($validated);

        // Redirige avec un message d'alerte de confirmation
        return to_route('employes.index')->with('alert', 'Employé créé avec succès.');
    }

    /**
     * Affiche les détails d'un employé spécifique.
     */
    public function show(Employe $employe)
    {
        // Retourne la vue de consultation de l'employé
        return view('employes.show', compact('employe'));
    }

    /**
     * Affiche le formulaire d'édition d'un employé.
     */
    public function edit(Employe $employe)
    {
        // Options de statut
        $statuts = [
            '1' => 'Actif',
            '0' => 'Inactif',
        ];

        // Retourne la vue d'édition préremplie
        return view('employes.edit', compact('employe', 'statuts'));
    }

    /**
     * Met à jour les informations de l'employé.
     */
    public function update(EmployeRequest $request, Employe $employe)
    {
        // Extrait les données validées
        $validated = $request->validated();

        // Met à jour la fiche
        $employe->update($validated);

        // Redirige vers l'index des employés
        return to_route('employes.index')->with('alert', 'Employé mis à jour avec succès.');
    }

    /**
     * Supprime la fiche de l'employé.
     */
    public function destroy(Employe $employe)
    {
        // Supprime l'enregistrement
        $employe->delete();

        // Redirige vers la liste des employés
        return to_route('employes.index')->with('alert', 'Employé supprimé avec succès.');
    }
}

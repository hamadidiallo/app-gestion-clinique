<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategorieDepenseRequest;
use App\Models\CategorieDepense;

class CategorieDepenseController extends Controller
{
    /**
     * Affiche les catégories de charges et dépenses.
     */
    public function index()
    {
        // Récupère l'ensemble des catégories
        $categorieDepenses = CategorieDepense::orderBy('nom')->get();

        // Vue d'index
        return view('categoriedepenses.index', compact('categorieDepenses'));
    }

    /**
     * Formulaire de création de catégorie.
     */
    public function create()
    {
        $statuts = [
            '1' => 'Actif',
            '0' => 'Inactif',
        ];

        return view('categoriedepenses.create', compact('statuts'));
    }

    /**
     * Enregistre une nouvelle catégorie de dépense.
     */
    public function store(CategorieDepenseRequest $request)
    {
        $validated = $request->validated();

        CategorieDepense::create($validated);

        return to_route('categoriedepenses.index')->with('alert', 'Catégorie de dépense créée avec succès.');
    }

    /**
     * Détails d'une catégorie.
     */
    public function show(CategorieDepense $categoriedepense)
    {
        $categoriedepense->load('depenses');

        return view('categoriedepenses.show', compact('categoriedepense'));
    }

    /**
     * Formulaire d'édition.
     */
    public function edit(CategorieDepense $categoriedepense)
    {
        $statuts = [
            '1' => 'Actif',
            '0' => 'Inactif',
        ];

        return view('categoriedepenses.edit', compact('categoriedepense', 'statuts'));
    }

    /**
     * Met à jour la catégorie.
     */
    public function update(CategorieDepenseRequest $request, CategorieDepense $categoriedepense)
    {
        $validated = $request->validated();

        $categoriedepense->update($validated);

        return to_route('categoriedepenses.index')->with('alert', 'Catégorie de dépense mise à jour avec succès.');
    }

    /**
     * Supprime la catégorie.
     */
    public function destroy(CategorieDepense $categoriedepense)
    {
        $categoriedepense->delete();

        return to_route('categoriedepenses.index')->with('alert', 'Catégorie de dépense supprimée avec succès.');
    }
}

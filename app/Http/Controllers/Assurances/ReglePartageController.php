<?php

namespace App\Http\Controllers\Assurances;

use App\Http\Controllers\Controller;
use App\Http\Requests\Assurances\ReglePartageRequest;
use App\Models\ReglePartage;
use App\Models\Service;

class ReglePartageController extends Controller
{
    /**
     * Affiche la liste des règles de partage/rétrocession par service.
     */
    public function index()
    {
        // Charge les règles avec leur service médical rattaché
        $reglesPartage = ReglePartage::with('service')->latest()->get();

        // Vue d'index
        return view('assurances.reglespartage.index', compact('reglesPartage'));
    }

    /**
     * Formulaire de création d'une règle de partage.
     */
    public function create()
    {
        $services = Service::orderBy('nom')->get();

        $statuts = [
            '1' => 'Actif',
            '0' => 'Inactif',
        ];

        return view('assurances.reglespartage.create', compact('services', 'statuts'));
    }

    /**
     * Enregistre une règle de partage.
     */
    public function store(ReglePartageRequest $request)
    {
        $validated = $request->validated();

        ReglePartage::create($validated);

        return to_route('reglespartage.index')->with('alert', 'Règle de partage enregistrée avec succès.');
    }

    /**
     * Détails d'une règle de partage.
     */
    public function show(ReglePartage $reglespartage)
    {
        $reglespartage->load('service');

        return view('assurances.reglespartage.show', compact('reglespartage'));
    }

    /**
     * Formulaire d'édition de règle de partage.
     */
    public function edit(ReglePartage $reglespartage)
    {
        $services = Service::orderBy('nom')->get();

        $statuts = [
            '1' => 'Actif',
            '0' => 'Inactif',
        ];

        return view('assurances.reglespartage.edit', compact('reglespartage', 'services', 'statuts'));
    }

    /**
     * Met à jour la règle de partage.
     */
    public function update(ReglePartageRequest $request, ReglePartage $reglespartage)
    {
        $validated = $request->validated();

        $reglespartage->update($validated);

        return to_route('reglespartage.index')->with('alert', 'Règle de partage mise à jour avec succès.');
    }

    /**
     * Supprime la règle de partage.
     */
    public function destroy(ReglePartage $reglespartage)
    {
        $reglespartage->delete();

        return to_route('reglespartage.index')->with('alert', 'Règle de partage supprimée avec succès.');
    }
}

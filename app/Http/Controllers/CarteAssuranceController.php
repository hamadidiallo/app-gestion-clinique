<?php

namespace App\Http\Controllers;

use App\Http\Requests\CarteAssuranceRequest;
use App\Models\Assurance;
use App\Models\CarteAssurance;
use App\Traits\HasPeriodFilter;

class CarteAssuranceController extends Controller
{
    use HasPeriodFilter;

    /**
     * Affiche la liste de toutes les cartes d'assurance filtrées par période de délivrance.
     */
    public function index()
    {
        $period = request('periode', 'tous');
        $customStart = request('date_debut');
        $customEnd = request('date_fin');

        $filter = $this->getPeriodDates($period, $customStart, $customEnd);

        $query = CarteAssurance::with(['patient', 'assurance'])->latest();

        $this->applyDateFilter($query, $filter['start'], $filter['end'], 'created_at');

        $cartesAssurance = $query->get();
        $currentPeriod = $filter['period'];
        $periodLabel = $filter['label'];

        return view('cartesassurances.index', compact('cartesAssurance', 'currentPeriod', 'periodLabel'));
    }

    /**
     * Affiche le formulaire de création d'une carte d'assurance.
     */
    public function create()
    {
        $assurances = Assurance::where('statut', true)->pluck('nom', 'id');

        $statuts = [
            '1' => 'Actif',
            '0' => 'Inactif',
        ];

        $selectedPatient = null;
        if (request()->has('patient_id')) {
            $selectedPatient = \App\Models\Patient::find(request()->get('patient_id'));
        }

        return view('cartesassurances.create', compact('assurances', 'statuts', 'selectedPatient'));
    }

    /**
     * Enregistre une nouvelle carte d'assurance en base de données.
     */
    public function store(CarteAssuranceRequest $request)
    {
        $validated = $request->validated();

        CarteAssurance::create($validated);

        return to_route('cartesassurances.index')->with('alert', 'Création de la carte d\'assurance réussie');
    }

    /**
     * Affiche les détails d'une carte d'assurance spécifique.
     */
    public function show(CarteAssurance $carteassurance)
    {
        $carteassurance->load(['patient', 'assurance']);

        return view('cartesassurances.show', compact('carteassurance'));
    }

    /**
     * Affiche le formulaire d'édition d'une carte d'assurance existante.
     */
    public function edit(CarteAssurance $carteassurance)
    {
        $carteassurance->load(['patient', 'assurance']);

        $assurances = Assurance::pluck('nom', 'id');

        $statuts = [
            '1' => 'Actif',
            '0' => 'Inactif',
        ];

        return view('cartesassurances.edit', compact('carteassurance', 'assurances', 'statuts'));
    }

    /**
     * Met à jour les informations d'une carte d'assurance en base de données.
     */
    public function update(CarteAssuranceRequest $request, CarteAssurance $carteassurance)
    {
        $validated = $request->validated();

        $carteassurance->update($validated);

        return to_route('cartesassurances.index')->with('alert', 'Modification de la carte d\'assurance réussie');
    }

    /**
     * Supprime une carte d'assurance de la base de données.
     */
    public function destroy(CarteAssurance $carteassurance)
    {
        $carteassurance->delete();

        return to_route('cartesassurances.index')->with('alert', 'Suppression de la carte d\'assurance réussie');
    }
}

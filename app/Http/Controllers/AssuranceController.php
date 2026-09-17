<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssuranceRequest;
use App\Models\Assurance;
use App\Traits\HasPeriodFilter;

class AssuranceController extends Controller
{
    use HasPeriodFilter;

    /**
     * Affiche la liste de toutes les compagnies d'assurance avec leurs prises en charge par période.
     */
    public function index()
    {
        $period = request('periode', 'tous');
        $customStart = request('date_debut');
        $customEnd = request('date_fin');

        $filter = $this->getPeriodDates($period, $customStart, $customEnd);

        $assurances = Assurance::withCount(['tickets' => function ($q) use ($filter) {
            $this->applyDateFilter($q, $filter['start'], $filter['end'], 'date_ticket');
        }])->withSum(['tickets' => function ($q) use ($filter) {
            $this->applyDateFilter($q, $filter['start'], $filter['end'], 'date_ticket');
        }], 'montant_assurance')->orderBy('nom')->get();

        $currentPeriod = $filter['period'];
        $periodLabel = $filter['label'];

        return view('assurances.index', compact('assurances', 'currentPeriod', 'periodLabel'));
    }

    /**
     * Affiche le formulaire de création d'une compagnie d'assurance.
     */
    public function create()
    {
        $statuts = [
            '1' => 'Actif',
            '0' => 'Inactif',
        ];

        return view('assurances.create', compact('statuts'));
    }

    /**
     * Enregistre une nouvelle compagnie d'assurance en base de données.
     */
    public function store(AssuranceRequest $request)
    {
        $validated = $request->validated();

        Assurance::create($validated);

        return to_route('assurances.index')->with('alert', 'Création de l\'assurance réussie');
    }

    /**
     * Affiche les détails d'une compagnie d'assurance spécifique.
     */
    public function show(Assurance $assurance)
    {
        $assurance->load(['patients', 'cartesAssurance.patient']);

        return view('assurances.show', compact('assurance'));
    }

    /**
     * Affiche le formulaire d'édition d'une assurance existante.
     */
    public function edit(Assurance $assurance)
    {
        $statuts = [
            '1' => 'Actif',
            '0' => 'Inactif',
        ];

        return view('assurances.edit', compact('assurance', 'statuts'));
    }

    /**
     * Met à jour les données d'une assurance en base de données.
     */
    public function update(AssuranceRequest $request, Assurance $assurance)
    {
        $validated = $request->validated();

        $assurance->update($validated);

        return to_route('assurances.index')->with('alert', 'Modification de l\'assurance réussie');
    }

    /**
     * Supprime une compagnie d'assurance de la base de données.
     */
    public function destroy(Assurance $assurance)
    {
        $assurance->delete();

        return to_route('assurances.index')->with('alert', 'Suppression de l\'assurance réussie');
    }
}

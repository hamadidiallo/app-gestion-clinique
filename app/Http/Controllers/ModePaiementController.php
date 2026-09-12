<?php

namespace App\Http\Controllers;

use App\Http\Requests\ModePaiementRequest;
use App\Models\ModePaiement;
use App\Traits\HasPeriodFilter;

class ModePaiementController extends Controller
{
    use HasPeriodFilter;

    /**
     * Affiche la liste des modes de paiement (Espèces, Carte, Virement, Mobile Money...) filtrés par période.
     */
    public function index()
    {
        $period = request('periode', 'tous');
        $customStart = request('date_debut');
        $customEnd = request('date_fin');

        $filter = $this->getPeriodDates($period, $customStart, $customEnd);

        $modePaiements = ModePaiement::withCount(['paiements' => function ($q) use ($filter) {
            $this->applyDateFilter($q, $filter['start'], $filter['end'], 'date_paiement');
        }])->withSum(['paiements' => function ($q) use ($filter) {
            $this->applyDateFilter($q, $filter['start'], $filter['end'], 'date_paiement');
        }], 'montant_impute')->orderBy('nom')->get();

        $currentPeriod = $filter['period'];
        $periodLabel = $filter['label'];

        return view('modepaiements.index', compact('modePaiements', 'currentPeriod', 'periodLabel'));
    }

    /**
     * Formulaire de création d'un mode de paiement.
     */
    public function create()
    {
        $statuts = [
            '1' => 'Actif',
            '0' => 'Inactif',
        ];

        return view('modepaiements.create', compact('statuts'));
    }

    /**
     * Enregistre un nouveau mode de paiement.
     */
    public function store(ModePaiementRequest $request)
    {
        $validated = $request->validated();

        ModePaiement::create($validated);

        return to_route('modepaiements.index')->with('alert', 'Mode de paiement créé avec succès.');
    }

    /**
     * Détails d'un mode de paiement.
     */
    public function show(ModePaiement $modepaiement)
    {
        $modepaiement->load(['paiements', 'recettes', 'depenses']);

        return view('modepaiements.show', compact('modepaiement'));
    }

    /**
     * Formulaire d'édition d'un mode de paiement.
     */
    public function edit(ModePaiement $modepaiement)
    {
        $statuts = [
            '1' => 'Actif',
            '0' => 'Inactif',
        ];

        return view('modepaiements.edit', compact('modepaiement', 'statuts'));
    }

    /**
     * Met à jour le mode de paiement.
     */
    public function update(ModePaiementRequest $request, ModePaiement $modepaiement)
    {
        $validated = $request->validated();

        $modepaiement->update($validated);

        return to_route('modepaiements.index')->with('alert', 'Mode de paiement mis à jour avec succès.');
    }

    /**
     * Supprime le mode de paiement.
     */
    public function destroy(ModePaiement $modepaiement)
    {
        $modepaiement->delete();

        return to_route('modepaiements.index')->with('alert', 'Mode de paiement supprimé avec succès.');
    }
}

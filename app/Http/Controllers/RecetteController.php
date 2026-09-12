<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecetteRequest;
use App\Models\ModePaiement;
use App\Models\Paiement;
use App\Models\Recette;
use App\Models\Ticket;
use App\Models\User;
use App\Traits\HasPeriodFilter;

class RecetteController extends Controller
{
    use HasPeriodFilter;

    /**
     * Affiche l'ensemble des recettes financières perçues par la clinique filtrées par période.
     */
    public function index()
    {
        $period = request('periode', 'tous');
        $customStart = request('date_debut');
        $customEnd = request('date_fin');

        $filter = $this->getPeriodDates($period, $customStart, $customEnd);

        $query = Recette::with(['ticket', 'paiement', 'user'])->latest('date_recette');

        $this->applyDateFilter($query, $filter['start'], $filter['end'], 'date_recette');

        $recettes = $query->get();
        $currentPeriod = $filter['period'];
        $periodLabel = $filter['label'];

        return view('recettes.index', compact('recettes', 'currentPeriod', 'periodLabel'));
    }

    /**
     * Formulaire d'enregistrement d'une recette.
     */
    public function create()
    {
        $tickets = Ticket::latest()->get();
        $paiements = Paiement::latest()->get();
        $modePaiements = ModePaiement::where('statut', true)->orderBy('nom')->get();
        $users = User::orderBy('nom')->get();

        $statuts = [
            '1' => 'Validée',
            '0' => 'Annulée',
        ];

        $defaultReference = 'REC-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        return view('recettes.create', compact('tickets', 'paiements', 'modePaiements', 'users', 'statuts', 'defaultReference'));
    }

    /**
     * Enregistre une recette.
     */
    public function store(RecetteRequest $request)
    {
        $validated = $request->validated();

        if (empty($validated['mode_paiement_id'])) {
            $defaultMode = ModePaiement::where('statut', true)->first();
            $validated['mode_paiement_id'] = $defaultMode ? $defaultMode->id : 1;
        }

        Recette::create($validated);

        return to_route('recettes.index')->with('alert', 'Recette comptabilisée avec succès.');
    }

    /**
     * Détails d'une recette.
     */
    public function show(Recette $recette)
    {
        $recette->load(['ticket.patient', 'paiement', 'user']);

        return view('recettes.show', compact('recette'));
    }

    /**
     * Formulaire d'édition de recette.
     */
    public function edit(Recette $recette)
    {
        $tickets = Ticket::latest()->get();
        $paiements = Paiement::latest()->get();
        $modePaiements = ModePaiement::orderBy('nom')->get();
        $users = User::orderBy('nom')->get();

        $statuts = [
            '1' => 'Validée',
            '0' => 'Annulée',
        ];

        return view('recettes.edit', compact('recette', 'tickets', 'paiements', 'modePaiements', 'users', 'statuts'));
    }

    /**
     * Met à jour la recette.
     */
    public function update(RecetteRequest $request, Recette $recette)
    {
        $validated = $request->validated();

        if (empty($validated['mode_paiement_id'])) {
            $defaultMode = ModePaiement::where('statut', true)->first();
            $validated['mode_paiement_id'] = $defaultMode ? $defaultMode->id : 1;
        }

        $recette->update($validated);

        return to_route('recettes.index')->with('alert', 'Recette mise à jour avec succès.');
    }

    /**
     * Supprime la recette.
     */
    public function destroy(Recette $recette)
    {
        $recette->delete();

        return to_route('recettes.index')->with('alert', 'Recette supprimée avec succès.');
    }
}

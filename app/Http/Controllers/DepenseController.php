<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepenseRequest;
use App\Models\CategorieDepense;
use App\Models\Depense;
use App\Models\ModePaiement;
use App\Models\User;
use App\Services\DepenseService;
use App\Traits\HasPeriodFilter;

class DepenseController extends Controller
{
    use HasPeriodFilter;

    public function __construct(
        protected DepenseService $depenseService
    ) {}

    /**
     * Affiche l'historique des charges et dépenses filtrées par période.
     */
    public function index()
    {
        $period = request('periode', 'tous');
        $customStart = request('date_debut');
        $customEnd = request('date_fin');

        $filter = $this->getPeriodDates($period, $customStart, $customEnd);

        $query = Depense::with(['categorieDepense', 'modePaiement', 'user'])->latest('date_depense');

        $this->applyDateFilter($query, $filter['start'], $filter['end'], 'date_depense');

        $depenses = $query->get();
        $currentPeriod = $filter['period'];
        $periodLabel = $filter['label'];

        return view('depenses.index', compact('depenses', 'currentPeriod', 'periodLabel'));
    }

    /**
     * Formulaire d'enregistrement d'une dépense.
     */
    public function create()
    {
        $categories = CategorieDepense::where('statut', true)->orderBy('nom')->get();
        $modePaiements = ModePaiement::where('statut', true)->orderBy('nom')->get();
        $users = User::orderBy('nom')->get();

        $statuts = [
            '1' => 'Validée',
            '0' => 'Annulée',
        ];

        $defaultReference = 'DEP-'.date('Ymd').'-'.strtoupper(substr(uniqid(), -4));

        return view('depenses.create', compact('categories', 'modePaiements', 'users', 'statuts', 'defaultReference'));
    }

    /**
     * Enregistre une dépense et génère le mouvement de caisse automatique.
     */
    public function store(DepenseRequest $request)
    {
        $validated = $request->validated();

        $depense = $this->depenseService->enregistrerDepense($validated);

        return to_route('depenses.index')->with('alert', 'Dépense #'.$depense->reference.' enregistrée avec succès. Mouvement de caisse généré.');
    }

    /**
     * Détails d'une dépense.
     */
    public function show(Depense $depense)
    {
        $depense->load(['categorieDepense', 'modePaiement', 'user']);

        return view('depenses.show', compact('depense'));
    }

    /**
     * Formulaire de modification d'une dépense.
     */
    public function edit(Depense $depense)
    {
        $categories = CategorieDepense::orderBy('nom')->get();
        $modePaiements = ModePaiement::orderBy('nom')->get();
        $users = User::orderBy('nom')->get();

        $statuts = [
            '1' => 'Validée',
            '0' => 'Annulée',
        ];

        return view('depenses.edit', compact('depense', 'categories', 'modePaiements', 'users', 'statuts'));
    }

    /**
     * Met à jour les informations de la dépense.
     */
    public function update(DepenseRequest $request, Depense $depense)
    {
        $validated = $request->validated();

        $depense->update($validated);

        return to_route('depenses.index')->with('alert', 'Dépense mise à jour avec succès.');
    }

    /**
     * Supprime une dépense.
     */
    public function destroy(Depense $depense)
    {
        $depense->delete();

        return to_route('depenses.index')->with('alert', 'Dépense supprimée avec succès.');
    }
}

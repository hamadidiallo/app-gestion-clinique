<?php

namespace App\Http\Controllers;

use App\Http\Requests\MedecinRequest;
use App\Models\Medecin;
use App\Traits\HasPeriodFilter;

class MedecinController extends Controller
{
    use HasPeriodFilter;

    /**
     * Affiche la liste complète des médecins enregistrés dans la clinique avec leurs activités filtrées par période.
     */
    public function index()
    {
        $period = request('periode', 'tous');
        $customStart = request('date_debut');
        $customEnd = request('date_fin');

        $filter = $this->getPeriodDates($period, $customStart, $customEnd);

        $medecins = Medecin::withCount(['prestations' => function ($q) use ($filter) {
            $this->applyDateFilter($q, $filter['start'], $filter['end'], 'date_prestation');
        }])->withSum(['prestations' => function ($q) use ($filter) {
            $this->applyDateFilter($q, $filter['start'], $filter['end'], 'date_prestation');
        }], 'part_medecin')->orderBy('nom')->get();

        $currentPeriod = $filter['period'];
        $periodLabel = $filter['label'];

        return view('medecins.index', compact('medecins', 'currentPeriod', 'periodLabel'));
    }

    /**
     * Affiche le formulaire de création d'un nouveau médecin.
     */
    public function create()
    {
        $statuts = [
            '1' => 'Actif',
            '0' => 'Inactif',
        ];

        return view('medecins.create', compact('statuts'));
    }

    /**
     * Enregistre un nouveau médecin dans la base de données après validation.
     */
    public function store(MedecinRequest $request)
    {
        $validated = $request->validated();

        Medecin::create($validated);

        return to_route('medecins.index')->with('alert', 'Médecin enregistré avec succès.');
    }

    /**
     * Affiche les détails spécifiques d'un médecin donné.
     */
    public function show(Medecin $medecin)
    {
        $medecin->load(['prestations', 'remunerations']);

        return view('medecins.show', compact('medecin'));
    }

    /**
     * Affiche le formulaire d'édition des informations d'un médecin existant.
     */
    public function edit(Medecin $medecin)
    {
        $statuts = [
            '1' => 'Actif',
            '0' => 'Inactif',
        ];

        return view('medecins.edit', compact('medecin', 'statuts'));
    }

    /**
     * Met à jour les informations du médecin en base de données.
     */
    public function update(MedecinRequest $request, Medecin $medecin)
    {
        $validated = $request->validated();

        $medecin->update($validated);

        return to_route('medecins.index')->with('alert', 'Fiche du médecin mise à jour avec succès.');
    }

    /**
     * Supprime la fiche d'un médecin de la base de données.
     */
    public function destroy(Medecin $medecin)
    {
        $medecin->delete();

        return to_route('medecins.index')->with('alert', 'Médecin supprimé avec succès.');
    }
}

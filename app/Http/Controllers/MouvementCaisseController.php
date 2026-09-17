<?php

namespace App\Http\Controllers;

use App\Http\Requests\MouvementCaisseRequest;
use App\Models\Caisse;
use App\Models\MouvementCaisse;
use App\Models\User;
use App\Traits\HasPeriodFilter;

class MouvementCaisseController extends Controller
{
    use HasPeriodFilter;

    /**
     * Affiche l'ensemble des mouvements d'espèces d'une ou plusieurs caisses filtrés par période.
     */
    public function index()
    {
        $period = request('periode', 'tous');
        $customStart = request('date_debut');
        $customEnd = request('date_fin');

        $filter = $this->getPeriodDates($period, $customStart, $customEnd);

        $query = MouvementCaisse::with(['caisse', 'user'])->latest('date_mouvement');

        $this->applyDateFilter($query, $filter['start'], $filter['end'], 'date_mouvement');

        $mouvementCaisses = $query->get();
        $currentPeriod = $filter['period'];
        $periodLabel = $filter['label'];

        return view('mouvementcaisses.index', compact('mouvementCaisses', 'currentPeriod', 'periodLabel'));
    }

    /**
     * Formulaire d'enregistrement d'un flux de caisse.
     */
    public function create()
    {
        $caisses = Caisse::where('statut', 'ouverte')->latest()->get();
        $users = User::orderBy('nom')->get();

        $types = [
            'entree' => 'Entrée d\'espèces',
            'sortie' => 'Sortie / Retrait d\'espèces',
        ];

        $statuts = [
            '1' => 'Validé',
            '0' => 'Annulé',
        ];

        $defaultReference = 'MVT-'.date('Ymd').'-'.strtoupper(substr(uniqid(), -4));

        return view('mouvementcaisses.create', compact('caisses', 'users', 'types', 'statuts', 'defaultReference'));
    }

    /**
     * Enregistre un mouvement et incrémente/décrémente le total de la caisse.
     */
    public function store(MouvementCaisseRequest $request)
    {
        $validated = $request->validated();

        $mouvement = MouvementCaisse::create($validated);

        // Mise à jour des totaux de la caisse
        $caisse = Caisse::find($validated['caisse_id']);
        if ($caisse) {
            if ($validated['type'] === 'entree') {
                $caisse->increment('total_entrees', $validated['montant']);
            } else {
                $caisse->increment('total_sorties', $validated['montant']);
            }
            $caisse->update([
                'solde_theorique' => $caisse->fonds_initial + $caisse->total_entrees - $caisse->total_sorties,
            ]);
        }

        return to_route('mouvementcaisses.index')->with('alert', 'Mouvement de caisse enregistré avec succès.');
    }

    /**
     * Affiche les détails d'un mouvement.
     */
    public function show(MouvementCaisse $mouvementcaiss)
    {
        $mouvementcaiss->load(['caisse', 'user']);

        return view('mouvementcaisses.show', ['mouvementCaisse' => $mouvementcaiss]);
    }

    /**
     * Formulaire d'édition d'un mouvement.
     */
    public function edit(MouvementCaisse $mouvementcaiss)
    {
        $caisses = Caisse::latest()->get();
        $users = User::orderBy('nom')->get();

        $types = [
            'entree' => 'Entrée d\'espèces',
            'sortie' => 'Sortie / Retrait d\'espèces',
        ];

        $statuts = [
            '1' => 'Validé',
            '0' => 'Annulé',
        ];

        return view('mouvementcaisses.edit', ['mouvementCaisse' => $mouvementcaiss, 'caisses' => $caisses, 'users' => $users, 'types' => $types, 'statuts' => $statuts]);
    }

    /**
     * Met à jour le mouvement de caisse.
     */
    public function update(MouvementCaisseRequest $request, MouvementCaisse $mouvementcaiss)
    {
        $validated = $request->validated();

        $mouvementcaiss->update($validated);

        return to_route('mouvementcaisses.index')->with('alert', 'Mouvement de caisse mis à jour avec succès.');
    }

    /**
     * Supprime un mouvement de caisse.
     */
    public function destroy(MouvementCaisse $mouvementcaiss)
    {
        $mouvementcaiss->delete();

        return to_route('mouvementcaisses.index')->with('alert', 'Mouvement de caisse supprimé avec succès.');
    }
}

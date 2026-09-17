<?php

namespace App\Http\Controllers;

use App\Http\Requests\DetteRequest;
use App\Models\Dette;
use App\Models\Patient;
use App\Models\Ticket;
use App\Models\User;
use App\Traits\HasPeriodFilter;

class DetteController extends Controller
{
    use HasPeriodFilter;

    /**
     * Affiche le suivi des créances et impayés patients filtrés par période et mot-clé.
     */
    public function index()
    {
        $period = request('periode', 'tous');
        $customStart = request('date_debut');
        $customEnd = request('date_fin');

        $filter = $this->getPeriodDates($period, $customStart, $customEnd);

        $query = Dette::with(['ticket', 'patient', 'user'])->latest('date_creation');

        $this->applyDateFilter($query, $filter['start'], $filter['end'], 'date_creation');
        $this->applySearchFilter($query, request('q', request('search')), ['patient.nom', 'patient.prenom', 'ticket.reference', 'description']);

        if (request('statut')) {
            $query->where('statut', request('statut'));
        }

        $dettes = $query->get();
        $currentPeriod = $filter['period'];
        $periodLabel = $filter['label'];

        return view('dettes.index', compact('dettes', 'currentPeriod', 'periodLabel'));
    }

    /**
     * Formulaire d'enregistrement d'une dette.
     */
    public function create()
    {
        $tickets = Ticket::latest()->get();
        $patients = Patient::orderBy('nom')->get();
        $users = User::orderBy('nom')->get();

        $statuts = [
            'en_cours' => 'En cours',
            'partiel' => 'Règlement partiel',
            'soldee' => 'Soldée',
            'douteuse' => 'Créance douteuse',
        ];

        return view('dettes.create', compact('tickets', 'patients', 'users', 'statuts'));
    }

    /**
     * Enregistre une nouvelle dette.
     */
    public function store(DetteRequest $request)
    {
        $validated = $request->validated();

        Dette::create($validated);

        return to_route('dettes.index')->with('alert', 'Dette enregistrée avec succès.');
    }

    /**
     * Affiche les détails complets d'une dette et son historique de recouvrement.
     */
    public function show(Dette $dette)
    {
        $dette->load(['ticket.details', 'ticket.paiements.modePaiement', 'patient', 'user']);

        return view('dettes.show', compact('dette'));
    }

    /**
     * Formulaire d'édition de dette.
     */
    public function edit(Dette $dette)
    {
        $tickets = Ticket::latest()->get();
        $patients = Patient::orderBy('nom')->get();
        $users = User::orderBy('nom')->get();

        $statuts = [
            'en_cours' => 'En cours',
            'partiel' => 'Règlement partiel',
            'soldee' => 'Soldée',
            'douteuse' => 'Créance douteuse',
        ];

        return view('dettes.edit', compact('dette', 'tickets', 'patients', 'users', 'statuts'));
    }

    /**
     * Met à jour les informations de la dette.
     */
    public function update(DetteRequest $request, Dette $dette)
    {
        $validated = $request->validated();

        $dette->update($validated);

        return to_route('dettes.index')->with('alert', 'Dette mise à jour avec succès.');
    }

    /**
     * Supprime la dette.
     */
    public function destroy(Dette $dette)
    {
        $dette->delete();

        return to_route('dettes.index')->with('alert', 'Dette supprimée avec succès.');
    }
}

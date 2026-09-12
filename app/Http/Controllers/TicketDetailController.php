<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketDetailRequest;
use App\Models\Prestation;
use App\Models\Ticket;
use App\Models\TicketDetail;

class TicketDetailController extends Controller
{
    /**
     * Affiche la liste globale de tous les détails de tickets.
     */
    public function index()
    {
        // Récupère l'ensemble des lignes de détails avec ticket et prestation
        $ticketDetails = TicketDetail::with(['ticket', 'prestation'])->latest()->get();

        // Retourne la vue d'index
        return view('ticketdetails.index', compact('ticketDetails'));
    }

    /**
     * Formulaire d'ajout d'une ligne de prestation à un ticket.
     */
    public function create()
    {
        // Charge la liste des tickets et des prestations
        $tickets = Ticket::latest()->get();
        $prestations = Prestation::orderBy('nom')->get();

        // Options de statut
        $statuts = [
            '1' => 'Actif',
            '0' => 'Inactif',
        ];

        // Retourne la vue de création
        return view('ticketdetails.create', compact('tickets', 'prestations', 'statuts'));
    }

    /**
     * Enregistre une nouvelle ligne de détail de ticket.
     */
    public function store(TicketDetailRequest $request)
    {
        // Extrait les données validées
        $validated = $request->validated();

        // Crée la ligne de détail
        TicketDetail::create($validated);

        // Redirige avec message de confirmation
        return to_route('ticketdetails.index')->with('alert', 'Ligne de prestation ajoutée au ticket avec succès.');
    }

    /**
     * Affiche les informations détaillées d'une ligne spécifique.
     */
    public function show(TicketDetail $ticketdetail)
    {
        // Charge les relations
        $ticketdetail->load(['ticket.patient', 'prestation']);

        // Vue de détails
        return view('ticketdetails.show', compact('ticketdetail'));
    }

    /**
     * Formulaire de modification d'une ligne de ticket.
     */
    public function edit(TicketDetail $ticketdetail)
    {
        $tickets = Ticket::latest()->get();
        $prestations = Prestation::orderBy('nom')->get();

        $statuts = [
            '1' => 'Actif',
            '0' => 'Inactif',
        ];

        // Vue d'édition
        return view('ticketdetails.edit', compact('ticketdetail', 'tickets', 'prestations', 'statuts'));
    }

    /**
     * Met à jour la ligne de ticket.
     */
    public function update(TicketDetailRequest $request, TicketDetail $ticketdetail)
    {
        // Validation
        $validated = $request->validated();

        // Mise à jour
        $ticketdetail->update($validated);

        // Redirection
        return to_route('ticketdetails.index')->with('alert', 'Ligne de détail mise à jour avec succès.');
    }

    /**
     * Supprime une ligne de détail de ticket.
     */
    public function destroy(TicketDetail $ticketdetail)
    {
        // Suppression
        $ticketdetail->delete();

        // Redirection
        return to_route('ticketdetails.index')->with('alert', 'Ligne de détail supprimée avec succès.');
    }
}

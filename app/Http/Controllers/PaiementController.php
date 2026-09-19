<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaiementRequest;
use App\Models\Assurance;
use App\Models\ModePaiement;
use App\Models\Paiement;
use App\Models\Ticket;
use App\Models\User;
use App\Services\PaiementService;
use App\Traits\HasPeriodFilter;

class PaiementController extends Controller
{
    use HasPeriodFilter;

    public function __construct(
        protected PaiementService $paiementService
    ) {}

    /**
     * Affiche l'historique des paiements / règlements filtrés par période.
     */
    public function index()
    {
        $period = request('periode', 'tous');
        $customStart = request('date_debut');
        $customEnd = request('date_fin');

        $filter = $this->getPeriodDates($period, $customStart, $customEnd);

        $query = Paiement::with(['ticket.patient', 'user', 'assurance', 'modePaiement'])->latest('date_paiement');

        $this->applyDateFilter($query, $filter['start'], $filter['end'], 'date_paiement');

        $paiements = $query->get();
        $currentPeriod = $filter['period'];
        $periodLabel = $filter['label'];

        $statuses = [
            'valide' => 'Validé',
            'actif' => 'Actif',
            'inactif' => 'Inactif',
            'annule' => 'Annulé',
        ];

        return view('paiements.index', compact('paiements', 'currentPeriod', 'periodLabel', 'statuses'));
    }

    /**
     * Formulaire de saisie d'un nouveau paiement / règlement de reliquat.
     */
    public function create()
    {
        $selectedTicketId = request('ticket_id');
        $selectedTicket = null;
        if ($selectedTicketId) {
            $selectedTicket = Ticket::with(['patient', 'assurance'])->find($selectedTicketId);
        }

        // Seuls les tickets ayant un reliquat/reste à payer > 0
        $tickets = Ticket::where('reste_a_payer', '>', 0)
            ->orWhere('statut', '!=', 'paye')
            ->with(['patient', 'assurance'])
            ->latest()
            ->get();

        $users = User::orderBy('nom')->get();
        $assurances = Assurance::where('statut', true)->orderBy('nom')->get();
        $modePaiements = ModePaiement::where('statut', true)->orderBy('nom')->get();

        $statuts = [
            'valide' => 'Validé',
            'actif' => 'Actif',
            'inactif' => 'Inactif',
            'annule' => 'Annulé',
        ];

        $defaultReference = 'PAY-'.date('Ymd').'-'.strtoupper(substr(uniqid(), -4));

        return view('paiements.create', compact('tickets', 'users', 'assurances', 'modePaiements', 'statuts', 'defaultReference', 'selectedTicket', 'selectedTicketId'));
    }

    /**
     * Enregistre un nouveau paiement via PaiementService avec création automatique de la recette et du mouvement de caisse.
     */
    public function store(PaiementRequest $request)
    {
        $validated = $request->validated();

        try {
            $paiement = $this->paiementService->enregistrerPaiement($validated);

            return to_route('paiements.show', $paiement)->with('alert', 'Règlement #'.$paiement->reference.' enregistré avec succès. Vous pouvez imprimer le reçu ci-dessous.');
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['montant_recu' => $e->getMessage()]);
        }
    }

    /**
     * Affiche les détails du reçu de paiement.
     */
    public function show(Paiement $paiement)
    {
        $paiement->load(['ticket.patient', 'ticket.details', 'user', 'assurance', 'modePaiement', 'recette']);

        return view('paiements.show', compact('paiement'));
    }

    /**
     * Génère la vue imprimable du reçu de caisse (Ticket thermique 80mm).
     */
    public function print(Paiement $paiement)
    {
        $paiement->load(['ticket.patient', 'ticket.details', 'user', 'assurance', 'modePaiement']);

        return view('paiements.print', compact('paiement'));
    }

    /**
     * Formulaire d'édition de règlement.
     */
    public function edit(Paiement $paiement)
    {
        $tickets = Ticket::with('patient')->latest()->get();
        $users = User::orderBy('nom')->get();
        $assurances = Assurance::orderBy('nom')->get();
        $modePaiements = ModePaiement::orderBy('nom')->get();

        $statuts = [
            'valide' => 'Validé',
            'actif' => 'Actif',
            'inactif' => 'Inactif',
            'annule' => 'Annulé',
        ];

        return view('paiements.edit', compact('paiement', 'tickets', 'users', 'assurances', 'modePaiements', 'statuts'));
    }

    /**
     * Met à jour les informations d'un paiement.
     */
    public function update(PaiementRequest $request, Paiement $paiement)
    {
        $validated = $request->validated();

        $paiement->update($validated);

        return to_route('paiements.index')->with('alert', 'Règlement mis à jour avec succès.');
    }

    /**
     * Supprime un paiement.
     */
    public function destroy(Paiement $paiement)
    {
        $paiement->delete();

        return to_route('paiements.index')->with('alert', 'Règlement supprimé avec succès.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketRequest;
use App\Models\Assurance;
use App\Models\Medecin;
use App\Models\Patient;
use App\Models\Prestation;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\User;
use App\Services\CalculFacturationService;
use App\Services\TicketService;
use App\Traits\HasPeriodFilter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class TicketController extends Controller
{
    use HasPeriodFilter;

    public function __construct(
        protected CalculFacturationService $calculService,
        protected TicketService $ticketService
    ) {}

    /**
     * Affiche la liste complète des tickets de caisse / factures filtrés par période et mot-clé.
     */
    public function index()
    {
        $period = request('periode', 'tous');
        $customStart = request('date_debut');
        $customEnd = request('date_fin');

        $filter = $this->getPeriodDates($period, $customStart, $customEnd);

        $query = Ticket::with(['patient', 'assurance', 'user', 'service', 'medecin', 'details.prestation.service', 'details.prestation.medecin'])->latest('date_ticket');

        $this->applyDateFilter($query, $filter['start'], $filter['end'], 'date_ticket');
        $this->applySearchFilter($query, request('q', request('search')), ['reference', 'patient.nom', 'patient.prenom', 'patient.matricule', 'description']);

        if (request('statut')) {
            $query->where('statut', request('statut'));
        }

        $tickets = $query->get();
        $currentPeriod = $filter['period'];
        $periodLabel = $filter['label'];

        return view('tickets.index', compact('tickets', 'currentPeriod', 'periodLabel'));
    }

    /**
     * Affiche le formulaire de création d'un ticket.
     */
    public function create()
    {
        $currentUser = auth()->user() ?? User::first();
        if (!$currentUser) {
            $role = \App\Models\Role::firstOrCreate(['nom' => 'Administrateur']);
            $currentUser = User::create([
                'nom' => 'Admin',
                'prenom' => 'Système',
                'email' => 'admin@clinique.com',
                'password' => bcrypt('password'),
                'role_id' => $role->id,
            ]);
        }

        $patients = Patient::orderBy('nom')->get();
        $users = User::orderBy('nom')->get();
        $assurances = Assurance::where('statut', true)->orderBy('nom')->get();
        $services = Service::where('statut', true)->orderBy('nom')->get();
        $medecins = Medecin::where('statut', true)->orderBy('nom')->get();

        $selectedPatient = null;
        if (request()->has('patient_id')) {
            $selectedPatient = Patient::with(['cartesAssurance' => function ($q) {
                $q->where('statut', true)->with('assurance');
            }])->find(request()->get('patient_id'));
        }

        $statuts = [
            'en_attente' => 'En attente de paiement',
            'partiellement_paye' => 'Partiellement payé',
            'paye' => 'Entièrement payé',
            'annule' => 'Annulé',
        ];

        $defaultReference = $this->ticketService->genererReferenceTicket();

        return view('tickets.create', compact('patients', 'users', 'assurances', 'services', 'medecins', 'statuts', 'defaultReference', 'currentUser', 'selectedPatient'));
    }

    /**
     * Enregistre un nouveau ticket dans la base de données.
     */
    public function store(TicketRequest $request)
    {
        $validated = $request->validated();

        if (!empty($request->input('prestation_ids'))) {
            $ticket = $this->ticketService->creerTicketDepuisPrestations(
                (array) $request->input('prestation_ids'),
                $validated['user_id'] ?? auth()->id(),
                $validated['description'] ?? null
            );

            return to_route('tickets.print', $ticket)->with('alert', 'Ticket ' . $ticket->reference . ' généré automatiquement avec succès.');
        }

        if (empty($validated['reference'])) {
            $validated['reference'] = $this->ticketService->genererReferenceTicket();
        }

        if (empty($validated['user_id'])) {
            $validated['user_id'] = auth()->id() ?? User::first()->id;
        }

        if (empty($validated['date_ticket'])) {
            $validated['date_ticket'] = Carbon::now();
        }

        if (empty($validated['date_expiration'])) {
            $validated['date_expiration'] = Carbon::parse($validated['date_ticket'])->addDays(7);
        }

        $montantTotal = (float) ($validated['montant_total'] ?? 0);
        $tauxAssurance = (float) ($request->input('taux_assurance', 0));

        $ventilation = $this->calculService->calculerVentilationFacture($montantTotal, $tauxAssurance);
        
        $validated['montant_total'] = $ventilation['montant_total'];
        $validated['montant_assurance'] = $ventilation['montant_assurance'];
        $validated['montant_patient'] = $ventilation['montant_patient'];
        $validated['montant_paye'] = (float) ($validated['montant_paye'] ?? 0);

        $calculs = $this->calculService->calculerMonnaieEtImputation(
            $validated['montant_paye'],
            $validated['montant_patient']
        );

        $validated['reste_a_payer'] = $calculs['reste_a_payer'];
        $validated['statut'] = $validated['reste_a_payer'] <= 0 ? 'paye' : ($validated['montant_paye'] > 0 ? 'partiellement_paye' : 'en_attente');

        // Sécurité schéma : ignorer les colonnes si la migration n'a pas encore été exécutée en BDD
        if (!Schema::hasColumn('tickets', 'service_id')) {
            unset($validated['service_id']);
        }
        if (!Schema::hasColumn('tickets', 'medecin_id')) {
            unset($validated['medecin_id']);
        }

        $ticket = Ticket::create($validated);

        return to_route('tickets.print', $ticket)->with('alert', 'Ticket de facturation généré avec succès.');
    }

    /**
     * Affiche le détail d'un ticket spécifique avec ses prestations associées.
     */
    public function show(Ticket $ticket)
    {
        $ticket->load(['patient', 'user', 'assurance', 'service', 'medecin', 'details.prestation.medecin', 'details.prestation.service', 'paiements.modePaiement', 'dette']);

        return view('tickets.show', compact('ticket'));
    }

    /**
     * Formulaire d'édition d'un ticket existant.
     */
    public function edit(Ticket $ticket)
    {
        $ticket->load(['assurance', 'service', 'medecin']);
        $patients = Patient::orderBy('nom')->get();
        $users = User::orderBy('nom')->get();
        $assurances = Assurance::where('statut', true)->orderBy('nom')->get();
        $services = Service::where('statut', true)->orderBy('nom')->get();
        $medecins = Medecin::where('statut', true)->orderBy('nom')->get();

        $statuts = [
            'en_attente' => 'En attente de paiement',
            'partiellement_paye' => 'Partiellement payé',
            'paye' => 'Entièrement payé',
            'annule' => 'Annulé',
        ];

        return view('tickets.edit', compact('ticket', 'patients', 'users', 'assurances', 'services', 'medecins', 'statuts'));
    }

    /**
     * Met à jour les informations du ticket.
     */
    public function update(TicketRequest $request, Ticket $ticket)
    {
        $validated = $request->validated();

        $calculs = $this->calculService->calculerMonnaieEtImputation(
            $validated['montant_paye'] ?? $ticket->montant_paye,
            $validated['montant_patient'] ?? $ticket->montant_patient
        );
        $validated['reste_a_payer'] = $calculs['reste_a_payer'];
        $validated['statut'] = $validated['reste_a_payer'] <= 0 ? 'paye' : ($validated['montant_paye'] > 0 ? 'partiellement_paye' : 'en_attente');

        // Sécurité schéma : ignorer les colonnes si la migration n'a pas encore été exécutée en BDD
        if (!Schema::hasColumn('tickets', 'service_id')) {
            unset($validated['service_id']);
        }
        if (!Schema::hasColumn('tickets', 'medecin_id')) {
            unset($validated['medecin_id']);
        }

        $ticket->update($validated);

        return to_route('tickets.index')->with('alert', 'Ticket mis à jour avec succès.');
    }

    /**
     * Supprime un ticket de caisse.
     */
    public function destroy(Ticket $ticket)
    {
        $ticket->delete();

        return to_route('tickets.index')->with('alert', 'Ticket supprimé avec succès.');
    }

    /**
     * Affiche la vue d'impression du ticket thermique 80mm et format PDF/A4.
     */
    public function print(Ticket $ticket)
    {
        $ticket->load(['patient', 'user', 'assurance', 'service', 'medecin', 'details.prestation.medecin', 'details.prestation.service', 'paiements.modePaiement']);

        return view('tickets.print', compact('ticket'));
    }
}

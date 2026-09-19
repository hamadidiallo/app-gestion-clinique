<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketRequest;
use App\Models\Acte;
use App\Models\Assurance;
use App\Models\Caisse;
use App\Models\Dette;
use App\Models\Medecin;
use App\Models\ModePaiement;
use App\Models\Patient;
use App\Models\Prestation;
use App\Models\Recette;
use App\Models\Role;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\User;
use App\Services\CalculFacturationService;
use App\Services\GestionCaisseService;
use App\Services\PrestationService;
use App\Services\TicketService;
use App\Traits\HasPeriodFilter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class TicketController extends Controller
{
    use HasPeriodFilter;

    public function __construct(
        protected CalculFacturationService $calculService,
        protected TicketService $ticketService,
        protected PrestationService $prestationService
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
        $this->applySearchFilter($query, request('q', request('search')), ['reference', 'patient.nom', 'patient.prenom', 'patient.numero_assure', 'patient.telephone', 'description']);

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
        if (! $currentUser) {
            $role = Role::firstOrCreate(['nom' => 'Administrateur']);
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
        $modesPaiement = ModePaiement::where('statut', true)->get();
        $actes = Acte::with('service')->where('statut', true)->orderBy('nom')->get();

        $selectedPatient = null;
        if (request()->has('patient_id')) {
            $selectedPatient = Patient::with(['cartesAssurance' => function ($q) {
                $q->where('statut', true)->with('assurance');
            }])->find(request()->get('patient_id'));
        }

        $selectedActe = null;
        if (request()->has('acte_id')) {
            $selectedActe = Acte::with('service')->find(request()->get('acte_id'));
        }

        $statuts = [
            'en_attente' => 'En attente de paiement',
            'partiellement_paye' => 'Partiellement payé',
            'paye' => 'Entièrement payé',
            'annule' => 'Annulé',
        ];

        $defaultReference = $this->ticketService->genererReferenceTicket();

        return view('tickets.create', compact('patients', 'users', 'assurances', 'services', 'medecins', 'statuts', 'defaultReference', 'currentUser', 'selectedPatient', 'selectedActe', 'modesPaiement', 'actes'));
    }

    /**
     * Enregistre un nouveau ticket dans la base de données avec ses lignes détaillées (actes, médicaments, hospitalisations).
     */
    public function store(TicketRequest $request)
    {
        $validated = $request->validated();

        if (! empty($request->input('prestation_ids'))) {
            $ticket = $this->ticketService->creerTicketDepuisPrestations(
                (array) $request->input('prestation_ids'),
                $validated['user_id'] ?? auth()->id(),
                $validated['description'] ?? null
            );

            return to_route('tickets.print', $ticket)->with('alert', 'Ticket '.$ticket->reference.' généré automatiquement avec succès.');
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

        // 1. Analyse des lignes d'articles dynamiques (actes, médicaments, hospitalisations)
        $rawItems = $request->input('items', []);
        $itemsToCreate = [];

        if (! empty($rawItems) && is_array($rawItems)) {
            $sumBrut = 0.0;
            $sumAssurance = 0.0;
            $sumPatient = 0.0;

            foreach ($rawItems as $item) {
                $designation = trim($item['designation'] ?? '');
                if ($designation === '') {
                    continue;
                }
                $typeItem = in_array($item['type_item'] ?? '', ['acte', 'medicament', 'hospitalisation'])
                    ? $item['type_item']
                    : 'acte';
                $quantite = max(0.01, (float) ($item['quantite'] ?? 1));
                $prixUnitaire = max(0.0, (float) ($item['prix_unitaire'] ?? 0));
                $totalLigne = round($quantite * $prixUnitaire, 2);

                $ligneTaux = isset($item['taux_couverture']) && $item['taux_couverture'] !== ''
                    ? (float) $item['taux_couverture']
                    : $tauxAssurance;

                $ligneAssurance = round(($totalLigne * $ligneTaux) / 100, 2);
                $lignePatient = round($totalLigne - $ligneAssurance, 2);

                $sumBrut += $totalLigne;
                $sumAssurance += $ligneAssurance;
                $sumPatient += $lignePatient;

                $itemsToCreate[] = [
                    'designation' => $designation,
                    'type_item' => $typeItem,
                    'quantite' => $quantite,
                    'prix_unitaire' => $prixUnitaire,
                    'montant_total' => $totalLigne,
                    'taux_couverture' => $ligneTaux,
                    'montant_assurance' => $ligneAssurance,
                    'montant_patient' => $lignePatient,
                    'prestation_id' => ! empty($item['prestation_id']) ? (int) $item['prestation_id'] : null,
                ];
            }

            if (! empty($itemsToCreate)) {
                $montantTotal = $sumBrut;
                $validated['montant_total'] = $sumBrut;
                $validated['montant_assurance'] = $sumAssurance;
                $validated['montant_patient'] = $sumPatient;
            }
        }

        if (empty($itemsToCreate)) {
            $ventilation = $this->calculService->calculerVentilationFacture($montantTotal, $tauxAssurance);
            $validated['montant_total'] = $ventilation['montant_total'];
            $validated['montant_assurance'] = $ventilation['montant_assurance'];
            $validated['montant_patient'] = $ventilation['montant_patient'];
        }

        $validated['montant_paye'] = (float) ($validated['montant_paye'] ?? 0);

        $calculs = $this->calculService->calculerMonnaieEtImputation(
            $validated['montant_paye'],
            $validated['montant_patient']
        );

        $validated['reste_a_payer'] = $calculs['reste_a_payer'];
        $validated['statut'] = $validated['reste_a_payer'] <= 0 ? 'paye' : ($validated['montant_paye'] > 0 ? 'partiellement_paye' : 'en_attente');

        // Auto-détection du service_id si non renseigné dans le formulaire mais présent dans les actes
        if (empty($validated['service_id']) && ! empty($itemsToCreate)) {
            foreach ($itemsToCreate as $item) {
                $acte = Acte::where('nom', $item['designation'])->first();
                if ($acte && $acte->service_id) {
                    $validated['service_id'] = $acte->service_id;
                    break;
                }
            }
        }

        // Sécurité schéma : ignorer les colonnes si la migration n'a pas encore été exécutée en BDD
        if (! Schema::hasColumn('tickets', 'service_id')) {
            unset($validated['service_id']);
        }
        $ticketData = $validated;
        unset($ticketData['items'], $ticketData['mode_paiement_id']);

        $ticket = Ticket::create($ticketData);

        // 2. Sauvegarde des lignes détaillées et génération automatique des Soins (Prestations)
        $medecinId = $ticket->medecin_id;
        $medecin = $medecinId ? Medecin::find($medecinId) : null;
        $dateTicket = $ticket->date_ticket ?? Carbon::now();

        if (! empty($itemsToCreate)) {
            foreach ($itemsToCreate as $it) {
                if ($it['type_item'] === 'acte' && empty($it['prestation_id'])) {
                    $acte = Acte::where('nom', $it['designation'])
                        ->orWhere('code', $it['designation'])
                        ->first();

                    $serviceId = $ticket->service_id
                        ?? $acte?->service_id
                        ?? Service::first()?->id;

                    if ($serviceId) {
                        $partage = $this->prestationService->obtenirPourcentagesPartage(
                            $serviceId,
                            $medecin,
                            $dateTicket,
                            $acte
                        );

                        $montantLigne = (float) $it['montant_total'];
                        $partMedecin = round(($montantLigne * $partage['pourcentage_medecin']) / 100.0, 2);
                        $partClinique = max(0.0, round($montantLigne - $partMedecin, 2));

                        $prestation = Prestation::create([
                            'patient_id' => $ticket->patient_id,
                            'service_id' => $serviceId,
                            'medecin_id' => $medecinId,
                            'acte_id' => $acte?->id,
                            'type' => $it['designation'],
                            'montant' => $montantLigne,
                            'taux_couverture' => (float) ($it['taux_couverture'] ?? 0),
                            'montant_assurance' => (float) ($it['montant_assurance'] ?? 0),
                            'montant_patient' => (float) ($it['montant_patient'] ?? $montantLigne),
                            'pourcentage_medecin' => $partage['pourcentage_medecin'],
                            'pourcentage_clinique' => $partage['pourcentage_clinique'],
                            'part_medecin' => $partMedecin,
                            'part_clinique' => $partClinique,
                            'date_prestation' => $dateTicket,
                            'description' => 'Soin généré automatiquement depuis Ticket #'.$ticket->reference,
                            'statut' => true,
                        ]);

                        $it['prestation_id'] = $prestation->id;
                    }
                }

                $ticket->details()->create($it);
            }
        } else {
            $service = isset($validated['service_id']) ? Service::find($validated['service_id']) : null;
            $serviceId = $service?->id ?? Service::first()?->id;
            $libelleDefaut = $service ? 'Prestation - '.$service->nom : 'Consultation Médicale';

            $prestationId = null;
            if ($serviceId) {
                $partage = $this->prestationService->obtenirPourcentagesPartage(
                    $serviceId,
                    $medecin,
                    $dateTicket
                );

                $montantLigne = (float) $validated['montant_total'];
                $partMedecin = round(($montantLigne * $partage['pourcentage_medecin']) / 100.0, 2);
                $partClinique = max(0.0, round($montantLigne - $partMedecin, 2));

                $prestation = Prestation::create([
                    'patient_id' => $ticket->patient_id,
                    'service_id' => $serviceId,
                    'medecin_id' => $medecinId,
                    'acte_id' => null,
                    'type' => $libelleDefaut,
                    'montant' => $montantLigne,
                    'taux_couverture' => (float) $tauxAssurance,
                    'montant_assurance' => (float) $validated['montant_assurance'],
                    'montant_patient' => (float) $validated['montant_patient'],
                    'pourcentage_medecin' => $partage['pourcentage_medecin'],
                    'pourcentage_clinique' => $partage['pourcentage_clinique'],
                    'part_medecin' => $partMedecin,
                    'part_clinique' => $partClinique,
                    'date_prestation' => $dateTicket,
                    'description' => 'Soin généré automatiquement depuis Ticket #'.$ticket->reference,
                    'statut' => true,
                ]);

                $prestationId = $prestation->id;
            }

            $ticket->details()->create([
                'prestation_id' => $prestationId,
                'designation' => $libelleDefaut,
                'type_item' => 'acte',
                'quantite' => 1,
                'prix_unitaire' => $validated['montant_total'],
                'montant_total' => $validated['montant_total'],
                'taux_couverture' => $tauxAssurance,
                'montant_assurance' => $validated['montant_assurance'],
                'montant_patient' => $validated['montant_patient'],
            ]);
        }

        // 3. Suivi de dette automatique si reste à payer
        if ($ticket->reste_a_payer > 0) {
            Dette::updateOrCreate(
                ['ticket_id' => $ticket->id],
                [
                    'patient_id' => $ticket->patient_id,
                    'user_id' => $ticket->user_id,
                    'montant_initial' => $ticket->montant_patient,
                    'montant_paye' => $ticket->montant_paye,
                    'reste_a_payer' => $ticket->reste_a_payer,
                    'statut' => $ticket->montant_paye > 0 ? 'partiellement_reglee' : 'en_cours',
                    'date_creation' => Carbon::now()->toDateString(),
                    'description' => 'Reste à payer sur ticket '.$ticket->reference,
                ]
            );
        }

        // 4. Traitement automatique du paiement guichet immédiat
        if ($validated['montant_paye'] > 0) {
            $modePaiementId = $request->input('mode_paiement_id');
            if (! $modePaiementId) {
                $modePaiement = ModePaiement::where('statut', true)->first()
                    ?? ModePaiement::firstOrCreate(['nom' => 'Espèces'], ['code' => 'ESP', 'statut' => true]);
                $modePaiementId = $modePaiement->id;
            }

            $montantImpute = min($validated['montant_paye'], $ticket->montant_patient);
            $montantRendu = max(0.0, round($validated['montant_paye'] - $ticket->montant_patient, 2));

            $paiement = $ticket->paiements()->create([
                'user_id' => $ticket->user_id,
                'type_payeur' => 'patient',
                'montant_recu' => $validated['montant_paye'],
                'montant_impute' => $montantImpute,
                'montant_rendu' => $montantRendu,
                'mode_paiement_id' => $modePaiementId,
                'date_paiement' => Carbon::now(),
                'statut' => 'valide',
                'description' => 'Encaissement initial ticket '.$ticket->reference,
            ]);

            // Résolution de la caisse ouverte pour l'encaissement immédiat
            $caisseOuverte = Caisse::where('user_id', $ticket->user_id)
                ->where('statut', 'ouverte')
                ->latest('date_ouverture')
                ->first();

            if (! $caisseOuverte) {
                if (auth()->check() && auth()->user()->hasRole('Caissier')) {
                    DB::rollBack();

                    return back()->withInput()->withErrors([
                        'montant_paye' => 'Votre session de caisse est actuellement fermée. Veuillez ouvrir votre caisse avant de percevoir un paiement, ou laissez le montant payé à 0 pour émettre le ticket en attente.',
                    ]);
                }

                $caisseOuverte = Caisse::where('statut', 'ouverte')->latest('date_ouverture')->first();
            }

            if ($caisseOuverte) {
                $refRecette = 'REC-'.date('Ymd').'-'.strtoupper(substr(uniqid(), -4));
                Recette::create([
                    'ticket_id' => $ticket->id,
                    'paiement_id' => $paiement->id,
                    'user_id' => $ticket->user_id,
                    'mode_paiement_id' => $modePaiementId,
                    'reference' => $refRecette,
                    'montant' => $montantImpute,
                    'date_recette' => Carbon::now()->toDateString(),
                    'description' => 'Recette ticket '.$ticket->reference,
                    'statut' => true,
                ]);

                app(GestionCaisseService::class)->enregistrerMouvement(
                    caisse: $caisseOuverte,
                    userId: $ticket->user_id,
                    type: 'entree',
                    origine: 'paiement',
                    reference: $refRecette,
                    montant: $montantImpute,
                    description: 'Encaissement ticket '.$ticket->reference
                );
            }
        }

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
        if (! Schema::hasColumn('tickets', 'service_id')) {
            unset($validated['service_id']);
        }
        if (! Schema::hasColumn('tickets', 'medecin_id')) {
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

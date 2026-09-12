<?php

namespace App\Services;

use App\Models\CarteAssurance;
use App\Models\Prestation;
use App\Models\Ticket;
use App\Models\TicketDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TicketService
{
    public function __construct(
        protected JournalActiviteService $journalService
    ) {}

    /**
     * Génère une référence unique de ticket (ou réutilise la référence de carte d'assurance si le patient est assuré).
     *
     * @param int|null $patientId
     * @return string
     */
    public function genererReferenceTicket(?int $patientId = null): string
    {
        if ($patientId) {
            $patient = \App\Models\Patient::with(['cartesAssurance' => function ($q) {
                $q->where('statut', true);
            }])->find($patientId);

            if ($patient && $patient->statut === 'assure') {
                $carte = $patient->cartesAssurance->first();
                if ($carte && !empty($carte->reference)) {
                    return $carte->reference;
                }
            }
        }

        return 'TCK-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
    }

    /**
     * Crée automatiquement un ticket et ses détails à partir d'une ou plusieurs prestations.
     *
     * @param array $prestationIds Identifiants des prestations à inclure
     * @param int|null $userId Identifiant de l'agent caissier
     * @param string|null $description Explication ou observation
     * @return Ticket
     */
    public function creerTicketDepuisPrestations(array $prestationIds, ?int $userId = null, ?string $description = null): Ticket
    {
        return DB::transaction(function () use ($prestationIds, $userId, $description) {
            $effectiveUserId = $userId ?? auth()->id();

            if (!$effectiveUserId) {
                $user = User::first();
                $effectiveUserId = $user ? $user->id : null;
            }

            if (!$effectiveUserId) {
                throw new \InvalidArgumentException("Un utilisateur caissier est requis pour générer un ticket.");
            }

            $prestations = Prestation::with(['patient', 'service'])->whereIn('id', $prestationIds)->get();

            if ($prestations->isEmpty()) {
                throw new \InvalidArgumentException("Aucune prestation valide n'a été trouvée.");
            }

            $firstPrestation = $prestations->first();
            $patientId = $firstPrestation->patient_id;

            // Vérification de la carte d'assurance pour la référence de l'assurance
            $carte = CarteAssurance::where('patient_id', $patientId)->where('statut', true)->first();
            $assuranceId = $carte ? $carte->assurance_id : null;

            $dateTicket = Carbon::now();
            $dateExpiration = $dateTicket->copy()->addDays(7); // Ticket valide 7 jours

            $montantTotal = 0.0;
            $montantAssurance = 0.0;
            $montantPatient = 0.0;

            foreach ($prestations as $p) {
                $montantTotal += (float) $p->montant;
                $montantAssurance += (float) $p->montant_assurance;
                $montantPatient += (float) $p->montant_patient;
            }

            $ticketData = [
                'patient_id' => $patientId,
                'assurance_id' => $assuranceId,
                'user_id' => $effectiveUserId,
                'reference' => $this->genererReferenceTicket($patientId),
                'date_ticket' => $dateTicket,
                'date_expiration' => $dateExpiration,
                'montant_total' => round($montantTotal, 2),
                'montant_assurance' => round($montantAssurance, 2),
                'montant_patient' => round($montantPatient, 2),
                'montant_paye' => 0.0,
                'reste_a_payer' => round($montantPatient, 2),
                'statut' => 'en_attente',
                'description' => $description,
            ];

            if (\Illuminate\Support\Facades\Schema::hasColumn('tickets', 'service_id')) {
                $ticketData['service_id'] = $firstPrestation->service_id ?? null;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('tickets', 'medecin_id')) {
                $ticketData['medecin_id'] = $firstPrestation->medecin_id ?? null;
            }

            $ticket = Ticket::create($ticketData);

            foreach ($prestations as $p) {
                TicketDetail::create([
                    'ticket_id' => $ticket->id,
                    'prestation_id' => $p->id,
                    'quantite' => 1.0,
                    'prix_unitaire' => $p->montant,
                    'montant_total' => $p->montant,
                    'taux_couverture' => $p->taux_couverture,
                    'montant_assurance' => $p->montant_assurance,
                    'montant_patient' => $p->montant_patient,
                ]);
            }

            $this->journalService->log(
                action: 'creation_ticket',
                module: 'facturation',
                objetType: Ticket::class,
                objetId: $ticket->id,
                description: 'Génération automatique ticket ' . $ticket->reference . ' (Reste: ' . $ticket->reste_a_payer . ' FCFA)',
                nouvellesValeurs: $ticket->toArray()
            );

            return $ticket;
        });
    }
}

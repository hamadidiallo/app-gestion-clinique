<?php

namespace App\Services;

use App\Models\Caisse;
use App\Models\Dette;
use App\Models\Paiement;
use App\Models\Recette;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaiementService
{
    public function __construct(
        protected CalculFacturationService $calculService,
        protected GestionCaisseService $caisseService,
        protected JournalActiviteService $journalService,
        protected RemunerationService $remunerationService
    ) {}

    /**
     * Enregistre un paiement financier atomique avec toutes ses répercussions automatiques.
     *
     * @param  array  $donnees  Contient ticket_id, montant_recu, mode_paiement_id, user_id optionnel, description
     */
    public function enregistrerPaiement(array $donnees): Paiement
    {
        return DB::transaction(function () use ($donnees) {
            $effectiveUserId = $donnees['user_id'] ?? auth()->id();

            if (! $effectiveUserId) {
                $user = User::first();
                $effectiveUserId = $user ? $user->id : null;
            }

            if (! $effectiveUserId) {
                throw new \InvalidArgumentException('Identifiant utilisateur requis pour effectuer le paiement.');
            }

            // 1. Vérification du ticket
            $ticket = Ticket::where('id', $donnees['ticket_id'])->lockForUpdate()->firstOrFail();

            if ($ticket->statut === 'annule') {
                throw new \InvalidArgumentException("Impossible d'effectuer un paiement sur un ticket annulé.");
            }

            if ($ticket->reste_a_payer <= 0 || $ticket->statut === 'paye') {
                throw new \InvalidArgumentException('Ce ticket est déjà entièrement réglé.');
            }

            $montantRecu = (float) $donnees['montant_recu'];
            if ($montantRecu <= 0) {
                throw new \InvalidArgumentException('Le montant du paiement doit être supérieur à zéro.');
            }

            // 2. Vérification de la caisse ouverte
            $caisseOuverte = Caisse::where('user_id', $effectiveUserId)
                ->where('statut', 'ouverte')
                ->lockForUpdate()
                ->first();

            if (! $caisseOuverte) {
                $user = User::find($effectiveUserId);
                if ($user && $user->hasRole('Caissier')) {
                    throw new \InvalidArgumentException('Votre session de caisse est actuellement fermée. Vous devez ouvrir votre caisse avant de percevoir un règlement.');
                }

                // Pour un administrateur ou comptable, rattacher à une caisse ouverte active
                $caisseOuverte = Caisse::where('statut', 'ouverte')->lockForUpdate()->first();
            }

            if (! $caisseOuverte) {
                throw new \InvalidArgumentException("Aucune session de caisse n'est actuellement ouverte pour enregistrer le mouvement.");
            }

            // 3. Calculs de monnaie et imputation (Surpaiement / Rendu)
            $resteAPayerActuel = (float) $ticket->reste_a_payer;
            $montantImpute = min($montantRecu, $resteAPayerActuel);
            $montantRendu = max(0.0, round($montantRecu - $resteAPayerActuel, 2));

            $typePayeur = $donnees['type_payeur'] ?? 'patient';
            $assuranceId = $typePayeur === 'assurance' ? ($donnees['assurance_id'] ?? $ticket->assurance_id) : null;

            // 4. Création du Paiement
            $paiement = Paiement::create([
                'ticket_id' => $ticket->id,
                'user_id' => $effectiveUserId,
                'assurance_id' => $assuranceId,
                'type_payeur' => $typePayeur,
                'montant_recu' => $montantRecu,
                'montant_impute' => $montantImpute,
                'montant_rendu' => $montantRendu,
                'mode_paiement_id' => $donnees['mode_paiement_id'],
                'date_paiement' => Carbon::now(),
                'statut' => 'valide',
                'description' => $donnees['description'] ?? 'Règlement ticket '.$ticket->reference,
            ]);

            // 5. Mise à jour du Ticket
            $nouveauMontantPaye = round((float) $ticket->montant_paye + $montantImpute, 2);
            $nouveauResteAPayer = max(0.0, round((float) $ticket->montant_patient - $nouveauMontantPaye, 2));
            $nouveauStatutTicket = $nouveauResteAPayer <= 0 ? 'paye' : 'partiellement_paye';

            $ticket->update([
                'montant_paye' => $nouveauMontantPaye,
                'reste_a_payer' => $nouveauResteAPayer,
                'statut' => $nouveauStatutTicket,
            ]);

            // 6. Gestion automatique de la Dette
            if ($nouveauResteAPayer > 0) {
                $statutDette = $nouveauMontantPaye > 0 ? 'partiellement_reglee' : 'en_cours';
                Dette::updateOrCreate(
                    ['ticket_id' => $ticket->id],
                    [
                        'patient_id' => $ticket->patient_id,
                        'user_id' => $effectiveUserId,
                        'montant_initial' => $ticket->montant_patient,
                        'montant_paye' => $nouveauMontantPaye,
                        'reste_a_payer' => $nouveauResteAPayer,
                        'statut' => $statutDette,
                        'date_creation' => Carbon::now()->toDateString(),
                        'description' => 'Dette suite au paiement partiel du ticket '.$ticket->reference,
                    ]
                );
            } else {
                Dette::where('ticket_id', $ticket->id)->update([
                    'montant_paye' => $nouveauMontantPaye,
                    'reste_a_payer' => 0.0,
                    'statut' => 'reglee',
                    'date_reglement' => Carbon::now()->toDateString(),
                ]);
            }

            // 7. Création automatique de la Recette
            $refRecette = 'REC-'.date('Ymd').'-'.strtoupper(substr(uniqid(), -4));
            $recette = Recette::create([
                'ticket_id' => $ticket->id,
                'paiement_id' => $paiement->id,
                'user_id' => $effectiveUserId,
                'mode_paiement_id' => $donnees['mode_paiement_id'],
                'montant' => $montantImpute, // Le montant de la recette correspond au montant réellement encaissé pour les soins
                'date_recette' => Carbon::now()->toDateString(),
                'reference' => $refRecette,
                'description' => 'Recette automatique pour le ticket '.$ticket->reference,
                'statut' => true,
            ]);

            // 8. Création automatique du Mouvement de Caisse
            $this->caisseService->enregistrerMouvement(
                caisse: $caisseOuverte,
                userId: $effectiveUserId,
                type: 'entree',
                origine: 'paiement',
                reference: $refRecette,
                montant: $montantImpute,
                description: 'Entrée caisse automatique pour paiement '.$ticket->reference
            );

            // 9. Actualisation automatique des honoraires médecins et règles de partage
            $this->remunerationService->actualiserRemunerationAutomatiquePourPaiementTicket($ticket);

            // 10. Audit Trail
            $this->journalService->log(
                action: 'enregistrement_paiement',
                module: 'paiement',
                objetType: Paiement::class,
                objetId: $paiement->id,
                description: 'Paiement de '.$montantImpute.' FBU (Rendu: '.$montantRendu.' FBU) sur ticket '.$ticket->reference,
                nouvellesValeurs: $paiement->toArray()
            );

            return $paiement;
        });
    }
}

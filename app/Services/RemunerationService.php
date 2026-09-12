<?php

namespace App\Services;

use App\Models\Medecin;
use App\Models\Prestation;
use App\Models\ReglePartage;
use App\Models\Remuneration;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RemunerationService
{
    public function __construct(
        protected JournalActiviteService $journalService
    ) {}

    /**
     * Calcule la répartition d'un montant d'acte médical entre le médecin et la clinique.
     *
     * @param float $montantActe Montant brut de l'acte médical
     * @param int|null $serviceId Identifiant du service pour vérifier l'existence d'une règle spécifique
     * @param Medecin|null $medecin Objet médecin avec ses paramètres de rémunération
     * @return array{montant_medecin: float, montant_clinique: float, pourcentage_medecin: float, pourcentage_clinique: float}
     */
    public function calculerPartMedecinEtClinique(float $montantActe, ?int $serviceId = null, ?Medecin $medecin = null): array
    {
        $pourcentageMedecin = 50.0;

        if ($serviceId) {
            $regle = ReglePartage::where('service_id', $serviceId)
                ->where('statut', true)
                ->latest('date_debut')
                ->first();

            if ($regle) {
                $pourcentageMedecin = (float) $regle->pourcentage_medecin;
            }
        }

        if ($medecin && $medecin->pourcentage && $medecin->pourcentage > 0) {
            $pourcentageMedecin = (float) $medecin->pourcentage;
        }

        $pourcentageClinique = max(0.0, round(100.0 - $pourcentageMedecin, 2));

        $montantMedecin = round(($montantActe * $pourcentageMedecin) / 100.0, 2);
        $montantClinique = max(0.0, round($montantActe - $montantMedecin, 2));

        return [
            'montant_medecin' => $montantMedecin,
            'montant_clinique' => $montantClinique,
            'pourcentage_medecin' => $pourcentageMedecin,
            'pourcentage_clinique' => $pourcentageClinique,
        ];
    }

    /**
     * Calcule et génère la synthèse de rémunération d'un médecin pour une période donnée.
     *
     * @param int $medecinId
     * @param string|Carbon $periodeDebut
     * @param string|Carbon $periodeFin
     * @param int|null $userId
     * @return Remuneration
     */
    public function genererRemunerationMensuelle(int $medecinId, $periodeDebut, $periodeFin, ?int $userId = null): Remuneration
    {
        return DB::transaction(function () use ($medecinId, $periodeDebut, $periodeFin, $userId) {
            $medecin = Medecin::findOrFail($medecinId);

            $effectiveUserId = $userId ?? auth()->id();
            if (!$effectiveUserId) {
                $user = User::first();
                $effectiveUserId = $user ? $user->id : null;
            }

            $debut = Carbon::parse($periodeDebut)->startOfDay();
            $fin = Carbon::parse($periodeFin)->endOfDay();

            // Vérification si une rémunération déjà payée existe sur la période
            $remunerationExistante = Remuneration::where('medecin_id', $medecinId)
                ->where('periode_debut', $debut->toDateString())
                ->where('periode_fin', $fin->toDateString())
                ->first();

            if ($remunerationExistante && $remunerationExistante->statut === 'payee') {
                throw new \InvalidArgumentException("Une rémunération déjà réglée existe pour cette période. Elle ne peut plus être recalculée.");
            }

            $typeRemuneration = $medecin->type_remuneration ?? 'pourcentage';
            $salaireFixe = (float) ($medecin->salaire_fixe ?? 0);
            $pourcentage = (float) ($medecin->pourcentage ?? 50);

            $montantBase = 0.0;
            $montantMedecin = 0.0;
            $montantClinique = 0.0;

            if ($typeRemuneration === 'salaire_fixe') {
                $montantBase = $salaireFixe;
                $montantMedecin = $salaireFixe;
                $montantClinique = 0.0;
            } else {
                // Rémunération au pourcentage : cumul des prestations du médecin sur la période
                $prestations = Prestation::where('medecin_id', $medecinId)
                    ->where('statut', true)
                    ->whereBetween('date_prestation', [$debut, $fin])
                    ->get();

                foreach ($prestations as $p) {
                    $montantBase += (float) $p->montant;
                    $montantMedecin += (float) $p->part_medecin;
                    $montantClinique += (float) $p->part_clinique;
                }
            }

            $remuneration = Remuneration::updateOrCreate(
                [
                    'medecin_id' => $medecinId,
                    'periode_debut' => $debut->toDateString(),
                    'periode_fin' => $fin->toDateString(),
                ],
                [
                    'user_id' => $effectiveUserId,
                    'type_remuneration' => $typeRemuneration,
                    'salaire_fixe' => $salaireFixe,
                    'pourcentage' => $pourcentage,
                    'montant_base' => round($montantBase, 2),
                    'montant_medecin' => round($montantMedecin, 2),
                    'montant_clinique' => round($montantClinique, 2),
                    'statut' => $remunerationExistante ? $remunerationExistante->statut : 'calculee',
                    'description' => 'Calcul automatique rémunération période du ' . $debut->format('d/m/Y') . ' au ' . $fin->format('d/m/Y'),
                ]
            );

            $this->journalService->log(
                action: 'calcul_remuneration',
                module: 'remuneration',
                objetType: Remuneration::class,
                objetId: $remuneration->id,
                description: 'Calcul rémunération Dr ' . $medecin->nom . ' (' . $remuneration->montant_medecin . ' FCFA)',
                nouvellesValeurs: $remuneration->toArray()
            );

            return $remuneration;
        });
    }

    /**
     * Valide le paiement d'une rémunération.
     *
     * @param Remuneration $remuneration
     * @return Remuneration
     */
    public function validerPaiementRemuneration(Remuneration $remuneration): Remuneration
    {
        return DB::transaction(function () use ($remuneration) {
            $remuneration->update([
                'statut' => 'payee',
                'date_paiement' => Carbon::now()->toDateString(),
            ]);

            $this->journalService->log(
                action: 'paiement_remuneration',
                module: 'remuneration',
                objetType: Remuneration::class,
                objetId: $remuneration->id,
                description: 'Règlement effectué de la rémunération #' . $remuneration->id . ' pour Dr ' . $remuneration->medecin->nom,
                nouvellesValeurs: $remuneration->toArray()
            );

            return $remuneration;
        });
    }

    /**
     * Actualise automatiquement le partage d'honoraires et la rémunération des médecins
     * à chaque fois qu'un paiement est effectué sur un ticket.
     *
     * @param \App\Models\Ticket $ticket
     * @return void
     */
    public function actualiserRemunerationAutomatiquePourPaiementTicket(\App\Models\Ticket $ticket): void
    {
        $ticket->loadMissing(['details.prestation.medecin', 'details.prestation.service']);

        $medecinIdsImpactes = [];

        foreach ($ticket->details as $detail) {
            $prestation = $detail->prestation;
            if ($prestation && $prestation->medecin_id) {
                // 1. Calcul des parts médecin et clinique selon les règles de partage actives
                $parts = $this->calculerPartMedecinEtClinique(
                    (float) $prestation->montant,
                    $prestation->service_id,
                    $prestation->medecin
                );

                $prestation->update([
                    'pourcentage_medecin' => $parts['pourcentage_medecin'],
                    'pourcentage_clinique' => $parts['pourcentage_clinique'],
                    'part_medecin' => $parts['montant_medecin'],
                    'part_clinique' => $parts['montant_clinique'],
                ]);

                $medecinIdsImpactes[] = $prestation->medecin_id;
            }
        }

        // 2. Mise à jour automatique de la synthèse de rémunération mensuelle du/des médecin(s)
        $medecinIdsUnique = array_unique($medecinIdsImpactes);
        $debutMois = Carbon::now()->startOfMonth();
        $finMois = Carbon::now()->endOfMonth();

        foreach ($medecinIdsUnique as $medecinId) {
            try {
                $this->genererRemunerationMensuelle($medecinId, $debutMois, $finMois);
            } catch (\Exception $e) {
                // Si la rémunération mensuelle est déjà verrouillée/payée, enregistrer le log ou ignorer
            }
        }
    }
}

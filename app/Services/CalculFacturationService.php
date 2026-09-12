<?php

namespace App\Services;

use App\Models\CarteAssurance;
use App\Models\Tarif;

class CalculFacturationService
{
    /**
     * Calcule la ventilation d'un montant d'acte entre la part assurance et la part patient.
     *
     * @param float $montantActe Montant brut de la prestation / consultation
     * @param float $tauxAssurance Taux de prise en charge en % (ex: 70 pour 70%)
     * @return array{montant_total: float, montant_assurance: float, montant_patient: float}
     */
    public function calculerVentilationFacture(float $montantActe, float $tauxAssurance = 0.0): array
    {
        // Nettoyage et borne du taux entre 0% et 100%
        $tauxNormalise = max(0.0, min(100.0, $tauxAssurance));

        // Part couverte par l'organisme d'assurance
        $partAssurance = round(($montantActe * $tauxNormalise) / 100.0, 2);

        // Part restante à la charge directe du patient
        $partPatient = max(0.0, round($montantActe - $partAssurance, 2));

        return [
            'montant_total' => round($montantActe, 2),
            'montant_assurance' => $partAssurance,
            'montant_patient' => $partPatient,
        ];
    }

    /**
     * Calcule la monnaie à rendre (trop-perçu) et le solde effectif à imputer sur le ticket.
     *
     * @param float $montantRecu Somme physique remise par le payeur
     * @param float $montantDu Part patient à payer
     * @return array{montant_impute: float, montant_rendu: float, reste_a_payer: float}
     */
    public function calculerMonnaieEtImputation(float $montantRecu, float $montantDu): array
    {
        // Montant réellement appliqué pour éponger la facture
        $montantImpute = min($montantRecu, $montantDu);

        // Monnaie à restituer au patient en cas d'excédent
        $montantRendu = max(0.0, round($montantRecu - $montantDu, 2));

        // Solde restant dû devenant une créance / dette
        $resteAPayer = max(0.0, round($montantDu - $montantImpute, 2));

        return [
            'montant_impute' => round($montantImpute, 2),
            'montant_rendu' => $montantRendu,
            'reste_a_payer' => $resteAPayer,
        ];
    }

    /**
     * Recherche le taux de prise en charge valide associé à une référence de carte d'assurance ou un patient.
     *
     * @param string $referenceCarte Numéro / Référence de carte d'assurance
     * @param int|null $patientId Identifiant optionnel du patient pour sécurité
     * @param string|\DateTimeInterface|null $datePrestation Date d'exécution de la prestation
     * @return float Taux de prise en charge (0 si introuvable ou expirée)
     */
    public function obtenirTauxAssuranceParCarte(string $referenceCarte, ?int $patientId = null, $datePrestation = null): float
    {
        $query = CarteAssurance::where('reference', $referenceCarte)
            ->where('statut', true);

        if ($patientId) {
            $query->where('patient_id', $patientId);
        }

        $carte = $query->first();

        if (!$carte) {
            return 0.0;
        }

        $date = $datePrestation ? \Carbon\Carbon::parse($datePrestation) : now();

        if ($carte->date_debut && $date->lt($carte->date_debut)) {
            return 0.0;
        }

        if ($carte->date_fin && $date->gt($carte->date_fin)) {
            return 0.0;
        }

        return (float) $carte->taux_couverture;
    }
}

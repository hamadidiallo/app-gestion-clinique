<?php

namespace App\Services;

use App\Models\Acte;
use App\Models\CarteAssurance;
use App\Models\Medecin;
use App\Models\Patient;
use App\Models\Prestation;
use App\Models\ReglePartage;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PrestationService
{
    public function __construct(
        protected JournalActiviteService $journalService
    ) {}

    /**
     * Recherche l'acte actif pour un service donné ou par identifiant d'acte.
     */
    public function trouverActeActif(int $serviceId, ?int $acteId = null): ?Acte
    {
        if ($acteId) {
            return Acte::find($acteId);
        }

        return Acte::where('service_id', $serviceId)->where('statut', true)->first();
    }

    /**
     * Recherche la carte d'assurance active et valide pour un patient.
     *
     * @param  Carbon|string|null  $datePrestation
     */
    public function trouverCarteAssuranceValide(int $patientId, $datePrestation = null): ?CarteAssurance
    {
        $date = $datePrestation ? Carbon::parse($datePrestation) : Carbon::now();

        return CarteAssurance::where('patient_id', $patientId)
            ->where('statut', true)
            ->where(function ($query) use ($date) {
                $query->whereNull('date_debut')
                    ->orWhere('date_debut', '<=', $date);
            })
            ->where(function ($query) use ($date) {
                $query->whereNull('date_fin')
                    ->orWhere('date_fin', '>=', $date);
            })
            ->first();
    }

    /**
     * Recherche la règle de partage des honoraires pour un service et un médecin.
     *
     * @param  Carbon|string|null  $datePrestation
     * @return array{pourcentage_medecin: float, pourcentage_clinique: float}
     */
    public function ObtenirPourcentagesPartage(int $serviceId, ?Medecin $medecin = null, $datePrestation = null, ?Acte $acte = null): array
    {
        $date = $datePrestation ? Carbon::parse($datePrestation) : Carbon::now();

        // 1. Chercher règle spécifique active pour le service
        $regle = ReglePartage::where('service_id', $serviceId)
            ->where('statut', true)
            ->where(function ($query) use ($date) {
                $query->whereNull('date_debut')
                    ->orWhere('date_debut', '<=', $date);
            })
            ->where(function ($query) use ($date) {
                $query->whereNull('date_fin')
                    ->orWhere('date_fin', '>=', $date);
            })
            ->latest('date_debut')
            ->first();

        if ($regle) {
            $pourcentageMedecin = (float) $regle->pourcentage_medecin;
        } elseif ($medecin && $medecin->pourcentage && $medecin->pourcentage > 0) {
            $pourcentageMedecin = (float) $medecin->pourcentage;
        } elseif ($acte && $acte->part_medecin_pourcentage !== null) {
            $pourcentageMedecin = (float) $acte->part_medecin_pourcentage;
        } else {
            $pourcentageMedecin = 50.0; // Par défaut 50 / 50
        }

        $pourcentageClinique = max(0.0, round(100.0 - $pourcentageMedecin, 2));

        return [
            'pourcentage_medecin' => $pourcentageMedecin,
            'pourcentage_clinique' => $pourcentageClinique,
        ];
    }

    /**
     * Simule les calculs automatiques d'une prestation avant enregistrement (pour AJAX ou prévisualisation).
     *
     * @param  Carbon|string|null  $datePrestation
     */
    public function calculerPrestationAutomatique(int $patientId, int $serviceId, ?int $medecinId = null, $datePrestation = null): array
    {
        $patient = Patient::findOrFail($patientId);
        $service = Service::findOrFail($serviceId);
        $medecin = $medecinId ? Medecin::find($medecinId) : null;
        $date = $datePrestation ? Carbon::parse($datePrestation) : Carbon::now();

        // 1. Recherche de l'acte actif
        $acte = $this->trouverActeActif($serviceId);
        $montantBrut = $acte ? (float) $acte->tarif_normal : 0.0;

        // 2. Recherche couverture assurance
        $tauxCouverture = 0.0;
        $assuranceId = null;
        if ($patient->statut === 'assure') {
            if ($patient->assurance_id) {
                $assuranceId = $patient->assurance_id;
                $tauxCouverture = (float) ($patient->taux_couverture ?? $patient->assurance?->taux_par_defaut ?? 80);
            } else {
                $carte = $this->trouverCarteAssuranceValide($patientId, $date);
                if ($carte) {
                    $tauxCouverture = (float) $carte->taux_couverture;
                    $assuranceId = $carte->assurance_id;
                }
            }
        }

        // Taux borné entre 0 et 100
        $tauxCouverture = max(0.0, min(100.0, $tauxCouverture));

        $montantAssurance = round(($montantBrut * $tauxCouverture) / 100.0, 2);
        $montantPatient = max(0.0, round($montantBrut - $montantAssurance, 2));

        // 3. Règle de partage médecin / clinique
        $partage = $this->obtenirPourcentagesPartage($serviceId, $medecin, $date, $acte);
        $pourcentageMedecin = $partage['pourcentage_medecin'];
        $pourcentageClinique = $partage['pourcentage_clinique'];

        $partMedecin = round(($montantBrut * $pourcentageMedecin) / 100.0, 2);
        $partClinique = max(0.0, round($montantBrut - $partMedecin, 2));

        return [
            'patient' => $patient,
            'service' => $service,
            'medecin' => $medecin,
            'acte' => $acte,
            'assurance_id' => $assuranceId,
            'montant' => $montantBrut,
            'taux_couverture' => $tauxCouverture,
            'montant_assurance' => $montantAssurance,
            'montant_patient' => $montantPatient,
            'pourcentage_medecin' => $pourcentageMedecin,
            'pourcentage_clinique' => $pourcentageClinique,
            'part_medecin' => $partMedecin,
            'part_clinique' => $partClinique,
            'date_prestation' => $date,
        ];
    }

    /**
     * Enregistre une prestation automatisée dans la base de données.
     *
     * @param  array  $donnees  Contient patient_id, service_id, medecin_id optionnel, date_prestation optionnelle, description
     */
    public function creerPrestationAutomatique(array $donnees): Prestation
    {
        return DB::transaction(function () use ($donnees) {
            $patientId = (int) $donnees['patient_id'];
            $serviceId = (int) $donnees['service_id'];
            $medecinId = ! empty($donnees['medecin_id']) ? (int) $donnees['medecin_id'] : null;
            $datePrestation = $donnees['date_prestation'] ?? Carbon::now();

            $calculs = $this->calculerPrestationAutomatique($patientId, $serviceId, $medecinId, $datePrestation);

            $prestation = Prestation::create([
                'patient_id' => $patientId,
                'service_id' => $serviceId,
                'medecin_id' => $medecinId,
                'acte_id' => $calculs['acte']?->id,
                'type' => $donnees['type'] ?? ($calculs['acte'] ? $calculs['acte']->nom : $calculs['service']->nom),
                'montant' => $calculs['montant'],
                'taux_couverture' => $calculs['taux_couverture'],
                'montant_assurance' => $calculs['montant_assurance'],
                'montant_patient' => $calculs['montant_patient'],
                'pourcentage_medecin' => $calculs['pourcentage_medecin'],
                'pourcentage_clinique' => $calculs['pourcentage_clinique'],
                'part_medecin' => $calculs['part_medecin'],
                'part_clinique' => $calculs['part_clinique'],
                'date_prestation' => $calculs['date_prestation'],
                'description' => $donnees['description'] ?? null,
                'statut' => true,
            ]);

            $this->journalService->log(
                action: 'creation_prestation',
                module: 'prestation',
                objetType: Prestation::class,
                objetId: $prestation->id,
                description: 'Création automatique prestation #'.$prestation->id.' pour '.$calculs['patient']->nom.' '.$calculs['patient']->prenom,
                nouvellesValeurs: $prestation->toArray()
            );

            return $prestation;
        });
    }
}

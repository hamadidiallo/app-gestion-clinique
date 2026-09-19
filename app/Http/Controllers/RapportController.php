<?php

namespace App\Http\Controllers;

// Importation des modèles nécessaires pour les rapports analytiques
use App\Models\Assurance;
use App\Models\Medecin;
use App\Models\Prestation;
use App\Models\Ticket;
use App\Models\TicketDetail;
use App\Services\NumberToWordsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Contrôleur gérant la génération des rapports financiers et administratifs (Assurances, Médecins, Caisses).
 */
class RapportController extends Controller
{
    /**
     * Affiche l'index des rapports disponibles dans le système.
     *
     * @return View
     */
    public function index()
    {
        // Retourne la vue principale de sélection des rapports
        return view('rapports.index');
    }

    /**
     * Génère le relevé détaillé des prestations prises en charge pour une assurance (Tiers Payant).
     * Requis par le cahier des charges : Nom, Prénom, Sexe, Date visite, Acte/Consultation, Tarif, Taux %, Montant Assurance.
     *
     * @return View
     */
    public function assurances(Request $request)
    {
        // Récupération de la liste de toutes les sociétés d'assurance enregistrées
        $assurances = Assurance::orderBy('nom')->get();

        // Récupération de l'assurance sélectionnée dans les filtres
        $assuranceId = $request->get('assurance_id');
        $assuranceSelected = null;
        $effectiveAssuranceId = null;
        if (! empty($assuranceId)) {
            $assuranceSelected = Assurance::where('id', $assuranceId)->orWhere('code', $assuranceId)->first();
            $effectiveAssuranceId = $assuranceSelected?->id ?? $assuranceId;
        }

        // Période de filtrage (par défaut : mois en cours)
        $dateDebut = $request->get('date_debut', Carbon::now()->startOfMonth()->toDateString());
        $dateFin = $request->get('date_fin', Carbon::now()->endOfMonth()->toDateString());

        // Initialisation de la collection des détails de tickets concernés avec relations optimisées
        $detailsQuery = TicketDetail::with(['ticket.patient', 'ticket.assurance', 'prestation.service', 'prestation.acte'])
            ->whereHas('ticket', function ($query) use ($effectiveAssuranceId, $dateDebut, $dateFin) {
                // Filtrer par date de création du ticket
                $query->whereBetween('date_ticket', [$dateDebut.' 00:00:00', $dateFin.' 23:59:59']);

                // Filtrer par assurance spécifique si sélectionnée
                if (! empty($effectiveAssuranceId)) {
                    $query->where('assurance_id', $effectiveAssuranceId);
                } else {
                    // Sinon filtrer uniquement les tickets ayant une assurance associée
                    $query->whereNotNull('assurance_id');
                }
            });

        // Calcul des totaux cumulés pour le rapport
        $totalTarifPublic = (float) (clone $detailsQuery)->sum('montant_total');
        $totalPartAssurance = (float) (clone $detailsQuery)->sum('montant_assurance');
        $totalPartPatient = (float) (clone $detailsQuery)->sum('montant_patient');

        // Exécution de la requête avec pagination
        $details = $detailsQuery->orderBy('created_at', 'desc')->paginate(25);

        // Retourne la vue dédiée au relevé détaillé des assurances
        return view('rapports.assurances', compact(
            'assurances',
            'assuranceId',
            'assuranceSelected',
            'dateDebut',
            'dateFin',
            'details',
            'totalTarifPublic',
            'totalPartAssurance',
            'totalPartPatient'
        ));
    }

    /**
     * Génère et affiche le Bordereau Officiel de Transmission des Prises en Charge (Tiers Payant).
     * Document légal et certifié au format A4 à transmettre à l'organisme assureur (ex: CANAM, INPS).
     *
     * @return View
     */
    public function bordereau(Request $request)
    {
        $assurances = Assurance::orderBy('nom')->get();

        $assuranceId = $request->get('assurance_id');
        $assuranceSelected = null;
        $effectiveAssuranceId = null;
        if (! empty($assuranceId)) {
            $assuranceSelected = Assurance::where('id', $assuranceId)->orWhere('code', $assuranceId)->first();
            $effectiveAssuranceId = $assuranceSelected?->id ?? $assuranceId;
        }

        $dateDebut = $request->get('date_debut', Carbon::now()->startOfMonth()->toDateString());
        $dateFin = $request->get('date_fin', Carbon::now()->endOfMonth()->toDateString());

        // Récupérer les tickets avec assurance
        $ticketsQuery = Ticket::with(['patient', 'assurance', 'ticketDetails', 'service', 'user'])
            ->whereBetween('date_ticket', [$dateDebut.' 00:00:00', $dateFin.' 23:59:59']);

        if (! empty($effectiveAssuranceId)) {
            $ticketsQuery->where('assurance_id', $effectiveAssuranceId);
        } else {
            $ticketsQuery->whereNotNull('assurance_id');
        }

        $tickets = $ticketsQuery->orderBy('date_ticket', 'asc')->get();

        // Totaux récapitulatifs
        $nbDossiers = $tickets->count();
        $totalMontantBrut = (float) $tickets->sum('montant_total');
        $totalPartPatient = (float) $tickets->sum('montant_patient');
        $totalPartAssurance = (float) $tickets->sum('montant_assurance');

        // Référence officielle du bordereau
        $codeAssur = $assuranceSelected ? ($assuranceSelected->code ?: 'ASSUR') : 'GLOBAL';
        $periodeRef = Carbon::parse($dateDebut)->format('Ym');
        $numeroBordereau = 'BORD-'.strtoupper($codeAssur).'-'.$periodeRef.'-001';

        // Montant en lettres
        $montantEnLettres = NumberToWordsService::toCurrency($totalPartAssurance, 'Francs CFA');

        return view('rapports.bordereau', compact(
            'assurances',
            'assuranceId',
            'assuranceSelected',
            'dateDebut',
            'dateFin',
            'tickets',
            'nbDossiers',
            'totalMontantBrut',
            'totalPartPatient',
            'totalPartAssurance',
            'numeroBordereau',
            'montantEnLettres'
        ));
    }

    /**
     * Génère le rapport des honoraires et rétrocessions des médecins.
     *
     * @return View
     */
    public function medecins(Request $request)
    {
        // Récupération des médecins actifs
        $medecins = Medecin::where('statut', true)->orderBy('nom')->get();

        // Filtres par médecin (par ID ou par Code) et par période
        $medecinId = $request->get('medecin_id');
        $medecinSelected = null;
        $effectiveMedecinId = null;
        if (! empty($medecinId)) {
            $medecinSelected = Medecin::where('id', $medecinId)->orWhere('code', $medecinId)->first();
            $effectiveMedecinId = $medecinSelected?->id ?? $medecinId;
        }

        $dateDebut = $request->get('date_debut', Carbon::now()->startOfMonth()->toDateString());
        $dateFin = $request->get('date_fin', Carbon::now()->endOfMonth()->toDateString());

        // Requête de recherche sur les prestations associées à un médecin
        $query = Prestation::with(['medecin', 'patient', 'service', 'acte'])
            ->whereNotNull('medecin_id')
            ->whereBetween('date_prestation', [$dateDebut.' 00:00:00', $dateFin.' 23:59:59']);

        if (! empty($effectiveMedecinId)) {
            $query->where('medecin_id', $effectiveMedecinId);
        }

        // Calcul des sommes des rétrocessions et du chiffre d'affaires
        $totalMontantActes = (float) (clone $query)->sum('montant');
        $totalPartMedecin = (float) (clone $query)->sum('part_medecin');
        $totalPartClinique = (float) (clone $query)->sum('part_clinique');
        $totalActes = (int) (clone $query)->count();

        $remunerations = $query->orderBy('date_prestation', 'desc')->paginate(25);

        return view('rapports.medecins', compact(
            'medecins',
            'medecinId',
            'medecinSelected',
            'dateDebut',
            'dateFin',
            'remunerations',
            'totalMontantActes',
            'totalPartMedecin',
            'totalPartClinique',
            'totalActes'
        ));
    }

    /**
     * Génère et affiche le Bordereau Officiel de Décompte et Liquidation des Honoraires Médicaux (A4).
     * Document justificatif légal pour paiement et émargement des praticiens de la clinique.
     *
     * @return View
     */
    public function bordereauMedecins(Request $request)
    {
        $medecins = Medecin::where('statut', true)->orderBy('nom')->get();

        $medecinId = $request->get('medecin_id');
        $medecinSelected = null;
        $effectiveMedecinId = null;
        if (! empty($medecinId)) {
            $medecinSelected = Medecin::where('id', $medecinId)->orWhere('code', $medecinId)->first();
            $effectiveMedecinId = $medecinSelected?->id ?? $medecinId;
        }

        $dateDebut = $request->get('date_debut', Carbon::now()->startOfMonth()->toDateString());
        $dateFin = $request->get('date_fin', Carbon::now()->endOfMonth()->toDateString());

        $query = Prestation::with(['medecin', 'patient', 'service', 'acte'])
            ->whereNotNull('medecin_id')
            ->whereBetween('date_prestation', [$dateDebut.' 00:00:00', $dateFin.' 23:59:59']);

        if (! empty($effectiveMedecinId)) {
            $query->where('medecin_id', $effectiveMedecinId);
        }

        $prestations = $query->orderBy('date_prestation', 'asc')->get();

        // Totaux
        $nbActes = $prestations->count();
        $totalMontantBrut = (float) $prestations->sum('montant');
        $totalPartMedecin = (float) $prestations->sum('part_medecin');
        $totalPartClinique = (float) $prestations->sum('part_clinique');

        // Référence officielle du bordereau
        $codeMed = $medecinSelected ? ($medecinSelected->code ?: 'DR-'.$medecinSelected->id) : 'GLOBAL';
        $periodeRef = Carbon::parse($dateDebut)->format('Ym');
        $numeroBordereau = 'BORD-HON-'.strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $codeMed)).'-'.$periodeRef.'-001';

        // Montant net en lettres
        $montantEnLettres = NumberToWordsService::toCurrency($totalPartMedecin, 'Francs CFA');

        return view('rapports.bordereau_medecins', compact(
            'medecins',
            'medecinId',
            'medecinSelected',
            'dateDebut',
            'dateFin',
            'prestations',
            'nbActes',
            'totalMontantBrut',
            'totalPartMedecin',
            'totalPartClinique',
            'numeroBordereau',
            'montantEnLettres'
        ));
    }
}

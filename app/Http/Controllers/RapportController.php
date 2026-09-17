<?php

namespace App\Http\Controllers;

// Importation des modèles nécessaires pour les rapports analytiques
use App\Models\Assurance;
use App\Models\Medecin;
use App\Models\Prestation;
use App\Models\Ticket;
use App\Models\TicketDetail;
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

        // Période de filtrage (par défaut : mois en cours)
        $dateDebut = $request->get('date_debut', Carbon::now()->startOfMonth()->toDateString());
        $dateFin = $request->get('date_fin', Carbon::now()->endOfMonth()->toDateString());

        // Initialisation de la collection des détails de tickets concernés
        $detailsQuery = TicketDetail::with(['ticket.patient', 'ticket.assurance', 'prestation'])
            ->whereHas('ticket', function ($query) use ($assuranceId, $dateDebut, $dateFin) {
                // Filtrer par date de création du ticket
                $query->whereBetween('date_ticket', [$dateDebut.' 00:00:00', $dateFin.' 23:59:59']);

                // Filtrer par assurance spécifique si sélectionnée
                if (! empty($assuranceId)) {
                    $query->where('assurance_id', $assuranceId);
                } else {
                    // Sinon filtrer uniquement les tickets ayant une assurance associée
                    $query->whereNotNull('assurance_id');
                }
            });

        // Exécution de la requête avec pagination
        $details = $detailsQuery->orderBy('created_at', 'desc')->paginate(25);

        // Calcul des totaux cumulés pour le rapport
        $totalTarifPublic = (clone $detailsQuery)->get()->sum(function ($item) {
            return $item->montant_total ?? ($item->prix_unitaire * $item->quantite);
        });

        $totalPartAssurance = (clone $detailsQuery)->get()->sum('montant_assurance');
        $totalPartPatient = (clone $detailsQuery)->get()->sum('montant_patient');

        // Retourne la vue dédiée au relevé détaillé des assurances
        return view('rapports.assurances', compact(
            'assurances',
            'assuranceId',
            'dateDebut',
            'dateFin',
            'details',
            'totalTarifPublic',
            'totalPartAssurance',
            'totalPartPatient'
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

        // Filtres par médecin et par période
        $medecinId = $request->get('medecin_id');
        $dateDebut = $request->get('date_debut', Carbon::now()->startOfMonth()->toDateString());
        $dateFin = $request->get('date_fin', Carbon::now()->endOfMonth()->toDateString());

        // Requête de recherche sur les prestations associées à un médecin
        $query = Prestation::with(['medecin', 'patient', 'service'])
            ->whereNotNull('medecin_id')
            ->whereBetween('date_prestation', [$dateDebut.' 00:00:00', $dateFin.' 23:59:59']);

        if (! empty($medecinId)) {
            $query->where('medecin_id', $medecinId);
        }

        // Calcul des sommes des rétrocessions
        $totalPartMedecin = (clone $query)->sum('part_medecin');
        $totalPartClinique = (clone $query)->sum('part_clinique');

        $remunerations = $query->orderBy('date_prestation', 'desc')->paginate(25);

        return view('rapports.medecins', compact(
            'medecins',
            'medecinId',
            'dateDebut',
            'dateFin',
            'remunerations',
            'totalPartMedecin',
            'totalPartClinique'
        ));
    }
}

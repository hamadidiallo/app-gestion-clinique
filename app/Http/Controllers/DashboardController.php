<?php

namespace App\Http\Controllers;

use App\Models\Caisse;
use App\Models\Depense;
use App\Models\Dette;
use App\Models\Recette;
use App\Models\Remuneration;
use App\Models\Service;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Affiche le Tableau de bord V1 chiffré sans graphiques.
     */
    public function index(Request $request)
    {
        // 1. Détermination de la période de filtrage (default: ce mois)
        $periode = $request->get('periode', 'mois');

        $debut = match ($periode) {
            'jour' => Carbon::today(),
            'semaine' => Carbon::now()->startOfWeek(),
            'mois' => Carbon::now()->startOfMonth(),
            'trimestre' => Carbon::now()->startOfQuarter(),
            'semestre' => Carbon::now()->month >= 7 ? Carbon::create(null, 7, 1)->startOfDay() : Carbon::create(null, 1, 1)->startOfDay(),
            'annee' => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfMonth(),
        };

        $fin = Carbon::now()->endOfDay();

        // 2. Indicateurs Financiers (KPIs)
        $totalRecettes = Recette::where('statut', true)
            ->whereBetween('date_recette', [$debut, $fin])
            ->sum('montant');

        $totalDepenses = Depense::where('statut', true)
            ->whereBetween('date_depense', [$debut, $fin])
            ->sum('montant');

        $soldeNet = $totalRecettes - $totalDepenses;

        $totalDettesRestantes = Dette::whereIn('statut', ['en_cours', 'partiel'])
            ->sum('reste_a_payer');

        $partsMedecinsDues = Remuneration::where('statut', 'en_attente')
            ->sum('montant_medecin');

        // 3. Compteurs d'actes & consultations
        $nombreTickets = Ticket::whereBetween('date_ticket', [$debut, $fin])->count();
        $totalMontantTickets = Ticket::whereBetween('date_ticket', [$debut, $fin])->sum('montant_total');

        // 4. Caisses Ouvertes actuellement
        $caissesOuvertes = Caisse::where('statut', 'ouverte')->with('user')->get();

        // 5. Synthèse des prestations par Service médical sur la période
        $servicesStats = Service::withCount([
            'actes',
            'prestations as prestations_periode_count' => function ($q) use ($debut, $fin) {
                $q->whereBetween('date_prestation', [$debut, $fin]);
            },
        ])
            ->get()
            ->map(function ($service) use ($debut, $fin) {
                $actesNoms = $service->actes->pluck('nom')->filter()->toArray();

                // Recettes réelles encaissées au guichet sur la période pour ce service
                $recettesEncaissees = (float) Recette::where('statut', true)
                    ->whereBetween('date_recette', [$debut, $fin])
                    ->whereHas('ticket', function ($qt) use ($service, $actesNoms) {
                        $qt->where('service_id', $service->id)
                            ->orWhereHas('details.prestation', fn ($qp) => $qp->where('service_id', $service->id))
                            ->orWhereHas('details', function ($qd) use ($actesNoms) {
                                if (! empty($actesNoms)) {
                                    $qd->whereIn('designation', $actesNoms);
                                }
                            });
                    })
                    ->sum('montant');

                // Montant des tickets facturés sur la période pour ce service
                $montantFacture = (float) Ticket::where(function ($q) use ($service, $actesNoms) {
                    $q->where('service_id', $service->id)
                        ->orWhereHas('details.prestation', fn ($qp) => $qp->where('service_id', $service->id))
                        ->orWhereHas('details', function ($qd) use ($actesNoms) {
                            if (! empty($actesNoms)) {
                                $qd->whereIn('designation', $actesNoms);
                            }
                        });
                })
                    ->whereBetween('date_ticket', [$debut, $fin])
                    ->sum('montant_total');

                // Chiffre d'Affaires du service : correspond aux encaissements réels (ou à la facturation si pas d'encaissement sur la période)
                $service->chiffre_affaires = $recettesEncaissees > 0 ? $recettesEncaissees : $montantFacture;
                $service->recettes_encaissees = $recettesEncaissees;
                $service->montant_facture = $montantFacture;

                return $service;
            });

        // Libellé clair de la période sélectionnée
        $periodesLabels = [
            'jour' => 'Aujourd\'hui',
            'semaine' => 'Cette Semaine',
            'mois' => 'Ce Mois',
            'trimestre' => 'Ce Trimestre',
            'semestre' => 'Ce Semestre',
            'annee' => 'Cette Année',
        ];

        return view('dashboard.index', compact(
            'periode',
            'periodesLabels',
            'debut',
            'fin',
            'totalRecettes',
            'totalDepenses',
            'soldeNet',
            'totalDettesRestantes',
            'partsMedecinsDues',
            'nombreTickets',
            'totalMontantTickets',
            'caissesOuvertes',
            'servicesStats'
        ));
    }
}

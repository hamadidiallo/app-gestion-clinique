<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AbonnementController extends Controller
{
    /**
     * Affiche les formules d'abonnement et le statut de la licence actuelle de la clinique.
     */
    public function index(): View
    {
        $user = auth()->user();
        $clinique = $user->clinique;

        $formules = [
            [
                'id' => 'standard',
                'nom' => 'Formule Cabinet / Starter',
                'badge' => 'Essentiel',
                'badge_color' => '#64748b',
                'badge_bg' => '#f1f5f9',
                'prix_mensuel' => 35000,
                'prix_annuel' => 350000,
                'description' => 'Pour les cabinets médicaux privés, dispensaires et centres de soins de proximité.',
                'caracteristiques' => [
                    '1 Guichet de Caisse actif',
                    'Jusqu\'à 3 Médecins & Praticiens',
                    'Dossiers Patients & Impression de tickets thermiques',
                    'Consultations médicales et actes de soins',
                    'Clôtures journalières de caisse',
                    'Statistiques d\'activité de base',
                    'Support par e-mail',
                ],
                'non_inclus' => [
                    'Multi-guichets de caisse simultanés',
                    'Règles de partage automatique des honoraires',
                    'Bordereaux de tiers-payant AMO / INPS',
                ],
                'is_popular' => false,
            ],
            [
                'id' => 'pro',
                'nom' => 'Formule Pro / Clinique Médicale',
                'badge' => 'Recommandé & Populaire',
                'badge_color' => '#0f766e',
                'badge_bg' => '#e2f1ef',
                'prix_mensuel' => 75000,
                'prix_annuel' => 750000,
                'description' => 'La solution complète conçue pour les polycliniques et cliniques médico-chirurgicales.',
                'caracteristiques' => [
                    'Multi-guichets de caisse simultanés (jusqu\'à 5)',
                    'Jusqu\'à 15 Médecins, Spécialistes & Collaborateurs',
                    'Automatisation & Calcul des honoraires médecins',
                    'Gestion complète des Dettes et Rapprochement de caisse',
                    'Bordereaux de facturation AMO, INPS & Assurances',
                    'Journal d\'encaissement avec exports Excel & PDF',
                    'Support prioritaire WhatsApp & Téléphone 6j/7',
                ],
                'non_inclus' => [
                    'Guichets et comptes illimités',
                    'Monitoring d\'infrastructure dédié',
                ],
                'is_popular' => true,
            ],
            [
                'id' => 'entreprise',
                'nom' => 'Formule Entreprise / Hôpital & Groupe',
                'badge' => 'Performance Maximale',
                'badge_color' => '#1e293b',
                'badge_bg' => '#f8fafc',
                'prix_mensuel' => 150000,
                'prix_annuel' => 1500000,
                'description' => 'Conçu pour les hôpitaux de référence, centres hospitaliers et réseaux de cliniques.',
                'caracteristiques' => [
                    'Guichets de Caisse illimités',
                    'Médecins, spécialistes et collaborateurs illimités',
                    'Gestion multi-services et hospitalisation',
                    'Paiements mixtes, acomptes et tiers-payant complet',
                    'Journal d\'audit, traçabilité et conformité financière',
                    'Rapports d\'analyse de rentabilité et exports comptables',
                    'Sauvegardes automatiques quotidiennes',
                    'Interlocuteur dédié & Support 24/7',
                ],
                'non_inclus' => [],
                'is_popular' => false,
            ],
        ];

        return view('abonnement.index', compact('clinique', 'formules'));
    }
}

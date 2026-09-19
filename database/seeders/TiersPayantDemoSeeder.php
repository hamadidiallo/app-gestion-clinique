<?php

namespace Database\Seeders;

use App\Models\Assurance;
use App\Models\CarteAssurance;
use App\Models\Patient;
use App\Models\Ticket;
use App\Models\TicketDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TiersPayantDemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $userId = $user ? $user->id : 1;

        $canam = Assurance::firstOrCreate(
            ['code' => 'AMO'],
            ['nom' => 'CANAM / AMO (Caisse Nationale d\'Assurance Maladie)', 'taux_par_defaut' => 80, 'statut' => true]
        );

        $inps = Assurance::firstOrCreate(
            ['code' => 'INPS'],
            ['nom' => 'INPS (Institut National de Prévoyance Sociale)', 'taux_par_defaut' => 70, 'statut' => true]
        );

        // 1. Patient Oumar Coulibaly (CANAM)
        $p1 = Patient::firstOrCreate(
            ['telephone' => '76010203'],
            [
                'reference' => 'PAT-2026-0010',
                'nom' => 'Coulibaly',
                'prenom' => 'Oumar',
                'sexe' => 'M',
                'assurance_id' => $canam->id,
                'numero_assure' => 'AMO-7890123-BKO',
                'taux_couverture' => 80,
            ]
        );
        CarteAssurance::firstOrCreate(
            ['reference' => 'AMO-7890123-BKO'],
            [
                'patient_id' => $p1->id,
                'assurance_id' => $canam->id,
                'taux_couverture' => 80,
                'date_debut' => '2026-01-01',
                'date_fin' => '2026-12-31',
                'statut' => true,
            ]
        );

        // Ticket 1 - CANAM
        $t1 = Ticket::create([
            'patient_id' => $p1->id,
            'assurance_id' => $canam->id,
            'service_id' => 1,
            'user_id' => $userId,
            'reference' => 'TCK-20260918-AMO01',
            'date_ticket' => Carbon::now()->subDays(2),
            'montant_total' => 11000,
            'montant_assurance' => 8800,
            'montant_patient' => 2200,
            'montant_paye' => 2200,
            'reste_a_payer' => 0,
            'statut' => 'paye',
        ]);
        TicketDetail::create([
            'ticket_id' => $t1->id,
            'designation' => 'Consultation Médecine Générale',
            'type_item' => 'acte',
            'quantite' => 1,
            'prix_unitaire' => 5000,
            'montant_total' => 5000,
            'taux_couverture' => 80,
            'montant_assurance' => 4000,
            'montant_patient' => 1000,
        ]);
        TicketDetail::create([
            'ticket_id' => $t1->id,
            'designation' => 'Numération Formule Sanguine (NFS / Hémogramme)',
            'type_item' => 'acte',
            'quantite' => 1,
            'prix_unitaire' => 6000,
            'montant_total' => 6000,
            'taux_couverture' => 80,
            'montant_assurance' => 4800,
            'montant_patient' => 1200,
        ]);

        // 2. Patient Fatoumata Diarra (CANAM)
        $p2 = Patient::firstOrCreate(
            ['telephone' => '78020304'],
            [
                'reference' => 'PAT-2026-0011',
                'nom' => 'Diarra',
                'prenom' => 'Fatoumata',
                'sexe' => 'F',
                'assurance_id' => $canam->id,
                'numero_assure' => 'AMO-4567891-BKO',
                'taux_couverture' => 80,
            ]
        );
        CarteAssurance::firstOrCreate(
            ['reference' => 'AMO-4567891-BKO'],
            [
                'patient_id' => $p2->id,
                'assurance_id' => $canam->id,
                'taux_couverture' => 80,
                'date_debut' => '2026-01-01',
                'date_fin' => '2026-12-31',
                'statut' => true,
            ]
        );

        // Ticket 2 - CANAM
        $t2 = Ticket::create([
            'patient_id' => $p2->id,
            'assurance_id' => $canam->id,
            'service_id' => 2,
            'user_id' => $userId,
            'reference' => 'TCK-20260918-AMO02',
            'date_ticket' => Carbon::now()->subDays(1),
            'montant_total' => 23000,
            'montant_assurance' => 18400,
            'montant_patient' => 4600,
            'montant_paye' => 4600,
            'reste_a_payer' => 0,
            'statut' => 'paye',
        ]);
        TicketDetail::create([
            'ticket_id' => $t2->id,
            'designation' => 'Consultation Prénatale (CPN)',
            'type_item' => 'acte',
            'quantite' => 1,
            'prix_unitaire' => 8000,
            'montant_total' => 8000,
            'taux_couverture' => 80,
            'montant_assurance' => 6400,
            'montant_patient' => 1600,
        ]);
        TicketDetail::create([
            'ticket_id' => $t2->id,
            'designation' => 'Échographie Obstétricale T1 / T2 / T3',
            'type_item' => 'acte',
            'quantite' => 1,
            'prix_unitaire' => 15000,
            'montant_total' => 15000,
            'taux_couverture' => 80,
            'montant_assurance' => 12000,
            'montant_patient' => 3000,
        ]);

        // 3. Patient Ibrahim Traoré (INPS)
        $p3 = Patient::firstOrCreate(
            ['telephone' => '71030405'],
            [
                'reference' => 'PAT-2026-0012',
                'nom' => 'Traoré',
                'prenom' => 'Ibrahim',
                'sexe' => 'M',
                'assurance_id' => $inps->id,
                'numero_assure' => 'INPS-1234567-ML',
                'taux_couverture' => 70,
            ]
        );
        CarteAssurance::firstOrCreate(
            ['reference' => 'INPS-1234567-ML'],
            [
                'patient_id' => $p3->id,
                'assurance_id' => $inps->id,
                'taux_couverture' => 70,
                'date_debut' => '2026-01-01',
                'date_fin' => '2026-12-31',
                'statut' => true,
            ]
        );

        // Ticket 3 - INPS
        $t3 = Ticket::create([
            'patient_id' => $p3->id,
            'assurance_id' => $inps->id,
            'service_id' => 3,
            'user_id' => $userId,
            'reference' => 'TCK-20260918-INPS01',
            'date_ticket' => Carbon::now(),
            'montant_total' => 9500,
            'montant_assurance' => 6650,
            'montant_patient' => 2850,
            'montant_paye' => 2850,
            'reste_a_payer' => 0,
            'statut' => 'paye',
        ]);
        TicketDetail::create([
            'ticket_id' => $t3->id,
            'designation' => 'Consultation Pédiatrique',
            'type_item' => 'acte',
            'quantite' => 1,
            'prix_unitaire' => 6000,
            'montant_total' => 6000,
            'taux_couverture' => 70,
            'montant_assurance' => 4200,
            'montant_patient' => 1800,
        ]);
        TicketDetail::create([
            'ticket_id' => $t3->id,
            'designation' => 'Goutte Épaisse / Test Rapide Paludisme (GE/TDR)',
            'type_item' => 'acte',
            'quantite' => 1,
            'prix_unitaire' => 3500,
            'montant_total' => 3500,
            'taux_couverture' => 70,
            'montant_assurance' => 2450,
            'montant_patient' => 1050,
        ]);
    }
}

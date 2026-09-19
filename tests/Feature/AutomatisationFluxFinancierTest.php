<?php

namespace Tests\Feature;

use App\Models\Acte;
use App\Models\Assurance;
use App\Models\Caisse;
use App\Models\CarteAssurance;
use App\Models\CategorieDepense;
use App\Models\Dette;
use App\Models\Medecin;
use App\Models\ModePaiement;
use App\Models\MouvementCaisse;
use App\Models\Paiement;
use App\Models\Patient;
use App\Models\Prestation;
use App\Models\Recette;
use App\Models\ReglePartage;
use App\Models\Role;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\User;
use App\Services\DepenseService;
use App\Services\GestionCaisseService;
use App\Services\PaiementService;
use App\Services\PrestationService;
use App\Services\RemunerationService;
use App\Services\TicketService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutomatisationFluxFinancierTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected ModePaiement $modeEspeces;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['nom' => 'Administrateur']);
        $this->user = User::create([
            'nom' => 'Admin',
            'prenom' => 'Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
        ]);
        $this->actingAs($this->user);

        $this->modeEspeces = ModePaiement::create(['nom' => 'Espèces', 'code' => 'ESPECES', 'statut' => true]);
    }

    /** TEST 1 & 2 : Prestation Consultation & Partage Médecin/Clinique */
    public function test_prestation_calculs_partage_medecin_clinique(): void
    {
        $serviceGen = Service::create(['code' => 'CG', 'nom' => 'Consultation Générale', 'statut' => true]);
        $acteGen = Acte::create(['service_id' => $serviceGen->id, 'code' => 'CG-01', 'nom' => 'Consultation Générale', 'tarif_normal' => 3000, 'statut' => true]);

        $serviceSpec = Service::create(['code' => 'CS', 'nom' => 'Consultation Spécialisée', 'statut' => true]);
        $acteSpec = Acte::create(['service_id' => $serviceSpec->id, 'code' => 'CS-01', 'nom' => 'Consultation Spécialisée', 'tarif_normal' => 10000, 'statut' => true]);

        $patient = Patient::create(['nom' => 'Diop', 'prenom' => 'Awa', 'sexe' => 'F', 'statut' => 'non_assure']);
        $medecin = Medecin::create(['nom' => 'Sow', 'prenom' => 'Dr', 'type_remuneration' => 'pourcentage', 'pourcentage' => 50, 'statut' => true]);

        $service = app(PrestationService::class);

        // Test 1: Consultation générale 3000 -> 1500 / 1500
        $p1 = $service->creerPrestationAutomatique([
            'patient_id' => $patient->id,
            'service_id' => $serviceGen->id,
            'medecin_id' => $medecin->id,
        ]);
        $this->assertEquals(3000, $p1->montant);
        $this->assertEquals(1500, $p1->part_medecin);
        $this->assertEquals(1500, $p1->part_clinique);

        // Test 2: Consultation spécialisée 10000 -> 5000 / 5000
        $p2 = $service->creerPrestationAutomatique([
            'patient_id' => $patient->id,
            'service_id' => $serviceSpec->id,
            'medecin_id' => $medecin->id,
        ]);
        $this->assertEquals(10000, $p2->montant);
        $this->assertEquals(5000, $p2->part_medecin);
        $this->assertEquals(5000, $p2->part_clinique);
    }

    /** TEST 3 : Patient Assuré 70% */
    public function test_patient_assure_70_pourcent(): void
    {
        $patient = Patient::create(['nom' => 'Ndiaye', 'prenom' => 'Moussa', 'sexe' => 'M', 'statut' => 'assure']);
        $assurance = Assurance::create(['nom' => 'IPM', 'statut' => true]);
        CarteAssurance::create([
            'patient_id' => $patient->id,
            'assurance_id' => $assurance->id,
            'reference' => 'CARD-1234',
            'taux_couverture' => 70,
            'statut' => true,
        ]);

        $service = Service::create(['code' => 'RAD', 'nom' => 'Radiographie', 'statut' => true]);
        Acte::create(['service_id' => $service->id, 'code' => 'RAD-01', 'nom' => 'Radiographie', 'tarif_normal' => 10000, 'statut' => true]);

        $prestationService = app(PrestationService::class);
        $prestation = $prestationService->creerPrestationAutomatique([
            'patient_id' => $patient->id,
            'service_id' => $service->id,
        ]);

        $this->assertEquals(10000, $prestation->montant);
        $this->assertEquals(70, $prestation->taux_couverture);
        $this->assertEquals(7000, $prestation->montant_assurance);
        $this->assertEquals(3000, $prestation->montant_patient);
    }

    /** TEST 4 & 5 & 7 : Paiement Partiel, Dette, Recette et Caisse */
    public function test_paiement_partiel_et_solde_final(): void
    {
        // 1. Ouvrir caisse
        $caisseService = app(GestionCaisseService::class);
        $caisse = $caisseService->ouvrirCaisse($this->user->id, 5000);

        // 2. Prestation & Ticket de 10 000 FCFA
        $patient = Patient::create(['nom' => 'Kane', 'prenom' => 'Oumar', 'sexe' => 'M', 'statut' => 'non_assure']);
        $service = Service::create(['code' => 'ECO', 'nom' => 'Échographie', 'statut' => true]);
        Acte::create(['service_id' => $service->id, 'code' => 'ECO-01', 'nom' => 'Échographie', 'tarif_normal' => 10000, 'statut' => true]);

        $prestation = app(PrestationService::class)->creerPrestationAutomatique([
            'patient_id' => $patient->id,
            'service_id' => $service->id,
        ]);
        $ticket = app(TicketService::class)->creerTicketDepuisPrestations([$prestation->id], $this->user->id);

        $this->assertEquals(10000, $ticket->montant_patient);

        // TEST 4: Premier paiement partiel de 4000
        $paiementService = app(PaiementService::class);
        $p1 = $paiementService->enregistrerPaiement([
            'ticket_id' => $ticket->id,
            'montant_recu' => 4000,
            'mode_paiement_id' => $this->modeEspeces->id,
            'user_id' => $this->user->id,
        ]);

        $ticket->refresh();
        $this->assertEquals(4000, $ticket->montant_paye);
        $this->assertEquals(6000, $ticket->reste_a_payer);
        $this->assertEquals('partiellement_paye', $ticket->statut);

        // Dette créée pour 6000
        $dette = Dette::where('ticket_id', $ticket->id)->first();
        $this->assertNotNull($dette);
        $this->assertEquals(6000, $dette->reste_a_payer);
        $this->assertEquals('partiellement_reglee', $dette->statut);

        // TEST 7: Recette et mouvement de caisse automatiques
        $recette = Recette::where('paiement_id', $p1->id)->first();
        $this->assertNotNull($recette);
        $this->assertEquals(4000, $recette->montant);

        $mvt = MouvementCaisse::where('caisse_id', $caisse->id)->where('type', 'entree')->first();
        $this->assertNotNull($mvt);
        $this->assertEquals(4000, $mvt->montant);

        // TEST 5: Paiement final de 6000
        $p2 = $paiementService->enregistrerPaiement([
            'ticket_id' => $ticket->id,
            'montant_recu' => 6000,
            'mode_paiement_id' => $this->modeEspeces->id,
            'user_id' => $this->user->id,
        ]);

        $ticket->refresh();
        $this->assertEquals(10000, $ticket->montant_paye);
        $this->assertEquals(0, $ticket->reste_a_payer);
        $this->assertEquals('paye', $ticket->statut);

        $dette->refresh();
        $this->assertEquals(0, $dette->reste_a_payer);
        $this->assertEquals('reglee', $dette->statut);
    }

    /** TEST 6 : Surpaiement et Trop-Perçu (Rendu Monnaie) */
    public function test_surpaiement_et_monnaie_rendue(): void
    {
        app(GestionCaisseService::class)->ouvrirCaisse($this->user->id, 10000);

        $patient = Patient::create(['nom' => 'Sy', 'prenom' => 'Fatou', 'sexe' => 'F', 'statut' => 'non_assure']);
        $service = Service::create(['code' => 'LAB', 'nom' => 'Analyse Sang', 'statut' => true]);
        Acte::create(['service_id' => $service->id, 'code' => 'LAB-01', 'nom' => 'Analyse Sang', 'tarif_normal' => 5000, 'statut' => true]);

        $prestation = app(PrestationService::class)->creerPrestationAutomatique([
            'patient_id' => $patient->id,
            'service_id' => $service->id,
        ]);
        $ticket = app(TicketService::class)->creerTicketDepuisPrestations([$prestation->id], $this->user->id);

        // Patient donne 10000 pour une facture de 5000
        $paiement = app(PaiementService::class)->enregistrerPaiement([
            'ticket_id' => $ticket->id,
            'montant_recu' => 10000,
            'mode_paiement_id' => $this->modeEspeces->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals(10000, $paiement->montant_recu);
        $this->assertEquals(5000, $paiement->montant_impute);
        $this->assertEquals(5000, $paiement->montant_rendu);

        // La recette doit être de 5000 (montant imputé), et non 10000
        $recette = Recette::where('paiement_id', $paiement->id)->first();
        $this->assertEquals(5000, $recette->montant);
    }

    /** TEST 8 : Dépense et Mouvement Caisse Sortie */
    public function test_depense_et_mouvement_caisse_sortie(): void
    {
        $caisse = app(GestionCaisseService::class)->ouvrirCaisse($this->user->id, 50000);
        $cat = CategorieDepense::create(['nom' => 'Fournitures', 'code' => 'FOURNITURES', 'statut' => true]);

        $depense = app(DepenseService::class)->enregistrerDepense([
            'categorie_depense_id' => $cat->id,
            'mode_paiement_id' => $this->modeEspeces->id,
            'user_id' => $this->user->id,
            'montant' => 25000,
            'beneficiaire' => 'Papeterie Centrale',
        ]);

        $this->assertEquals(25000, $depense->montant);

        $mvt = MouvementCaisse::where('caisse_id', $caisse->id)->where('type', 'sortie')->first();
        $this->assertNotNull($mvt);
        $this->assertEquals(25000, $mvt->montant);
    }

    /** TEST 9 : Fermeture Caisse & Calcul Écart */
    public function test_fermeture_caisse_et_calcul_ecart(): void
    {
        $caisseService = app(GestionCaisseService::class);
        $caisse = $caisseService->ouvrirCaisse($this->user->id, 10000);

        // Entrée de 5000
        $caisseService->enregistrerMouvement($caisse, $this->user->id, 'entree', 'paiement', 'REF1', 5000);

        // Sortie de 2000
        $caisseService->enregistrerMouvement($caisse, $this->user->id, 'sortie', 'depense', 'REF2', 2000);

        // Solde théorique = 10000 + 5000 - 2000 = 13000
        // Comptage physique = 12500 -> Écart = -500
        $caisseFermee = $caisseService->fermerCaisse($caisse, 12500);

        $this->assertEquals(13000, $caisseFermee->solde_theorique);
        $this->assertEquals(12500, $caisseFermee->solde_physique);
        $this->assertEquals(-500, $caisseFermee->ecart);
        $this->assertEquals('fermee', $caisseFermee->statut);
    }

    /** TEST 10, 11, 12 : Snapshot Historique aux modifications ultérieures */
    public function test_snapshot_historique_stabilite(): void
    {
        $patient = Patient::create(['nom' => 'Ba', 'prenom' => 'Amadou', 'sexe' => 'M', 'statut' => 'assure']);
        $assurance = Assurance::create(['nom' => 'CNSS', 'statut' => true]);
        $carte = CarteAssurance::create([
            'patient_id' => $patient->id,
            'assurance_id' => $assurance->id,
            'reference' => 'CARD-99',
            'taux_couverture' => 70,
            'statut' => true,
        ]);

        $service = Service::create(['code' => 'CONS', 'nom' => 'Consultation', 'statut' => true]);
        $acte = Acte::create(['service_id' => $service->id, 'code' => 'CONS-01', 'nom' => 'Consultation', 'tarif_normal' => 10000, 'statut' => true]);
        $medecin = Medecin::create(['nom' => 'Diallo', 'prenom' => 'Dr', 'type_remuneration' => 'pourcentage', 'pourcentage' => 50, 'statut' => true]);

        $prestation = app(PrestationService::class)->creerPrestationAutomatique([
            'patient_id' => $patient->id,
            'service_id' => $service->id,
            'medecin_id' => $medecin->id,
        ]);

        $this->assertEquals(10000, $prestation->montant);
        $this->assertEquals(7000, $prestation->montant_assurance);
        $this->assertEquals(3000, $prestation->montant_patient);
        $this->assertEquals(5000, $prestation->part_medecin);

        // Modification ultérieure du tarif à 15 000 FCFA
        $acte->update(['tarif_normal' => 15000]);

        // Modification ultérieure du taux assurance à 80%
        $carte->update(['taux_couverture' => 80]);

        // L'ancienne prestation reste inchangée !
        $prestation->refresh();
        $this->assertEquals(10000, $prestation->montant);
        $this->assertEquals(7000, $prestation->montant_assurance);
        $this->assertEquals(3000, $prestation->montant_patient);
        $this->assertEquals(5000, $prestation->part_medecin);
    }

    /** TEST 13 & 14 : Rémunération des Médecins (Salaire Fixe & Pourcentage) */
    public function test_remuneration_medecins_salaire_fixe_et_pourcentage(): void
    {
        $remunService = app(RemunerationService::class);
        $debut = Carbon::now()->startOfMonth()->toDateString();
        $fin = Carbon::now()->endOfMonth()->toDateString();

        // TEST 13: Médecin à salaire fixe
        $medecinFixe = Medecin::create([
            'nom' => 'Sarr',
            'prenom' => 'Dr',
            'type_remuneration' => 'salaire_fixe',
            'salaire_fixe' => 500000,
            'statut' => true,
        ]);

        $remunFixe = $remunService->genererRemunerationMensuelle($medecinFixe->id, $debut, $fin, $this->user->id);
        $this->assertEquals(500000, $remunFixe->montant_medecin);
        $this->assertEquals('salaire_fixe', $remunFixe->type_remuneration);

        // TEST 14: Médecin au pourcentage
        $medecinPct = Medecin::create([
            'nom' => 'Fall',
            'prenom' => 'Dr',
            'type_remuneration' => 'pourcentage',
            'pourcentage' => 60,
            'statut' => true,
        ]);

        $patient = Patient::create(['nom' => 'Gaye', 'prenom' => 'Ibrahima', 'sexe' => 'M', 'statut' => 'non_assure']);
        $service = Service::create(['code' => 'SURG', 'nom' => 'Chirurgie', 'statut' => true]);
        Acte::create(['service_id' => $service->id, 'code' => 'SURG-01', 'nom' => 'Chirurgie', 'tarif_normal' => 100000, 'statut' => true]);

        ReglePartage::create([
            'service_id' => $service->id,
            'pourcentage_medecin' => 60,
            'pourcentage_clinique' => 40,
            'date_debut' => Carbon::now()->subDays(5),
            'statut' => true,
        ]);

        // Prestation de 100 000 -> 60 000 médecin / 40 000 clinique
        app(PrestationService::class)->creerPrestationAutomatique([
            'patient_id' => $patient->id,
            'service_id' => $service->id,
            'medecin_id' => $medecinPct->id,
        ]);

        $remunPct = $remunService->genererRemunerationMensuelle($medecinPct->id, $debut, $fin, $this->user->id);
        $this->assertEquals(100000, $remunPct->montant_base);
        $this->assertEquals(60000, $remunPct->montant_medecin);
        $this->assertEquals(40000, $remunPct->montant_clinique);
    }

    /** TEST 15 : Route HTTP Clôture de Caisse et Impression Ticket Z */
    public function test_caisse_route_cloturer_et_impression_ticket_z(): void
    {
        $caisse = app(GestionCaisseService::class)->ouvrirCaisse($this->user->id, 25000, 'Guichet 1');

        $response = $this->post(route('caisses.cloturer', $caisse), [
            'solde_physique' => 24000,
            'observation' => 'Manquant 1000 FCFA pièces manquantes',
        ]);

        $response->assertRedirect(route('caisses.show', $caisse));

        $caisse->refresh();
        $this->assertEquals('fermee', $caisse->statut);
        $this->assertEquals(24000, $caisse->solde_physique);
        $this->assertEquals(-1000, $caisse->ecart);

        // Test Impression Rapport Z
        $printResponse = $this->get(route('caisses.print', $caisse));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('BILAN DE CLÔTURE DE CAISSE');
    }

    /** TEST 16 : Route HTTP Impression Reçu de Paiement */
    public function test_paiement_route_impression_recu(): void
    {
        $patient = Patient::create(['nom' => 'Kane', 'prenom' => 'Aissata', 'sexe' => 'F', 'statut' => 'non_assure']);
        $ticket = Ticket::create([
            'patient_id' => $patient->id,
            'reference' => 'TCK-TEST-99',
            'date_ticket' => now(),
            'montant_total' => 15000,
            'part_assurance' => 0,
            'part_patient' => 15000,
            'montant_paye' => 15000,
            'reste_a_payer' => 0,
            'statut_paiement' => 'paye',
            'user_id' => $this->user->id,
        ]);

        $paiement = Paiement::create([
            'ticket_id' => $ticket->id,
            'mode_paiement_id' => $this->modeEspeces->id,
            'montant_recu' => 15000,
            'montant_impute' => 15000,
            'montant_rendu' => 0,
            'date_paiement' => now(),
            'user_id' => $this->user->id,
        ]);

        $response = $this->get(route('paiements.print', $paiement));
        $response->assertStatus(200);
        $response->assertSee('QUITTANCE DE PAIEMENT');
        $response->assertSee($paiement->reference);
        $response->assertSee('15 000 FCFA');
    }
}

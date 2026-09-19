<?php

namespace Tests\Feature;

use App\Models\Acte;
use App\Models\Caisse;
use App\Models\Dette;
use App\Models\Medecin;
use App\Models\ModePaiement;
use App\Models\Paiement;
use App\Models\Patient;
use App\Models\Remuneration;
use App\Models\Role;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReferenceUrlsAndUserCreationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $roleAdmin = Role::create(['nom' => 'Administrateur']);
        $this->admin = User::create([
            'nom' => 'Admin',
            'prenom' => 'Principal',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role_id' => $roleAdmin->id,
        ]);

        $this->actingAs($this->admin);
    }

    /** Test : La page /create/user se charge avec un statut 200 OK (pas de TypeError) */
    public function test_create_user_page_loads_successfully_without_type_error(): void
    {
        $response = $this->get(route('user.create'));
        $response->assertStatus(200);
        $response->assertSee('Créer un Nouvel Utilisateur');
    }

    /** Test : Les URLs utilisent les références métier au lieu des IDs numériques */
    public function test_urls_use_business_references_instead_of_database_ids(): void
    {
        // 1. Patient
        $patient = Patient::create([
            'nom' => 'Diallo',
            'prenom' => 'Moussa',
            'sexe' => 'M',
            'telephone' => '77123456',
        ]);
        $this->assertNotNull($patient->reference);
        $this->assertStringStartsWith('PAT-', $patient->reference);
        $this->assertEquals(url('/patients/'.$patient->reference), route('patients.show', $patient));

        $respPatient = $this->get(route('patients.show', $patient));
        $respPatient->assertStatus(200);

        // 2. Service
        $service = Service::create([
            'code' => 'MED-GEN',
            'nom' => 'Médecine Générale',
            'statut' => true,
        ]);
        $this->assertEquals(url('/services/MED-GEN'), route('services.show', $service));
        $this->get(route('services.show', $service))->assertStatus(200);

        // 3. Acte
        $acte = Acte::create([
            'service_id' => $service->id,
            'code' => 'CS-GEN-01',
            'nom' => 'Consultation Générale',
            'tarif_normal' => 5000,
            'statut' => true,
        ]);
        $this->assertEquals(url('/actes/CS-GEN-01'), route('actes.show', $acte));
        $this->get(route('actes.show', $acte))->assertStatus(200);

        // 4. Ticket
        $ticket = Ticket::create([
            'patient_id' => $patient->id,
            'reference' => 'TCK-2026-9999',
            'date_ticket' => now(),
            'montant_total' => 5000,
            'part_assurance' => 0,
            'part_patient' => 5000,
            'montant_paye' => 0,
            'reste_a_payer' => 5000,
            'statut' => 'valide',
            'user_id' => $this->admin->id,
        ]);
        $this->assertEquals(url('/tickets/TCK-2026-9999'), route('tickets.show', $ticket));
        $this->get(route('tickets.show', $ticket))->assertStatus(200);

        // 5. Caisse (Session)
        $caisse = Caisse::create([
            'user_id' => $this->admin->id,
            'date_ouverture' => now(),
            'fonds_initial' => 20000,
            'statut' => 'ouverte',
        ]);
        $this->assertNotNull($caisse->reference);
        $this->assertStringStartsWith('SES-', $caisse->reference);
        $this->assertEquals(url('/caisses/'.$caisse->reference), route('caisses.show', $caisse));
        $this->get(route('caisses.show', $caisse))->assertStatus(200);

        // 6. Paiement
        $modeEspeces = ModePaiement::create(['nom' => 'Espèces', 'code' => 'ESP', 'statut' => true]);
        $paiement = Paiement::create([
            'ticket_id' => $ticket->id,
            'mode_paiement_id' => $modeEspeces->id,
            'montant_recu' => 5000,
            'montant_impute' => 5000,
            'montant_rendu' => 0,
            'date_paiement' => now(),
            'user_id' => $this->admin->id,
        ]);
        $this->assertNotNull($paiement->reference);
        $this->assertStringStartsWith('PAY-', $paiement->reference);
        $this->assertEquals(url('/paiements/'.$paiement->reference), route('paiements.show', $paiement));
        $this->get(route('paiements.show', $paiement))->assertStatus(200);

        // 7. Dette
        $dette = Dette::create([
            'ticket_id' => $ticket->id,
            'patient_id' => $patient->id,
            'user_id' => $this->admin->id,
            'montant_initial' => 5000,
            'montant_paye' => 0,
            'reste_a_payer' => 5000,
            'date_creation' => now(),
            'statut' => 'en_cours',
        ]);
        $this->assertNotNull($dette->reference);
        $this->assertStringStartsWith('DET-', $dette->reference);
        $this->assertEquals(url('/dettes/'.$dette->reference), route('dettes.show', $dette));
        $this->get(route('dettes.show', $dette))->assertStatus(200);

        // 8. Rémunération
        $medecin = Medecin::create([
            'nom' => 'Diallo',
            'prenom' => 'Mahamadou',
            'specialite' => 'Médecine Générale',
            'type_remuneration' => 'pourcentage',
            'pourcentage' => 40,
            'statut' => true,
        ]);
        $this->assertNotNull($medecin->code);
        $this->assertStringStartsWith('DR-', $medecin->code);
        $this->assertEquals(url('/medecins/'.$medecin->code), route('medecins.show', $medecin));
        $this->get(route('medecins.show', $medecin))->assertStatus(200);

        $remuneration = Remuneration::create([
            'medecin_id' => $medecin->id,
            'user_id' => $this->admin->id,
            'type_remuneration' => 'pourcentage',
            'periode_debut' => '2026-09-01',
            'periode_fin' => '2026-09-30',
            'montant_base' => 5000,
            'montant_medecin' => 2000,
            'montant_clinique' => 3000,
            'statut' => 'calculee',
        ]);
        $this->assertNotNull($remuneration->reference);
        $this->assertStringStartsWith('REM-', $remuneration->reference);
        $this->assertEquals(url('/remunerations/'.$remuneration->reference), route('remunerations.show', $remuneration));
        $this->get(route('remunerations.show', $remuneration))->assertStatus(200);

        // Vérification du fallback par ID
        $this->get('/remunerations/'.$remuneration->id)->assertStatus(200);
    }
}

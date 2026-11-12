<?php

use App\Models\Clinique;
use App\Models\Consultation;
use App\Models\Medecin;
use App\Models\Patient;
use App\Models\Role;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Crée un compte « Médecin » relié à une fiche médecin du service donné.
 *
 * @return array{0: User, 1: Medecin}
 */
function creerMedecinConnectable(Clinique $clinique, string $email, ?Service $service = null): array
{
    $role = Role::firstOrCreate(['nom' => 'Médecin'], ['description' => 'Praticien']);
    $compte = creerUtilisateur($clinique, $email, $role);

    $fiche = Medecin::create([
        'clinique_id' => $clinique->id,
        'user_id' => $compte->id,
        'service_id' => $service?->id,
        'nom' => 'Diallo',
        'prenom' => 'Alpha',
        'telephone' => '76000000',
        'specialite' => 'Médecine Générale',
        'type_remuneration' => 'pourcentage',
        'statut' => true,
    ]);

    return [$compte, $fiche];
}

function creerTicketDuJour(Clinique $clinique, array $attributs = []): Ticket
{
    static $numero = 0;
    $numero++;

    $patient = Patient::create([
        'clinique_id' => $clinique->id,
        'nom' => 'Patient'.$numero,
        'prenom' => 'Test',
        'sexe' => 'F',
    ]);

    return Ticket::create(array_merge([
        'clinique_id' => $clinique->id,
        'user_id' => User::where('clinique_id', $clinique->id)->value('id'),
        'patient_id' => $patient->id,
        'reference' => 'TCK-FILE-'.$numero,
        'date_ticket' => now()->subMinutes(20),
        'montant_total' => 5000,
        'montant_assurance' => 0,
        'montant_patient' => 5000,
        'montant_paye' => 5000,
        'reste_a_payer' => 0,
        'statut' => 'paye',
    ], $attributs));
}

beforeEach(function () {
    $this->clinique = creerClinique('Clinique File', 'CFI');
    $this->service = Service::create(['clinique_id' => $this->clinique->id, 'nom' => 'Pédiatrie', 'code' => 'PED', 'statut' => true]);
    $this->autreService = Service::create(['clinique_id' => $this->clinique->id, 'nom' => 'Chirurgie', 'code' => 'CHI', 'statut' => true]);
    [$this->compte, $this->medecin] = creerMedecinConnectable($this->clinique, 'dr.diallo@file.test', $this->service);
});

it('envoie le médecin sur sa file d\'attente au lieu du tableau de bord financier', function () {
    $this->actingAs($this->compte)
        ->get(route('dashboard'))
        ->assertRedirect(route('medecin.file-attente'));
});

it('affiche seulement les tickets payés du jour attribués au médecin', function () {
    creerTicketDuJour($this->clinique, ['medecin_id' => $this->medecin->id, 'reference' => 'TCK-MOI']);
    $nonPaye = creerTicketDuJour($this->clinique, ['medecin_id' => $this->medecin->id, 'statut' => 'en_attente', 'montant_paye' => 0, 'reste_a_payer' => 5000]);
    $hier = creerTicketDuJour($this->clinique, ['medecin_id' => $this->medecin->id, 'date_ticket' => now()->subDay()]);

    $reponse = $this->actingAs($this->compte)->get(route('medecin.file-attente'))->assertOk();

    $reponse->assertSee('Mes patients en attente')
        ->assertSee(route('medecin.prendre', Ticket::where('reference', 'TCK-MOI')->first()), false)
        ->assertDontSee(route('medecin.prendre', $nonPaye), false)
        ->assertDontSee(route('medecin.prendre', $hier), false);
});

it('propose les tickets sans médecin de son service, pas ceux des autres services', function () {
    $duService = creerTicketDuJour($this->clinique, ['service_id' => $this->service->id]);
    $autreService = creerTicketDuJour($this->clinique, ['service_id' => $this->autreService->id]);

    $this->actingAs($this->compte)->get(route('medecin.file-attente'))
        ->assertSee(route('medecin.prendre', $duService), false)
        ->assertDontSee(route('medecin.prendre', $autreService), false);
});

it('ouvre une consultation en cours quand le médecin prend un patient en charge', function () {
    $ticket = creerTicketDuJour($this->clinique, ['service_id' => $this->service->id]);

    $reponse = $this->actingAs($this->compte)->post(route('medecin.prendre', $ticket));

    $consultation = Consultation::where('ticket_id', $ticket->id)->first();
    expect($consultation)->not->toBeNull()
        ->and($consultation->statut)->toBe('en_cours')
        ->and((int) $consultation->medecin_id)->toBe($this->medecin->id)
        ->and((int) $ticket->fresh()->medecin_id)->toBe($this->medecin->id);

    $reponse->assertRedirect(route('consultations.edit', $consultation));

    // Le patient quitte la file et passe dans « en consultation »
    $this->get(route('medecin.file-attente'))
        ->assertDontSee(route('medecin.prendre', $ticket), false)
        ->assertSee('Reprendre la consultation');
});

it('reprend la consultation ouverte à l\'accueil quand les constantes ont déjà été prises', function () {
    $ticket = creerTicketDuJour($this->clinique, ['medecin_id' => $this->medecin->id]);
    $consultationAccueil = Consultation::create([
        'clinique_id' => $this->clinique->id,
        'patient_id' => $ticket->patient_id,
        'ticket_id' => $ticket->id,
        'reference' => Consultation::genererReference(),
        'date_consultation' => now(),
        'motif_consultation' => 'Fièvre',
        'temperature' => 38.5,
        'statut' => 'en_attente',
    ]);

    $this->actingAs($this->compte)->get(route('medecin.file-attente'))->assertSee('Constantes prises');

    $this->post(route('medecin.prendre', $ticket))->assertRedirect(route('consultations.edit', $consultationAccueil));

    expect(Consultation::where('ticket_id', $ticket->id)->count())->toBe(1)
        ->and($consultationAccueil->fresh()->statut)->toBe('en_cours');
});

it('empêche un médecin de prendre un patient déjà attribué à un confrère', function () {
    [, $confrere] = creerMedecinConnectable($this->clinique, 'dr.keita@file.test', $this->service);
    $ticket = creerTicketDuJour($this->clinique, ['medecin_id' => $confrere->id]);

    $this->actingAs($this->compte)
        ->post(route('medecin.prendre', $ticket))
        ->assertRedirect(route('medecin.file-attente'))
        ->assertSessionHas('error');

    expect(Consultation::where('ticket_id', $ticket->id)->exists())->toBeFalse();
});

it('refuse la prise en charge d\'un ticket non payé', function () {
    $ticket = creerTicketDuJour($this->clinique, ['medecin_id' => $this->medecin->id, 'statut' => 'en_attente']);

    $this->actingAs($this->compte)
        ->post(route('medecin.prendre', $ticket))
        ->assertSessionHas('error');

    expect(Consultation::where('ticket_id', $ticket->id)->exists())->toBeFalse();
});

it('indique au médecin non relié à une fiche comment obtenir sa file', function () {
    $role = Role::firstOrCreate(['nom' => 'Médecin'], ['description' => 'Praticien']);
    $sansFiche = creerUtilisateur($this->clinique, 'sans.fiche@file.test', $role);

    $this->actingAs($sansFiche)->get(route('medecin.file-attente'))
        ->assertOk()
        ->assertSee('Votre compte n\'est relié à aucune fiche médecin', false);
});

it('n\'accepte comme compte de connexion qu\'un compte Médecin de la même clinique', function () {
    $admin = connecterAdmin($this->clinique);
    $roleCaissier = Role::firstOrCreate(['nom' => 'Caissier'], ['description' => 'Caisse']);
    $caissier = creerUtilisateur($this->clinique, 'caisse@file.test', $roleCaissier);

    $autreClinique = creerClinique('Autre Clinique', 'AUT');
    [$medecinAilleurs] = creerMedecinConnectable($autreClinique, 'dr.ailleurs@file.test');

    $donnees = [
        'nom' => 'Koné', 'prenom' => 'Awa', 'telephone' => '70000000', 'specialite' => 'Pédiatrie',
        'type_remuneration' => 'pourcentage', 'pourcentage' => 30, 'statut' => 1,
    ];

    $this->actingAs($admin)->post(route('medecins.store'), $donnees + ['user_id' => $caissier->id])
        ->assertSessionHasErrors('user_id');
    $this->actingAs($admin)->post(route('medecins.store'), $donnees + ['user_id' => $medecinAilleurs->id])
        ->assertSessionHasErrors('user_id');
    // Compte déjà relié à la fiche du Dr Diallo
    $this->actingAs($admin)->post(route('medecins.store'), $donnees + ['user_id' => $this->compte->id])
        ->assertSessionHasErrors('user_id');
});

it('termine la consultation et renvoie le médecin à sa file', function () {
    $ticket = creerTicketDuJour($this->clinique, ['service_id' => $this->service->id]);
    $this->actingAs($this->compte)->post(route('medecin.prendre', $ticket));
    $consultation = Consultation::where('ticket_id', $ticket->id)->first();

    $this->put(route('consultations.update', $consultation), [
        'patient_id' => $ticket->patient_id,
        'medecin_id' => $this->medecin->id,
        'date_consultation' => now()->format('Y-m-d\TH:i'),
        'motif_consultation' => 'Toux',
        'diagnostic' => 'Bronchite',
        'statut' => 'terminee',
    ])->assertRedirect(route('medecin.file-attente'));

    expect($consultation->fresh()->statut)->toBe('terminee');

    $this->get(route('medecin.file-attente'))->assertSee('Vus aujourd\'hui', false)->assertSee('Bronchite');
});

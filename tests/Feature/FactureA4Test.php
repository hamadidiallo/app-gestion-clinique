<?php

use App\Models\Assurance;
use App\Models\Patient;
use App\Models\Ticket;
use App\Models\TicketDetail;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function creerTicketAssure(): Ticket
{
    $assurance = Assurance::create([
        'nom' => 'AMO',
        'code' => 'AMO',
        'taux_par_defaut' => 70,
        'statut' => true,
    ]);

    $patient = Patient::create([
        'nom' => 'Traoré',
        'prenom' => 'Aminata',
        'telephone' => '76000000',
        'sexe' => 'F',
        'numero_assure' => 'AMO-123456',
    ]);

    $ticket = Ticket::create([
        'patient_id' => $patient->id,
        'assurance_id' => $assurance->id,
        'user_id' => auth()->id(),
        'reference' => 'TCK-FACTURE-A4',
        'date_ticket' => Carbon::parse('2026-11-12 09:42'),
        'montant_total' => 12500,
        'montant_assurance' => 8750,
        'montant_patient' => 3750,
        'montant_paye' => 2000,
        'reste_a_payer' => 1750,
        'statut' => 'partiel',
    ]);

    TicketDetail::create([
        'ticket_id' => $ticket->id,
        'designation' => 'Consultation générale',
        'type_item' => 'acte',
        'quantite' => 1,
        'prix_unitaire' => 10000,
        'montant_total' => 10000,
        'taux_couverture' => 70,
        'montant_assurance' => 7000,
        'montant_patient' => 3000,
    ]);

    return $ticket;
}

it('imprime la facture A4 d\'un ticket avec les montants, l\'assurance et le montant en lettres', function () {
    connecterAdmin(creerClinique('Clinique Baobab', 'BAO'));
    $ticket = creerTicketAssure();

    $this->get(route('tickets.facture', $ticket))
        ->assertOk()
        ->assertViewIs('facturation.tickets.facture')
        ->assertSee('FACTURE')
        ->assertSee('TCK-FACTURE-A4')
        ->assertSee('Clinique Baobab')
        ->assertSee('Consultation générale')
        ->assertSee('12 500')
        ->assertSee('8 750')
        ->assertSee('3 750')
        ->assertSee('Reste dû')
        ->assertSee('1 750')
        ->assertSee('AMO-123456')
        ->assertSee('Arrêtée la présente facture à la somme de')
        ->assertSee('Trois mille sept cent cinquante francs CFA')
        ->assertSee('size: A4', false);
});

it('ne charge aucune ressource externe, pour s\'imprimer même avec une connexion faible', function () {
    connecterAdmin();
    $ticket = creerTicketAssure();

    $this->get(route('tickets.facture', $ticket))
        ->assertOk()
        ->assertDontSee('cdn.jsdelivr.net', false)
        ->assertDontSee('unpkg.com', false)
        ->assertDontSee('fonts.googleapis.com', false);
});

it('propose la facture A4 depuis la fiche du ticket', function () {
    connecterAdmin();
    $ticket = creerTicketAssure();

    $this->get(route('tickets.show', $ticket))
        ->assertOk()
        ->assertSee(route('tickets.facture', $ticket), false)
        ->assertSee('Facture A4');
});

it('refuse la facture d\'un ticket appartenant à une autre clinique', function () {
    connecterAdmin(creerClinique('Clinique A', 'CLA'));
    $ticket = creerTicketAssure();

    connecterAdmin(creerClinique('Clinique B', 'CLB'));

    $this->get(route('tickets.facture', $ticket))->assertNotFound();
});

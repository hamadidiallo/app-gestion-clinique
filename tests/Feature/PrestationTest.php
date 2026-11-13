<?php

use App\Models\Facturation\Prestation;
use App\Models\Facturation\Service;
use App\Models\Facturation\Ticket;
use App\Models\Medical\Acte;
use App\Models\Patients\Patient;
use App\Models\Personnel\Medecin;

beforeEach(function () {
    // Session explicite : l'administrateur est rattache a une clinique reelle,
    // le cloisonnement multi-clinique s'applique donc pendant ces tests.
    connecterAdmin();
});

test('peut afficher la liste des prestations', function () {
    $response = $this->get(route('prestations.index'));

    $response->assertStatus(200);
});

test('peut afficher le formulaire de création de prestation', function () {
    $response = $this->get(route('prestation.create'));

    $response->assertStatus(200);
});

test('peut créer une nouvelle prestation', function () {
    $patient = Patient::create([
        'nom' => 'Kouassi',
        'prenom' => 'Jean',
        'sexe' => 'M',
        'statut' => 'assure',
    ]);

    $service = Service::create([
        'nom' => 'Laboratoire',
        'code' => 'SERV-LAB-02',
        'statut' => 1,
    ]);

    $acte = Acte::create([
        'service_id' => $service->id,
        'code' => 'LAB-NFS-01',
        'nom' => 'Numération Formule Sanguine',
        'tarif_normal' => 20000,
        'statut' => 1,
    ]);

    $medecin = Medecin::create([
        'nom' => 'Diop',
        'prenom' => 'Amadou',
        'specialite' => 'Biologiste',
        'statut' => 1,
    ]);

    $prestationData = [
        'patient_id' => $patient->id,
        'service_id' => $service->id,
        'acte_id' => $acte->id,
        'medecin_id' => $medecin->id,
        'type' => 'Examen sanguin',
        'montant' => 20000,
        'date_prestation' => now()->format('Y-m-d H:i:s'),
        'description' => 'Test sanguin complet',
        'statut' => 1,
    ];

    $response = $this->post(route('prestations.store'), $prestationData);

    $ticket = Ticket::first();
    $response->assertRedirect(route('tickets.print', $ticket));
    $this->assertDatabaseHas('prestations', [
        'patient_id' => $patient->id,
        'service_id' => $service->id,
        'montant' => 20000,
    ]);
});

test('peut modifier une prestation existante', function () {
    $patient = Patient::create([
        'nom' => 'Traoré',
        'prenom' => 'Fatou',
        'sexe' => 'F',
        'statut' => 'non_assure',
    ]);

    $service = Service::create([
        'nom' => 'Ophtalmologie',
        'code' => 'SERV-OPH-01',
        'statut' => 1,
    ]);

    $acte = Acte::create([
        'service_id' => $service->id,
        'code' => 'OPH-FOND-01',
        'nom' => 'Fond d\'oeil',
        'tarif_normal' => 25000,
        'statut' => 1,
    ]);

    $prestation = Prestation::create([
        'patient_id' => $patient->id,
        'service_id' => $service->id,
        'acte_id' => $acte->id,
        'montant' => 25000,
        'date_prestation' => now(),
        'statut' => 1,
    ]);

    $updateData = [
        'patient_id' => $patient->id,
        'service_id' => $service->id,
        'acte_id' => $acte->id,
        'type' => 'Consultation Spécialisée',
        'montant' => 30000,
        'date_prestation' => now()->format('Y-m-d H:i:s'),
        'statut' => 1,
    ];

    $response = $this->put(route('prestations.update', $prestation), $updateData);

    $response->assertRedirect(route('prestations.index'));
    $this->assertDatabaseHas('prestations', [
        'id' => $prestation->id,
        'montant' => 30000,
    ]);
});

test('peut supprimer une prestation', function () {
    $patient = Patient::create([
        'nom' => 'Sow',
        'prenom' => 'Moussa',
        'sexe' => 'M',
        'statut' => 'assure',
    ]);

    $service = Service::create([
        'nom' => 'Radiologie',
        'code' => 'SERV-RAD-02',
        'statut' => 1,
    ]);

    $acte = Acte::create([
        'service_id' => $service->id,
        'code' => 'RAD-PMR-01',
        'nom' => 'Radio Poumon',
        'tarif_normal' => 18000,
        'statut' => 1,
    ]);

    $prestation = Prestation::create([
        'patient_id' => $patient->id,
        'service_id' => $service->id,
        'acte_id' => $acte->id,
        'montant' => 18000,
        'date_prestation' => now(),
        'statut' => 1,
    ]);

    $response = $this->delete(route('prestations.destroy', $prestation));

    $response->assertRedirect(route('prestations.index'));
    $this->assertDatabaseMissing('prestations', [
        'id' => $prestation->id,
    ]);
});

<?php

use App\Models\Medecin;
use App\Models\Patient;
use App\Models\Prestation;
use App\Models\Service;
use App\Models\Tarif;

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
        'statut' => 'assure',
    ]);

    $service = Service::create([
        'nom' => 'Laboratoire',
        'code' => 'SERV-LAB-02',
        'statut' => 1,
    ]);

    $tarif = Tarif::create([
        'service_id' => $service->id,
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
        'tarif_id' => $tarif->id,
        'medecin_id' => $medecin->id,
        'type' => 'Examen sanguin',
        'montant' => 20000,
        'date_prestation' => now()->format('Y-m-d H:i:s'),
        'description' => 'Test sanguin complet',
        'statut' => 1,
    ];

    $response = $this->post(route('prestations.store'), $prestationData);

    $response->assertRedirect(route('prestations.index'));
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
        'statut' => 'non_assure',
    ]);

    $service = Service::create([
        'nom' => 'Ophtalmologie',
        'code' => 'SERV-OPH-01',
        'statut' => 1,
    ]);

    $tarif = Tarif::create([
        'service_id' => $service->id,
        'tarif_normal' => 25000,
        'statut' => 1,
    ]);

    $prestation = Prestation::create([
        'patient_id' => $patient->id,
        'service_id' => $service->id,
        'tarif_id' => $tarif->id,
        'montant' => 25000,
        'date_prestation' => now(),
        'statut' => 1,
    ]);

    $updateData = [
        'patient_id' => $patient->id,
        'service_id' => $service->id,
        'tarif_id' => $tarif->id,
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
        'statut' => 'assure',
    ]);

    $service = Service::create([
        'nom' => 'Radiologie',
        'code' => 'SERV-RAD-02',
        'statut' => 1,
    ]);

    $tarif = Tarif::create([
        'service_id' => $service->id,
        'tarif_normal' => 18000,
        'statut' => 1,
    ]);

    $prestation = Prestation::create([
        'patient_id' => $patient->id,
        'service_id' => $service->id,
        'tarif_id' => $tarif->id,
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

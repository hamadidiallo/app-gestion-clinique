<?php

use App\Models\Service;

test('peut afficher la liste des services', function () {
    $response = $this->get(route('services.index'));

    $response->assertStatus(200);
});

test('peut afficher le formulaire de création de service', function () {
    $response = $this->get(route('service.create'));

    $response->assertStatus(200);
});

test('peut créer un nouveau service', function () {
    $serviceData = [
        'nom' => 'Consultation Généraliste',
        'code' => 'SERV-CONS-01',
        'description' => 'Consultation de médecine générale',
        'statut' => 1,
    ];

    $response = $this->post(route('services.store'), $serviceData);

    $response->assertRedirect(route('services.index'));
    $this->assertDatabaseHas('services', [
        'code' => 'SERV-CONS-01',
    ]);
});

test('peut modifier un service existant', function () {
    $service = Service::create([
        'nom' => 'Radiologie X',
        'code' => 'SERV-RADIO-01',
        'description' => 'Radiographie thoracique',
        'statut' => 1,
    ]);

    $updateData = [
        'nom' => 'Radiologie Thoracique',
        'code' => 'SERV-RADIO-01',
        'description' => 'Description mise à jour',
        'statut' => 1,
    ];

    $response = $this->put(route('services.update', $service), $updateData);

    $response->assertRedirect(route('services.index'));
    $this->assertDatabaseHas('services', [
        'id' => $service->id,
        'nom' => 'Radiologie Thoracique',
    ]);
});

test('peut supprimer un service sans tarifs', function () {
    $service = Service::create([
        'nom' => 'Analyse Sanguine',
        'code' => 'SERV-LABO-01',
        'statut' => 1,
    ]);

    $response = $this->delete(route('services.destroy', $service));

    $response->assertRedirect(route('services.index'));
    $this->assertDatabaseMissing('services', [
        'id' => $service->id,
    ]);
});

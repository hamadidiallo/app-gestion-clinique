<?php

use App\Models\Service;
use App\Models\Tarif;

test('peut afficher la liste des tarifs', function () {
    $response = $this->get(route('tarifs.index'));

    $response->assertStatus(200);
});

test('peut afficher le formulaire de création de tarif', function () {
    $response = $this->get(route('tarif.create'));

    $response->assertStatus(200);
});

test('peut créer un tarif pour un service', function () {
    $service = Service::create([
        'nom' => 'Pédiatrie',
        'code' => 'SERV-PED-01',
        'statut' => 1,
    ]);

    $tarifData = [
        'service_id' => $service->id,
        'tarif_normal' => 15000,
        'tarif_amo' => 12000,
        'tarif_specifique' => 10000,
        'statut' => 1,
        'description' => 'Tarif de consultation pédiatrique',
    ];

    $response = $this->post(route('tarifs.store'), $tarifData);

    $response->assertRedirect(route('tarifs.index'));
    $this->assertDatabaseHas('tarifs', [
        'service_id' => $service->id,
        'tarif_normal' => 15000,
    ]);
});

test('peut modifier un tarif existant', function () {
    $service = Service::create([
        'nom' => 'Dermatologie',
        'code' => 'SERV-DERM-01',
        'statut' => 1,
    ]);

    $tarif = Tarif::create([
        'service_id' => $service->id,
        'tarif_normal' => 20000,
        'statut' => 1,
    ]);

    $updateData = [
        'service_id' => $service->id,
        'tarif_normal' => 25000,
        'statut' => 1,
    ];

    $response = $this->put(route('tarifs.update', $tarif), $updateData);

    $response->assertRedirect(route('tarifs.index'));
    $this->assertDatabaseHas('tarifs', [
        'id' => $tarif->id,
        'tarif_normal' => 25000,
    ]);
});

test('peut supprimer un tarif', function () {
    $service = Service::create([
        'nom' => 'Cardiologie',
        'code' => 'SERV-CARD-01',
        'statut' => 1,
    ]);

    $tarif = Tarif::create([
        'service_id' => $service->id,
        'tarif_normal' => 30000,
        'statut' => 1,
    ]);

    $response = $this->delete(route('tarifs.destroy', $tarif));

    $response->assertRedirect(route('tarifs.index'));
    $this->assertDatabaseMissing('tarifs', [
        'id' => $tarif->id,
    ]);
});

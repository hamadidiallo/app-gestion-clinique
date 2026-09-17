<?php

use App\Models\Acte;
use App\Models\Service;

test('peut afficher le catalogue des actes médicaux', function () {
    $response = $this->get(route('actes.index'));

    $response->assertStatus(200);
    $response->assertSee('Catalogue des Actes Médicaux');
});

test('peut afficher le formulaire de création d\'un acte', function () {
    $response = $this->get(route('actes.create'));

    $response->assertStatus(200);
    $response->assertSeeText('Créer un Type d\'Acte Médical');
});

test('peut créer un nouvel acte médical avec validation et tarifs', function () {
    $service = Service::firstOrCreate(
        ['code' => 'TEST-SERV'],
        ['nom' => 'Service Test', 'statut' => true]
    );

    $acteData = [
        'service_id' => $service->id,
        'code' => 'ACT-TEST-001',
        'nom' => 'Acte de Test Unitaire',
        'categorie' => 'consultation',
        'tarif_normal' => 15000,
        'tarif_amo' => 12000,
        'tarif_specifique' => null,
        'part_medecin_pourcentage' => 60.00,
        'part_clinique_pourcentage' => 40.00,
        'statut' => 1,
        'description' => 'Description test',
    ];

    $response = $this->post(route('actes.store'), $acteData);

    $response->assertRedirect(route('actes.index'));
    $this->assertDatabaseHas('actes', [
        'code' => 'ACT-TEST-001',
        'nom' => 'Acte de Test Unitaire',
        'tarif_normal' => 15000,
    ]);
});

test('peut afficher la fiche détaillée d\'un acte médical', function () {
    $service = Service::firstOrCreate(
        ['code' => 'TEST-SERV'],
        ['nom' => 'Service Test', 'statut' => true]
    );

    $acte = Acte::create([
        'service_id' => $service->id,
        'code' => 'ACT-SHOW-01',
        'nom' => 'Acte Fiche Test',
        'categorie' => 'imagerie',
        'tarif_normal' => 20000,
        'tarif_amo' => 15000,
        'part_medecin_pourcentage' => 50,
        'part_clinique_pourcentage' => 50,
        'statut' => true,
    ]);

    $response = $this->get(route('actes.show', $acte));

    $response->assertStatus(200);
    $response->assertSee('ACT-SHOW-01');
    $response->assertSee('Acte Fiche Test');
    $response->assertSee('20 000');
});

test('peut modifier un acte médical existant', function () {
    $service = Service::firstOrCreate(
        ['code' => 'TEST-SERV'],
        ['nom' => 'Service Test', 'statut' => true]
    );

    $acte = Acte::create([
        'service_id' => $service->id,
        'code' => 'ACT-EDIT-01',
        'nom' => 'Acte Original',
        'categorie' => 'soins',
        'tarif_normal' => 5000,
        'part_medecin_pourcentage' => 50,
        'part_clinique_pourcentage' => 50,
        'statut' => true,
    ]);

    $updateData = [
        'service_id' => $service->id,
        'code' => 'ACT-EDIT-01',
        'nom' => 'Acte Modifié avec Succès',
        'categorie' => 'soins',
        'tarif_normal' => 7500,
        'tarif_amo' => 6000,
        'tarif_specifique' => null,
        'part_medecin_pourcentage' => 40,
        'part_clinique_pourcentage' => 60,
        'statut' => 1,
        'description' => 'Mise à jour effectuée',
    ];

    $response = $this->put(route('actes.update', $acte), $updateData);

    $response->assertRedirect(route('actes.index'));
    $this->assertDatabaseHas('actes', [
        'id' => $acte->id,
        'nom' => 'Acte Modifié avec Succès',
        'tarif_normal' => 7500,
    ]);
});

test('peut supprimer un acte médical', function () {
    $service = Service::firstOrCreate(
        ['code' => 'TEST-SERV'],
        ['nom' => 'Service Test', 'statut' => true]
    );

    $acte = Acte::create([
        'service_id' => $service->id,
        'code' => 'ACT-DEL-01',
        'nom' => 'Acte à Supprimer',
        'categorie' => 'biologie',
        'tarif_normal' => 3000,
        'part_medecin_pourcentage' => 20,
        'part_clinique_pourcentage' => 80,
        'statut' => true,
    ]);

    $response = $this->delete(route('actes.destroy', $acte));

    $response->assertRedirect(route('actes.index'));
    $this->assertDatabaseMissing('actes', [
        'id' => $acte->id,
    ]);
});

test('peut rechercher des actes via l\'endpoint json search pour l\'autocomplétion', function () {
    $service = Service::firstOrCreate(
        ['code' => 'TEST-SERV'],
        ['nom' => 'Service Test', 'statut' => true]
    );

    Acte::create([
        'service_id' => $service->id,
        'code' => 'ACT-SEARCH-01',
        'nom' => 'Recherche Échographie Doppler',
        'categorie' => 'imagerie',
        'tarif_normal' => 25000,
        'tarif_amo' => 20000,
        'part_medecin_pourcentage' => 50,
        'part_clinique_pourcentage' => 50,
        'statut' => true,
    ]);

    $response = $this->getJson(route('actes.search', ['q' => 'Doppler']));

    $response->assertStatus(200);
    $response->assertJsonFragment([
        'code' => 'ACT-SEARCH-01',
        'nom' => 'Recherche Échographie Doppler',
        'tarif_normal' => 25000.0,
    ]);
});

test('peut filtrer le catalogue des actes par service et statut', function () {
    $serviceA = Service::firstOrCreate(['code' => 'SERV-A'], ['nom' => 'Département A', 'statut' => true]);
    $serviceB = Service::firstOrCreate(['code' => 'SERV-B'], ['nom' => 'Département B', 'statut' => true]);

    Acte::create([
        'service_id' => $serviceA->id,
        'code' => 'ACT-FLT-A',
        'nom' => 'Acte Spécifique A',
        'categorie' => 'consultation',
        'tarif_normal' => 5000,
        'part_medecin_pourcentage' => 50,
        'part_clinique_pourcentage' => 50,
        'statut' => true,
    ]);

    Acte::create([
        'service_id' => $serviceB->id,
        'code' => 'ACT-FLT-B',
        'nom' => 'Acte Spécifique B',
        'categorie' => 'chirurgie',
        'tarif_normal' => 50000,
        'part_medecin_pourcentage' => 50,
        'part_clinique_pourcentage' => 50,
        'statut' => true,
    ]);

    $response = $this->get(route('actes.index', ['service_id' => $serviceA->id]));
    $response->assertStatus(200);
    $response->assertSee('ACT-FLT-A');
    $response->assertDontSee('ACT-FLT-B');
});

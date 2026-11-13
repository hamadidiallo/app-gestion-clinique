<?php

use App\Models\Acte;
use App\Models\ActeParametre;
use App\Models\Role;
use App\Models\Service;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function creerExamen(string $categorie = 'biologie'): Acte
{
    $service = Service::create(['nom' => 'Laboratoire', 'code' => 'LAB', 'statut' => true]);

    return Acte::create([
        'service_id' => $service->id,
        'code' => 'NFS-'.uniqid(),
        'nom' => 'Numération formule sanguine',
        'categorie' => $categorie,
        'tarif_normal' => 7500,
        'statut' => true,
    ]);
}

it('crée le rôle Laborantin avec les rôles de référence', function () {
    $this->seed(RoleSeeder::class);

    expect(Role::where('nom', 'Laborantin')->exists())->toBeTrue();
});

it('ajoute un paramètre chiffré avec virgule décimale et l\'affiche sur la fiche de l\'examen', function () {
    connecterAdmin();
    $examen = creerExamen();

    $this->post(route('actes.parametres.store', $examen), [
        'nom' => 'Hémoglobine',
        'type' => 'numerique',
        'unite' => 'g/dL',
        'valeur_min' => '12',
        'valeur_max' => '16,5',
    ])->assertRedirect(route('actes.show', $examen));

    $parametre = $examen->parametres()->first();
    expect($parametre->nom)->toBe('Hémoglobine')
        ->and((float) $parametre->valeur_max)->toBe(16.5);

    $this->get(route('actes.show', $examen))
        ->assertOk()
        ->assertSee('Paramètres de l\'examen', false)
        ->assertSee('Hémoglobine')
        ->assertSee('12 – 16,5 g/dL');
});

it('détecte les valeurs hors norme, chiffrées ou en texte', function () {
    connecterAdmin();
    $examen = creerExamen();

    $hemoglobine = $examen->parametres()->create(['nom' => 'Hémoglobine', 'type' => 'numerique', 'valeur_min' => 12, 'valeur_max' => 16]);
    $tdr = $examen->parametres()->create(['nom' => 'TDR paludisme', 'type' => 'texte', 'valeur_normale_texte' => 'Négatif']);

    expect($hemoglobine->estHorsNorme('13,2'))->toBeFalse()
        ->and($hemoglobine->estHorsNorme('9,8'))->toBeTrue()
        ->and($hemoglobine->estHorsNorme('17'))->toBeTrue()
        ->and($hemoglobine->estHorsNorme(''))->toBeFalse()
        ->and($tdr->estHorsNorme('négatif'))->toBeFalse()
        ->and($tdr->estHorsNorme('Positif'))->toBeTrue();
});

it('refuse une valeur maximale inférieure à la minimale', function () {
    connecterAdmin();
    $examen = creerExamen();

    $this->post(route('actes.parametres.store', $examen), [
        'nom' => 'Glycémie', 'type' => 'numerique', 'valeur_min' => '1,10', 'valeur_max' => '0,70',
    ])->assertSessionHasErrors('valeur_max');

    expect($examen->parametres()->count())->toBe(0);
});

it('n\'accepte des paramètres que sur un examen de laboratoire', function () {
    connecterAdmin();
    $consultation = creerExamen('consultation');

    $this->post(route('actes.parametres.store', $consultation), [
        'nom' => 'Hémoglobine', 'type' => 'numerique', 'valeur_min' => '12',
    ])->assertNotFound();
});

it('réserve le réglage des paramètres à l\'administrateur', function () {
    $clinique = creerClinique();
    connecterAdmin($clinique);
    $examen = creerExamen();

    $roleCaissier = Role::firstOrCreate(['nom' => 'Caissier'], ['description' => 'Caisse']);
    $caissier = creerUtilisateur($clinique, 'caisse@param.test', $roleCaissier);

    $this->actingAs($caissier)->post(route('actes.parametres.store', $examen), [
        'nom' => 'Hémoglobine', 'type' => 'numerique', 'valeur_min' => '12',
    ])->assertForbidden();

    expect(ActeParametre::count())->toBe(0);
});

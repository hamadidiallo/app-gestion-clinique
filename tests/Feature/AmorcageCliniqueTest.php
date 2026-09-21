<?php

use App\Models\Acte;
use App\Models\Assurance;
use App\Models\CategorieDepense;
use App\Models\Clinique;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use App\Services\AmorcageCliniqueService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('une clinique creee a l inscription recoit son referentiel de demarrage', function () {
    Role::firstOrCreate(['nom' => 'Administrateur'], ['description' => 'x']);

    auth()->logout();

    $this->post('/register', [
        'action_type' => 'creer',
        'nom_clinique' => 'Clinique du Fleuve',
        'ville' => 'Ségou',
        'nom' => 'Kone', 'prenom' => 'Dr Seni',
        'email' => 'direction@fleuve.ml',
        'telephone' => '76 55 44 33',
        'password' => 'motdepasse', 'password_confirmation' => 'motdepasse',
    ])->assertRedirect(route('dashboard'));

    $clinique = Clinique::where('nom', 'Clinique du Fleuve')->first();
    expect($clinique)->not->toBeNull();

    // Le referentiel appartient bien a cette clinique
    expect(Service::withoutGlobalScopes()->where('clinique_id', $clinique->id)->count())->toBeGreaterThan(0)
        ->and(Acte::withoutGlobalScopes()->where('clinique_id', $clinique->id)->count())->toBeGreaterThan(0)
        ->and(Assurance::withoutGlobalScopes()->where('clinique_id', $clinique->id)->count())->toBeGreaterThan(0)
        ->and(CategorieDepense::withoutGlobalScopes()->where('clinique_id', $clinique->id)->count())->toBeGreaterThan(0);

    // Et il est visible par l'administrateur fraichement connecte
    expect(Acte::count())->toBeGreaterThan(0)
        ->and(Assurance::count())->toBeGreaterThan(0);

    // Aucune ligne de referentiel orpheline
    expect(Acte::withoutGlobalScopes()->whereNull('clinique_id')->count())->toBe(0);
});

test('le referentiel de deux cliniques reste cloisonne', function () {
    Role::firstOrCreate(['nom' => 'Administrateur'], ['description' => 'x']);

    foreach ([['A', 'a@a.ml', '76 00 00 01'], ['B', 'b@b.ml', '76 00 00 02']] as [$nom, $mail, $tel]) {
        auth()->logout();
        $this->post('/register', [
            'action_type' => 'creer',
            'nom_clinique' => "Clinique {$nom}", 'ville' => 'Bamako',
            'nom' => $nom, 'prenom' => 'Dr', 'email' => $mail, 'telephone' => $tel,
            'password' => 'motdepasse', 'password_confirmation' => 'motdepasse',
        ]);
    }

    $totalActes = Acte::withoutGlobalScopes()->count();
    $userA = User::where('email', 'a@a.ml')->first();

    $this->actingAs($userA);

    // L'administrateur A ne voit que la moitie du catalogue : le sien
    expect(Acte::count())->toBe($totalActes / 2)
        ->and(Acte::count())->toBeGreaterThan(0);
});

test('l amorcage est idempotent', function () {
    $clinique = Clinique::create([
        'nom' => 'C', 'slug' => 'c', 'code' => 'C', 'ville' => 'Bamako', 'pays' => 'Mali',
        'devise' => 'FCFA', 'statut' => 'actif', 'prefixe_ticket' => 'T',
        'prefixe_patient' => 'P', 'code_invitation' => 'C-1',
    ]);

    $service = app(AmorcageCliniqueService::class);
    $service->amorcer($clinique);
    $apresPremier = Acte::withoutGlobalScopes()->count();

    $service->amorcer($clinique);

    expect(Acte::withoutGlobalScopes()->count())->toBe($apresPremier);
});

test('deux cliniques aux noms proches peuvent coexister', function () {
    Role::firstOrCreate(['nom' => 'Administrateur'], ['description' => 'x']);

    // « Clinique du Fleuve » et « Clinique des Collines » produisent toutes deux
    // le code « CLI » et doivent pourtant pouvoir s'inscrire l'une apres l'autre.
    $noms = ['Clinique du Fleuve', 'Clinique des Collines', 'Clinique du Fleuve'];

    foreach ($noms as $i => $nomClinique) {
        auth()->logout();

        $this->post('/register', [
            'action_type' => 'creer',
            'nom_clinique' => $nomClinique, 'ville' => 'Bamako',
            'nom' => 'Kone', 'prenom' => 'Dr', 'email' => "dir{$i}@x.ml",
            'telephone' => '76 00 10 0'.$i,
            'password' => 'motdepasse', 'password_confirmation' => 'motdepasse',
        ])->assertRedirect(route('dashboard'));
    }

    expect(Clinique::count())->toBe(3)
        ->and(Clinique::pluck('code')->unique()->count())->toBe(3)
        ->and(Clinique::pluck('slug')->unique()->count())->toBe(3)
        ->and(Clinique::pluck('code_invitation')->unique()->count())->toBe(3);
});

<?php

use App\Models\Patient;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Incident du 02/11/2026 : la 2e clinique ne pouvait pas créer son premier
// patient (PAT-00001 déjà pris par la 1re), le numéro étant calculé sur les
// seuls patients de la clinique connectée alors qu'il est unique partout.
test('la premiere fiche patient d une deuxieme clinique ne reprend pas une reference existante', function () {
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

    $this->actingAs(User::where('email', 'a@a.ml')->first());
    $a = Patient::create(['nom' => 'Traore', 'prenom' => 'Awa', 'sexe' => 'F', 'statut' => 'non_assure']);

    $this->actingAs(User::where('email', 'b@b.ml')->first());
    $b = Patient::create(['nom' => 'Dia', 'prenom' => 'Seydou', 'sexe' => 'M', 'statut' => 'non_assure']);

    expect($b->reference)->not->toBe($a->reference)
        ->and($b->clinique_id)->not->toBe($a->clinique_id);
});

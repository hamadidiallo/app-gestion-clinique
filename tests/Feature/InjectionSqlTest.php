<?php

use App\Models\Clinique;
use App\Models\Patient;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('les charges utiles SQL classiques sont neutralisees', function () {
    $role = Role::firstOrCreate(['nom' => 'Administrateur'], ['description' => 'x']);
    $c = Clinique::create([
        'nom' => 'C', 'slug' => 'c', 'code' => 'CX', 'ville' => 'Bamako', 'pays' => 'Mali',
        'devise' => 'FCFA', 'statut' => 'actif', 'prefixe_ticket' => 'T',
        'prefixe_patient' => 'P', 'code_invitation' => 'CX-AAAAAA',
    ]);
    $u = User::create([
        'clinique_id' => $c->id, 'nom' => 'A', 'prenom' => 'B', 'email' => 'a@a.ml',
        'password' => bcrypt('password'), 'role_id' => $role->id,
    ]);
    Patient::create([
        'clinique_id' => $c->id, 'nom' => 'Diallo', 'prenom' => 'Awa',
        'sexe' => 'F', 'statut' => 'actif',
    ]);

    $charges = [
        "' OR '1'='1",
        "'; DROP TABLE patients; --",
        "' UNION SELECT null,email,password,null FROM users --",
        "1' AND (SELECT COUNT(*) FROM users) > 0 --",
        "%' OR 1=1 --",
    ];

    $this->actingAs($u);

    foreach ($charges as $charge) {
        $r = $this->getJson(route('patients.search', ['q' => $charge]));

        // La requete aboutit sans erreur SQL et ne remonte aucun enregistrement
        $r->assertOk();
        expect($r->json())->toBe([]);
    }

    // La table existe toujours et son contenu est intact
    expect(DB::table('patients')->count())->toBe(1);
});

test('les charges utiles SQL ne contournent pas la connexion', function () {
    $role = Role::firstOrCreate(['nom' => 'Caissier'], ['description' => 'x']);
    $c = Clinique::create([
        'nom' => 'C', 'slug' => 'c', 'code' => 'CX', 'ville' => 'Bamako', 'pays' => 'Mali',
        'devise' => 'FCFA', 'statut' => 'actif', 'prefixe_ticket' => 'T',
        'prefixe_patient' => 'P', 'code_invitation' => 'CX-AAAAAA',
    ]);
    User::create([
        'clinique_id' => $c->id, 'nom' => 'A', 'prenom' => 'B', 'email' => 'cible@a.ml',
        'telephone' => '76000000', 'password' => bcrypt('motdepasse'), 'role_id' => $role->id,
    ]);

    foreach (["' OR '1'='1", "cible@a.ml'--", "admin' OR 1=1 #"] as $charge) {
        auth()->logout();
        $this->post('/login', ['email' => $charge, 'password' => "' OR '1'='1"]);
        expect(auth()->check())->toBeFalse();
    }
});

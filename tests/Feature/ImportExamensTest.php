<?php

use App\Models\Administration\Role;
use App\Models\Facturation\Service;
use App\Models\Medical\Acte;
use App\Models\Medical\ActeParametre;
use App\Models\Patients\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

function fichierCsv(string $contenu): UploadedFile
{
    return UploadedFile::fake()->createWithContent('examens.csv', "\xEF\xBB\xBF".$contenu);
}

const ENTETE_CSV = 'code_examen;nom_examen;tarif;parametre;type;unite;min;max;min_homme;max_homme;min_femme;max_femme;min_enfant;max_enfant;valeur_normale_texte';

it('importe les examens cochés du catalogue de départ, inactifs tant que le tarif manque', function () {
    connecterAdmin();

    $this->post(route('actes.import.catalogue'), ['codes' => ['ACT-NFS-01', 'ACT-CRP-01']])
        ->assertRedirect(route('actes.import'))
        ->assertSessionHas('bilan', fn ($bilan) => $bilan['examens_crees'] === 2);

    $nfs = Acte::where('code', 'ACT-NFS-01')->first();
    expect($nfs->categorie)->toBe('biologie')
        ->and($nfs->statut)->toBeFalse()
        ->and($nfs->parametres()->count())->toBe(10)
        ->and(Service::where('code', 'LAB-BIO')->exists())->toBeTrue()
        ->and(Acte::where('code', 'ACT-GE-01')->exists())->toBeFalse();
});

it('complète un examen existant sans le dupliquer ni écraser les valeurs ajustées par la clinique', function () {
    connecterAdmin();
    $service = Service::create(['nom' => 'Laboratoire', 'code' => 'LAB-BIO', 'statut' => true]);
    $nfs = Acte::create(['service_id' => $service->id, 'code' => 'ACT-NFS-01', 'nom' => 'NFS', 'categorie' => 'biologie', 'tarif_normal' => 6000, 'statut' => true]);
    $nfs->parametres()->create(['nom' => 'Hémoglobine', 'type' => 'numerique', 'unite' => 'g/dL', 'valeur_min' => 11.5, 'valeur_max' => 16.5]);

    $this->post(route('actes.import.catalogue'), ['codes' => ['ACT-NFS-01']])
        ->assertSessionHas('bilan', fn ($bilan) => $bilan['examens_crees'] === 0 && $bilan['examens_completes'] === 1);

    expect(Acte::where('code', 'ACT-NFS-01')->count())->toBe(1)
        ->and($nfs->fresh()->statut)->toBeTrue()
        ->and($nfs->parametres()->count())->toBe(10)
        ->and((float) $nfs->parametres()->where('nom', 'Hémoglobine')->first()->valeur_min)->toBe(11.5);

    // Un second import ne crée rien de plus
    $this->post(route('actes.import.catalogue'), ['codes' => ['ACT-NFS-01']]);
    expect($nfs->parametres()->count())->toBe(10);
});

it('importe un fichier CSV avec virgules décimales, tarif et valeurs par sexe et pour l\'enfant', function () {
    connecterAdmin();

    $csv = ENTETE_CSV."\r\n"
        ."act-hb-01;Hémoglobine seule;3 500;Hémoglobine;numerique;g/dL;12;17;13;17;12;16;11;14;\r\n"
        ."ACT-TDR-01;TDR paludisme;2500;TDR;texte;;;;;;;;;;Négatif\r\n";

    $this->post(route('actes.import.fichier'), ['fichier' => fichierCsv($csv)])
        ->assertSessionHas('bilan', fn ($bilan) => $bilan['examens_crees'] === 2 && $bilan['parametres_ajoutes'] === 2);

    $hb = Acte::where('code', 'ACT-HB-01')->first();
    expect((float) $hb->tarif_normal)->toBe(3500.0)
        ->and($hb->statut)->toBeTrue();

    $parametre = $hb->parametres()->first();
    expect((float) $parametre->valeur_min_femme)->toBe(12.0)
        ->and($parametre->valeurs_normales)->toBe('12 – 17 · H 13 – 17 · F 12 – 16 · Enf. 11 – 14 g/dL');

    $tdr = Acte::where('code', 'ACT-TDR-01')->first()->parametres()->first();
    expect($tdr->type)->toBe('texte')->and($tdr->valeur_normale_texte)->toBe('Négatif');
});

it('n\'importe rien quand une ligne du fichier contient une erreur', function () {
    connecterAdmin();

    $csv = ENTETE_CSV."\r\n"
        ."ACT-OK-01;Examen correct;1000;Paramètre;numerique;g/L;1;2;;;;;;;\r\n"
        ."ACT-KO-01;Examen faux;1000;Paramètre;numerique;g/L;douze;2;;;;;;;\r\n";

    $this->post(route('actes.import.fichier'), ['fichier' => fichierCsv($csv)])
        ->assertSessionHas('erreursImport', fn ($erreurs) => str_contains($erreurs[0], 'Ligne 3') && str_contains($erreurs[0], 'douze'));

    expect(Acte::count())->toBe(0);
});

it('signale un fichier sans les colonnes du modèle', function () {
    connecterAdmin();

    $this->post(route('actes.import.fichier'), ['fichier' => fichierCsv("nom;prix\r\nNFS;6000\r\n")])
        ->assertSessionHas('erreursImport', fn ($erreurs) => str_contains($erreurs[0], 'Colonnes manquantes'));
});

it('fournit un modèle CSV lisible par Excel', function () {
    connecterAdmin();

    $reponse = $this->get(route('actes.import.modele'))->assertOk();

    expect($reponse->headers->get('content-disposition'))->toContain('modele-examens-medigest.csv')
        ->and($reponse->getContent())->toStartWith("\xEF\xBB\xBF".'code_examen;nom_examen');
});

it('applique les valeurs normales selon le sexe et l\'âge du patient', function () {
    connecterAdmin();
    $service = Service::create(['nom' => 'Laboratoire', 'code' => 'LAB-BIO', 'statut' => true]);
    $acte = Acte::create(['service_id' => $service->id, 'code' => 'ACT-HB-02', 'nom' => 'Hb', 'categorie' => 'biologie', 'tarif_normal' => 1000, 'statut' => true]);
    $hb = $acte->parametres()->create([
        'nom' => 'Hémoglobine', 'type' => 'numerique', 'unite' => 'g/dL',
        'valeur_min' => 12, 'valeur_max' => 17,
        'valeur_min_homme' => 13, 'valeur_max_homme' => 17,
        'valeur_min_femme' => 12, 'valeur_max_femme' => 16,
        'valeur_min_enfant' => 11, 'valeur_max_enfant' => 14,
    ]);

    $homme = Patient::create(['nom' => 'Traoré', 'prenom' => 'Moussa', 'sexe' => 'M', 'date_naissance' => now()->subYears(40)]);
    $femme = Patient::create(['nom' => 'Diarra', 'prenom' => 'Awa', 'sexe' => 'F', 'date_naissance' => now()->subYears(30)]);
    $enfant = Patient::create(['nom' => 'Koné', 'prenom' => 'Issa', 'sexe' => 'M', 'date_naissance' => now()->subYears(6)]);
    $sansAge = Patient::create(['nom' => 'Sidibé', 'prenom' => 'Ali', 'sexe' => 'M']);

    // 12,5 g/dL : normal chez la femme et l'enfant, bas chez l'homme adulte
    expect($hb->estHorsNorme('12,5', $homme))->toBeTrue()
        ->and($hb->estHorsNorme('12,5', $femme))->toBeFalse()
        ->and($hb->estHorsNorme('12,5', $enfant))->toBeFalse()
        ->and($hb->estHorsNorme('15', $enfant))->toBeTrue()
        // Sans date de naissance : les valeurs de l'homme adulte s'appliquent
        ->and($hb->estHorsNorme('12,5', $sansAge))->toBeTrue()
        ->and($hb->valeursNormalesPour($enfant))->toBe('11 – 14 g/dL');
});

it('enregistre la date de naissance du patient', function () {
    connecterAdmin();

    $this->post(route('patients.store'), [
        'nom' => 'Coulibaly', 'prenom' => 'Fanta', 'sexe' => 'F',
        'date_naissance' => '2019-03-15', 'statut' => 'non_assure',
    ]);

    $patient = Patient::where('nom', 'Coulibaly')->first();
    expect($patient?->date_naissance?->format('Y-m-d'))->toBe('2019-03-15')
        ->and($patient->estEnfant())->toBeTrue();
});

it('réserve l\'import des examens à l\'administrateur', function () {
    $clinique = creerClinique();
    $roleMedecin = Role::firstOrCreate(['nom' => 'Médecin'], ['description' => 'Praticien']);
    $medecin = creerUtilisateur($clinique, 'dr@import.test', $roleMedecin);

    $this->actingAs($medecin)->get(route('actes.import'))->assertForbidden();
    $this->actingAs($medecin)->post(route('actes.import.catalogue'), ['codes' => ['ACT-NFS-01']])->assertForbidden();

    expect(ActeParametre::count())->toBe(0);
});

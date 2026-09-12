<?php

// Importation des contrôleurs existants
use App\Http\Controllers\AssuranceController;
use App\Http\Controllers\CarteAssuranceController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PrestationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TarifController;
use App\Http\Controllers\UserController;

// Importation des 15 nouveaux contrôleurs
use App\Http\Controllers\MedecinController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketDetailController;
use App\Http\Controllers\ModePaiementController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\DetteController;
use App\Http\Controllers\ReglePartageController;
use App\Http\Controllers\RemunerationController;
use App\Http\Controllers\CaisseController;
use App\Http\Controllers\MouvementCaisseController;
use App\Http\Controllers\CategorieDepenseController;
use App\Http\Controllers\DepenseController;
use App\Http\Controllers\RecetteController;
use App\Http\Controllers\JournalActiviteController;

// Importation des contrôleurs du Dashboard, des Rapports et de l'Authentification
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\AuthController;

use Illuminate\Support\Facades\Route;

// Route utilitaire pour exécuter les migrations en base de données
Route::get('/run-migrations', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return '<h3>Migration exécutée avec succès !</h3><pre>' . \Illuminate\Support\Facades\Artisan::output() . '</pre><br><a href="' . route('tickets.create') . '">➔ Obtenir ou créer un ticket</a>';
    } catch (\Throwable $e) {
        return '<h3>Erreur lors de la migration :</h3><pre>' . $e->getMessage() . '</pre>';
    }
});

// Routes d'authentification publiques (Connexion et Inscription)
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::get('/register', 'showRegisterForm')->name('register');
    Route::post('/register', 'register');
});

// GROUPE SÉCURISÉ : TOUTES LES ROUTES DE L'APPLICATION PROTÉGÉES PAR LE MIDDLEWARE AUTH
Route::middleware(['auth'])->group(function () {

    // Route de déconnexion
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Route utilitaire pour exécuter les migrations en base de données
    Route::get('/run-migrations', function () {
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            return '<h3>Migration exécutée avec succès !</h3><pre>' . \Illuminate\Support\Facades\Artisan::output() . '</pre><br><a href="' . route('tickets.create') . '">➔ Obtenir ou créer un ticket</a>';
        } catch (\Throwable $e) {
            return '<h3>Erreur lors de la migration :</h3><pre>' . $e->getMessage() . '</pre>';
        }
    });

    // Route principale dirigeant vers le Tableau de Bord V1 (Dashboard)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Groupe de routes web gérant la consultation des Rapports et Statistiques
    Route::controller(RapportController::class)->group(function () {
        Route::get('/rapports/index', 'index')->name('rapports.index');
        Route::get('/rapports/assurances', 'assurances')->name('rapports.assurances');
        Route::get('/rapports/medecins', 'medecins')->name('rapports.medecins');
    });

    // Groupe de routes web gérant toutes les opérations CRUD liées aux rôles
    Route::controller(RoleController::class)->group(function () {
        Route::get('/role/index', 'index')->name('roles.index');
        Route::get('/create/role', 'create')->name('role.create');
        Route::post('/create/role', 'store')->name('roles.store');
        Route::get('/roles/{role}', 'show')->name('roles.show');
        Route::get('/roles/{role}/edit', 'edit')->name('roles.edit');
        Route::put('/roles/{role}', 'update')->name('roles.update');
        Route::delete('/roles/{role}', 'destroy')->name('roles.destroy');
    });

    // Groupe de routes web gérant toutes les opérations CRUD liées aux utilisateurs
    Route::controller(UserController::class)->group(function () {
        Route::get('/user/index', 'index')->name('users.index');
        Route::get('/create/user', 'create')->name('user.create');
        Route::post('/create/user', 'store')->name('users.store');
        Route::get('/users/{user}', 'show')->name('users.show');
        Route::get('/users/{user}/edit', 'edit')->name('users.edit');
        Route::put('/users/{user}', 'update')->name('users.update');
        Route::delete('/users/{user}', 'destroy')->name('users.destroy');
    });

    // Groupe de routes web gérant toutes les opérations CRUD liées aux patients
    Route::controller(PatientController::class)->group(function () {
        Route::get('/patient/index', 'index')->name('patients.index');
        Route::get('/create/patient', 'create')->name('patient.create');
        Route::post('/create/patient', 'store')->name('patients.store');
        Route::get('/patients/search', 'search')->name('patients.search');
        Route::get('/patients/{patient}', 'show')->name('patients.show');
        Route::get('/patients/{patient}/edit', 'edit')->name('patients.edit');
        Route::put('/patients/{patient}', 'update')->name('patients.update');
        Route::delete('/patients/{patient}', 'destroy')->name('patients.destroy');
    });

    // Groupe de routes web gérant toutes les opérations CRUD liées aux assurances
    Route::controller(AssuranceController::class)->group(function () {
        Route::get('/assurance/index', 'index')->name('assurances.index');
        Route::get('/create/assurance', 'create')->name('assurance.create');
        Route::post('/create/assurance', 'store')->name('assurances.store');
        Route::get('/assurances/{assurance}', 'show')->name('assurances.show');
        Route::get('/assurances/{assurance}/edit', 'edit')->name('assurances.edit');
        Route::put('/assurances/{assurance}', 'update')->name('assurances.update');
        Route::delete('/assurances/{assurance}', 'destroy')->name('assurances.destroy');
    });

    // Groupe de routes web gérant toutes les opérations CRUD liées aux cartes d'assurance
    Route::controller(CarteAssuranceController::class)->group(function () {
        Route::get('/carteassurance/index', 'index')->name('cartesassurances.index');
        Route::get('/create/carteassurance', 'create')->name('carteassurance.create');
        Route::post('/create/carteassurance', 'store')->name('cartesassurances.store');
        Route::get('/cartesassurances/{carteassurance}', 'show')->name('cartesassurances.show');
        Route::get('/cartesassurances/{carteassurance}/edit', 'edit')->name('cartesassurances.edit');
        Route::put('/cartesassurances/{carteassurance}', 'update')->name('cartesassurances.update');
        Route::delete('/cartesassurances/{carteassurance}', 'destroy')->name('cartesassurances.destroy');
    });

    // Groupe de routes web gérant toutes les opérations CRUD liées aux services médicaux
    Route::controller(ServiceController::class)->group(function () {
        Route::get('/service/index', 'index')->name('services.index');
        Route::get('/create/service', 'create')->name('service.create');
        Route::post('/create/service', 'store')->name('services.store');
        Route::get('/services/search', 'search')->name('services.search');
        Route::get('/services/{service}', 'show')->name('services.show');
        Route::get('/services/{service}/edit', 'edit')->name('services.edit');
        Route::put('/services/{service}', 'update')->name('services.update');
        Route::delete('/services/{service}', 'destroy')->name('services.destroy');
    });

    // Groupe de routes web gérant toutes les opérations CRUD liées aux tarifs
    Route::controller(TarifController::class)->group(function () {
        Route::get('/tarif/index', 'index')->name('tarifs.index');
        Route::get('/create/tarif', 'create')->name('tarif.create');
        Route::post('/create/tarif', 'store')->name('tarifs.store');
        Route::get('/tarifs/{tarif}', 'show')->name('tarifs.show');
        Route::get('/tarifs/{tarif}/edit', 'edit')->name('tarifs.edit');
        Route::put('/tarifs/{tarif}', 'update')->name('tarifs.update');
        Route::delete('/tarifs/{tarif}', 'destroy')->name('tarifs.destroy');
    });

    // Groupe de routes web gérant toutes les opérations CRUD liées aux prestations
    Route::controller(PrestationController::class)->group(function () {
        Route::get('/prestation/index', 'index')->name('prestations.index');
        Route::get('/create/prestation', 'create')->name('prestation.create');
        Route::post('/create/prestation', 'store')->name('prestations.store');
        Route::post('/prestations/preview-calculs', 'previewCalculs')->name('prestations.preview');
        Route::get('/prestations/{prestation}', 'show')->name('prestations.show');
        Route::get('/prestations/{prestation}/edit', 'edit')->name('prestations.edit');
        Route::put('/prestations/{prestation}', 'update')->name('prestations.update');
        Route::delete('/prestations/{prestation}', 'destroy')->name('prestations.destroy');
    });

    // Groupe de routes pour la gestion des Médecins
    Route::controller(MedecinController::class)->group(function () {
        Route::get('/medecin/index', 'index')->name('medecins.index');
        Route::get('/create/medecin', 'create')->name('medecins.create');
        Route::post('/create/medecin', 'store')->name('medecins.store');
        Route::get('/medecins/{medecin}', 'show')->name('medecins.show');
        Route::get('/medecins/{medecin}/edit', 'edit')->name('medecins.edit');
        Route::put('/medecins/{medecin}', 'update')->name('medecins.update');
        Route::delete('/medecins/{medecin}', 'destroy')->name('medecins.destroy');
    });

    // Groupe de routes pour la gestion des Employés
    Route::controller(EmployeController::class)->group(function () {
        Route::get('/employe/index', 'index')->name('employes.index');
        Route::get('/create/employe', 'create')->name('employes.create');
        Route::post('/create/employe', 'store')->name('employes.store');
        Route::get('/employes/{employe}', 'show')->name('employes.show');
        Route::get('/employes/{employe}/edit', 'edit')->name('employes.edit');
        Route::put('/employes/{employe}', 'update')->name('employes.update');
        Route::delete('/employes/{employe}', 'destroy')->name('employes.destroy');
    });

    // Groupe de routes pour la gestion des Tickets de caisse / factures
    Route::controller(TicketController::class)->group(function () {
        Route::get('/ticket/index', 'index')->name('tickets.index');
        Route::get('/create/ticket', 'create')->name('tickets.create');
        Route::post('/create/ticket', 'store')->name('tickets.store');
        Route::get('/tickets/{ticket}', 'show')->name('tickets.show');
        Route::get('/tickets/{ticket}/print', 'print')->name('tickets.print');
        Route::get('/tickets/{ticket}/edit', 'edit')->name('tickets.edit');
        Route::put('/tickets/{ticket}', 'update')->name('tickets.update');
        Route::delete('/tickets/{ticket}', 'destroy')->name('tickets.destroy');
    });

    // Groupe de routes pour les détails de prestations par ticket
    Route::controller(TicketDetailController::class)->group(function () {
        Route::get('/ticketdetail/index', 'index')->name('ticketdetails.index');
        Route::get('/create/ticketdetail', 'create')->name('ticketdetails.create');
        Route::post('/create/ticketdetail', 'store')->name('ticketdetails.store');
        Route::get('/ticketdetails/{ticketdetail}', 'show')->name('ticketdetails.show');
        Route::get('/ticketdetails/{ticketdetail}/edit', 'edit')->name('ticketdetails.edit');
        Route::put('/ticketdetails/{ticketdetail}', 'update')->name('ticketdetails.update');
        Route::delete('/ticketdetails/{ticketdetail}', 'destroy')->name('ticketdetails.destroy');
    });

    // Groupe de routes pour les Modes de Paiement
    Route::controller(ModePaiementController::class)->group(function () {
        Route::get('/modepaiement/index', 'index')->name('modepaiements.index');
        Route::get('/create/modepaiement', 'create')->name('modepaiements.create');
        Route::post('/create/modepaiement', 'store')->name('modepaiements.store');
        Route::get('/modepaiements/{modepaiement}', 'show')->name('modepaiements.show');
        Route::get('/modepaiements/{modepaiement}/edit', 'edit')->name('modepaiements.edit');
        Route::put('/modepaiements/{modepaiement}', 'update')->name('modepaiements.update');
        Route::delete('/modepaiements/{modepaiement}', 'destroy')->name('modepaiements.destroy');
    });

    // Groupe de routes pour l'historique des Paiements
    Route::controller(PaiementController::class)->group(function () {
        Route::get('/paiement/index', 'index')->name('paiements.index');
        Route::get('/create/paiement', 'create')->name('paiements.create');
        Route::post('/create/paiement', 'store')->name('paiements.store');
        Route::get('/paiements/{paiement}', 'show')->name('paiements.show');
        Route::get('/paiements/{paiement}/edit', 'edit')->name('paiements.edit');
        Route::put('/paiements/{paiement}', 'update')->name('paiements.update');
        Route::delete('/paiements/{paiement}', 'destroy')->name('paiements.destroy');
    });

    // Groupe de routes pour le suivi des Dettes / Impayés
    Route::controller(DetteController::class)->group(function () {
        Route::get('/dette/index', 'index')->name('dettes.index');
        Route::get('/create/dette', 'create')->name('dettes.create');
        Route::post('/create/dette', 'store')->name('dettes.store');
        Route::get('/dettes/{dette}', 'show')->name('dettes.show');
        Route::get('/dettes/{dette}/edit', 'edit')->name('dettes.edit');
        Route::put('/dettes/{dette}', 'update')->name('dettes.update');
        Route::delete('/dettes/{dette}', 'destroy')->name('dettes.destroy');
    });

    // Groupe de routes pour les Règles de Partage d'honoraires
    Route::controller(ReglePartageController::class)->group(function () {
        Route::get('/reglepartage/index', 'index')->name('reglespartage.index');
        Route::get('/create/reglepartage', 'create')->name('reglespartage.create');
        Route::post('/create/reglepartage', 'store')->name('reglespartage.store');
        Route::get('/reglespartage/{reglespartage}', 'show')->name('reglespartage.show');
        Route::get('/reglespartage/{reglespartage}/edit', 'edit')->name('reglespartage.edit');
        Route::put('/reglespartage/{reglespartage}', 'update')->name('reglespartage.update');
        Route::delete('/reglespartage/{reglespartage}', 'destroy')->name('reglespartage.destroy');
    });

    // Groupe de routes pour les Rémunérations Médecins / Paies
    Route::controller(RemunerationController::class)->group(function () {
        Route::get('/remuneration/index', 'index')->name('remunerations.index');
        Route::get('/create/remuneration', 'create')->name('remunerations.create');
        Route::post('/create/remuneration', 'store')->name('remunerations.store');
        Route::get('/remunerations/{remuneration}', 'show')->name('remunerations.show');
        Route::get('/remunerations/{remuneration}/edit', 'edit')->name('remunerations.edit');
        Route::put('/remunerations/{remuneration}', 'update')->name('remunerations.update');
        Route::delete('/remunerations/{remuneration}', 'destroy')->name('remunerations.destroy');
    });

    // Groupe de routes pour les Caisses & Guichets
    Route::controller(CaisseController::class)->group(function () {
        Route::get('/caisse/index', 'index')->name('caisses.index');
        Route::get('/create/caisse', 'create')->name('caisses.create');
        Route::post('/create/caisse', 'store')->name('caisses.store');
        Route::get('/caisses/{caiss}', 'show')->name('caisses.show');
        Route::get('/caisses/{caiss}/edit', 'edit')->name('caisses.edit');
        Route::put('/caisses/{caiss}', 'update')->name('caisses.update');
        Route::delete('/caisses/{caiss}', 'destroy')->name('caisses.destroy');
    });

    // Groupe de routes pour les Mouvements d'espèces en Caisse
    Route::controller(MouvementCaisseController::class)->group(function () {
        Route::get('/mouvementcaisse/index', 'index')->name('mouvementcaisses.index');
        Route::get('/create/mouvementcaisse', 'create')->name('mouvementcaisses.create');
        Route::post('/create/mouvementcaisse', 'store')->name('mouvementcaisses.store');
        Route::get('/mouvementcaisses/{mouvementcaiss}', 'show')->name('mouvementcaisses.show');
        Route::get('/mouvementcaisses/{mouvementcaiss}/edit', 'edit')->name('mouvementcaisses.edit');
        Route::put('/mouvementcaisses/{mouvementcaiss}', 'update')->name('mouvementcaisses.update');
        Route::delete('/mouvementcaisses/{mouvementcaiss}', 'destroy')->name('mouvementcaisses.destroy');
    });

    // Groupe de routes pour les Catégories de Dépenses
    Route::controller(CategorieDepenseController::class)->group(function () {
        Route::get('/categoriedepense/index', 'index')->name('categoriedepenses.index');
        Route::get('/create/categoriedepense', 'create')->name('categoriedepenses.create');
        Route::post('/create/categoriedepense', 'store')->name('categoriedepenses.store');
        Route::get('/categoriedepenses/{categoriedepense}', 'show')->name('categoriedepenses.show');
        Route::get('/categoriedepenses/{categoriedepense}/edit', 'edit')->name('categoriedepenses.edit');
        Route::put('/categoriedepenses/{categoriedepense}', 'update')->name('categoriedepenses.update');
        Route::delete('/categoriedepenses/{categoriedepense}', 'destroy')->name('categoriedepenses.destroy');
    });

    // Groupe de routes pour les Dépenses & Charges
    Route::controller(DepenseController::class)->group(function () {
        Route::get('/depense/index', 'index')->name('depenses.index');
        Route::get('/create/depense', 'create')->name('depenses.create');
        Route::post('/create/depense', 'store')->name('depenses.store');
        Route::get('/depenses/{depense}', 'show')->name('depenses.show');
        Route::get('/depenses/{depense}/edit', 'edit')->name('depenses.edit');
        Route::put('/depenses/{depense}', 'update')->name('depenses.update');
        Route::delete('/depenses/{depense}', 'destroy')->name('depenses.destroy');
    });

    // Groupe de routes pour les Recettes
    Route::controller(RecetteController::class)->group(function () {
        Route::get('/recette/index', 'index')->name('recettes.index');
        Route::get('/create/recette', 'create')->name('recettes.create');
        Route::post('/create/recette', 'store')->name('recettes.store');
        Route::get('/recettes/{recette}', 'show')->name('recettes.show');
        Route::get('/recettes/{recette}/edit', 'edit')->name('recettes.edit');
        Route::put('/recettes/{recette}', 'update')->name('recettes.update');
        Route::delete('/recettes/{recette}', 'destroy')->name('recettes.destroy');
    });

    // Groupe de routes pour le Journal d'Activité / Audit
    Route::controller(JournalActiviteController::class)->group(function () {
        Route::get('/journalactivite/index', 'index')->name('journalactivites.index');
        Route::get('/journalactivites/{journalactivite}', 'show')->name('journalactivites.show');
    });

});


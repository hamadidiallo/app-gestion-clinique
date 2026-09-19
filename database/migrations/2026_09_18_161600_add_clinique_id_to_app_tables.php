<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables recevant une colonne clinique_id.
     *
     * @var list<string>
     */
    protected array $tables = [
        'users',
        'patients',
        'services',
        'actes',
        'medecins',
        'tickets',
        'prestations',
        'caisses',
        'paiements',
        'dettes',
        'recettes',
        'depenses',
        'categorie_depenses',
        'assurances',
        'remunerations',
        'regle_partages',
        'consultations',
        'journal_activites',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && ! Schema::hasColumn($tableName, 'clinique_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('clinique_id')
                        ->nullable()
                        ->after('id')
                        ->constrained('cliniques')
                        ->nullOnDelete();

                    $table->index('clinique_id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'clinique_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['clinique_id']);
                    $table->dropColumn('clinique_id');
                });
            }
        }
    }
};

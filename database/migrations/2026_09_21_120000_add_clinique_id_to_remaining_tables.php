<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables restées hors du cloisonnement multi-clinique, avec la table parente
     * permettant de reconstituer leur clinique d'origine.
     *
     * @var array<string, array{0: string, 1: string}>
     */
    protected array $tables = [
        'ticket_details' => ['tickets', 'ticket_id'],
        'mouvement_caisses' => ['caisses', 'caisse_id'],
        'carte_assurances' => ['patients', 'patient_id'],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $tableName => [$parentTable, $foreignKey]) {
            if (! Schema::hasTable($tableName) || Schema::hasColumn($tableName, 'clinique_id')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('clinique_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('cliniques')
                    ->nullOnDelete();

                $table->index('clinique_id');
            });

            // Reprise des données existantes : chaque ligne hérite de la clinique de son parent
            if (Schema::hasTable($parentTable) && Schema::hasColumn($parentTable, 'clinique_id')) {
                DB::table($tableName)->update([
                    'clinique_id' => DB::raw(
                        "(select {$parentTable}.clinique_id from {$parentTable} where {$parentTable}.id = {$tableName}.{$foreignKey})"
                    ),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (array_keys($this->tables) as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'clinique_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['clinique_id']);
                    $table->dropColumn('clinique_id');
                });
            }
        }
    }
};

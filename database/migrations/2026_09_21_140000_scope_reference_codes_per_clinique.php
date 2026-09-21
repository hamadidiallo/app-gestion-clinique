<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Les catalogues de référence sont devenus propres à chaque clinique, mais leurs
     * codes restaient uniques sur toute la plateforme. Deux établissements ne pouvaient
     * donc pas posséder chacun un service « MED-GEN » ou une assurance « AMO », ce qui
     * bloquait l'amorçage de la deuxième clinique inscrite.
     *
     * L'unicité est déplacée sur le couple (clinique_id, code).
     *
     * @var array<string, list<string>>
     */
    protected array $contraintes = [
        'services' => ['code'],
        'actes' => ['code'],
        'assurances' => ['code'],
        'categorie_depenses' => ['code', 'nom'],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->contraintes as $table => $colonnes) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'clinique_id')) {
                continue;
            }

            foreach ($colonnes as $colonne) {
                Schema::table($table, function (Blueprint $blueprint) use ($table, $colonne) {
                    $blueprint->dropUnique("{$table}_{$colonne}_unique");
                    $blueprint->unique(['clinique_id', $colonne], "{$table}_clinique_{$colonne}_unique");
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->contraintes as $table => $colonnes) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'clinique_id')) {
                continue;
            }

            foreach ($colonnes as $colonne) {
                Schema::table($table, function (Blueprint $blueprint) use ($table, $colonne) {
                    $blueprint->dropUnique("{$table}_clinique_{$colonne}_unique");
                    $blueprint->unique($colonne, "{$table}_{$colonne}_unique");
                });
            }
        }
    }
};

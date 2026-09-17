<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute la colonne assurance_id à la table tickets si elle n'existe pas encore.
     */
    public function up(): void
    {
        if (Schema::hasTable('tickets') && ! Schema::hasColumn('tickets', 'assurance_id')) {
            Schema::table('tickets', function (Blueprint $table) {
                // Clé étrangère facultative liée à la table assurances
                $table->foreignId('assurance_id')->nullable()->after('patient_id')->constrained('assurances')->nullOnDelete();
            });
        }
    }

    /**
     * Annule la migration en supprimant la colonne assurance_id.
     */
    public function down(): void
    {
        if (Schema::hasTable('tickets') && Schema::hasColumn('tickets', 'assurance_id')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropForeign(['assurance_id']);
                $table->dropColumn('assurance_id');
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('prestations', function (Blueprint $table) {
            $table->decimal('taux_couverture', 5, 2)->default(0)->after('montant');
            $table->decimal('montant_assurance', 15, 2)->default(0)->after('taux_couverture');
            $table->decimal('montant_patient', 15, 2)->default(0)->after('montant_assurance');
            $table->decimal('pourcentage_medecin', 5, 2)->default(0)->after('montant_patient');
            $table->decimal('pourcentage_clinique', 5, 2)->default(0)->after('pourcentage_medecin');
            $table->decimal('part_medecin', 15, 2)->default(0)->after('pourcentage_clinique');
            $table->decimal('part_clinique', 15, 2)->default(0)->after('part_medecin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prestations', function (Blueprint $table) {
            $table->dropColumn([
                'taux_couverture',
                'montant_assurance',
                'montant_patient',
                'pourcentage_medecin',
                'pourcentage_clinique',
                'part_medecin',
                'part_clinique',
            ]);
        });
    }
};

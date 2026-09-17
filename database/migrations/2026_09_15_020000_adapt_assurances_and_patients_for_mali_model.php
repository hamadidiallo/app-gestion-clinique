<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ajouter taux_par_defaut sur la table assurances
        if (Schema::hasTable('assurances') && ! Schema::hasColumn('assurances', 'taux_par_defaut')) {
            Schema::table('assurances', function (Blueprint $table) {
                $table->decimal('taux_par_defaut', 5, 2)->default(80.00)->after('code');
            });
        }

        // 2. Ajouter les champs d'assurance directement sur la table patients
        if (Schema::hasTable('patients')) {
            Schema::table('patients', function (Blueprint $table) {
                if (! Schema::hasColumn('patients', 'assurance_id')) {
                    $table->foreignId('assurance_id')->nullable()->after('statut')->constrained('assurances')->nullOnDelete();
                }
                if (! Schema::hasColumn('patients', 'numero_assure')) {
                    $table->string('numero_assure', 100)->nullable()->after('assurance_id');
                }
                if (! Schema::hasColumn('patients', 'taux_couverture')) {
                    $table->decimal('taux_couverture', 5, 2)->nullable()->after('numero_assure');
                }
            });
        }

        // 3. Migration rétroactive des données de carte_assurances vers patients
        if (Schema::hasTable('carte_assurances')) {
            $cartes = DB::table('carte_assurances')->where('statut', true)->get();
            foreach ($cartes as $carte) {
                DB::table('patients')->where('id', $carte->patient_id)->update([
                    'assurance_id' => $carte->assurance_id,
                    'numero_assure' => $carte->reference,
                    'taux_couverture' => $carte->taux_couverture ?? 80.00,
                    'statut' => 'assure',
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('patients')) {
            Schema::table('patients', function (Blueprint $table) {
                if (Schema::hasColumn('patients', 'assurance_id')) {
                    $table->dropForeign(['assurance_id']);
                    $table->dropColumn(['assurance_id', 'numero_assure', 'taux_couverture']);
                }
            });
        }

        if (Schema::hasTable('assurances') && Schema::hasColumn('assurances', 'taux_par_defaut')) {
            Schema::table('assurances', function (Blueprint $table) {
                $table->dropColumn('taux_par_defaut');
            });
        }
    }
};

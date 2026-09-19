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
        // 1. Ajouter la référence aux rémunérations
        if (Schema::hasTable('remunerations') && ! Schema::hasColumn('remunerations', 'reference')) {
            Schema::table('remunerations', function (Blueprint $table) {
                $table->string('reference', 50)->nullable()->unique()->after('id');
            });

            // Rétro-remplissage des rémunérations existantes
            $remunerations = DB::table('remunerations')->get();
            foreach ($remunerations as $remun) {
                $dateStr = $remun->created_at ? date('Ymd', strtotime($remun->created_at)) : date('Ymd');
                $ref = 'REM-'.$dateStr.'-'.str_pad($remun->id, 4, '0', STR_PAD_LEFT);
                DB::table('remunerations')->where('id', $remun->id)->update(['reference' => $ref]);
            }
        }

        // 2. Ajouter la référence aux prestations
        if (Schema::hasTable('prestations') && ! Schema::hasColumn('prestations', 'reference')) {
            Schema::table('prestations', function (Blueprint $table) {
                $table->string('reference', 50)->nullable()->unique()->after('id');
            });

            // Rétro-remplissage des prestations existantes
            $prestations = DB::table('prestations')->get();
            foreach ($prestations as $prest) {
                $dateStr = $prest->date_prestation ? date('Ymd', strtotime($prest->date_prestation)) : ($prest->created_at ? date('Ymd', strtotime($prest->created_at)) : date('Ymd'));
                $ref = 'SOIN-'.$dateStr.'-'.str_pad($prest->id, 4, '0', STR_PAD_LEFT);
                DB::table('prestations')->where('id', $prest->id)->update(['reference' => $ref]);
            }
        }

        // 3. Ajouter le code métier aux médecins
        if (Schema::hasTable('medecins') && ! Schema::hasColumn('medecins', 'code')) {
            Schema::table('medecins', function (Blueprint $table) {
                $table->string('code', 50)->nullable()->unique()->after('id');
            });

            // Rétro-remplissage des médecins existants
            $medecins = DB::table('medecins')->get();
            foreach ($medecins as $med) {
                $nomClean = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $med->nom ?: 'MED'), 0, 4));
                $code = 'DR-'.$nomClean.'-'.str_pad($med->id, 3, '0', STR_PAD_LEFT);
                DB::table('medecins')->where('id', $med->id)->update(['code' => $code]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('remunerations') && Schema::hasColumn('remunerations', 'reference')) {
            Schema::table('remunerations', function (Blueprint $table) {
                $table->dropColumn('reference');
            });
        }

        if (Schema::hasTable('prestations') && Schema::hasColumn('prestations', 'reference')) {
            Schema::table('prestations', function (Blueprint $table) {
                $table->dropColumn('reference');
            });
        }

        if (Schema::hasTable('medecins') && Schema::hasColumn('medecins', 'code')) {
            Schema::table('medecins', function (Blueprint $table) {
                $table->dropColumn('code');
            });
        }
    }
};

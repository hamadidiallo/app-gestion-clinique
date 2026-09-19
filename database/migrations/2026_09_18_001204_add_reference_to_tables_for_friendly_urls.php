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
        // 1. Table Patients
        if (Schema::hasTable('patients') && ! Schema::hasColumn('patients', 'reference')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->string('reference', 50)->nullable()->unique()->after('id');
            });

            // Rétro-remplissage des références pour les patients existants
            $patients = DB::table('patients')->get(['id']);
            foreach ($patients as $patient) {
                DB::table('patients')->where('id', $patient->id)->update([
                    'reference' => 'PAT-'.str_pad((string) $patient->id, 5, '0', STR_PAD_LEFT),
                ]);
            }
        }

        // 2. Table Paiements
        if (Schema::hasTable('paiements') && ! Schema::hasColumn('paiements', 'reference')) {
            Schema::table('paiements', function (Blueprint $table) {
                $table->string('reference', 50)->nullable()->unique()->after('id');
            });

            $paiements = DB::table('paiements')->get(['id']);
            foreach ($paiements as $paiement) {
                DB::table('paiements')->where('id', $paiement->id)->update([
                    'reference' => 'PAY-'.str_pad((string) $paiement->id, 5, '0', STR_PAD_LEFT),
                ]);
            }
        }

        // 3. Table Caisses (Sessions)
        if (Schema::hasTable('caisses') && ! Schema::hasColumn('caisses', 'reference')) {
            Schema::table('caisses', function (Blueprint $table) {
                $table->string('reference', 50)->nullable()->unique()->after('id');
            });

            $caisses = DB::table('caisses')->get(['id']);
            foreach ($caisses as $caisse) {
                DB::table('caisses')->where('id', $caisse->id)->update([
                    'reference' => 'SES-'.str_pad((string) $caisse->id, 5, '0', STR_PAD_LEFT),
                ]);
            }
        }

        // 4. Table Dettes
        if (Schema::hasTable('dettes') && ! Schema::hasColumn('dettes', 'reference')) {
            Schema::table('dettes', function (Blueprint $table) {
                $table->string('reference', 50)->nullable()->unique()->after('id');
            });

            $dettes = DB::table('dettes')->get(['id']);
            foreach ($dettes as $dette) {
                DB::table('dettes')->where('id', $dette->id)->update([
                    'reference' => 'DET-'.str_pad((string) $dette->id, 5, '0', STR_PAD_LEFT),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('patients') && Schema::hasColumn('patients', 'reference')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->dropColumn('reference');
            });
        }

        if (Schema::hasTable('paiements') && Schema::hasColumn('paiements', 'reference')) {
            Schema::table('paiements', function (Blueprint $table) {
                $table->dropColumn('reference');
            });
        }

        if (Schema::hasTable('caisses') && Schema::hasColumn('caisses', 'reference')) {
            Schema::table('caisses', function (Blueprint $table) {
                $table->dropColumn('reference');
            });
        }

        if (Schema::hasTable('dettes') && Schema::hasColumn('dettes', 'reference')) {
            Schema::table('dettes', function (Blueprint $table) {
                $table->dropColumn('reference');
            });
        }
    }
};

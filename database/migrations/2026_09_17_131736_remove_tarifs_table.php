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
        if (Schema::hasTable('prestations')) {
            Schema::table('prestations', function (Blueprint $table) {
                if (Schema::hasColumn('prestations', 'tarif_id')) {
                    $table->dropForeign(['tarif_id']);
                    $table->dropColumn('tarif_id');
                }
                if (! Schema::hasColumn('prestations', 'acte_id')) {
                    $table->foreignId('acte_id')->nullable()->after('service_id')->constrained('actes')->nullOnDelete();
                }
            });
        }

        Schema::dropIfExists('tarifs');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('tarifs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->decimal('tarif_normal', 15, 2)->default(0);
            $table->decimal('tarif_amo', 15, 2)->nullable();
            $table->decimal('tarif_specifique', 15, 2)->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->boolean('statut')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        if (Schema::hasTable('prestations')) {
            Schema::table('prestations', function (Blueprint $table) {
                if (! Schema::hasColumn('prestations', 'tarif_id')) {
                    $table->foreignId('tarif_id')->nullable()->constrained('tarifs')->nullOnDelete();
                }
                if (Schema::hasColumn('prestations', 'acte_id')) {
                    $table->dropForeign(['acte_id']);
                    $table->dropColumn('acte_id');
                }
            });
        }
    }
};

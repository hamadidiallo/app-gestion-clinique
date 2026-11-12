<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Relie une fiche médecin au compte qui s'en sert pour se connecter, et à son service principal.
     *
     * - user_id : permet d'afficher au médecin connecté sa propre file d'attente.
     * - service_id : permet de lui proposer les tickets payés de son service qui n'ont pas encore de médecin.
     */
    public function up(): void
    {
        Schema::table('medecins', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->unique()->after('id')->constrained('users')->nullOnDelete();
            $table->foreignId('service_id')->nullable()->after('specialite')->constrained('services')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('medecins', function (Blueprint $table) {
            $table->dropConstrainedForeignId('service_id');
            $table->dropConstrainedForeignId('user_id');
        });
    }
};

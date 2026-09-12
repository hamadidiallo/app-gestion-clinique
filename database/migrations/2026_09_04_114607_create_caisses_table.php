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
        Schema::create('caisses', function (Blueprint $table) {
            $table->id();
            // Utilisateur responsable de la caisse
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            // Date d'ouverture
            $table->dateTime('date_ouverture');
            // Date de fermeture
            $table->dateTime('date_fermeture')->nullable();
            // Fonds disponible au début de la journée
            $table->decimal('fonds_initial', 15, 2)->default(0);
            // Total des entrées
            $table->decimal('total_entrees', 15, 2)->default(0);
            // Total des sorties
            $table->decimal('total_sorties', 15, 2)->default(0);
            // Solde théorique
            $table->decimal('solde_theorique', 15, 2)->default(0);
            // Montant réellement compté lors de la fermeture
            $table->decimal('solde_physique', 15, 2)->nullable();
            // Différence entre théorique et physique
            $table->decimal('ecart', 15, 2)->nullable();
            // État de la caisse
            $table->enum('statut', [
                'ouverte',
                'fermee'
            ])->default('ouverte');
            $table->text('observation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caisses');
    }
};

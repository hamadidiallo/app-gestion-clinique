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
        Schema::create('remunerations', function (Blueprint $table) {
            $table->id();
            // Médecin concerné
            $table->foreignId('medecin_id')
                ->constrained('medecins')
                ->restrictOnDelete();

            // Utilisateur ayant enregistré la rémunération
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();
            // Type de rémunération
            $table->enum('type_remuneration', [
                'salaire_fixe',
                'pourcentage'
            ]);

            // Période concernée
            $table->date('periode_debut');
            $table->date('periode_fin');
            // Salaire fixe éventuel
            $table->decimal('salaire_fixe', 15, 2)->nullable();

            // Pourcentage appliqué
            $table->decimal('pourcentage', 5, 2)->nullable();

            // Montant total des prestations concernées
            $table->decimal('montant_base', 15, 2)->default(0);

            // Part du médecin
            $table->decimal('montant_medecin', 15, 2)->default(0);
            // Part de la clinique
            $table->decimal('montant_clinique', 15, 2)->default(0);

            // Statut du paiement
            $table->enum('statut', [
                'calculee',
                'payee',
                'annulee'
            ])->default('calculee');

            $table->date('date_paiement')->nullable();

            $table->text('description')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remunerations');
    }
};

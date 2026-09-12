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
        Schema::create('recettes', function (Blueprint $table) {
            $table->id();
            // Origine de la recette
            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->restrictOnDelete();
            // Paiement à l'origine de la recette
            $table->foreignId('paiement_id')->nullable()->constrained('paiements')->restrictOnDelete();
            // Utilisateur ayant enregistré l'opération
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            // Mode de paiement
            $table->foreignId('mode_paiement_id')->constrained('mode_paiements')->restrictOnDelete();
            // Informations financières
            $table->decimal('montant', 15, 2);
            $table->date('date_recette');
            $table->string('reference')->unique();
            $table->text('description')->nullable();
            // 1 = recette valide, 0 = annulée
            $table->boolean('statut')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recettes');
    }
};

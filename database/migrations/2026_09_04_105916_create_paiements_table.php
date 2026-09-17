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
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            // Ticket concerné
            $table->foreignId('ticket_id')->constrained('tickets')->restrictOnDelete();
            // Utilisateur/cashier qui enregistre le paiement
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            // Assurance concernée si le payeur est une assurance
            $table->foreignId('assurance_id')->nullable()->constrained('assurances')->restrictOnDelete();
            // Patient ou assurance
            $table->enum('type_payeur', [
                'patient',
                'assurance',
            ])->default('patient');
            // Montant donné/reçu
            $table->decimal('montant_recu', 15, 2);
            // Montant réellement affecté au ticket
            $table->decimal('montant_impute', 15, 2);
            // Monnaie rendue au payeur
            $table->decimal('montant_rendu', 15, 2)->default(0);
            $table->timestamps();
            // Clé étrangère du mode de paiement
            $table->foreignId('mode_paiement_id')->constrained('mode_paiements')->restrictOnDelete();
            // Date et heure
            $table->dateTime('date_paiement');
            // État du paiement
            $table->enum('statut', [
                'valide',
                'annule',
            ])->default('valide');
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};

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
        Schema::create('dettes', function (Blueprint $table) {
            $table->id();
            // Ticket à l'origine de la dette
            $table->foreignId('ticket_id')->constrained('tickets')->restrictOnDelete();
            // Patient concerné
            $table->foreignId('patient_id')->constrained('patients')->restrictOnDelete();
            // Utilisateur ayant enregistré la dette
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            // Montant initial de la dette
            $table->decimal('montant_initial', 15, 2);
            // Total déjà payé
            $table->decimal('montant_paye', 15, 2)->default(0);
            // Montant restant
            $table->decimal('reste_a_payer', 15, 2);
            // État de la dette
            $table->enum('statut', [
                'en_cours',
                'partiellement_reglee',
                'reglee',
                'annulee',
            ])->default('en_cours');
            $table->date('date_creation');
            $table->date('date_reglement')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dettes');
    }
};

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
        Schema::create('depenses', function (Blueprint $table) {
            $table->id();
            // Catégorie de la dépense
            $table->foreignId('categorie_depense_id')->constrained('categorie_depenses')->restrictOnDelete();
            // Mode de paiement
            $table->foreignId('mode_paiement_id')->constrained('mode_paiements')->restrictOnDelete();
            // Utilisateur ayant enregistré la dépense
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            // Montant
            $table->decimal('montant', 15, 2);
            // Date
            $table->date('date_depense');
            // Bénéficiaire de la dépense
            $table->string('beneficiaire')->nullable();
            // Référence
            $table->string('reference')->unique();
            // Description
            $table->text('description')->nullable();
            // 1 = valide, 0 = annulée
            $table->boolean('statut')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depenses');
    }
};

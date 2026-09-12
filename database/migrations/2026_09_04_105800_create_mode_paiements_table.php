<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécute les migrations pour créer la table mode_paiements.
     */
    public function up(): void
    {
        Schema::create('mode_paiements', function (Blueprint $table) {
            $table->id();
            // Nom unique du mode de paiement (ex: Espèces, Carte Bancaire, Chèque, Mobile Money)
            $table->string('nom')->unique();
            // Code identifiant unique du mode de paiement (ex: CASH, CB, CHEQUE, MM)
            $table->string('code')->unique();
            // Description optionnelle du mode de paiement
            $table->text('description')->nullable();
            // Statut d'activation du mode de paiement (1 => Actif, 0 => Inactif)
            $table->boolean('statut')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Annule la migration en supprimant la table mode_paiements.
     */
    public function down(): void
    {
        Schema::dropIfExists('mode_paiements');
    }
};

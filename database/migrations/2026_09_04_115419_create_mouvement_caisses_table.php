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
        Schema::create('mouvement_caisses', function (Blueprint $table) {
            $table->id();
            // Caisse concernée
            $table->foreignId('caisse_id')
                ->constrained('caisses')
                ->restrictOnDelete();

            // Utilisateur ayant effectué l'opération
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Type du mouvement
            $table->enum('type', [
                'entree',
                'sortie',
            ]);
            // Origine du mouvement
            $table->enum('origine', [
                'paiement',
                'recette',
                'depense',
                'dette',
                'autre',
            ]);
            // Référence de l'opération
            $table->string('reference')->nullable();
            // Montant
            $table->decimal('montant', 15, 2);
            // Date et heure
            $table->dateTime('date_mouvement');
            $table->text('description')->nullable();
            // Mouvement valide ou annulé
            $table->boolean('statut')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mouvement_caisses');
    }
};

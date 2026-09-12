<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécute les migrations pour ajouter les index de performance MySQL.
     */
    public function up(): void
    {
        // Indexation de la table des tickets
        Schema::table('tickets', function (Blueprint $table) {
            $table->index('date_ticket');
            $table->index('statut');
        });

        // Indexation de la table des paiements
        Schema::table('paiements', function (Blueprint $table) {
            $table->index('date_paiement');
            $table->index('statut');
        });

        // Indexation de la table des caisses
        Schema::table('caisses', function (Blueprint $table) {
            $table->index('date_ouverture');
            $table->index('statut');
        });

        // Indexation de la table des recettes
        Schema::table('recettes', function (Blueprint $table) {
            $table->index('date_recette');
            $table->index('statut');
        });

        // Indexation de la table des dépenses
        Schema::table('depenses', function (Blueprint $table) {
            $table->index('date_depense');
            $table->index('statut');
        });

        // Indexation de la table des mouvements de caisse
        Schema::table('mouvement_caisses', function (Blueprint $table) {
            $table->index('date_mouvement');
            $table->index('type');
        });

        // Indexation de la table des dettes
        Schema::table('dettes', function (Blueprint $table) {
            $table->index('date_creation');
            $table->index('statut');
        });
    }

    /**
     * Annule les migrations d'indexation.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['date_ticket']);
            $table->dropIndex(['statut']);
        });

        Schema::table('paiements', function (Blueprint $table) {
            $table->dropIndex(['date_paiement']);
            $table->dropIndex(['statut']);
        });

        Schema::table('caisses', function (Blueprint $table) {
            $table->dropIndex(['date_ouverture']);
            $table->dropIndex(['statut']);
        });

        Schema::table('recettes', function (Blueprint $table) {
            $table->dropIndex(['date_recette']);
            $table->dropIndex(['statut']);
        });

        Schema::table('depenses', function (Blueprint $table) {
            $table->dropIndex(['date_depense']);
            $table->dropIndex(['statut']);
        });

        Schema::table('mouvement_caisses', function (Blueprint $table) {
            $table->dropIndex(['date_mouvement']);
            $table->dropIndex(['type']);
        });

        Schema::table('dettes', function (Blueprint $table) {
            $table->dropIndex(['date_creation']);
            $table->dropIndex(['statut']);
        });
    }
};

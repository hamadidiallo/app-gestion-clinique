<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécute les migrations pour créer la table des utilisateurs.
     */
    public function up(): void
    {
        // Création de la table des utilisateurs "users"
        Schema::create('users', function (Blueprint $table) {
            // Clé primaire auto-incrémentée (id)
            $table->id();

            // Champ pour le prénom de l'utilisateur
            $table->string('prenom');

            // Champ pour le nom de famille de l'utilisateur
            $table->string('nom');

            // Champ email unique pour chaque utilisateur
            $table->string('email')->unique();

            // Date de vérification de l'adresse email (facultatif)
            $table->timestamp('email_verified_at')->nullable();

            // Mot de passe haché de l'utilisateur
            $table->string('password');

            // Jeton pour la fonctionnalité "se souvenir de moi"
            $table->rememberToken();

            // Horodatages automatiques created_at et updated_at
            $table->timestamps();

            // Clé étrangère liée à la table roles (empêche la suppression d'un rôle attribué)
            $table->foreignId('role_id')->constrained('roles')->restrictOnDelete();
        });

        // Table pour la réinitialisation des mots de passe
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Table pour la gestion des sessions utilisateur
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Annule les migrations en supprimant les tables créées.
     */
    public function down(): void
    {
        // Suppression de la table des utilisateurs
        Schema::dropIfExists('users');

        // Suppression de la table des jetons de réinitialisation de mot de passe
        Schema::dropIfExists('password_reset_tokens');

        // Suppression de la table des sessions
        Schema::dropIfExists('sessions');
    }
};

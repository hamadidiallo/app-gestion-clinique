<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Paramètres mesurés par un examen de laboratoire (ex. NFS : hémoglobine, leucocytes…).
     *
     * - numerique : valeur chiffrée, hors norme si elle sort de [valeur_min, valeur_max]
     * - texte : résultat qualitatif (ex. « Négatif »), hors norme s'il diffère de valeur_normale_texte
     *
     * Les bornes « homme », « femme » et « enfant » sont facultatives : quand elles sont
     * renseignées, elles remplacent les bornes générales pour le patient concerné.
     */
    public function up(): void
    {
        Schema::create('acte_parametres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinique_id')->nullable()->constrained('cliniques')->cascadeOnDelete();
            $table->foreignId('acte_id')->constrained('actes')->cascadeOnDelete();
            $table->string('nom');
            $table->string('unite', 30)->nullable();
            $table->string('type', 20)->default('numerique');
            $table->decimal('valeur_min', 12, 3)->nullable();
            $table->decimal('valeur_max', 12, 3)->nullable();
            $table->decimal('valeur_min_homme', 12, 3)->nullable();
            $table->decimal('valeur_max_homme', 12, 3)->nullable();
            $table->decimal('valeur_min_femme', 12, 3)->nullable();
            $table->decimal('valeur_max_femme', 12, 3)->nullable();
            $table->decimal('valeur_min_enfant', 12, 3)->nullable();
            $table->decimal('valeur_max_enfant', 12, 3)->nullable();
            $table->string('valeur_normale_texte')->nullable();
            $table->unsignedSmallInteger('ordre')->default(0);
            $table->timestamps();

            $table->index(['acte_id', 'ordre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acte_parametres');
    }
};

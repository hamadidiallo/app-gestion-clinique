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
        Schema::create('employes', function (Blueprint $table) {
            $table->id();
            $table->string('prenom');
            $table->string('nom');

            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->string('fonction');

            $table->enum('type_remuneration', [
                'salaire_fixe',
                'pourcentage',
            ])->default('salaire_fixe');
            $table->decimal('salaire_fixe', 15, 2)->nullable();
            $table->decimal('pourcentage', 5, 2)->nullable();
            $table->date('date_embauche')->nullable();
            $table->boolean('statut')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employes');
    }
};

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
        Schema::create('cliniques', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('slug')->unique();
            $table->string('code', 10)->unique()->nullable();
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            $table->string('adresse')->nullable();
            $table->string('ville')->default('Bamako');
            $table->string('pays')->default('Mali');
            $table->string('devise')->default('FCFA');
            $table->string('logo')->nullable();
            $table->string('plan')->default('standard'); // standard, pro, enterprise
            $table->string('statut')->default('actif');   // actif, suspendu, essai
            $table->date('date_expiration')->nullable();
            $table->string('prefixe_ticket', 10)->default('TCK');
            $table->string('prefixe_patient', 10)->default('PAT');
            $table->timestamps();

            $table->index('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cliniques');
    }
};

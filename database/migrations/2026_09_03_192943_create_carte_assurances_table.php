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
        Schema::create('carte_assurances', function (Blueprint $table) {
            $table->id();
            // Clé etrangère patient
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            // Clé etrangère assurance
            $table->foreignId('assurance_id')->constrained('assurances')->restrictOnDelete();
            $table->string('reference')->unique();
            $table->decimal('taux_couverture', 5, 2);
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->boolean('statut')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carte_assurances');
    }
};

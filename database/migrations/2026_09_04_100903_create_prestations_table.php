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
        Schema::create('prestations', function (Blueprint $table) {
            $table->id();
            // CLE ETRANGERE PATIENT
            $table->foreignId('patient_id')->constrained('patients')->restrictOnDelete();
            // CLE ETRANGERE SERVICE
            $table->foreignId('service_id')->constrained('services')->restrictOnDelete();
            // CLE ETRANGERE MEDECIN
            $table->foreignId('medecin_id') ->nullable()->constrained('medecins')->nullOnDelete();
            // CLE ETRANGERE TARIF
            $table->foreignId('tarif_id')->constrained('tarifs')->restrictOnDelete();
            $table->string('type')->nullable();
            $table->decimal('montant', 15, 2);
            $table->dateTime('date_prestation');
            $table->text('description')->nullable();
            $table->boolean('statut')->default(true);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestations');
    }
};

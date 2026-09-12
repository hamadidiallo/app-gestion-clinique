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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            // CLE ETRANGERE PATIENT
            $table->foreignId('patient_id')->constrained('patients')->restrictOnDelete();
            // CLE ETRANGERE ASSURANCE (Facultative si Tiers Payant)
            $table->foreignId('assurance_id')->nullable()->constrained('assurances')->nullOnDelete();
            // CLE ETRANGERE USER
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('reference')->unique();
            $table->dateTime('date_ticket');
            $table->dateTime('date_expiration')->nullable();
            $table->decimal('montant_total', 15, 2)->default(0);
            $table->decimal('montant_assurance', 15, 2)->default(0);
            $table->decimal('montant_patient', 15, 2)->default(0);
            $table->decimal('montant_paye', 15, 2)->default(0);
            $table->decimal('reste_a_payer', 15, 2)->default(0);
            $table->enum('statut', [
                'en_attente',
                'partiellement_paye',
                'paye',
                'annule'
            ])->default('en_attente');

            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};

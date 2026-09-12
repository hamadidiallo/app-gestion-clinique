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
        Schema::create('ticket_details', function (Blueprint $table) {
            $table->id();
            // CLE ETRANGERE TICKET
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            // CLE ETRANGERE PRESTATION
            $table->foreignId('prestation_id')->constrained('prestations')->restrictOnDelete();
            $table->decimal('quantite', 10, 2)->default(1);
            $table->decimal('prix_unitaire', 15, 2);
            $table->decimal('montant_total', 15, 2);
            $table->decimal('taux_couverture', 5, 2)->default(0);
            $table->decimal('montant_assurance', 15, 2)->default(0);
            $table->decimal('montant_patient', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_details');
    }
};

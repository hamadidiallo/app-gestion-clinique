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
        Schema::create('regles_partage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')
                ->constrained('services')
                ->restrictOnDelete();
            $table->decimal('pourcentage_medecin', 5, 2);
            $table->decimal('pourcentage_clinique', 5, 2);
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
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
        Schema::dropIfExists('regles_partage');
    }
};

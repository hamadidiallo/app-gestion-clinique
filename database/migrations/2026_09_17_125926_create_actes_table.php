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
        Schema::create('actes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('nom');
            $table->string('categorie')->default('consultation');
            $table->decimal('tarif_normal', 15, 2)->default(0);
            $table->decimal('tarif_amo', 15, 2)->nullable();
            $table->decimal('tarif_specifique', 15, 2)->nullable();
            $table->decimal('part_medecin_pourcentage', 5, 2)->default(50.00);
            $table->decimal('part_clinique_pourcentage', 5, 2)->default(50.00);
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
        Schema::dropIfExists('actes');
    }
};

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
        // 1. Table des dossiers médicaux permanents des patients
        Schema::create('dossiers_medicaux', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->unique()->constrained('patients')->cascadeOnDelete();
            $table->string('numero_dossier')->unique();
            $table->string('groupe_sanguin', 10)->nullable(); // A+, A-, B+, B-, AB+, AB-, O+, O-
            $table->text('allergies')->nullable();
            $table->text('antecedents_personnels')->nullable(); // HTA, Diabète, Drépanocytose, etc.
            $table->text('antecedents_familiaux')->nullable();
            $table->text('antecedents_chirurgicaux')->nullable();
            $table->text('notes_particulieres')->nullable();
            $table->timestamps();
        });

        // 2. Table des consultations médicales et prises de constantes
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('medecin_id')->nullable()->constrained('medecins')->nullOnDelete();
            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('reference')->unique(); // CS-YYYYMMDD-XXXX
            $table->dateTime('date_consultation');

            // Constantes vitales (Infirmerie / Prise en charge)
            $table->string('tension_arterielle', 20)->nullable(); // ex: 12/8 ou 120/80
            $table->decimal('temperature', 4, 1)->nullable(); // ex: 37.5
            $table->decimal('poids', 5, 2)->nullable(); // ex: 72.50 kg
            $table->integer('taille')->nullable(); // en cm
            $table->integer('pouls')->nullable(); // bpm
            $table->integer('frequence_respiratoire')->nullable(); // cpm
            $table->decimal('glycemie', 4, 2)->nullable(); // g/L
            $table->integer('saturation_oxygene')->nullable(); // SpO2 %

            // Partie Médicale / Observation clinique
            $table->string('motif_consultation');
            $table->text('histoire_maladie')->nullable();
            $table->text('examen_physique')->nullable();
            $table->text('diagnostic')->nullable();
            $table->text('conduite_a_tenir')->nullable(); // Conseils, repos, examens demandés

            $table->string('statut')->default('terminee'); // en_attente, en_cours, terminee, annulee
            $table->timestamps();
        });

        // 3. Table des ordonnances médicales
        Schema::create('ordonnances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_id')->constrained('consultations')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('medecin_id')->nullable()->constrained('medecins')->nullOnDelete();
            $table->string('reference')->unique(); // ORD-YYYYMMDD-XXXX
            $table->date('date_ordonnance');
            $table->text('instructions_generales')->nullable();
            $table->timestamps();
        });

        // 4. Table des lignes de prescription de l'ordonnance
        Schema::create('ordonnance_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ordonnance_id')->constrained('ordonnances')->cascadeOnDelete();
            $table->string('medicament');
            $table->string('forme')->nullable(); // Comprimé, Sirop, Injectable, Gélule, etc.
            $table->string('dosage')->nullable(); // 500mg, 1g, etc.
            $table->string('posologie'); // ex: 1 comprimé 3 fois par jour
            $table->string('duree')->nullable(); // ex: 5 jours, 10 jours
            $table->text('instructions')->nullable(); // ex: À prendre après les repas
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordonnance_lignes');
        Schema::dropIfExists('ordonnances');
        Schema::dropIfExists('consultations');
        Schema::dropIfExists('dossiers_medicaux');
    }
};

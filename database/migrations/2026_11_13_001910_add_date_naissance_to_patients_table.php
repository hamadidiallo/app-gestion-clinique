<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Date de naissance du patient (facultative) : sert à appliquer les valeurs normales
     * « enfant » des examens de laboratoire et à afficher l'âge.
     */
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->date('date_naissance')->nullable()->after('sexe');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('date_naissance');
        });
    }
};

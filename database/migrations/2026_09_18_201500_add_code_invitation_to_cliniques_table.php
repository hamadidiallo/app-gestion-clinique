<?php

use App\Models\Clinique;
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
        Schema::table('cliniques', function (Blueprint $table) {
            $table->string('code_invitation', 30)->nullable()->unique()->after('code');
        });

        // Générer un code d'invitation pour les cliniques existantes
        foreach (Clinique::all() as $clinique) {
            $prefix = strtoupper($clinique->code ?? substr(preg_replace('/[^A-Za-z0-9]/', '', $clinique->nom), 0, 3));
            $clinique->update([
                'code_invitation' => $prefix.'-'.rand(1000, 9999),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cliniques', function (Blueprint $table) {
            $table->dropColumn('code_invitation');
        });
    }
};

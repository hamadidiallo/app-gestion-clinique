<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * L'email et le téléphone deviennent deux identifiants au choix.
     *
     * Tout le personnel d'une clinique ne dispose pas d'une adresse
     * professionnelle : imposer l'email empêchait de créer le compte d'un agent
     * qui n'a qu'un numéro. La colonne devient donc nullable, l'unicité étant
     * conservée (MySQL et SQLite autorisent plusieurs NULL sur un index unique).
     *
     * La validation impose qu'au moins l'un des deux soit renseigné.
     */
    public function up(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'email')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('email')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'email')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('email')->nullable(false)->change();
            });
        }
    }
};

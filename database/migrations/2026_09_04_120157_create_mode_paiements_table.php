<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Migration obsolète remplacée par 2026_09_04_105800_create_mode_paiements_table.php
     * pour garantir que la table mode_paiements existe avant la création des clés étrangères dans paiements.
     */
    public function up(): void
    {
        //
    }

    public function down(): void
    {
        //
    }
};

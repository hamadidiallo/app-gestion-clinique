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
        if (Schema::hasTable('regles_partage') && ! Schema::hasColumn('regles_partage', 'clinique_id')) {
            Schema::table('regles_partage', function (Blueprint $table) {
                $table->foreignId('clinique_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('cliniques')
                    ->nullOnDelete();

                $table->index('clinique_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('regles_partage') && Schema::hasColumn('regles_partage', 'clinique_id')) {
            Schema::table('regles_partage', function (Blueprint $table) {
                $table->dropForeign(['clinique_id']);
                $table->dropColumn('clinique_id');
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cliniques', function (Blueprint $table) {
            if (! Schema::hasColumn('cliniques', 'type_etablissement')) {
                $table->string('type_etablissement', 50)->nullable()->after('nom');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cliniques', function (Blueprint $table) {
            if (Schema::hasColumn('cliniques', 'type_etablissement')) {
                $table->dropColumn('type_etablissement');
            }
        });
    }
};

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
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->after('assurance_id')->constrained('services')->nullOnDelete();
            $table->foreignId('medecin_id')->nullable()->after('service_id')->constrained('medecins')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropForeign(['medecin_id']);
            $table->dropColumn(['service_id', 'medecin_id']);
        });
    }
};

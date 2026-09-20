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
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinique_id')->constrained('cliniques')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->string('code', 30)->unique();
            $table->string('prenom', 100)->nullable();
            $table->string('nom', 100)->nullable();
            $table->string('email', 255)->nullable();
            $table->foreignId('cree_par_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('utilise_le')->nullable();
            $table->foreignId('utilise_par_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};

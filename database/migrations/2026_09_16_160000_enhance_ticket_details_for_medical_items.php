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
        Schema::table('ticket_details', function (Blueprint $table) {
            if (! Schema::hasColumn('ticket_details', 'designation')) {
                $table->string('designation')->nullable()->after('ticket_id');
            }
            if (! Schema::hasColumn('ticket_details', 'type_item')) {
                $table->string('type_item', 50)->default('acte')->after('designation'); // 'acte', 'medicament', 'hospitalisation'
            }
            if (Schema::hasColumn('ticket_details', 'prestation_id')) {
                $table->unsignedBigInteger('prestation_id')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_details', function (Blueprint $table) {
            if (Schema::hasColumn('ticket_details', 'designation')) {
                $table->dropColumn('designation');
            }
            if (Schema::hasColumn('ticket_details', 'type_item')) {
                $table->dropColumn('type_item');
            }
        });
    }
};

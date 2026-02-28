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
        Schema::table('commande_taches', function (Blueprint $table) {
            $table->string('dents')->nullable()->after('nb_elem');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commande_taches', function (Blueprint $table) {
            $table->dropColumn('dents');
        });
    }
};

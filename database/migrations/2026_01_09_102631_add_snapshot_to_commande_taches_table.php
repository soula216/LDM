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
            $table->decimal('prix_unitaire_ttc_snapshot', 10, 2)->default(0)->after('nb_elem');
            $table->decimal('total_ligne_ttc', 10, 2)->default(0)->after('prix_unitaire_ttc_snapshot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commande_taches', function (Blueprint $table) {
            $table->dropColumn(['prix_unitaire_ttc_snapshot', 'total_ligne_ttc']);
        });
    }
};

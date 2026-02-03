<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modifier la colonne mode_reglement en ENUM MySQL
        DB::statement("ALTER TABLE echeances MODIFY COLUMN mode_reglement ENUM('especes', 'virement_bancaire', 'lettre_change') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir à VARCHAR
        Schema::table('echeances', function (Blueprint $table) {
            $table->string('mode_reglement')->change();
        });
    }
};

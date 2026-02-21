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
        // Ajouter 'cheque' à l'ENUM mode_reglement
        DB::statement("ALTER TABLE echeances MODIFY COLUMN mode_reglement ENUM('especes', 'virement_bancaire', 'cheque', 'lettre_change') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Retirer 'cheque' de l'ENUM mode_reglement
        DB::statement("ALTER TABLE echeances MODIFY COLUMN mode_reglement ENUM('especes', 'virement_bancaire', 'lettre_change') NOT NULL");
    }
};

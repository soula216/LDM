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
        // Convertir les valeurs existantes vers les valeurs enum
        DB::table('echeances')
            ->where('mode_reglement', 'Espèces')
            ->orWhere('mode_reglement', 'especes')
            ->update(['mode_reglement' => 'especes']);

        DB::table('echeances')
            ->where('mode_reglement', 'Virement bancaire')
            ->orWhere('mode_reglement', 'virement_bancaire')
            ->update(['mode_reglement' => 'virement_bancaire']);

        DB::table('echeances')
            ->where('mode_reglement', 'Lettre de change (كمبيالة)')
            ->orWhere('mode_reglement', 'lettre de change')
            ->orWhere('mode_reglement', 'Lettre de change')
            ->orWhere('mode_reglement', 'lettre de change (كمبيالة)')
            ->orWhere('mode_reglement', 'lettre_change')
            ->update(['mode_reglement' => 'lettre_change']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir aux valeurs affichées (optionnel)
        DB::table('echeances')
            ->where('mode_reglement', 'especes')
            ->update(['mode_reglement' => 'Espèces']);

        DB::table('echeances')
            ->where('mode_reglement', 'virement_bancaire')
            ->update(['mode_reglement' => 'Virement bancaire']);

        DB::table('echeances')
            ->where('mode_reglement', 'lettre_change')
            ->update(['mode_reglement' => 'Lettre de change (كمبيالة)']);
    }
};

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
        // Changer le type de colonne de ENUM à VARCHAR pour permettre la nouvelle valeur
        Schema::table('echeances', function (Blueprint $table) {
            $table->string('mode_reglement')->change();
        });

        // Mettre à jour les échéances existantes avec "lettre de change" vers "Lettre de change (كمبيالة)"
        DB::table('echeances')
            ->where('mode_reglement', 'lettre de change')
            ->orWhere('mode_reglement', 'Lettre de change')
            ->orWhere('mode_reglement', 'lettre de change (كمبيالة)')
            ->update(['mode_reglement' => 'Lettre de change (كمبيالة)']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir à "lettre de change" (sans majuscule et sans arabe)
        DB::table('echeances')
            ->where('mode_reglement', 'Lettre de change (كمبيالة)')
            ->update(['mode_reglement' => 'lettre de change']);

        // Remettre le type ENUM (optionnel, car cela peut causer des problèmes)
        // Schema::table('echeances', function (Blueprint $table) {
        //     $table->enum('mode_reglement', ['Espèces', 'Virement bancaire', 'lettre de change'])->change();
        // });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commande_taches', function (Blueprint $table) {
            $table->unsignedInteger('calendar_sort_order')->nullable()->after('date_livraison');
        });
    }

    public function down(): void
    {
        Schema::table('commande_taches', function (Blueprint $table) {
            $table->dropColumn('calendar_sort_order');
        });
    }
};

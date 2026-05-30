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
            $table->dropForeign(['service_id']);
        });

        Schema::table('commande_taches', function (Blueprint $table) {
            $table->unsignedBigInteger('service_id')->nullable()->change();
            $table->string('custom_service')->nullable()->after('service_id');
            $table->foreign('service_id')->references('id')->on('services')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commande_taches', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropColumn('custom_service');
        });

        Schema::table('commande_taches', function (Blueprint $table) {
            $table->unsignedBigInteger('service_id')->nullable(false)->change();
            $table->foreign('service_id')->references('id')->on('services');
        });
    }
};

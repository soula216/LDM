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
        Schema::table('critere_quality', function (Blueprint $table) {
            $table->foreignId('groupe_id')->nullable()->after('description')->constrained('groupes')->onDelete('set null');
            $table->string('type')->nullable()->after('groupe_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('critere_quality', function (Blueprint $table) {
            $table->dropForeign(['groupe_id']);
            $table->dropColumn(['groupe_id', 'type']);
        });
    }
};

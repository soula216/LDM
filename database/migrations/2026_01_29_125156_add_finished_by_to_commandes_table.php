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
        Schema::table('commandes', function (Blueprint $table) {
            $table->foreignId('finished_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            $table->index('finished_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropForeign(['finished_by']);
            $table->dropIndex(['finished_by']);
            $table->dropColumn('finished_by');
        });
    }
};

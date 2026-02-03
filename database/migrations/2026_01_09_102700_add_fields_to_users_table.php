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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nom')->nullable()->after('name');
            $table->string('prénom')->nullable()->after('nom');
            $table->string('gouvernorat')->nullable()->after('prénom');
            $table->string('ville')->nullable()->after('gouvernorat');
            $table->text('adresse')->nullable()->after('ville');
            $table->string('tél')->nullable()->after('adresse');
            $table->string('num_ordinaire')->nullable()->after('tél');
            $table->foreignId('groupe_id')->nullable()->constrained('groupes')->nullOnDelete()->after('num_ordinaire');
            $table->softDeletes()->after('updated_at');
            
            $table->index('groupe_id');
            $table->index('email');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['groupe_id']);
            $table->dropIndex(['groupe_id']);
            $table->dropIndex(['email']);
            $table->dropIndex(['deleted_at']);
            $table->dropColumn(['nom', 'prénom', 'gouvernorat', 'ville', 'adresse', 'tél', 'num_ordinaire', 'groupe_id', 'deleted_at']);
        });
    }
};

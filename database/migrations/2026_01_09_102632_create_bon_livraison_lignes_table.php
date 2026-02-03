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
        Schema::create('bon_livraison_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bon_livraison_id')->constrained('bons_livraison')->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->string('service_name_snapshot');
            $table->decimal('prix_unitaire_ttc_snapshot', 10, 2)->notNullable();
            $table->unsignedInteger('quantite')->default(1);
            $table->decimal('total_ligne_ttc', 10, 2)->notNullable();
            $table->timestamps();
            
            $table->index('bon_livraison_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bon_livraison_lignes');
    }
};

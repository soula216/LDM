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
        Schema::create('commande_taches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained('commandes')->cascadeOnDelete();
            $table->foreignId('groupe_id')->constrained('groupes');
            $table->foreignId('service_id')->constrained('services');
            $table->unsignedInteger('nb_elem');
            $table->string('teinte')->nullable();
            $table->date('date_livraison');
            $table->timestamps();
            
            $table->index('commande_id');
            $table->index('groupe_id');
            $table->index('service_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commande_taches');
    }
};

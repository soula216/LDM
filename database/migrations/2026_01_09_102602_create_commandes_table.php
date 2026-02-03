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
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dentiste_id')->constrained('users');
            $table->string('num_cmd')->unique();
            $table->string('nom_patient');
            $table->boolean('urgent')->default(false);
            $table->enum('status', ['Reçue', 'En cours', 'Terminée', 'Livrée'])->default('Reçue');
            $table->longText('commentaire')->nullable();
            $table->timestamps();
            
            $table->index('dentiste_id');
            $table->index('status');
            $table->index('num_cmd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};

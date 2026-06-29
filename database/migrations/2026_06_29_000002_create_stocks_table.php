<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('element_id')->constrained('elements')->cascadeOnDelete();
            $table->unsignedInteger('qte');
            $table->timestamps();

            $table->unique('element_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};

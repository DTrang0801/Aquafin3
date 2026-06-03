<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBestellingMaterialenTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bestelling_materialen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bestelling_id')->constrained('bestellingen')->cascadeOnDelete();
            $table->foreignId('materiaal_id')->constrained('materialen')->restrictOnDelete();
            $table->unsignedInteger('aantal')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bestelling_materialen');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMateriaalSubcategorieenTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('materiaal_subcategorieen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materiaal_categorie_id')->constrained('materiaal_categorieen')->cascadeOnDelete();
            $table->string('naam');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materiaal_subcategorieen');
    }
}

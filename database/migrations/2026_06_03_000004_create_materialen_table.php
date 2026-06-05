<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialenTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('materialen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materiaal_subcategorie_id')->nullable()->constrained('materiaal_subcategorieen');
            $table->string('naam');
            $table->text('beschrijving')->nullable();
           // $table->boolean('belangrijk')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materialen');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMandjeMaterialenTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mandje_materialen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mandje_id')->constrained('mandjes')->cascadeOnDelete();
            $table->foreignId('materiaal_id')->constrained('materialen');
            $table->unsignedInteger('aantal')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mandje_materialen');
    }
}

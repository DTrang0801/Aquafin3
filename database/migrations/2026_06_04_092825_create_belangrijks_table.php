<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
    {
        Schema::create('belangrijkeItems', function (Blueprint $blueprint) {
            $blueprint->id();

            $blueprint->foreignId('materiaal_id')->constrained('materialen')->onDelete('cascade');
                        
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('belangrijkeItems');
    }
};

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
        Schema::table('materialen', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('materiaal_subcategorieen', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('materiaal_categorieen', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('bestellingen', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('bestellingen', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('materiaal_categorieen', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('materiaal_subcategorieen', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('materialen', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};

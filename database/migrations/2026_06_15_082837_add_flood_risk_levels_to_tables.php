<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add risk_level to belangrijkeItems so each critical material has a minimum trigger level
        Schema::table('belangrijkeItems', function (Blueprint $table) {
            $table->string('risk_level')->default('medium')->after('materiaal_id');
        });

        // Change materialen.belangrijk from boolean to nullable string storing the active risk level
        Schema::table('materialen', function (Blueprint $table) {
            $table->dropColumn('belangrijk');
        });

        Schema::table('materialen', function (Blueprint $table) {
            $table->string('belangrijk')->nullable()->default(null)->after('beschrijving');
        });
    }

    public function down(): void
    {
        Schema::table('materialen', function (Blueprint $table) {
            $table->dropColumn('belangrijk');
        });

        Schema::table('materialen', function (Blueprint $table) {
            $table->boolean('belangrijk')->default(false)->after('beschrijving');
        });

        Schema::table('belangrijkeItems', function (Blueprint $table) {
            $table->dropColumn('risk_level');
        });
    }
};

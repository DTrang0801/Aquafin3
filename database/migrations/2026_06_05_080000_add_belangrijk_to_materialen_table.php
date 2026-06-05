<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('materialen', 'belangrijk')) {
            return;
        }

        Schema::table('materialen', function (Blueprint $table) {
            $table->boolean('belangrijk')->default(false)->after('beschrijving');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('materialen', 'belangrijk')) {
            return;
        }

        Schema::table('materialen', function (Blueprint $table) {
            $table->dropColumn('belangrijk');
        });
    }
};

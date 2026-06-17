<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First, set all non-NULL values to NULL to handle any stale data
        DB::table('materialen')
            ->whereNotNull('belangrijk')
            ->where('belangrijk', '!=', '')
            ->update(['belangrijk' => null]);

        // Ensure the column type is correct (nullable string)
        Schema::table('materialen', function (Blueprint $table) {
            $table->string('belangrijk')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('materialen', function (Blueprint $table) {
            $table->string('belangrijk')->nullable()->change();
        });
    }
};

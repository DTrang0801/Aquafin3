<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Clear all existing boolean 0/1 values to NULL so enum casting works
        DB::table('materialen')->update(['belangrijk' => null]);
    }

    public function down(): void
    {
        // No rollback needed
    }
};

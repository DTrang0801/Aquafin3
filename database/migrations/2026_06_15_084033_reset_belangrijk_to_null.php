<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

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

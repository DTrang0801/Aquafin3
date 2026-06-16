<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure all belangrijk values are NULL to prevent enum casting errors
        DB::table('materialen')->update(['belangrijk' => null]);
    }

    public function down(): void
    {
        // No rollback needed
    }
};

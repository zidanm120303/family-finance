<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('transactions')
            ->where('status', 'pending')
            ->update(['status' => 'cancel']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE transactions MODIFY status ENUM('success', 'cancel') NOT NULL DEFAULT 'success'"
            );
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE transactions MODIFY status ENUM('pending', 'success', 'cancel') NOT NULL DEFAULT 'pending'"
            );
        }
    }
};

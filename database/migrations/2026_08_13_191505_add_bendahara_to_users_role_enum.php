<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `users`
                MODIFY COLUMN `role` ENUM('admin', 'bendahara', 'guru', 'wali_murid')
                NOT NULL DEFAULT 'guru'");
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `users`
                MODIFY COLUMN `role` ENUM('admin', 'guru', 'wali_murid')
                NOT NULL DEFAULT 'guru'");
        }
    }
};

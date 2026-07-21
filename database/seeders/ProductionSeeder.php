<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Seeder untuk server produksi/hosting.
 * Hanya membuat 1 akun admin — TANPA data demo.
 *
 * Jalankan: php artisan db:seed --class=ProductionSeeder
 * Password bisa ditentukan lewat env SEED_ADMIN_PASSWORD,
 * jika tidak diisi akan digenerate acak dan ditampilkan sekali di console.
 */
class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        if (User::where('role', 'admin')->exists()) {
            $this->command->warn('Akun admin sudah ada — tidak ada yang dibuat.');

            return;
        }

        $password = env('SEED_ADMIN_PASSWORD') ?: Str::password(16);

        User::create([
            'name' => 'Admin SMA',
            'email' => 'admin@madani.id',
            'password' => Hash::make($password),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->command->info('Akun admin dibuat: admin@madani.id');

        if (! env('SEED_ADMIN_PASSWORD')) {
            $this->command->warn("Password (catat sekarang, tidak akan ditampilkan lagi): {$password}");
        }
    }
}

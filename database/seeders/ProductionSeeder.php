<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Seeder untuk server produksi/hosting.
 * Hanya membuat akun admin & bendahara — TANPA data demo.
 *
 * Jalankan: php artisan db:seed --class=ProductionSeeder
 * Password bisa ditentukan lewat env SEED_ADMIN_PASSWORD / SEED_BENDAHARA_PASSWORD,
 * jika tidak diisi akan digenerate acak dan ditampilkan sekali di console.
 */
class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        if (! User::where('role', 'admin')->exists()) {
            $adminPassword = env('SEED_ADMIN_PASSWORD') ?: Str::password(16);

            User::create([
                'name' => 'Admin SMA',
                'email' => 'admin@madani.id',
                'password' => Hash::make($adminPassword),
                'role' => 'admin',
                'is_active' => true,
            ]);

            $this->command->info('Akun admin dibuat: admin@madani.id');

            if (! env('SEED_ADMIN_PASSWORD')) {
                $this->command->warn("Password admin (catat sekarang, tidak akan ditampilkan lagi): {$adminPassword}");
            }
        } else {
            $this->command->warn('Akun admin sudah ada — tidak ada yang dibuat.');
        }

        if (! User::where('role', 'bendahara')->exists()) {
            $bendaharaPassword = env('SEED_BENDAHARA_PASSWORD') ?: Str::password(16);

            User::create([
                'name' => 'Bendahara SMA',
                'email' => 'bendahara@madani.id',
                'password' => Hash::make($bendaharaPassword),
                'role' => 'bendahara',
                'is_active' => true,
            ]);

            $this->command->info('Akun bendahara dibuat: bendahara@madani.id');

            if (! env('SEED_BENDAHARA_PASSWORD')) {
                $this->command->warn("Password bendahara (catat sekarang, tidak akan ditampilkan lagi): {$bendaharaPassword}");
            }
        } else {
            $this->command->warn('Akun bendahara sudah ada — tidak ada yang dibuat.');
        }
    }
}

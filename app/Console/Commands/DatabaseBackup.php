<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('db:backup')]
#[Description('Backup database to a SQL file in storage/backups')]
class DatabaseBackup extends Command
{
    public function handle(): int
    {
        $filename = 'backup-'.now()->format('Y-m-d-Hi').'.sql';
        $path = storage_path('backups/'.$filename);

        if (! is_dir(storage_path('backups'))) {
            mkdir(storage_path('backups'), 0755, true);
        }

        $db = config('database.connections.mysql');
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s --port=%s %s > %s',
            escapeshellarg($db['username']),
            escapeshellarg($db['password']),
            escapeshellarg($db['host']),
            escapeshellarg($db['port']),
            escapeshellarg($db['database']),
            escapeshellarg($path),
        );

        $output = [];
        $resultCode = 0;
        exec($command, $output, $resultCode);

        if ($resultCode === 0) {
            $size = round(filesize($path) / 1024 / 1024, 2);
            $this->info("Backup berhasil: {$filename} ({$size} MB)");

            $this->cleanOldBackups();

            return self::SUCCESS;
        }

        $this->error('Backup gagal. Pastikan mysqldump terinstall dan ada di PATH.');
        $this->warn('Cara install:');
        $this->warn('- Laragon: sudah include mysqldump, jalankan dari terminal Laragon');
        $this->warn('- Alternatif: install spatie/laravel-backup via composer');

        return self::FAILURE;
    }

    private function cleanOldBackups(int $keep = 7): void
    {
        $files = glob(storage_path('backups/*.sql'));
        if (! $files) {
            return;
        }

        $files = array_slice($files, 0, -$keep);
        foreach ($files as $file) {
            unlink($file);
        }
    }
}

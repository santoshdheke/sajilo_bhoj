<?php

namespace App\Console\Commands;

use App\Mail\DatabaseBackupMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Process;

/**
 * Daily database backup: dump MySQL to storage/app/backups (gzipped) and email it.
 * Recipient comes from BACKUP_EMAIL (config/services.php); the last 7 dumps are kept on disk.
 */
class BackupDatabase extends Command
{
    protected $signature = 'db:backup {--email= : Send to this address instead of BACKUP_EMAIL}';

    protected $description = 'Dump the database and email the SQL file';

    private const KEEP = 7;

    public function handle(): int
    {
        $db  = config('database.connections.' . config('database.default'));
        $dir = storage_path('app/backups');
        File::ensureDirectoryExists($dir);

        $file = "{$dir}/{$db['database']}-" . now()->format('Y-m-d_His') . '.sql.gz';

        if (! $this->dump($db, $file)) {
            return self::FAILURE;
        }

        $to = $this->option('email') ?: config('services.backup.email');
        Mail::to($to)->send(new DatabaseBackupMail($file, $db['database']));

        $this->pruneOldBackups($dir);

        $this->info("Backup saved to {$file} and emailed to {$to}.");

        return self::SUCCESS;
    }

    private function dump(array $db, string $file): bool
    {
        // Pass the password through a temp options file so it never shows up in the process list.
        $cnf = tempnam(sys_get_temp_dir(), 'mycnf');
        file_put_contents($cnf, sprintf(
            "[client]\nuser=\"%s\"\npassword=\"%s\"\nhost=\"%s\"\nport=%s\n",
            $db['username'], $db['password'], $db['host'], $db['port'],
        ));

        $cmd = sprintf(
            'set -o pipefail; mysqldump --defaults-extra-file=%s --single-transaction --routines --triggers --no-tablespaces %s | gzip > %s',
            escapeshellarg($cnf), escapeshellarg($db['database']), escapeshellarg($file),
        );

        try {
            $result = Process::run(['bash', '-c', $cmd]);
        } finally {
            @unlink($cnf);
        }

        if ($result->failed()) {
            @unlink($file);
            $this->error('Backup failed: ' . trim($result->errorOutput()));

            return false;
        }

        return true;
    }

    private function pruneOldBackups(string $dir): void
    {
        collect(glob("{$dir}/*.sql.gz"))->sortDesc()->slice(self::KEEP)->each(fn ($old) => @unlink($old));
    }
}

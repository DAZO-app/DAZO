<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Services\AuditService;

class BackupService
{
    public function __construct(
        private AuditService $auditService
    ) {
    }
    /**
     * Generate a database backup.
     */
    public function generateBackup(): string
    {
        $backupDir = storage_path('app/backups/database');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $filename = 'bkp_db_' . date('Y-m-d_H-i-s') . '.sql.gz';
        $path = $backupDir . '/' . $filename;

        $dbConfig = config('database.connections.pgsql');
        
        $command = sprintf(
            'PGPASSWORD=%s pg_dump -h %s -p %s -U %s %s | gzip > %s',
            escapeshellarg($dbConfig['password']),
            escapeshellarg($dbConfig['host']),
            escapeshellarg($dbConfig['port']),
            escapeshellarg($dbConfig['username']),
            escapeshellarg($dbConfig['database']),
            escapeshellarg($path)
        );

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception('Erreur lors de la génération du dump PostgreSQL.');
        }

        $this->auditService->log('backup_db_generated', null, null, ['filename' => $filename]);

        return $filename;
    }

    /**
     * Generate a file backup (zip of storage/app/public).
     */
    public function generateFileBackup(): string
    {
        $backupDir = storage_path('app/backups/files');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $filename = 'bkp_files_' . date('Y-m-d_H-i-s') . '.zip';
        $path = $backupDir . '/' . $filename;
        $sourcePath = storage_path('app/public');

        // Check if zip command is available
        if (!shell_exec('which zip')) {
            throw new \Exception('La commande "zip" n\'est pas installée sur le serveur.');
        }

        $command = sprintf(
            'cd %s && zip -r %s .',
            escapeshellarg($sourcePath),
            escapeshellarg($path)
        );

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception('Erreur lors de la création de l\'archive zip des fichiers.');
        }

        $this->auditService->log('backup_files_generated', null, null, ['filename' => $filename]);

        return $filename;
    }

    /**
     * Clean up old backups based on retention configuration.
     */
    public function cleanOldBackups(string $type, int $retentionDays): int
    {
        $backupDir = storage_path('app/backups/' . $type);
        if (!File::exists($backupDir)) {
            return 0;
        }

        $files = File::files($backupDir);
        $deletedCount = 0;
        $now = time();

        foreach ($files as $file) {
            if ($now - $file->getMTime() > ($retentionDays * 86400)) {
                File::delete($file->getPathname());
                $deletedCount++;
            }
        }

        return $deletedCount;
    }
}

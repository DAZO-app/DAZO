<?php

namespace App\Jobs;

use App\Services\BackupService;
use App\Services\ConfigService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BackupFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(BackupService $backupService, ConfigService $configService): void
    {
        if ($configService->get('auto_backup_files_enabled') !== 'true') {
            return;
        }

        // Vérification de l'heure (même heure que BDD par défaut ou spécifique ?)
        $now = now();
        $targetTime = $configService->get('auto_backup_files_time', '03:30'); 
        $targetHour = (int) explode(':', $targetTime)[0];

        if ($now->hour !== $targetHour) {
            return;
        }

        // Vérification de la fréquence
        $frequency = $configService->get('auto_backup_files_frequency', 'daily');
        if ($frequency === 'weekly' && $now->dayOfWeek !== 1) {
            return;
        }
        if ($frequency === 'monthly' && $now->day !== 1) {
            return;
        }

        try {
            Log::info('Démarrage de la sauvegarde automatique des fichiers...');
            
            $filename = $backupService->generateFileBackup();
            
            Log::info("Sauvegarde fichiers réussie : {$filename}");

            // Nettoyage des anciennes sauvegardes
            $retention = (int) $configService->get('auto_backup_files_retention_days', 7);
            $deleted = $backupService->cleanOldBackups('files', $retention);
            
            if ($deleted > 0) {
                Log::info("Nettoyage fichiers : {$deleted} anciennes sauvegardes supprimées.");
            }

        } catch (\Exception $e) {
            Log::error("Échec de la sauvegarde automatique des fichiers : " . $e->getMessage());
        }
    }
}

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

class BackupDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(BackupService $backupService, ConfigService $configService): void
    {
        if ($configService->get('auto_backup_enabled') !== 'true') {
            return;
        }

        // Vérification de l'heure
        $now = now();
        $targetTime = $configService->get('auto_backup_time', '03:00'); // format "HH:MM"
        $targetHour = (int) explode(':', $targetTime)[0];

        if ($now->hour !== $targetHour) {
            return;
        }

        // Vérification de la fréquence
        $frequency = $configService->get('auto_backup_frequency', 'daily');
        if ($frequency === 'weekly' && $now->dayOfWeek !== 1) { // Lundi
            return;
        }
        if ($frequency === 'monthly' && $now->day !== 1) { // 1er du mois
            return;
        }

        try {
            Log::info('Démarrage de la sauvegarde automatique de la base de données...');
            
            $filename = $backupService->generateBackup();
            
            Log::info("Sauvegarde réussie : {$filename}");

            // Nettoyage des anciennes sauvegardes
            $retention = (int) $configService->get('auto_backup_retention_days', 7);
            $deleted = $backupService->cleanOldBackups('database', $retention);
            
            if ($deleted > 0) {
                Log::info("Nettoyage : {$deleted} anciennes sauvegardes supprimées.");
            }

        } catch (\Exception $e) {
            Log::error("Échec de la sauvegarde automatique : " . $e->getMessage());
        }
    }
}

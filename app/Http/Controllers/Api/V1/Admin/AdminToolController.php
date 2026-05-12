<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redis;
use App\Models\ActivityLog;

class AdminToolController extends Controller
{
    /**
     * Get real-time database statistics and both backup histories.
     */
    public function databaseStats()
    {
        // Total DB size
        $totalSize = DB::select("SELECT pg_size_pretty(pg_database_size(current_database())) as size")[0]->size;

        // Table stats
        $tables = DB::select("
            SELECT 
                relname as name,
                pg_size_pretty(pg_total_relation_size(relid)) as size,
                n_live_tup as rows
            FROM pg_stat_user_tables
            ORDER BY pg_total_relation_size(relid) DESC
        ");

        return response()->json([
            'engine' => 'PostgreSQL ' . DB::getPdo()->getAttribute(\PDO::ATTR_SERVER_VERSION),
            'total_size' => $totalSize,
            'connection' => config('database.connections.' . config('database.default')),
            'tables' => $tables
        ]);
    }

    public function backupStats()
    {
        // Total DB size
        $totalSize = DB::select("SELECT pg_size_pretty(pg_database_size(current_database())) as size")[0]->size;

        $dbBackups = $this->listBackups('database');
        $fileBackups = $this->listBackups('files');

        $configService = app(\App\Services\ConfigService::class);

        return response()->json([
            'engine' => 'PostgreSQL ' . DB::getPdo()->getAttribute(\PDO::ATTR_SERVER_VERSION),
            'total_size' => $totalSize,
            'db_backups' => $dbBackups,
            'file_backups' => $fileBackups,
            'active_slot' => $this->getActiveStorageSlot(),
            'connection' => config('database.connections.' . config('database.default')),
            'auto_backup_db' => [
                'enabled' => $configService->get('auto_backup_enabled') === 'true',
                'frequency' => $configService->get('auto_backup_frequency', 'daily'),
                'retention' => (int) $configService->get('auto_backup_retention_days', 7),
                'time' => $configService->get('auto_backup_time', '03:00'),
            ],
            'auto_backup_files' => [
                'enabled' => $configService->get('auto_backup_files_enabled') === 'true',
                'frequency' => $configService->get('auto_backup_files_frequency', 'daily'),
                'retention' => (int) $configService->get('auto_backup_files_retention_days', 7),
                'time' => $configService->get('auto_backup_files_time', '03:30'),
            ]
        ]);
    }

    private function listBackups($type)
    {
        $backups = [];
        $path = storage_path('app/backups/' . $type);
        if (File::exists($path)) {
            $files = File::files($path);
            foreach ($files as $file) {
                $backups[] = [
                    'id' => $file->getFilename(),
                    'name' => $file->getFilename(),
                    'size' => $this->formatBytes($file->getSize()),
                    'date' => date('d/m/Y H:i:s', $file->getMTime()),
                ];
            }
        }
        // Sort by date desc
        usort($backups, fn($a, $b) => strcmp($b['id'], $a['id']));
        return $backups;
    }

    /**
     * Get server metrics and service status.
     */
    public function serverStats()
    {
        // CPU Load
        $load = sys_getloadavg();
        $cpuValue = round(($load[0] / $this->getCpuCount()) * 100);

        // RAM Usage
        $ram = $this->getRamUsage();
        
        // Disk Usage
        $diskTotal = disk_total_space('/');
        $diskFree = disk_free_space('/');
        $diskUsed = $diskTotal - $diskFree;
        $diskValue = round(($diskUsed / $diskTotal) * 100);

        // Services Status
        $services = [
            [
                'name' => 'Moteur PHP',
                'desc' => 'PHP v' . PHP_VERSION . ' (' . php_sapi_name() . ')',
                'icon' => 'fa-microchip',
                'status' => 'ok'
            ],
            [
                'name' => 'Redis Cache',
                'desc' => 'Gestionnaire de session & cache',
                'icon' => 'fa-bolt',
                'status' => $this->checkRedis() ? 'ok' : 'err'
            ],
            [
                'name' => 'Dossier Storage',
                'desc' => 'Permissions d\'écriture',
                'icon' => 'fa-folder-open',
                'status' => is_writable(storage_path()) ? 'ok' : 'err'
            ]
        ];

        return response()->json([
            'uptime' => $this->getUptime(),
            'gauges' => [
                ['label' => 'Utilisation CPU', 'value' => $cpuValue, 'detail' => $this->getCpuModel(), 'statusClass' => $cpuValue > 80 ? 'err' : ($cpuValue > 50 ? 'warn' : 'ok')],
                ['label' => 'Mémoire RAM', 'value' => $ram['percent'], 'detail' => $ram['detail'], 'statusClass' => $ram['percent'] > 90 ? 'err' : 'ok'],
                ['label' => 'Espace Disque', 'value' => $diskValue, 'detail' => $this->formatBytes($diskUsed) . ' / ' . $this->formatBytes($diskTotal), 'statusClass' => $diskValue > 90 ? 'err' : 'ok'],
            ],
            'services' => $services,
            'php_config' => [
                'version' => PHP_VERSION,
                'memory_limit' => ini_get('memory_limit'),
                'upload_max' => ini_get('upload_max_filesize'),
                'post_max' => ini_get('post_max_size'),
                'max_execution' => ini_get('max_execution_time') . 's',
            ]
        ]);
    }

    /**
     * Get the latest log lines.
     */
    public function logs()
    {
        $logPath = storage_path('logs/laravel.log');
        $logs = [];

        if (File::exists($logPath)) {
            $content = shell_exec('tail -n 100 ' . escapeshellarg($logPath));
            $lines = explode("\n", trim($content));
            
            foreach ($lines as $line) {
                if (preg_match('/^\[(?P<date>.*)\] (?P<env>\w+)\.(?P<level>\w+): (?P<message>.*)/', $line, $matches)) {
                    $logs[] = [
                        'time' => date('H:i:s', strtotime($matches['date'])),
                        'level' => strtolower($matches['level']),
                        'msg' => $matches['message']
                    ];
                } else if (!empty($line)) {
                     $logs[] = [
                        'time' => '--:--:--',
                        'level' => 'info',
                        'msg' => $line
                    ];
                }
            }
        }

        return response()->json(array_reverse($logs));
    }

    /**
     * Generate a manual database backup (PostgreSQL).
     */
    public function backup(\App\Services\BackupService $backupService)
    {
        try {
            $filename = $backupService->generateBackup();
            return response()->json(['success' => true, 'filename' => $filename]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Restore a database backup.
     */
    public function restore(Request $request)
    {
        $request->validate(['filename' => 'required|string']);
        $filename = basename($request->filename);
        $path = storage_path('app/backups/database/' . $filename);

        if (!File::exists($path)) {
            return response()->json(['error' => 'Fichier introuvable.'], 404);
        }

        $dbConfig = config('database.connections.pgsql');
        
        // This command assumes the dump is a gziped SQL file
        $command = sprintf(
            'gunzip < %s | PGPASSWORD=%s psql -h %s -p %s -U %s %s',
            escapeshellarg($path),
            escapeshellarg($dbConfig['password']),
            escapeshellarg($dbConfig['host']),
            escapeshellarg($dbConfig['port']),
            escapeshellarg($dbConfig['username']),
            escapeshellarg($dbConfig['database'])
        );

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            return response()->json(['error' => 'Erreur lors de la restauration.'], 500);
        }

        return response()->json(['success' => true]);
    }

    public function restoreFiles(Request $request)
    {
        $request->validate(['filename' => 'required|string']);
        $filename = basename($request->filename);
        $path = storage_path('app/backups/files/' . $filename);

        if (!File::exists($path)) {
            return response()->json(['error' => 'Fichier introuvable.'], 404);
        }

        // 1. Detect current active slot
        $activeSlot = $this->getActiveStorageSlot();
        $targetSlot = ($activeSlot === 'a') ? 'b' : 'a';

        // 2. Unzip into target slot
        $scriptPath = base_path('bash/DAZO-app-storage.sh');
        $command = sprintf('sh %s restore %s %s', escapeshellarg($scriptPath), escapeshellarg($path), $targetSlot);
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            return response()->json(['error' => 'Erreur lors de la décompression du backup.'], 500);
        }

        // 3. Switch symlink
        $commandSwitch = sprintf('sh %s switch %s', escapeshellarg($scriptPath), $targetSlot);
        exec($commandSwitch, $output, $returnVar);

        if ($returnVar !== 0) {
            return response()->json(['error' => 'Erreur lors du changement de lien symbolique.'], 500);
        }

        return response()->json([
            'success' => true,
            'active_slot' => $targetSlot,
            'can_rollback' => true
        ]);
    }

    public function rollbackFiles()
    {
        $activeSlot = $this->getActiveStorageSlot();
        $targetSlot = ($activeSlot === 'a') ? 'b' : 'a';
        
        $scriptPath = base_path('bash/DAZO-app-storage.sh');
        $command = sprintf('sh %s switch %s', escapeshellarg($scriptPath), $targetSlot);
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            return response()->json(['error' => 'Erreur lors du rollback.'], 500);
        }

        return response()->json([
            'success' => true,
            'active_slot' => $targetSlot
        ]);
    }

    private function getActiveStorageSlot()
    {
        $publicLink = storage_path('app/public');
        if (!is_link($publicLink)) {
            return 'a';
        }
        $target = readlink($publicLink);
        return (str_contains($target, 'files_b')) ? 'b' : 'a';
    }

    public function uploadBackupFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
            'type' => 'required|in:database,files'
        ]);

        $file = $request->file('file');
        $type = $request->input('type');
        $filename = $file->getClientOriginalName();
        
        // Security check on extension
        $ext = $file->getClientOriginalExtension();
        if ($type === 'database' && !in_array($ext, ['gz', 'sql'])) {
            return response()->json(['error' => 'Format invalide pour BDD (.sql ou .gz requis)'], 400);
        }
        if ($type === 'files' && $ext !== 'zip') {
            return response()->json(['error' => 'Format invalide pour fichiers (.zip requis)'], 400);
        }

        $path = storage_path('app/backups/' . $type);
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $file->move($path, $filename);

        return response()->json(['success' => true]);
    }

    public function downloadBackup($filename)
    {
        $filename = basename($filename);
        $path = storage_path('app/backups/database/' . $filename);

        if (!File::exists($path)) {
            abort(404, 'Fichier de sauvegarde introuvable.');
        }

        return response()->download($path);
    }

    public function downloadFileBackup($filename)
    {
        $filename = basename($filename);
        $path = storage_path('app/backups/files/' . $filename);

        if (!File::exists($path)) {
            abort(404, 'Fichier de sauvegarde introuvable.');
        }

        return response()->download($path);
    }

    public function getDownloadFileUrl($filename)
    {
        $url = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'admin.file-backup.download',
            now()->addMinutes(5),
            ['filename' => $filename]
        );

        return response()->json(['url' => $url]);
    }

    /**
     * Delete a specific backup file.
     */
    public function deleteBackup($filename)
    {
        $filename = basename($filename);
        $path = storage_path('app/backups/database/' . $filename);

        if (File::exists($path)) {
            File::delete($path);
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Fichier introuvable.'], 404);
    }

    public function deleteFileBackup($filename)
    {
        $filename = basename($filename);
        $path = storage_path('app/backups/files/' . $filename);

        if (File::exists($path)) {
            File::delete($path);
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Fichier introuvable.'], 404);
    }

    public function backupFiles(\App\Services\BackupService $backupService)
    {
        try {
            $filename = $backupService->generateFileBackup();
            return response()->json(['success' => true, 'filename' => $filename]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Generate a temporary signed URL for downloading a backup.
     */
    public function getDownloadUrl($filename)
    {
        $url = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'admin.backup.download',
            now()->addMinutes(5),
            ['filename' => $filename]
        );

        return response()->json(['url' => $url]);
    }

    // --- Private Helpers ---

    private function getCpuCount()
    {
        if (is_file('/proc/cpuinfo')) {
            $cpuinfo = file_get_contents('/proc/cpuinfo');
            preg_match_all('/^processor/m', $cpuinfo, $matches);
            return count($matches[0]) ?: 1;
        }
        return 1;
    }

    private function getCpuModel()
    {
        if (is_file('/proc/cpuinfo')) {
            $cpuinfo = file_get_contents('/proc/cpuinfo');
            if (preg_match('/model name\s+:\s+(.*)$/m', $cpuinfo, $matches)) {
                return trim($matches[1]);
            }
        }
        return 'Unknown CPU';
    }

    private function getRamUsage()
    {
        if (is_file('/proc/meminfo')) {
            $meminfo = file_get_contents('/proc/meminfo');
            preg_match('/MemTotal:\s+(\d+)/', $meminfo, $total);
            preg_match('/MemAvailable:\s+(\d+)/', $meminfo, $available);
            
            $totalKb = (int)$total[1];
            $availableKb = (int)$available[1];
            $usedKb = $totalKb - $availableKb;
            
            return [
                'percent' => round(($usedKb / $totalKb) * 100),
                'detail' => $this->formatBytes($usedKb * 1024) . ' / ' . $this->formatBytes($totalKb * 1024)
            ];
        }
        return ['percent' => 0, 'detail' => 'N/A'];
    }

    private function getUptime()
    {
        if (is_file('/proc/uptime')) {
            $uptime = (int)file_get_contents('/proc/uptime');
            $days = floor($uptime / 86400);
            $hours = floor(($uptime % 86400) / 3600);
            $minutes = floor(($uptime % 3600) / 60);
            
            return "{$days}j {$hours}h {$minutes}m";
        }
        return 'N/A';
    }

    private function checkRedis()
    {
        try {
            Redis::ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
    public function auditLogs(Request $request)
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        if ($request->has('resource_type')) {
            $query->where('auditable_type', 'like', '%' . $request->resource_type . '%');
        }

        return response()->json($query->paginate(50));
    }
}

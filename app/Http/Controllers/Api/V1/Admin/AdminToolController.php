<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redis;

class AdminToolController extends Controller
{
    /**
     * Get real-time database statistics (PostgreSQL specific).
     */
    public function databaseStats()
    {
        // Tables stats
        $tables = DB::select("
            SELECT 
                relname AS name, 
                reltuples AS rows,
                pg_size_pretty(pg_relation_size(C.oid)) AS data_size,
                pg_size_pretty(pg_total_relation_size(C.oid) - pg_relation_size(C.oid)) AS index_size
            FROM pg_class C
            LEFT JOIN pg_namespace N ON (N.oid = C.relnamespace)
            WHERE nspname NOT IN ('pg_catalog', 'information_schema')
              AND C.relkind <> 'i'
              AND nspname !~ '^pg_toast'
            ORDER BY pg_total_relation_size(C.oid) DESC
        ");

        // Total DB size
        $totalSize = DB::select("SELECT pg_size_pretty(pg_database_size(current_database())) as size")[0]->size;

        // Mock backups for now (will implement actual file listing in A.3)
        $backups = []; 
        $backupPath = storage_path('app/backups');
        if (File::exists($backupPath)) {
            $files = File::files($backupPath);
            foreach ($files as $file) {
                if ($file->getExtension() === 'gz' || $file->getExtension() === 'sql') {
                    $backups[] = [
                        'id' => $file->getFilename(),
                        'name' => $file->getFilename(),
                        'size' => $this->formatBytes($file->getSize()),
                        'date' => date('d/m/Y H:i:s', $file->getMTime()),
                    ];
                }
            }
        }

        return response()->json([
            'engine' => 'PostgreSQL ' . DB::getPdo()->getAttribute(\PDO::ATTR_SERVER_VERSION),
            'total_size' => $totalSize,
            'tables' => $tables,
            'backups' => $backups,
            'connection' => config('database.connections.' . config('database.default')),
        ]);
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
    public function backup()
    {
        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $filename = 'bkp_' . date('Y-m-d_H-i-s') . '.sql.gz';
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
            return response()->json(['error' => 'Erreur lors de la génération du dump.'], 500);
        }

        return response()->json(['success' => true, 'filename' => $filename]);
    }

    /**
     * Restore a database backup.
     */
    public function restore(Request $request)
    {
        $request->validate(['filename' => 'required|string']);
        $filename = basename($request->filename);
        $path = storage_path('app/backups/' . $filename);

        if (!File::exists($path)) {
            return response()->json(['error' => 'Fichier introuvable.'], 404);
        }

        $dbConfig = config('database.connections.pgsql');
        
        // This command assumes the dump is a gziped SQL file (from backup() method)
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

    /**
     * Download a specific backup file (Public but Signed).
     */
    public function downloadBackup($filename)
    {
        $filename = basename($filename);
        $path = storage_path('app/backups/' . $filename);

        if (!File::exists($path)) {
            abort(404, 'Fichier de sauvegarde introuvable.');
        }

        return response()->download($path);
    }

    /**
     * Delete a specific backup file.
     */
    public function deleteBackup($filename)
    {
        $filename = basename($filename);
        $path = storage_path('app/backups/' . $filename);

        if (File::exists($path)) {
            File::delete($path);
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Fichier introuvable.'], 404);
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
}

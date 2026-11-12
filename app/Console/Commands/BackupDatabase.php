<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Sauvegarde quotidienne de la base de données MariaDB/MySQL hotel_manager';

    public function handle()
    {
        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $fileName = 'backup_hotel_manager_' . now()->format('Y-m-d_His') . '.sql';
        $filePath = $backupDir . DIRECTORY_SEPARATOR . $fileName;

        $dbHost = config('database.connections.mysql.host');
        $dbPort = config('database.connections.mysql.port');
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');

        // Détection automatique de mysqldump.exe dans WAMP64 (MariaDB en priorité)
        $wampDumps = glob('C:\wamp64\bin\mariadb\mariadb*\bin\mysqldump.exe');
        if (empty($wampDumps)) {
            $wampDumps = glob('C:\wamp64\bin\mysql\mysql*\bin\mysqldump.exe');
        }
        $mysqldump = !empty($wampDumps) ? '"' . end($wampDumps) . '"' : 'mysqldump';

        $passArg = $dbPass ? "-p\"{$dbPass}\"" : '';

        // Exécution directe sans --column-statistics=0 avec le binaire natif MariaDB
        $command = "{$mysqldump} -h {$dbHost} -P {$dbPort} -u {$dbUser} {$passArg} {$dbName} > \"{$filePath}\"";
        exec($command, $output, $returnVar);

        if ($returnVar === 0 && File::exists($filePath) && File::size($filePath) > 0) {
            // Nettoyage automatique des sauvegardes de plus de 30 jours
            foreach (File::files($backupDir) as $file) {
                if (now()->timestamp - $file->getMTime() > (30 * 86400)) {
                    File::delete($file->getPathname());
                }
            }
            $this->info("✅ Sauvegarde réussie : storage/app/backups/{$fileName}");
        } else {
            $this->error("❌ Échec de la sauvegarde. Vérifiez que MariaDB/MySQL est démarré dans WAMP64.");
        }
    }
}

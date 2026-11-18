<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class BackupController extends Controller
{
    /**
     * Générer et télécharger une sauvegarde SQL complète de la base de données
     */
    public function downloadSql()
    {
        $dbName = config('database.connections.mysql.database');
        $tables = DB::select('SHOW TABLES');
        $keyName = 'Tables_in_' . $dbName;

        $sqlDump  = "-- ========================================================\n";
        $sqlDump .= "-- SAUVEGARDE HOTEL MANAGER : {$dbName}\n";
        $sqlDump .= "-- Date : " . now()->format('d/m/Y H:i:s') . "\n";
        $sqlDump .= "-- ========================================================\n\n";
        $sqlDump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $tableObj) {
            $table = $tableObj->$keyName;

            // Structure de la table
            $createStmt = DB::select("SHOW CREATE TABLE `{$table}`");
            $createSql = $createStmt[0]->{'Create Table'};

            $sqlDump .= "-- Structure de la table `{$table}`\n";
            $sqlDump .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sqlDump .= $createSql . ";\n\n";

            // Données de la table
            $rows = DB::table($table)->get();
            if ($rows->count() > 0) {
                $sqlDump .= "-- Données de la table `{$table}`\n";
                foreach ($rows as $row) {
                    $rowArray = (array) $row;
                    $columns = array_map(fn($col) => "`{$col}`", array_keys($rowArray));
                    $values = array_map(function ($val) {
                        if ($val === null) {
                            return 'NULL';
                        }
                        return "'" . addslashes((string) $val) . "'";
                    }, array_values($rowArray));

                    $sqlDump .= "INSERT INTO `{$table}` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
                }
                $sqlDump .= "\n";
            }
        }

        $sqlDump .= "SET FOREIGN_KEY_CHECKS=1;\n";

        $fileName = 'backup_hotel_manager_' . now()->format('Y-m-d_His') . '.sql';

        return response($sqlDump, 200, [
            'Content-Type'        => 'application/sql',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }
}

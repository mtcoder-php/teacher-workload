<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backup\RestoreBackupRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class BackupController extends Controller
{
    /**
     * Backuplar ro'yxati
     */
    public function index()
    {
        try {
            $backupPath = storage_path('app/backups');
            $backups = [];
            
            if (File::exists($backupPath)) {
                $files = File::files($backupPath);
                
                foreach ($files as $file) {
                    if (str_ends_with($file->getFilename(), '.sql')) {
                        $backups[] = [
                            'name' => $file->getFilename(),
                            'path' => $file->getPathname(),
                            'size' => $file->getSize(),
                            'size_formatted' => $this->formatBytes($file->getSize()),
                            'date' => $file->getMTime(),
                            'date_formatted' => date('Y-m-d H:i:s', $file->getMTime()),
                        ];
                    }
                }
                
                usort($backups, function($a, $b) {
                    return $b['date'] - $a['date'];
                });
            }
            
            return Inertia::render('Admin/Settings/Backups', [
                'backups' => $backups,
            ]);
            
        } catch (\Exception $e) {
            Log::error('List backups error: ' . $e->getMessage());
            
            return Inertia::render('Admin/Settings/Backups', [
                'backups' => [],
            ])->with('error', 'Backuplar ro\'yxatini olishda xatolik');
        }
    }

    /**
     * Backup yaratish
     */
    public function create(Request $request)
    {
        try {
            $filename = 'backup-' . now()->format('Y-m-d-His') . '.sql';
            $backupPath = storage_path('app/backups/' . $filename);
            
            if (!File::exists(storage_path('app/backups'))) {
                File::makeDirectory(storage_path('app/backups'), 0755, true);
            }
            
            $database = config('database.connections.mysql.database');
            $tables = DB::select('SHOW TABLES');
            $tableKey = 'Tables_in_' . $database;
            
            $sql = "-- ==========================================\n";
            $sql .= "-- Database Backup\n";
            $sql .= "-- Date: " . now()->toDateTimeString() . "\n";
            $sql .= "-- Database: {$database}\n";
            $sql .= "-- User: " . auth()->user()->name . "\n";
            $sql .= "-- ==========================================\n\n";
            
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
            $sql .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n\n";
            
            $totalRows = 0;
            
            foreach ($tables as $table) {
                $tableName = $table->$tableKey;
                
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $sql .= "\n-- Table: {$tableName}\n";
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sql .= $createTable[0]->{'Create Table'} . ";\n\n";
                
                $rows = DB::table($tableName)->get();
                
                if ($rows->count() > 0) {
                    $sql .= "-- Data for {$tableName}\n";
                    
                    foreach ($rows as $row) {
                        $values = array_map(function($value) {
                            if (is_null($value)) return 'NULL';
                            
                            $value = str_replace(
                                ['\\', "\0", "\n", "\r", "'", '"', "\x1a"], 
                                ['\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'], 
                                $value
                            );
                            return "'" . $value . "'";
                        }, (array)$row);
                        
                        $sql .= "INSERT INTO `{$tableName}` VALUES (" . implode(', ', $values) . ");\n";
                        $totalRows++;
                    }
                    $sql .= "\n";
                }
            }
            
            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
            
            File::put($backupPath, $sql);
            
            $fileSize = File::size($backupPath);
            
            Log::info("Backup created", [
                'user' => auth()->user()->name,
                'filename' => $filename,
                'size' => $fileSize,
                'tables' => count($tables),
                'rows' => $totalRows,
            ]);
            
            return redirect()->back()
                ->with('success', "Backup yaratildi: {$filename} (" . $this->formatBytes($fileSize) . ")");

        } catch (\Exception $e) {
            Log::error('Backup creation error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Backup xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Backup yuklab olish
     */
    public function download($filename)
    {
        try {
            $path = storage_path('app/backups/' . $filename);
            
            if (!File::exists($path)) {
                throw new \Exception('Backup fayli topilmadi');
            }
            
            if (!str_ends_with($filename, '.sql')) {
                throw new \Exception('Faqat .sql fayllar ruxsat etilgan');
            }
            
            Log::info("Backup downloaded: {$filename}", [
                'user' => auth()->user()->name,
            ]);
            
            return response()->download($path);
            
        } catch (\Exception $e) {
            Log::error('Download backup error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Yuklab olish xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Backup o'chirish
     */
    public function destroy($filename)
    {
        try {
            $path = storage_path('app/backups/' . $filename);
            
            if (!File::exists($path)) {
                throw new \Exception('Backup fayli topilmadi');
            }
            
            if (!str_ends_with($filename, '.sql')) {
                throw new \Exception('Faqat .sql fayllar ruxsat etilgan');
            }
            
            File::delete($path);
            
            Log::info("Backup deleted: {$filename}", [
                'user' => auth()->user()->name,
            ]);
            
            return redirect()->back()
                ->with('success', 'Backup o\'chirildi');
            
        } catch (\Exception $e) {
            Log::error('Delete backup error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'O\'chirish xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Eski backuplarni tozalash
     */
    public function cleanup()
    {
        try {
            $backupPath = storage_path('app/backups');
            $deletedCount = 0;
            
            if (File::exists($backupPath)) {
                $files = File::files($backupPath);
                $thirtyDaysAgo = now()->subDays(30)->timestamp;
                
                foreach ($files as $file) {
                    if (str_ends_with($file->getFilename(), '.sql')) {
                        if ($file->getMTime() < $thirtyDaysAgo) {
                            File::delete($file->getPathname());
                            $deletedCount++;
                        }
                    }
                }
            }
            
            Log::info("Backup cleanup", [
                'user' => auth()->user()->name,
                'deleted_count' => $deletedCount,
            ]);
            
            return redirect()->back()
                ->with('success', "{$deletedCount} ta eski backup o'chirildi");

        } catch (\Exception $e) {
            Log::error('Backup cleanup error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Tozalash xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Backupni tiklash
     */
    public function restore(RestoreBackupRequest $request, $filename)
    {
        try {
            $path = storage_path('app/backups/' . $filename);
            
            if (!File::exists($path)) {
                throw new \Exception('Backup fayli topilmadi');
            }
            
            $sql = File::get($path);
            
            DB::unprepared($sql);
            
            Log::warning("Database restored", [
                'user' => auth()->user()->name,
                'backup_file' => $filename,
            ]);
            
            return redirect()->route('admin.settings.index')
                ->with('success', 'Database muvaffaqiyatli tiklandi!');
            
        } catch (\Exception $e) {
            Log::error('Restore backup error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Tiklash xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Helper: Bytes formatiga o'tkazish
     */
    private function formatBytes($bytes)
    {
        if ($bytes === 0) return '0 Bytes';
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }
}
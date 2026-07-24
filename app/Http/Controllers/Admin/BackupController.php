<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleDrive;
use Carbon\Carbon;
use ZipArchive;
use Exception;

class BackupController extends Controller
{
    private function getGoogleClient()
    {
        $clientId = env('GOOGLE_DRIVE_CLIENT_ID');
        $clientSecret = env('GOOGLE_DRIVE_CLIENT_SECRET');
        $refreshToken = env('GOOGLE_DRIVE_REFRESH_TOKEN');

        if (!$clientId || !$clientSecret || !$refreshToken) {
            return null;
        }

        try {
            $client = new GoogleClient();
            $client->setClientId($clientId);
            $client->setClientSecret($clientSecret);
            $client->refreshToken($refreshToken);
            $client->addScope(GoogleDrive::DRIVE);

            return $client;
        } catch (Exception $e) {
            Log::error('Gagal inisialisasi Google Client OAuth2: ' . $e->getMessage());
            return null;
        }
    }

    public function index()
    {
        $backups = [];
        $googleDriveConnected = false;
        $client = $this->getGoogleClient();

        if ($client) {
            try {
                $service = new GoogleDrive($client);
                $folderId = env('GOOGLE_DRIVE_FOLDER_ID');
                
                $query = "mimeType = 'application/zip' and trashed = false";
                if ($folderId) {
                    $query .= " and '{$folderId}' in parents";
                }

                $results = $service->files->listFiles([
                    'q' => $query,
                    'fields' => 'files(id, name, size, createdTime)',
                    'orderBy' => 'createdTime desc'
                ]);

                foreach ($results->getFiles() as $file) {
                    $createdTime = Carbon::parse($file->getCreatedTime())->locale('id')->setTimezone('Asia/Jakarta');

                    $backups[] = [
                        'id' => $file->getId(),
                        'name' => $file->getName(),
                        'display_title' => $file->getName(),
                        'size' => $this->formatBytes($file->getSize()),
                        'created_at' => $createdTime->format('Y-m-d H:i:s'),
                        'source' => 'Google Drive'
                    ];
                }
                $googleDriveConnected = true;
            } catch (Exception $e) {
                Log::error('Error list file Google Drive: ' . $e->getMessage());
            }
        }

        // Backup Server Lokal
        $localPath = storage_path('app/backups');
        if (!file_exists($localPath)) {
            mkdir($localPath, 0755, true);
        }

        $localFiles = glob($localPath . '/*.zip');
        foreach ($localFiles as $file) {
            $filename = basename($file);
            $existsInGoogle = collect($backups)->contains('name', $filename);
            
            if (!$existsInGoogle) {
                $fileTime = Carbon::createFromTimestamp(filemtime($file))->locale('id')->setTimezone('Asia/Jakarta');

                $backups[] = [
                    'id' => $filename,
                    'name' => $filename,
                    'display_title' => $filename,
                    'size' => $this->formatBytes(filesize($file)),
                    'created_at' => $fileTime->format('Y-m-d H:i:s'),
                    'source' => 'Lokal (Server)'
                ];
            }
        }

        // Urutkan data terbaru di paling atas
        usort($backups, function ($a, $b) {
            return strcmp($b['created_at'], $a['created_at']);
        });

        return view('admin.dashboard.backup', compact('backups', 'googleDriveConnected'));
    }

    public function run()
    {
        return $this->backup();
    }

    public function backup()
    {
        try {
            set_time_limit(300);

            $timestamp = date('Y-m-d_H-i-s');
            $filename = "backup-{$timestamp}.zip";
            
            $backupPath = storage_path("app/backups");
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }
            $zipPath = $backupPath . '/' . $filename;

            // 1. Ekspor Database
            $sqlContent = $this->exportDatabaseSql();
            
            // 2. Buat file Zip
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new Exception("Gagal membuat file zip backup.");
            }

            $zip->addFromString('database.sql', $sqlContent);

            $publicStorage = storage_path('app/public');
            if (file_exists($publicStorage)) {
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($publicStorage),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ($files as $name => $file) {
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();
                        $relativePath = 'storage/' . substr($filePath, strlen($publicStorage) + 1);
                        $zip->addFile($filePath, $relativePath);
                    }
                }
            }

            $zip->close();

            // 3. Upload ke Google Drive 
            $client = $this->getGoogleClient();
            $uploadedToDrive = false;
            
            if ($client) {
                $service = new GoogleDrive($client);
                $fileMetadata = new \Google\Service\Drive\DriveFile([
                    'name' => $filename,
                    'parents' => env('GOOGLE_DRIVE_FOLDER_ID') ? [env('GOOGLE_DRIVE_FOLDER_ID')] : null
                ]);

                $content = file_get_contents($zipPath);
                $file = $service->files->create($fileMetadata, [
                    'data' => $content,
                    'mimeType' => 'application/zip',
                    'uploadType' => 'multipart',
                    'fields' => 'id'
                ]);

                if ($file->id) {
                    $uploadedToDrive = true;
                }
            }

            $message = $uploadedToDrive 
                ? "Backup berhasil dibuat dan diunggah ke Google Drive!" 
                : "Backup berhasil dibuat secara lokal di server!";

            session()->flash('success', $message);
            session()->regenerate();
            return redirect()->route('admin.backup.index');

        } catch (Exception $e) {
            Log::error('Backup failed: ' . $e->getMessage());
            
            session()->flash('error', 'Gagal membuat backup: ' . $e->getMessage());
            session()->regenerate();
            return redirect()->route('admin.backup.index');
        }
    }

    public function download($filename)
    {
        $filename = urldecode($filename);
        $backupPath = storage_path("app/backups/" . basename($filename));

        if (file_exists($backupPath)) {
            return response()->download($backupPath);
        }

        $client = $this->getGoogleClient();
        if ($client) {
            try {
                $service = new GoogleDrive($client);
                $query = "name = '{$filename}' and trashed = false";
                $results = $service->files->listFiles(['q' => $query]);

                if (count($results->getFiles()) > 0) {
                    $fileId = $results->getFiles()[0]->getId();
                    $response = $service->files->get($fileId, ['alt' => 'media']);
                    $content = $response->getBody()->getContents();

                    return response($content)
                        ->header('Content-Type', 'application/zip')
                        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
                }
            } catch (Exception $e) {
                Log::error('Gagal download dari Drive: ' . $e->getMessage());
            }
        }

        session()->flash('error', 'File cadangan tidak ditemukan.');
        session()->regenerate();
        return redirect()->route('admin.backup.index');
    }

    public function restore($filename)
    {
        try {
            set_time_limit(300);

            $filename = urldecode($filename);
            $backupPath = storage_path("app/backups");
            $zipPath = $backupPath . '/' . basename($filename);

            if (!file_exists($zipPath)) {
                $client = $this->getGoogleClient();
                if (!$client) {
                    throw new Exception("File backup tidak ditemukan secara lokal dan Google Drive tidak terhubung.");
                }

                $service = new GoogleDrive($client);
                $query = "name = '{$filename}' and mimeType = 'application/zip' and trashed = false";
                $results = $service->files->listFiles([
                    'q' => $query,
                    'fields' => 'files(id, name)'
                ]);

                $files = $results->getFiles();
                if (count($files) === 0) {
                    throw new Exception("File backup '{$filename}' tidak ditemukan di Google Drive.");
                }

                $fileId = $files[0]->getId();
                $response = $service->files->get($fileId, ['alt' => 'media']);
                $content = $response->getBody()->getContents();

                if (!file_exists($backupPath)) {
                    mkdir($backupPath, 0755, true);
                }
                file_put_contents($zipPath, $content);
            }

            $zip = new ZipArchive();
            if ($zip->open($zipPath) !== true) {
                throw new Exception("Gagal membuka file backup zip.");
            }

            $sqlContent = $zip->getFromName('database.sql');
            if (!$sqlContent) {
                $zip->close();
                throw new Exception("File database.sql tidak ditemukan di dalam backup.");
            }

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                if (str_starts_with($stat['name'], 'storage/')) {
                    $relativePath = substr($stat['name'], 8);
                    $destPath = storage_path('app/public/' . $relativePath);

                    $dir = dirname($destPath);
                    if (!file_exists($dir)) {
                        mkdir($dir, 0755, true);
                    }

                    file_put_contents($destPath, $zip->getFromIndex($i));
                }
            }
            $zip->close();

            // Simpan ID user login aktif sebelum Restore
            $currentUserId = Auth::id();

            $this->importDatabaseSql($sqlContent);

            // Pulihkan kembali login user
            if ($currentUserId) {
                Auth::loginUsingId($currentUserId);
            }

            return redirect()->route('admin.backup.index')->with('success', 'Data database dan media berhasil dipulihkan!');

        } catch (Exception $e) {
            Log::error('Restore failed: ' . $e->getMessage());

            return redirect()->route('admin.backup.index')->with('error', 'Gagal memulihkan data: ' . $e->getMessage());
        }
    }

    public function delete($filename)
    {
        try {
            $deleted = false;
            $filename = urldecode($filename);
            $localPath = storage_path("app/backups/" . basename($filename));

            if (file_exists($localPath)) {
                if (@unlink($localPath)) {
                    $deleted = true;
                }
            }

            $client = $this->getGoogleClient();
            if ($client) {
                try {
                    $service = new GoogleDrive($client);
                    $query = "name = '{$filename}' and trashed = false";
                    $results = $service->files->listFiles(['q' => $query]);

                    foreach ($results->getFiles() as $file) {
                        $service->files->delete($file->getId());
                        $deleted = true;
                    }
                } catch (Exception $e) {
                    Log::error('Gagal hapus file dari Drive: ' . $e->getMessage());
                }
            }

            if ($deleted) {
                session()->flash('success', 'File cadangan berhasil dihapus.');
            } else {
                session()->flash('error', 'File cadangan tidak ditemukan atau sedang digunakan.');
            }
            session()->regenerate();
            return redirect()->route('admin.backup.index');
        } catch (Exception $e) {
            Log::error('Delete failed: ' . $e->getMessage());
            session()->flash('error', 'Gagal menghapus file: ' . $e->getMessage());
            session()->regenerate();
            return redirect()->route('admin.backup.index');
        }
    }

    private function exportDatabaseSql(): string
    {
        $pdo = DB::connection()->getPdo();
        $tables = [];
        
        $result = $pdo->query("SHOW TABLES");
        while ($row = $result->fetch(\PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        $sql = "-- Backup Database System\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        // Kecualikan tabel session & cache agar login/session user tidak hancur saat restore
        $excludedTables = ['sessions', 'cache', 'cache_locks', 'jobs', 'failed_jobs', 'job_batches'];

        foreach ($tables as $table) {
            if (in_array($table, $excludedTables)) {
                continue;
            }

            $createTableResult = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_ASSOC);
            $sql .= "\n\n-- Structure table `{$table}`\n";
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= $createTableResult['Create Table'] . ";\n\n";

            $rowsResult = $pdo->query("SELECT * FROM `{$table}`");
            $rows = $rowsResult->fetchAll(\PDO::FETCH_ASSOC);

            if (count($rows) > 0) {
                $sql .= "-- Dumping data for table `{$table}`\n";
                foreach ($rows as $row) {
                    $keys = array_map(fn($k) => "`{$k}`", array_keys($row));
                    $values = array_map(function($v) use ($pdo) {
                        if ($v === null) return 'NULL';
                        return $pdo->quote($v);
                    }, array_values($row));

                    $sql .= "INSERT INTO `{$table}` (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $values) . ");\n";
                }
            }
        }

        $sql .= "\nSET FOREIGN_KEY_CHECKS=1;\n";
        return $sql;
    }

    private function importDatabaseSql(string $sql)
    {
        // Hapus query yang menyentuh tabel sessions jika menggunakan file backup lama
        $sql = preg_replace('/DROP TABLE IF EXISTS `sessions`;/i', '', $sql);
        $sql = preg_replace('/CREATE TABLE `sessions` [^;]+;/i', '', $sql);
        $sql = preg_replace('/INSERT INTO `sessions` [^;]+;/i', '', $sql);

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::unprepared($sql);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
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
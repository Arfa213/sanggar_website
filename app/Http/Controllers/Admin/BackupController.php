<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleDrive;
use ZipArchive;
use Exception;

class BackupController extends Controller
{
    private function getGoogleClient()
    {
        $serviceAccountPath = env('GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON');

        if (!$serviceAccountPath || !file_exists($serviceAccountPath)) {
            // Cek di root project jika file ditaruh di root
            $rootPath = base_path($serviceAccountPath ?: 'service-account.json');
            if (file_exists($rootPath)) {
                $serviceAccountPath = $rootPath;
            } else {
                return null;
            }
        }

        try {
            $client = new GoogleClient();
            $client->setAuthConfig($serviceAccountPath);
            $client->addScope(GoogleDrive::DRIVE);
            return $client;
        } catch (Exception $e) {
            Log::error('Gagal inisialisasi Google Client: ' . $e->getMessage());
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
                    $backups[] = [
                        'id' => $file->getId(),
                        'name' => $file->getName(),
                        'size' => $this->formatBytes($file->getSize()),
                        'created_at' => date('Y-m-d H:i:s', strtotime($file->getCreatedTime())),
                        'source' => 'Google Drive'
                    ];
                }
                $googleDriveConnected = true;
            } catch (Exception $e) {
                Log::error('Error list file Google Drive: ' . $e->getMessage());
                session()->now('warning', 'Gagal memuat daftar backup dari Google Drive: ' . $e->getMessage());
            }
        }

        // Fallback/gabungkan dengan local backup
        $localPath = storage_path('app/backups');
        if (!file_exists($localPath)) {
            mkdir($localPath, 0755, true);
        }

        $localFiles = glob($localPath . '/*.zip');
        foreach ($localFiles as $file) {
            $filename = basename($file);
            // Hindari duplikat jika namanya sama
            $existsInGoogle = collect($backups)->contains('name', $filename);
            if (!$existsInGoogle) {
                $backups[] = [
                    'id' => $filename,
                    'name' => $filename,
                    'size' => $this->formatBytes(filesize($file)),
                    'created_at' => date('Y-m-d H:i:s', filemtime($file)),
                    'source' => 'Lokal (Server)'
                ];
            }
        }

        // Sort descending by date
        usort($backups, function ($a, $b) {
            return strcmp($b['created_at'], $a['created_at']);
        });

        return view('admin.dashboard.backup', compact('backups', 'googleDriveConnected'));
    }

    public function backup()
    {
        try {
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

            // Tambahkan database SQL ke zip
            $zip->addFromString('database.sql', $sqlContent);

            // Tambahkan folder storage/app/public ke zip
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

            // 3. Upload ke Google Drive jika dikonfigurasi
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
                    // Hapus file zip lokal setelah di-upload agar hemat space jika mau, 
                    // tapi biarkan local backup tersimpan juga sebagai cadangan ganda.
                }
            }

            $message = $uploadedToDrive 
                ? "Backup berhasil dibuat dan diunggah ke Google Drive!" 
                : "Backup berhasil dibuat secara lokal! (Google Drive tidak terhubung)";

            return redirect()->back()->with('success', $message);

        } catch (Exception $e) {
            Log::error('Backup failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat backup: ' . $e->getMessage());
        }
    }

    public function download($file_name)
    {
        // Sesuaikan nama disk dengan config laravel-backup kamu (biasanya 'local' atau 'public')
        $disk = Storage::disk('local'); 
        $filePath = config('backup.backup.name') . '/' . $file_name;

        if ($disk->exists($filePath)) {
            return Storage::download($filePath);
        }

        return back()->with('error', 'File cadangan tidak ditemukan.');
    }
    
    public function restore($filename)
    {
        try {
            $backupPath = storage_path("app/backups");
            $zipPath = $backupPath . '/' . basename($filename);

            // 1. Download dari Google Drive jika file tidak ada di lokal
            if (!file_exists($zipPath)) {
                $client = $this->getGoogleClient();
                if (!$client) {
                    throw new Exception("File backup tidak ditemukan secara lokal dan Google Drive tidak terhubung.");
                }

                $service = new GoogleDrive($client);
                // Cari file ID dari Google Drive berdasarkan nama file
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

            // 2. Ekstrak file Zip
            $zip = new ZipArchive();
            if ($zip->open($zipPath) !== true) {
                throw new Exception("Gagal membuka file backup zip.");
            }

            // Dapatkan isi database.sql
            $sqlContent = $zip->getFromName('database.sql');
            if (!$sqlContent) {
                throw new Exception("File database.sql tidak ditemukan di dalam backup.");
            }

            // Ekstrak folder storage/ ke storage/app/public
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                if (str_starts_with($stat['name'], 'storage/')) {
                    $relativePath = substr($stat['name'], 8); // potong 'storage/'
                    $destPath = storage_path('app/public/' . $relativePath);
                    
                    $dir = dirname($destPath);
                    if (!file_exists($dir)) {
                        mkdir($dir, 0755, true);
                    }
                    
                    file_put_contents($destPath, $zip->getFromIndex($i));
                }
            }
            $zip->close();

            // 3. Impor Database SQL secara native (Aman dari pembatasan server)
            $this->importDatabaseSql($sqlContent);

            return redirect()->back()->with('success', "Restore berhasil! Database dan folder storage lokal telah dikembalikan.");

        } catch (Exception $e) {
            Log::error('Restore failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memulihkan backup: ' . $e->getMessage());
        }
    }

    public function delete($file_name)
    {
        $disk = Storage::disk('local');
        $filePath = config('backup.backup.name') . '/' . $file_name;

        if ($disk->exists($filePath)) {
            $disk->delete($filePath);
            return back()->with('success', 'File cadangan berhasil dihapus.');
        }

        return back()->with('error', 'File cadangan gagal dihapus atau tidak ditemukan.');
    }

    private function exportDatabaseSql(): string
    {
        $pdo = DB::connection()->getPdo();
        $tables = [];
        
        $result = $pdo->query("SHOW TABLES");
        while ($row = $result->fetch(\PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        $sql = "-- Backup Database Sanggar Mulya Bhakti\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            // Skip tables that hold sessions or cache if you want, but backup everything by default
            $createTableResult = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_ASSOC);
            $sql .= "\n\n-- Table structure for table `{$table}`\n";
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
        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET FOREIGN_KEY_CHECKS=0");

        // Pisahkan query berdasarkan titik koma (;)
        // Gunakan regex agar meminimalkan pemisahan di dalam string values
        $queries = preg_split("/;(?=(?:[^'\"]|'[^']*'|\"[^\"]*\")*$)/", $sql);

        foreach ($queries as $query) {
            $query = trim($query);
            if ($query) {
                try {
                    $pdo->exec($query);
                } catch (Exception $e) {
                    Log::warning("Gagal eksekusi query restore: " . $query . ". Error: " . $e->getMessage());
                }
            }
        }

        $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
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

-- =====================================================
-- SQL MANUAL untuk migrasi baru Sanggar Mulya Bhakti
-- Jalankan di phpMyAdmin atau MySQL CLI jika php artisan migrate gagal
-- =====================================================

-- 1. Tambah kolom jenis_kegiatan ke tabel tarian
ALTER TABLE `tarian` 
ADD COLUMN `jenis_kegiatan` ENUM('tari','gamelan','drama','srimpi') NOT NULL DEFAULT 'tari' 
AFTER `kategori`;

-- 2. Tambah kolom tipe anggota ke tabel users
ALTER TABLE `users`
ADD COLUMN `tipe_anggota` ENUM('anggota_tetap','pengunjung','private') NOT NULL DEFAULT 'anggota_tetap' AFTER `role`,
ADD COLUMN `tanggal_keluar` DATE NULL AFTER `tipe_anggota`,
ADD COLUMN `catatan_keanggotaan` VARCHAR(500) NULL AFTER `tanggal_keluar`;

-- 3. Tambah kolom barcode ke tabel kehadiran
ALTER TABLE `kehadiran`
ADD COLUMN `barcode_token` VARCHAR(64) NULL AFTER `dicatat_oleh`,
ADD COLUMN `scan_at` TIMESTAMP NULL AFTER `barcode_token`,
ADD COLUMN `metode_absen` ENUM('manual','barcode') NOT NULL DEFAULT 'manual' AFTER `scan_at`;

-- 4. Buat tabel sesi_kehadiran (untuk QR Code)
CREATE TABLE IF NOT EXISTS `sesi_kehadiran` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `jadwal_id` BIGINT UNSIGNED NOT NULL,
    `tarian_id` BIGINT UNSIGNED NOT NULL,
    `tanggal` DATE NOT NULL,
    `barcode_token` VARCHAR(64) NOT NULL UNIQUE,
    `aktif` TINYINT(1) NOT NULL DEFAULT 1,
    `expires_at` TIMESTAMP NULL,
    `dibuat_oleh` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    CONSTRAINT `fk_sesi_jadwal` FOREIGN KEY (`jadwal_id`) REFERENCES `jadwal_latihan`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_sesi_tarian` FOREIGN KEY (`tarian_id`) REFERENCES `tarian`(`id`) ON DELETE CASCADE
);

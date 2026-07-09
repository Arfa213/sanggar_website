-- =====================================================
-- MANUAL MIGRATION: ujian_pendaftaran
-- Run this SQL on the production server if artisan
-- migrate cannot be run directly.
-- =====================================================

CREATE TABLE IF NOT EXISTS `ujian_pendaftaran` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `event_id` BIGINT UNSIGNED NOT NULL,
    `tarian_id` BIGINT UNSIGNED NOT NULL,
    `status` ENUM('menunggu', 'diterima', 'ditolak') NOT NULL DEFAULT 'menunggu',
    `persen_kehadiran` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `catatan` TEXT NULL,
    `catatan_admin` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY `ujian_pendaftaran_unique` (`user_id`, `event_id`, `tarian_id`),
    CONSTRAINT `fk_ujian_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ujian_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ujian_tarian` FOREIGN KEY (`tarian_id`) REFERENCES `tarian` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Also record in migrations table so artisan doesn't re-run it
INSERT IGNORE INTO `migrations` (`migration`, `batch`)
VALUES ('2026_07_09_131506_create_ujian_pendaftaran_table', (SELECT COALESCE(MAX(batch), 0) + 1 FROM `migrations` m2));

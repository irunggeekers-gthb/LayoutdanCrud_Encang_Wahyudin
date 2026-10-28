-- =======================================================
-- Database Schema & Sample Data: Sistem Informasi Akademik
-- Modul: Pemrograman Web 1 (CRUD PHP PDO & Templating Layout)
-- =======================================================

CREATE DATABASE IF NOT EXISTS `akademik` 
  DEFAULT CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE `akademik`;

-- Tabel: mahasiswa
DROP TABLE IF EXISTS `mahasiswa`;
CREATE TABLE `mahasiswa` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nim` VARCHAR(20) NOT NULL UNIQUE,
    `nama` VARCHAR(100) NOT NULL,
    `prodi` VARCHAR(100) NOT NULL,
    `angkatan` YEAR NOT NULL,
    `foto` VARCHAR(255) DEFAULT NULL,
    `berkas` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data Awal / Sampel Mahasiswa
INSERT INTO `mahasiswa` (`id`, `nim`, `nama`, `prodi`, `angkatan`, `foto`, `berkas`) VALUES
(1, '230101001', 'Ahmad Pratama', 'Teknik Informatika', 2023, NULL, NULL),
(2, '230101002', 'Siti Nurhaliza', 'Sistem Informasi', 2023, NULL, NULL),
(3, '220101015', 'Budi Santoso', 'Teknik Informatika', 2022, NULL, NULL),
(4, '240101008', 'Dewi Lestari', 'Manajemen Informatika', 2024, NULL, NULL),
(5, '240101021', 'Rian Hidayat', 'Teknik Komputer', 2024, NULL, NULL),
(6, '210101044', 'Anisa Rahmawati', 'Sistem Informasi', 2021, NULL, NULL),
(7, '220101033', 'Fajar Ramadhan', 'Teknik Informatika', 2022, NULL, NULL),
(8, '230101056', 'Putri Ayu', 'Manajemen Informatika', 2023, NULL, NULL);

-- Tabel: users (Autentikasi: Login & Register)
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nama` VARCHAR(100) NOT NULL,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` VARCHAR(20) DEFAULT 'admin',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data Awal / Akun Administrator Default (Password: admin123)
INSERT INTO `users` (`id`, `nama`, `username`, `password`, `role`) VALUES
(1, 'Administrator Horizon', 'admin', '$2y$10$Bc/MxJa9yxsLKqq5dlj0lOi6joTkSidb23uOz.tPVfdLOsm73srzS', 'admin');

<?php
/**
 * ==============================================================================
 * File: koneksi.php
 * Fungsi: Mengatur koneksi ke database MySQL menggunakan PDO (PHP Data Objects)
 * Modul: Pemrograman Web 1 - CRUD PHP PDO & Templating Layout
 * ==============================================================================
 */

// 1. Variabel konfigurasi koneksi database MySQL lokal (Laragon / XAMPP)
$host = "localhost"; // Nama host server database
$user = "root";      // Username default MySQL
$pass = "";          // Password default MySQL (kosong di Laragon)
$db   = "akademik";  // Nama database yang digunakan

try {
    // 2. Inisialisasi objek PDO untuk menghubungkan PHP ke MySQL
    // - DSN (Data Source Name): mendefinisikan host, dbname, dan charset utf8mb4
    // - ATTR_ERRMODE: melempar PDOException jika terjadi kesalahan query SQL
    // - ATTR_DEFAULT_FETCH_MODE: hasil pengambilan data otomatis berupa Array Asosiatif
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

} catch (PDOException $e) {
    // 3. Mekanisme Auto-Setup jika database 'akademik' belum dibuat di MySQL
    try {
        // Hubungkan ke MySQL server tanpa memilih database terlebih dahulu
        $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
        
        // Buat database 'akademik' secara otomatis jika belum ada
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        // Gunakan database 'akademik'
        $pdo->exec("USE `$db`");
        
        // Buat tabel 'mahasiswa' secara otomatis sesuai skema modul
        $pdo->exec("CREATE TABLE IF NOT EXISTS `mahasiswa` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nim VARCHAR(20) NOT NULL UNIQUE,
            nama VARCHAR(100) NOT NULL,
            prodi VARCHAR(100) NOT NULL,
            angkatan YEAR NOT NULL,
            foto VARCHAR(255) DEFAULT NULL,
            berkas VARCHAR(255) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Sambungkan kembali objek PDO ke database 'akademik' yang baru dibuat
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

    } catch (PDOException $ex) {
        // Hentikan program dan tampilkan pesan kesalahan jika MySQL server mati
        die("Koneksi Database Gagal! Pastikan MySQL di Laragon sudah aktif (Start All): " . $ex->getMessage());
    }
}

// 4. Auto-Migration: Pastikan kolom 'foto' dan 'berkas' tersedia di tabel mahasiswa
try {
    $cols = $pdo->query("SHOW COLUMNS FROM `mahasiswa`")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('foto', $cols)) {
        $pdo->exec("ALTER TABLE `mahasiswa` ADD COLUMN `foto` VARCHAR(255) DEFAULT NULL AFTER `angkatan`");
    }
    if (!in_array('berkas', $cols)) {
        $pdo->exec("ALTER TABLE `mahasiswa` ADD COLUMN `berkas` VARCHAR(255) DEFAULT NULL AFTER `foto`");
    }
} catch (PDOException $eMigrate) {
    // Abaikan jika tabel mahasiswa belum ada saat inisialisasi awal
}

// 4. Mekanisme Auto-Setup Tabel Users untuk Autentikasi (Login & Register)
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `nama` VARCHAR(100) NOT NULL,
        `username` VARCHAR(50) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `role` VARCHAR(20) DEFAULT 'admin',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Pastikan akun admin default tersedia jika tabel users masih kosong (Password: admin123)
    $checkUser = $pdo->query("SELECT COUNT(*) FROM `users`")->fetchColumn();
    if ($checkUser == 0) {
        $defaultPass = password_hash('admin123', PASSWORD_DEFAULT);
        $stmtUser = $pdo->prepare("INSERT INTO `users` (`nama`, `username`, `password`, `role`) VALUES (:nama, :username, :password, :role)");
        $stmtUser->execute([
            'nama'     => 'Administrator Horizon',
            'username' => 'admin',
            'password' => $defaultPass,
            'role'     => 'admin'
        ]);
    }
} catch (PDOException $eUser) {
    // Abaikan galat jika tabel sudah siap
}

// 5. Interceptor Notifikasi SweetAlert2 (Gaya Web Resmi Pemerintah)
// Mentransformasi alert() standar JavaScript menjadi modal dialog SweetAlert2 modern
// tanpa perlu mengubah baris kode logika pada proses.php, delete.php, dll.
if (!ob_get_level()) {
    ob_start(function($buffer) {
        // Intersepsi alert() dengan redirect window.location
        if (preg_match("/alert\(\s*['\"](.*?)['\"]\s*\);\s*(?:window\.)?location(?:\.href)?\s*=\s*['\"](.*?)['\"];/s", $buffer, $matches)) {
            $msg = htmlspecialchars_decode($matches[1], ENT_QUOTES);
            $target = $matches[2];
            $isError = preg_match("/(gagal|melebihi|tidak didukung|tidak ditemukan|error)/i", $msg);
            $icon = $isError ? "error" : "success";
            $title = $isError ? "Terjadi Kesalahan!" : "Berhasil!";
            $btnColor = $isError ? "#ef4444" : "#2563eb";
            $msgEscaped = addslashes($msg);

            return <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIAKAD - Notifikasi Sistem</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background-color: #f1f5f9; margin: 0; min-height: 100vh; }
        .swal2-popup { font-family: inherit !important; border-radius: 12px !important; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05) !important; }
        .swal2-title { font-weight: 700 !important; font-size: 1.35rem !important; color: #1e293b !important; }
        .swal2-html-container { font-size: 0.95rem !important; color: #475569 !important; }
        .swal2-confirm { padding: 0.6rem 1.75rem !important; font-weight: 600 !important; font-size: 0.875rem !important; border-radius: 6px !important; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
document.addEventListener("DOMContentLoaded", function() {
    Swal.fire({
        title: "{$title}",
        text: "{$msgEscaped}",
        icon: "{$icon}",
        confirmButtonColor: "{$btnColor}",
        confirmButtonText: "OK",
        allowOutsideClick: false
    }).then(function() {
        window.location.href = "{$target}";
    });
});
</script>
</body>
</html>
HTML;
        }

        // Intersepsi alert() dengan window.history.back()
        if (preg_match("/alert\(\s*['\"](.*?)['\"]\s*\);\s*(?:window\.)?history\.back\(\);/s", $buffer, $matches)) {
            $msg = htmlspecialchars_decode($matches[1], ENT_QUOTES);
            $msgEscaped = addslashes($msg);
            return <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIAKAD - Peringatan Sistem</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background-color: #f1f5f9; margin: 0; min-height: 100vh; }
        .swal2-popup { font-family: inherit !important; border-radius: 12px !important; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05) !important; }
        .swal2-title { font-weight: 700 !important; font-size: 1.35rem !important; color: #1e293b !important; }
        .swal2-html-container { font-size: 0.95rem !important; color: #475569 !important; }
        .swal2-confirm { padding: 0.6rem 1.75rem !important; font-weight: 600 !important; font-size: 0.875rem !important; border-radius: 6px !important; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
document.addEventListener("DOMContentLoaded", function() {
    Swal.fire({
        title: "Peringatan!",
        text: "{$msgEscaped}",
        icon: "warning",
        confirmButtonColor: "#ef4444",
        confirmButtonText: "Kembali",
        allowOutsideClick: false
    }).then(function() {
        window.history.back();
    });
});
</script>
</body>
</html>
HTML;
        }

        return $buffer;
    });
}


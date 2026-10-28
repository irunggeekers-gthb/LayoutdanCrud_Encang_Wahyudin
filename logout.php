<?php
/**
 * ==============================================================================
 * File: logout.php
 * Fungsi: Menghapus Sesi Login Pengguna dan Mengalihkan ke Halaman Login
 * Modul: Pemrograman Web 1 - Autentikasi & CRUD PHP PDO
 * ==============================================================================
 */

session_start();

// Mengosongkan seluruh variabel sesi
$_SESSION = [];

// Menghapus cookie sesi jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Menghancurkan sesi server
session_destroy();

// Mengarahkan pengguna kembali ke login.php dengan notifikasi sukses
header("Location: login.php?msg=logout");
exit;
?>

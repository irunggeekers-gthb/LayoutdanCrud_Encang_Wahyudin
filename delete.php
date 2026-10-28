<?php
/**
 * ==============================================================================
 * File: delete.php
 * Fungsi: Menghapus (DELETE) Data Mahasiswa Berdasarkan ID & Hapus File Fisik
 * Modul: Pemrograman Web 1 - CRUD PHP PDO & Templating Layout
 * ==============================================================================
 */

// 1. Memulai Sesi PHP dan Proteksi Autentikasi Pengguna
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: login.php?msg=need_login");
    exit;
}

// 2. Menyisipkan file koneksi database PDO
require "koneksi.php";

// 2. Menangkap parameter ID mahasiswa dari parameter URL GET atau POST
$id = $_GET['id'] ?? $_POST['id'] ?? null;

// 3. Validasi Keberadaan ID
if (!empty($id)) {
    try {
        $uploadDir = __DIR__ . '/uploads/';

        // Ambil nama file foto dan berkas sebelum dihapus dari database
        $stmtCheck = $pdo->prepare("SELECT foto, berkas FROM mahasiswa WHERE id = :id");
        $stmtCheck->execute(['id' => $id]);
        $row = $stmtCheck->fetch();

        if ($row) {
            // Hapus file fisik foto jika ada
            if (!empty($row['foto']) && file_exists($uploadDir . $row['foto'])) {
                @unlink($uploadDir . $row['foto']);
            }
            // Hapus file fisik berkas jika ada
            if (!empty($row['berkas']) && file_exists($uploadDir . $row['berkas'])) {
                @unlink($uploadDir . $row['berkas']);
            }
        }

        // Menyiapkan sintaks SQL DELETE dengan klausa WHERE id = :id
        $sql = "DELETE FROM mahasiswa WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        // Menampilkan pesan sukses dan mengarahkan kembali ke index.php
        echo "<script>
                alert('Data mahasiswa beserta file terkait berhasil dihapus!');
                window.location='index.php';
              </script>";
        exit;

    } catch (PDOException $e) {
        // Menampilkan pesan error jika query gagal
        echo "<script>
                alert('Gagal menghapus data: " . addslashes($e->getMessage()) . "');
                window.location='index.php';
              </script>";
        exit;
    }
} else {
    // Jika ID tidak disertakan, alihkan langsung ke halaman utama
    echo "<script>
            alert('ID mahasiswa tidak ditemukan atau tidak valid!');
            window.location='index.php';
          </script>";
    exit;
}
?>

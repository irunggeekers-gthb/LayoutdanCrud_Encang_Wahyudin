<?php
/**
 * ==============================================================================
 * File: proses.php
 * Fungsi: Pengendali Utama (Controller) Transaksi Database CRUD (Create, Update, Delete)
 *         Beserta Fitur Upload Foto & Upload Berkas (Batas Ukuran Hingga 4 MB)
 * Modul: Pemrograman Web 1 - CRUD PHP Native Menggunakan PDO
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

// Pastikan folder uploads tersedia
$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Batas ukuran file maksimum: 4 MB (4 * 1024 * 1024 byte)
define('MAX_UPLOAD_SIZE', 4 * 1024 * 1024);

// Fungsi pembantu untuk memvalidasi dan memindahkan file upload
function handleFileUpload(string $fileInputName, array $allowedExtensions, string $prefix = 'file_') {
    global $uploadDir;
    if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // Tidak ada file yang diunggah
    }

    $file = $_FILES[$fileInputName];

    // Cek error bawaan PHP
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo "<script>
                alert('Gagal mengunggah file " . htmlspecialchars($fileInputName) . ". Kode error: " . $file['error'] . "');
                window.history.back();
              </script>";
        exit;
    }

    // Validasi batas ukuran file (Maksimal 4 MB)
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        echo "<script>
                alert('Ukuran file " . htmlspecialchars($fileInputName) . " melebihi batas maksimal 4 MB!');
                window.history.back();
              </script>";
        exit;
    }

    // Validasi ekstensi file
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExt, $allowedExtensions)) {
        echo "<script>
                alert('Format file " . htmlspecialchars($fileInputName) . " tidak didukung! Format yang diperbolehkan: " . implode(', ', $allowedExtensions) . "');
                window.history.back();
              </script>";
        exit;
    }

    // Buat nama file unik untuk mencegah bentrokan nama
    $uniqueName = $prefix . uniqid() . '_' . time() . '.' . $fileExt;
    $destination = $uploadDir . $uniqueName;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $uniqueName;
    } else {
        echo "<script>
                alert('Gagal memindahkan file yang diunggah ke direktori tujuan!');
                window.history.back();
              </script>";
        exit;
    }
}

// Menangkap parameter 'action' yang dikirim melalui POST (dari form) atau GET (dari link hapus)
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// ==============================================================================
// 3. PROSES CREATE (Tambah Data Mahasiswa Baru + Upload Foto & Berkas)
// ==============================================================================
if ($action == 'create') {
    // Menangkap data inputan form dari array $_POST
    $nim      = trim($_POST['nim'] ?? '');
    $nama     = trim($_POST['nama'] ?? '');
    $prodi    = trim($_POST['prodi'] ?? '');
    $angkatan = trim($_POST['angkatan'] ?? '');

    // Proses upload Foto (JPG, JPEG, PNG, WEBP) batas 4 MB
    $foto = handleFileUpload('foto', ['jpg', 'jpeg', 'png', 'webp'], 'foto_');

    // Proses upload Berkas / Dokumen (PDF, DOC, DOCX, ZIP, XLS, XLSX) batas 4 MB
    $berkas = handleFileUpload('berkas', ['pdf', 'doc', 'docx', 'zip', 'xls', 'xlsx'], 'berkas_');

    // Menyiapkan sintaks SQL INSERT
    $sql = "INSERT INTO mahasiswa
            (nim, nama, prodi, angkatan, foto, berkas)
            VALUES
            (:nim, :nama, :prodi, :angkatan, :foto, :berkas)";

    // Menyiapkan Prepared Statement PDO
    $stmt = $pdo->prepare($sql);

    // Mengeksekusi query dengan menyuntikkan array parameter secara aman (anti SQL Injection)
    $stmt->execute([
        'nim'      => $nim,
        'nama'     => $nama,
        'prodi'    => $prodi,
        'angkatan' => $angkatan,
        'foto'     => $foto,
        'berkas'   => $berkas
    ]);

    // Menampilkan popup alert sukses dan mengarahkan kembali ke index.php
    echo "<script>
            alert('Data mahasiswa berhasil ditambahkan!');
            window.location='index.php';
          </script>";
    exit;

// ==============================================================================
// 4. PROSES UPDATE (Mengubah Data Mahasiswa yang Sudah Ada + Ganti File)
// ==============================================================================
} elseif ($action == 'update') {
    // Menangkap ID dan data isian baru dari array $_POST
    $id        = $_POST['id'];
    $nim       = trim($_POST['nim'] ?? '');
    $nama      = trim($_POST['nama'] ?? '');
    $prodi     = trim($_POST['prodi'] ?? '');
    $angkatan  = trim($_POST['angkatan'] ?? '');
    $oldFoto   = $_POST['old_foto'] ?? null;
    $oldBerkas = $_POST['old_berkas'] ?? null;

    // Cek upload foto baru
    $newFoto = handleFileUpload('foto', ['jpg', 'jpeg', 'png', 'webp'], 'foto_');
    if ($newFoto) {
        // Hapus foto lama jika ada
        if (!empty($oldFoto) && file_exists($uploadDir . $oldFoto)) {
            @unlink($uploadDir . $oldFoto);
        }
        $fotoFinal = $newFoto;
    } else {
        $fotoFinal = $oldFoto;
    }

    // Cek upload berkas baru
    $newBerkas = handleFileUpload('berkas', ['pdf', 'doc', 'docx', 'zip', 'xls', 'xlsx'], 'berkas_');
    if ($newBerkas) {
        // Hapus berkas lama jika ada
        if (!empty($oldBerkas) && file_exists($uploadDir . $oldBerkas)) {
            @unlink($uploadDir . $oldBerkas);
        }
        $berkasFinal = $newBerkas;
    } else {
        $berkasFinal = $oldBerkas;
    }

    // Menyiapkan sintaks SQL UPDATE
    $sql = "UPDATE mahasiswa
            SET nim = :nim,
                nama = :nama,
                prodi = :prodi,
                angkatan = :angkatan,
                foto = :foto,
                berkas = :berkas
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'id'       => $id,
        'nim'      => $nim,
        'nama'     => $nama,
        'prodi'    => $prodi,
        'angkatan' => $angkatan,
        'foto'     => $fotoFinal,
        'berkas'   => $berkasFinal
    ]);

    // Menampilkan popup alert sukses dan mengarahkan kembali ke index.php
    echo "<script>
            alert('Data mahasiswa berhasil diubah!');
            window.location='index.php';
          </script>";
    exit;

// ==============================================================================
// 5. PROSES DELETE (Menghapus Data Mahasiswa dari Database & Hapus File Fisik)
// ==============================================================================
} elseif ($action == 'delete') {
    $id = $_GET['id'] ?? null;
    if (!empty($id)) {
        // Ambil info nama file foto & berkas yang tersimpan
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

        // Hapus baris dari tabel mahasiswa
        $sql = "DELETE FROM mahasiswa WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }

    echo "<script>
            alert('Data mahasiswa berhasil dihapus!');
            window.location='index.php';
          </script>";
    exit;

// ==============================================================================
// 6. DEFAULT / FALLBACK (Jika Aksi Tidak Dikenal atau Parameter Kosong)
// ==============================================================================
} else {
    echo "<script>
            alert('Aksi tidak dikenal!');
            window.location='index.php';
          </script>";
    exit;
}

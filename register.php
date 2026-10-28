<?php
/**
 * ==============================================================================
 * File: register.php
 * Fungsi: Halaman Pendaftaran Akun Pengguna Baru (Register) Menggunakan PDO
 * Modul: Pemrograman Web 1 - Autentikasi & CRUD PHP PDO
 * ==============================================================================
 */

// 1. Memulai Sesi PHP
session_start();

// 2. Jika pengguna sudah login, langsung arahkan ke index.php
if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

// 3. Menyisipkan file koneksi database PDO
require "koneksi.php";

$error = '';

// 4. Memproses formulir registrasi pengguna baru
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama             = trim($_POST['nama'] ?? '');
    $username         = trim($_POST['username'] ?? '');
    $password         = trim($_POST['password'] ?? '');
    $password_confirm = trim($_POST['password_confirm'] ?? '');

    // Validasi Kelengkapan Isian
    if (empty($nama) || empty($username) || empty($password) || empty($password_confirm)) {
        $error = "Seluruh kolom formulir pendaftaran wajib diisi!";
    } elseif (strlen($password) < 5) {
        $error = "Panjang password minimal harus 5 karakter!";
    } elseif ($password !== $password_confirm) {
        $error = "Konfirmasi password tidak cocok dengan password yang dimasukkan!";
    } else {
        // Cek apakah username sudah pernah didaftarkan sebelumnya
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
        $checkStmt->execute(['username' => $username]);
        $userExists = $checkStmt->fetchColumn();

        if ($userExists > 0) {
            $error = "Username '{$username}' sudah digunakan, silakan pilih username lain!";
        } else {
            // Enkripsi hash password menggunakan algoritma BCRYPT bawaan PHP
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Simpan akun pengguna baru ke tabel 'users'
            $insertSql = "INSERT INTO users (nama, username, password, role) VALUES (:nama, :username, :password, 'admin')";
            $insertStmt = $pdo->prepare($insertSql);
            $insertStmt->execute([
                'nama'     => $nama,
                'username' => $username,
                'password' => $hashedPassword
            ]);

            // Alihkan ke login.php dengan parameter sukses
            header("Location: login.php?msg=registered");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrasi Akun - SIAKAD HORIZON</title>

    <!-- Google Fonts: Inter -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- AdminLTE & Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1rem;
        }
        .register-card {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .register-header {
            text-align: center;
            padding: 2rem 1.5rem 1rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .register-brand-icon {
            width: 52px;
            height: 52px;
            background-color: #eef2ff;
            color: #4f46e5;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 1.5rem;
            margin-bottom: 0.75rem;
        }
        .register-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }
        .register-subtitle {
            font-size: 0.8125rem;
            color: #64748b;
        }
        .register-body {
            padding: 1.5rem 1.75rem 2rem 1.75rem;
        }
        .form-group label {
            font-size: 0.8125rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.35rem;
        }
        .input-group-text {
            background-color: #f8fafc;
            border-color: #cbd5e1;
            color: #64748b;
            font-size: 0.85rem;
        }
        .form-control {
            font-size: 0.85rem;
            border-color: #cbd5e1;
            height: calc(2.25rem + 2px);
        }
        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }
        .btn-register {
            background-color: #4f46e5;
            border-color: #4f46e5;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.875rem;
            padding: 0.6rem 1rem;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        .btn-register:hover {
            background-color: #4338ca;
            border-color: #4338ca;
            color: #ffffff;
        }
        .register-footer {
            background-color: #f8fafc;
            padding: 1rem 1.5rem;
            text-align: center;
            font-size: 0.8125rem;
            border-top: 1px solid #f1f5f9;
        }
    </style>
</head>
<body>

<div class="register-card">
    <!-- Header Kartu Registrasi -->
    <div class="register-header">
        <div class="register-brand-icon">
            <i class="fas fa-user-plus"></i>
        </div>
        <div class="register-title">Daftar Akun Baru</div>
        <div class="register-subtitle">SIAKAD HORIZON • Sistem Informasi Akademik</div>
    </div>

    <!-- Isi Formulir Registrasi -->
    <div class="register-body">
        <!-- Notifikasi Error jika validasi gagal -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 px-3 mb-3" style="font-size: 0.8rem; border-radius: 6px;">
                <i class="fas fa-exclamation-circle mr-1"></i> <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <!-- Kolom Nama Lengkap -->
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                    </div>
                    <input type="text" name="nama" id="nama" class="form-control" placeholder="Masukkan nama lengkap" required autofocus value="<?= htmlspecialchars($_POST['nama'] ?? ''); ?>">
                </div>
            </div>

            <!-- Kolom Username -->
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                    </div>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Pilih username unik" required value="<?= htmlspecialchars($_POST['username'] ?? ''); ?>">
                </div>
            </div>

            <!-- Kolom Password -->
            <div class="form-group">
                <label for="password">Password (minimal 5 karakter)</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    </div>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Buat password aman" required>
                </div>
            </div>

            <!-- Kolom Konfirmasi Password -->
            <div class="form-group mb-4">
                <label for="password_confirm">Konfirmasi Password</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-check-double"></i></span>
                    </div>
                    <input type="password" name="password_confirm" id="password_confirm" class="form-control" placeholder="Ulangi password" required>
                </div>
            </div>

            <!-- Tombol Submit Pendaftaran -->
            <button type="submit" class="btn btn-register btn-block">
                <i class="fas fa-user-check mr-1"></i> Daftar Sekarang
            </button>
        </form>
    </div>

    <!-- Footer Kartu: Tautan ke Halaman Login -->
    <div class="register-footer">
        Sudah memiliki akun? <a href="login.php" class="font-weight-bold text-primary">Masuk di sini</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (!empty($error)): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    Swal.fire({
        title: "Pendaftaran Gagal!",
        text: "<?= addslashes($error); ?>",
        icon: "warning",
        confirmButtonColor: "#4f46e5",
        confirmButtonText: "Perbaiki Isian"
    });
});
</script>
<?php endif; ?>
</body>
</html>

<?php
/**
 * ==============================================================================
 * File: login.php
 * Fungsi: Halaman Autentikasi Pengguna (Login) Menggunakan PHP Session & PDO
 * Modul: Pemrograman Web 1 - Autentikasi & CRUD PHP PDO
 * ==============================================================================
 */

// 1. Memulai Sesi PHP
session_start();

// 2. Jika pengguna sudah login sebelumnya, langsung arahkan ke halaman utama (index.php)
if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

// 3. Menyisipkan file koneksi database PDO
require "koneksi.php";

$error = '';
$success = '';

// 4. Menangkap pesan notifikasi dari parameter URL (misal setelah logout atau register)
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'logout') {
        $success = "Anda telah berhasil keluar dari sistem.";
    } elseif ($_GET['msg'] === 'registered') {
        $success = "Registrasi berhasil! Silakan masuk menggunakan akun baru Anda.";
    } elseif ($_GET['msg'] === 'need_login') {
        $error = "Silakan login terlebih dahulu untuk mengakses sistem!";
    }
}

// 5. Memproses pengiriman formulir login via metode HTTP POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = "Silakan masukkan username dan password Anda!";
    } else {
        // Query SELECT mencari user berdasarkan username dengan Prepared Statement
        $sql = "SELECT * FROM users WHERE username = :username LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        // Verifikasi kecocokan username dan hash password dengan password_verify()
        if ($user && password_verify($password, $user['password'])) {
            // Regenerasi ID sesi untuk mencegah serangan Session Fixation
            session_regenerate_id(true);

            // Menyimpan informasi identitas pengguna ke dalam sesi global PHP
            $_SESSION['is_logged_in'] = true;
            $_SESSION['user_id']      = $user['id'];
            $_SESSION['nama']         = $user['nama'];
            $_SESSION['username']     = $user['username'];
            $_SESSION['role']         = $user['role'];

            // Alihkan pengguna ke dashboard utama
            header("Location: index.php");
            exit;
        } else {
            $error = "Username atau password yang Anda masukkan salah!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - SIAKAD HORIZON</title>

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
        .login-card {
            width: 100%;
            max-width: 400px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .login-header {
            text-align: center;
            padding: 2rem 1.5rem 1rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .login-brand-icon {
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
        .login-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }
        .login-subtitle {
            font-size: 0.8125rem;
            color: #64748b;
        }
        .login-body {
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
        .btn-login {
            background-color: #4f46e5;
            border-color: #4f46e5;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.875rem;
            padding: 0.6rem 1rem;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        .btn-login:hover {
            background-color: #4338ca;
            border-color: #4338ca;
            color: #ffffff;
        }
        .login-footer {
            background-color: #f8fafc;
            padding: 1rem 1.5rem;
            text-align: center;
            font-size: 0.8125rem;
            border-top: 1px solid #f1f5f9;
        }
        .demo-box {
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 6px;
            padding: 0.65rem 0.85rem;
            font-size: 0.75rem;
            color: #475569;
            margin-top: 1rem;
        }
    </style>
</head>
<body>

<div class="login-card">
    <!-- Header Kartu Login -->
    <div class="login-header">
        <div class="login-brand-icon">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <div class="login-title">SIAKAD <strong>HORIZON</strong></div>
        <div class="login-subtitle">Sistem Informasi Akademik • Masuk Akun</div>
    </div>

    <!-- Isi Formulir Login -->
    <div class="login-body">
        <!-- Notifikasi Error jika login gagal -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 px-3 text-xs mb-3" style="font-size: 0.8rem; border-radius: 6px;">
                <i class="fas fa-exclamation-circle mr-1"></i> <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Notifikasi Sukses (setelah logout atau register) -->
        <?php if (!empty($success)): ?>
            <div class="alert alert-success py-2 px-3 text-xs mb-3" style="font-size: 0.8rem; border-radius: 6px;">
                <i class="fas fa-check-circle mr-1"></i> <?= htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <!-- Kolom Username -->
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                    </div>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan username" required autofocus value="<?= htmlspecialchars($_POST['username'] ?? ''); ?>">
                </div>
            </div>

            <!-- Kolom Password -->
            <div class="form-group mb-4">
                <label for="password">Password</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    </div>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
                </div>
            </div>

            <!-- Tombol Submit Login -->
            <button type="submit" class="btn btn-login btn-block">
                <i class="fas fa-sign-in-alt mr-1"></i> Masuk Sekarang
            </button>
        </form>

        <!-- Informasi Akun Default untuk Pengujian Praktikum -->
        <div class="demo-box text-center">
            <i class="fas fa-info-circle text-primary mr-1"></i> Akun Bawaan: <strong>admin</strong> | Password: <strong>admin123</strong>
        </div>
    </div>

    <!-- Footer Kartu: Tautan ke Pendaftaran Akun -->
    <div class="login-footer">
        Belum memiliki akun? <a href="register.php" class="font-weight-bold text-primary">Daftar Akun Baru</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (!empty($error)): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    Swal.fire({
        title: "Akses Ditolak!",
        text: "<?= addslashes($error); ?>",
        icon: "error",
        confirmButtonColor: "#ef4444",
        confirmButtonText: "Coba Lagi"
    });
});
</script>
<?php elseif (!empty($success)): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    Swal.fire({
        title: "Informasi",
        text: "<?= addslashes($success); ?>",
        icon: "success",
        confirmButtonColor: "#4f46e5",
        confirmButtonText: "OK"
    });
});
</script>
<?php endif; ?>
</body>
</html>

<?php
/**
 * ==============================================================================
 * File: layouts/header.php
 * Fungsi: Komponen layout bagian atas (Head HTML, Library CSS CDN, dan Top Navbar)
 * Prinsip: DRY (Don't Repeat Yourself) - Dipanggil menggunakan require_once
 * ==============================================================================
 */

// 1. Memulai Sesi PHP jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Proteksi Akses Halaman: Alihkan ke login.php jika pengguna belum terautentikasi
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: login.php?msg=need_login");
    exit;
}

// 3. Menangkap identitas pengguna yang sedang login
$loggedInUser = $_SESSION['nama'] ?? 'Administrator';
$userRole     = strtoupper($_SESSION['role'] ?? 'ADMIN');

// 4. Menentukan judul halaman untuk header atas secara dinamis
$currentPage = basename($_SERVER['PHP_SELF']);
$pageTitle = 'Data Mahasiswa';
if ($currentPage === 'create.php') {
    $pageTitle = 'Tambah Data Mahasiswa';
} elseif ($currentPage === 'edit.php') {
    $pageTitle = 'Edit Data Mahasiswa';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <!-- Pengaturan karakter encoding dokumen web agar mendukung karakter UTF-8 -->
    <meta charset="utf-8">
    <!-- Pengaturan viewport agar tampilan web responsif di HP, Tablet, dan Komputer -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Judul halaman pada tab browser -->
    <title>SIAKAD - CRUD PDO Mahasiswa</title>

    <!-- Memuat Google Fonts (Inter & Source Sans Pro) untuk tipografi modern dan rapi -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Source+Sans+Pro:wght@300;400;600;700&display=swap">
    <!-- Memuat ikon Font Awesome 6 melalui CDN untuk ikon tombol dan navigasi -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Memuat Framework CSS AdminLTE 3 dan Bootstrap 4 langsung via CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    <!-- Kustomisasi CSS: Tipografi lebih rapi, tombol proporsional, tabel ringkas & profesional -->
    <style>
        body, .main-header, .main-sidebar, .card, .table, .form-control, .btn {
            font-family: 'Inter', 'Source Sans Pro', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        /* Ukuran font dasar */
        body.text-sm {
            font-size: 0.8125rem; /* ~13px */
            color: #334155;
            background-color: #f4f6f9;
        }

        /* Navbar Bagian Atas Modern (Sesuai Desain Screenshot) */
        .main-header.navbar {
            min-height: 52px;
            padding: 0.45rem 1rem;
            background-color: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        /* Tombol Panah Buka/Tutup Sidebar di Header (Indigo/Ungu seperti screenshot) */
        .sidebar-toggle-arrow {
            color: #4f46e5 !important; /* Warna ungu/indigo sesuai screenshot */
            font-size: 1.15rem;
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: all 0.2s ease;
            text-decoration: none !important;
        }
        .sidebar-toggle-arrow:hover {
            background-color: #eef2ff;
            color: #4338ca !important;
        }

        /* Logika Ikon Panah Interaktif:
           - Saat sidebar terbuka: panah mengarah ke kiri (<-) untuk menciutkan
           - Saat sidebar tertutup (collapsed): panah mengarah ke kanan (->) persis seperti di screenshot */
        .sidebar-toggle-arrow .arrow-open {
            display: inline-block;
        }
        .sidebar-toggle-arrow .arrow-closed {
            display: none;
        }
        .sidebar-collapse .sidebar-toggle-arrow .arrow-open {
            display: none;
        }
        .sidebar-collapse .sidebar-toggle-arrow .arrow-closed {
            display: inline-block;
        }

        /* Teks Judul Halaman di samping tombol panah header */
        .header-page-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1e293b;
            letter-spacing: -0.2px;
        }

        /* Badge ADMIN sesuai screenshot */
        .badge-admin-pill {
            background-color: #e0e7ff;
            color: #4338ca;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 0.2rem 0.55rem;
            border-radius: 6px;
        }

        /* Header Judul Konten */
        .content-header {
            padding: 12px 0.5rem 6px 0.5rem;
        }
        .content-header h1 {
            font-size: 1.15rem !important;
            font-weight: 600;
            color: #1e293b;
        }

        /* Brand & Sidebar */
        .brand-link {
            font-size: 0.875rem !important;
            padding: 0.75rem 0.85rem !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        }
        .nav-sidebar .nav-link {
            font-size: 0.8125rem;
            padding: 0.45rem 0.75rem;
            border-radius: 4px;
        }
        .nav-sidebar .nav-link .nav-icon {
            font-size: 0.875rem;
            margin-right: 0.45rem;
        }

        /* Card / Kotak Konten */
        .card {
            box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.03);
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }
        .card-header {
            padding: 0.55rem 0.85rem;
            background-color: #ffffff;
            border-bottom: 1px solid #edf2f7;
        }
        .card-header::after {
            display: none !important;
        }
        .card-title {
            font-size: 0.9rem !important;
            font-weight: 600;
        }
        .card-body {
            padding: 0.85rem;
        }
        .card-footer {
            padding: 0.55rem 0.85rem;
            background-color: #f8fafc;
        }

        /* Tabel Data Mahasiswa - Rapi, Padat, & Bersih */
        .table-sm th,
        .table-sm td {
            padding: 0.45rem 0.65rem !important;
            vertical-align: middle !important;
            font-size: 0.8125rem;
        }
        .table thead th {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.35px;
            color: #475569;
            background-color: #f8fafc;
            border-top: none;
            border-bottom: 2px solid #e2e8f0;
        }
        .table-bordered td, .table-bordered th {
            border-color: #e2e8f0;
        }
        .table-hover tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Tombol - Ukuran Proporsional & Elegan */
        .btn {
            font-size: 0.8rem;
            font-weight: 500;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease-in-out;
        }
        .btn-sm {
            font-size: 0.75rem !important;
            padding: 0.28rem 0.65rem !important;
            line-height: 1.4 !important;
        }
        .btn-xs {
            font-size: 0.72rem !important;
            padding: 0.22rem 0.55rem !important;
            line-height: 1.35 !important;
            border-radius: 3px !important;
        }
        .btn i {
            font-size: 0.75rem;
        }
        .btn-xs i {
            font-size: 0.68rem;
            margin-right: 3px;
        }
        .btn-warning {
            background-color: #f59e0b;
            border-color: #f59e0b;
            color: #ffffff !important;
        }
        .btn-warning:hover {
            background-color: #d97706;
            border-color: #d97706;
            color: #ffffff !important;
        }
        .btn-danger {
            background-color: #ef4444;
            border-color: #ef4444;
        }
        .btn-danger:hover {
            background-color: #dc2626;
            border-color: #dc2626;
        }
        .btn-primary {
            background-color: #2563eb;
            border-color: #2563eb;
        }
        .btn-primary:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }
        .btn-secondary {
            background-color: #64748b;
            border-color: #64748b;
        }
        .btn-secondary:hover {
            background-color: #475569;
            border-color: #475569;
        }

        /* Form Inputs */
        .form-group label {
            font-size: 0.785rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.25rem;
        }
        .form-control {
            font-size: 0.8125rem;
            border-radius: 4px;
            border-color: #cbd5e1;
        }
        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15);
        }
        .input-group-sm > .form-control,
        .input-group-sm > .input-group-append > .btn {
            font-size: 0.775rem;
            padding: 0.25rem 0.55rem;
        }

        /* Footer */
        .main-footer {
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini text-sm">
<!-- Elemen wrapper utama pembungkus seluruh komponen layout AdminLTE -->
<div class="wrapper">

    <!-- Navbar Bagian Atas (Header Bar Dengan Tombol Toggle Panah & Judul Halaman) -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav d-flex align-items-center">
            <!-- Tombol Buka/Tutup Sidebar (Icon Panah Interaktif Seperti Screenshot) -->
            <li class="nav-item">
                <a class="nav-link sidebar-toggle-arrow p-0 mr-3" data-widget="pushmenu" href="#" role="button" title="Buka / Tutup Sidebar">
                    <!-- Saat sidebar terbuka: panah ke kiri (collapse) -->
                    <i class="fas fa-arrow-left arrow-open"></i>
                    <!-- Saat sidebar tertutup/mini: panah ke kanan (seperti di screenshot) -->
                    <i class="fas fa-arrow-right arrow-closed"></i>
                </a>
            </li>
            <!-- Judul Halaman dan Badge ADMIN di Header Atas -->
            <li class="nav-item d-flex align-items-center">
                <span class="header-page-title"><?= $pageTitle; ?></span>
                <span class="badge badge-admin-pill ml-2">ADMIN</span>
            </li>
        </ul>

        <!-- Menu Kanan Navbar (Informasi Akun Pengguna & Tombol Logout) -->
        <ul class="navbar-nav ml-auto d-flex align-items-center">
            <li class="nav-item mr-3 d-none d-sm-inline-flex align-items-center text-muted">
                <i class="fas fa-user-circle mr-1.5 text-primary" style="font-size: 1.15rem;"></i>
                <span class="font-weight-600 text-dark mr-1" style="font-size: 0.8rem;"><?= htmlspecialchars($loggedInUser); ?></span>
                <span class="badge badge-light border text-uppercase" style="font-size: 0.65rem; padding: 0.2rem 0.4rem;"><?= htmlspecialchars($_SESSION['username'] ?? 'admin'); ?></span>
            </li>
            <li class="nav-item">
                <a href="logout.php" class="btn btn-outline-danger btn-xs px-2.5 py-1" onclick="return confirm('Apakah Anda yakin ingin keluar (logout)?');" title="Keluar dari Sistem">
                    <i class="fas fa-sign-out-alt mr-1"></i> Logout
                </a>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->


<?php
/**
 * ==============================================================================
 * File: layouts/sidebar.php
 * Fungsi: Komponen layout navigasi bilah samping (Sidebar Navigation) AdminLTE
 * Prinsip: DRY - Menu samping modular dengan deteksi otomatis halaman yang aktif
 * ==============================================================================
 */

// Mengambil nama file script PHP yang sedang aktif dibuka di browser (misal: 'index.php')
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!-- Kontainer Sidebar Utama AdminLTE (Warna Gelap / Dark Mode) -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Logo & Nama Brand SIAKAD HORIZON -->
    <a href="index.php" class="brand-link">
        <i class="fas fa-graduation-cap ml-3 mr-2 text-primary" style="font-size: 1.15rem;"></i>
        <span class="brand-text font-weight-light">SIAKAD <strong>HORIZON</strong></span>
    </a>

    <!-- Area Navigasi Sidebar -->
    <div class="sidebar">
        <!-- Panel Info Pengguna Aktif (User Panel) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
            <div class="image text-white pl-2">
                <i class="fas fa-user-circle fa-2x text-info"></i>
            </div>
            <div class="info">
                <a href="index.php" class="d-block font-weight-bold" style="font-size: 0.83rem;">
                    <?= htmlspecialchars($_SESSION['nama'] ?? 'Admin SIAKAD'); ?>
                </a>
                <span class="text-xs text-success"><i class="fas fa-circle text-xs mr-1" style="font-size: 0.55rem;"></i> Online</span>
            </div>
        </div>

        <!-- Menu Navigasi Samping -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column text-sm" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-header text-uppercase text-xs text-muted" style="letter-spacing: 0.5px;">Menu Utama</li>
                <!-- Menu Data Mahasiswa: aktif saat di index.php atau edit.php -->
                <li class="nav-item">
                    <a href="index.php" class="nav-link <?= in_array($currentPage, ['index.php', 'edit.php']) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Data Mahasiswa</p>
                    </a>
                </li>
                <!-- Menu Tambah Data Mahasiswa: aktif saat di create.php -->
                <li class="nav-item">
                    <a href="create.php" class="nav-link <?= $currentPage === 'create.php' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-user-plus"></i>
                        <p>Tambah Mahasiswa</p>
                    </a>
                </li>

                <!-- Menu Akun & Otentikasi -->
                <li class="nav-header text-uppercase text-xs text-muted mt-2" style="letter-spacing: 0.5px;">Akun & Sesi</li>
                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-danger" onclick="return confirm('Apakah Anda yakin ingin keluar (logout)?');">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Keluar (Logout)</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

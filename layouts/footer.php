<?php
/**
 * ==============================================================================
 * File: layouts/footer.php
 * Fungsi: Komponen layout penutup bawah (Footer & Library JavaScript AdminLTE CDN)
 * Prinsip: DRY - Menutup wrapper HTML dan memuat pustaka JavaScript
 * ==============================================================================
 */
?>
    <!-- Bagian Footer Bawah Halaman -->
    <footer class="main-footer">
        <!-- Teks informasi teknologi di sisi kanan -->
        <div class="float-right d-none d-sm-inline text-muted">
            <i class="fas fa-code mr-1"></i> PHP Native + PDO & AdminLTE
        </div>
        <!-- Teks Hak Cipta & Universitas di sisi kiri -->
        <strong>Copyright &copy; <?= date('Y'); ?> <a href="#">Horizon University</a>.</strong> All rights reserved.
    </footer>
</div>
<!-- Penutup tag <div class="wrapper"> utama -->

<!-- PUSTAKA JAVASCRIPT (REQUIRED SCRIPTS) -->
<!-- 1. Pustaka jQuery untuk manipulasi DOM dan AJAX -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- 2. Pustaka Bootstrap 4 Bundle (termasuk Popper.js) untuk komponen interaktif -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- 3. Pustaka Utama AdminLTE 3 untuk kontrol interaktivitas layout dan sidebar -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<!-- 4. Pustaka SweetAlert2 untuk modal dialog notifikasi elegan (Standar Web Resmi Pemerintah) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    <?php if (!empty($_SESSION['is_logged_in']) && empty($_SESSION['login_welcome_shown'])): ?>
    <?php $_SESSION['login_welcome_shown'] = true; ?>
    // Popup Notifikasi Berhasil Login
    Swal.fire({
        title: 'Login Berhasil!',
        text: 'Selamat datang kembali, <?= htmlspecialchars($_SESSION['nama'] ?? 'Administrator'); ?>!',
        icon: 'success',
        confirmButtonColor: '#2563eb',
        confirmButtonText: '<i class="fas fa-check mr-1"></i> Masuk ke Sistem',
        customClass: {
            popup: 'rounded-lg shadow-xl'
        }
    });
    <?php endif; ?>

    // Intersepsi Konfirmasi Hapus Data Mahasiswa dengan SweetAlert2
    $(document).on('click', 'a[href*="delete.php"]', function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        var rowName = $(this).closest('tr').find('td:nth-child(4)').text().trim();
        var targetText = rowName ? ('data mahasiswa <strong>' + rowName + '</strong>') : 'data ini';

        Swal.fire({
            title: 'Konfirmasi Hapus Data',
            html: 'Apakah Anda yakin ingin menghapus ' + targetText + '?<br><small class="text-danger mt-1 d-block"><i class="fas fa-exclamation-triangle mr-1"></i>File foto dan berkas dokumen terkait akan otomatis ikut dibersihkan.</small>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fas fa-trash mr-1"></i> Ya, Hapus Data!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then(function(result) {
            if (result.isConfirmed) {
                window.location.href = href;
            }
        });
    });

    // Intersepsi Konfirmasi Logout dengan SweetAlert2
    $(document).on('click', 'a[href*="logout.php"]', function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        Swal.fire({
            title: 'Konfirmasi Keluar',
            text: 'Apakah Anda yakin ingin mengakhiri sesi dan keluar dari sistem?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fas fa-sign-out-alt mr-1"></i> Ya, Keluar',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then(function(result) {
            if (result.isConfirmed) {
                window.location.href = href;
            }
        });
    });
});
</script>
</body>
</html>

